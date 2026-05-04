<?php

namespace App\Http\Controllers;

use App\Models\KenaikanKelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TranskripsNilai;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RaportController extends Controller
{
    public function index(Request $request)
    {
        $search    = trim((string) $request->input('search', ''));
        $siswaId   = $request->integer('siswa_id');

        // Selalu load SEMUA siswa aktif agar JS live search punya data lengkap
        $siswas = Siswa::query()
            ->where('is_active', true)
            ->orderBy('nama')
            ->get(['id', 'nama', 'nis', 'kelas_id']);

        $selectedSiswa = null;
        $raportData    = collect(); // Collection of kelas-groups

        if ($siswaId > 0) {
            $selectedSiswa = Siswa::with(['kelas', 'kartuPelajar'])->find($siswaId);

            if ($selectedSiswa) {
                $raportData = $this->buildRaportData($selectedSiswa);
            }
        }

        return view('akademik.raport.index', [
            'siswas'        => $siswas,
            'selectedSiswa' => $selectedSiswa,
            'raportData'    => $raportData,
            'search'        => $search,
        ]);
    }

    public function pdf(Request $request, Siswa $siswa)
    {
        $siswa->load(['kelas', 'kartuPelajar']);
        $raportData = $this->buildRaportData($siswa);

        $pdf = Pdf::loadView('akademik.raport.pdf', [
            'siswa'      => $siswa,
            'raportData' => $raportData,
        ])->setPaper('a4', 'portrait');

        $namaFile = 'raport-' . \Illuminate\Support\Str::slug((string) $siswa->nama) . '.pdf';

        return $pdf->download($namaFile);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build raport data grouped by kelas (ascending tingkat).
     * Each group has semester 1 & semester 2 nilai.
     */
    private function buildRaportData(Siswa $siswa): Collection
    {
        // ── 1. Ambil semua kelas yang pernah ditempuh siswa dari riwayat kenaikan kelas
        //       + kelas saat ini jika belum pernah naik kelas
        $kelasRiwayat = $this->getKelasRiwayat($siswa);

        if ($kelasRiwayat->isEmpty()) {
            return collect();
        }

        // ── 2. Semua transkrip nilai siswa ini (semua kelas & semester)
        $semuaTranskrip = TranskripsNilai::query()
            ->with(['mataPelajaran', 'semester', 'tahunAjaran'])
            ->where('siswa_id', $siswa->id)
            ->get();

        // ── 3. Semua semester, keyed by id
        $semesterAll = Semester::orderBy('nomor_semester')->get()->keyBy('id');

        // ── 4. Build raport per kelas
        $raportData = $kelasRiwayat->map(function ($kelasInfo) use ($semuaTranskrip, $semesterAll) {
            $tahunAjaranId  = $kelasInfo['tahun_ajaran_id'] ?? null;
            $namaKelas      = $kelasInfo['nama_kelas'];
            $tingkat        = $kelasInfo['tingkat'];
            $statusKenaikan = $kelasInfo['status'];

            // Filter transkrip untuk tahun ajaran ini
            $transkripKelas = $semuaTranskrip->filter(function ($t) use ($tahunAjaranId) {
                if ($tahunAjaranId) {
                    return (int) $t->tahun_ajaran_id === (int) $tahunAjaranId;
                }
                return true;
            });

            // Kelompokkan per semester (nomor_semester 1 & 2)
            $perSemester = [];
            foreach ([1, 2] as $semesterNomor) {
                // Cari semester dengan nomor_semester = $semesterNomor di tahun ajaran ini
                $semester = $semesterAll->first(function ($s) use ($semesterNomor, $tahunAjaranId) {
                    $matchNomor = (int) ($s->nomor_semester ?? 0) === $semesterNomor;
                    if ($tahunAjaranId) {
                        return $matchNomor && (int) $s->tahun_ajaran_id === (int) $tahunAjaranId;
                    }
                    return $matchNomor;
                });

                $semesterId = $semester?->id ?? null;

                $transkripSemester = $semesterId
                    ? $transkripKelas->filter(fn ($t) => (int) $t->semester_id === (int) $semesterId)
                    : collect();

                // Build rows per mapel
                $mapelRows = $transkripSemester
                    ->sortBy(fn ($t) => $t->mataPelajaran?->nama_mapel ?? '')
                    ->map(fn ($t) => [
                        'mapel_nama'   => $t->mataPelajaran?->nama_mapel ?? '-',
                        'mapel_kode'   => $t->mataPelajaran?->kode_mapel ?? '',
                        'nilai_tugas'  => $t->nilai_harian,
                        'nilai_uts'    => $t->nilai_uts,
                        'nilai_uas'    => $t->nilai_uas,
                        'nilai_akhir'  => $t->nilai_akhir,
                        'grade'        => $t->grade,
                    ])
                    ->values();

                // Rata-rata nilai akhir
                $avgAkhir = $mapelRows->isNotEmpty()
                    ? $mapelRows->whereNotNull('nilai_akhir')->avg(fn ($r) => (float) $r['nilai_akhir'])
                    : null;

                $perSemester[$semesterNomor] = [
                    'semester_nama'  => $semester ? 'Semester ' . $semesterNomor : 'Semester ' . $semesterNomor,
                    'semester_id'    => $semesterId,
                    'tahun_ajaran'   => $kelasInfo['tahun_ajaran_nama'] ?? '-',
                    'mapel_rows'     => $mapelRows,
                    'avg_nilai'      => $avgAkhir !== null ? round($avgAkhir, 2) : null,
                    'avg_grade'      => $avgAkhir !== null ? $this->nilaiToGrade($avgAkhir) : null,
                ];
            }

            return [
                'kelas_id'       => $kelasInfo['kelas_id'],
                'nama_kelas'     => $namaKelas,
                'tingkat'        => $tingkat,
                'tahun_ajaran'   => $kelasInfo['tahun_ajaran_nama'] ?? '-',
                'status'         => $statusKenaikan,
                'per_semester'   => $perSemester,
            ];
        });

        // Urutkan ascending berdasarkan tingkat
        return $raportData->sortBy('tingkat')->values();
    }

    /**
     * Ambil riwayat kelas siswa dari tabel kenaikan_kelas + kelas saat ini.
     * Return Collection of ['kelas_id', 'nama_kelas', 'tingkat', 'tahun_ajaran_id', 'tahun_ajaran_nama', 'status']
     */
    private function getKelasRiwayat(Siswa $siswa): Collection
    {
        $riwayat = collect();

        // ── Riwayat dari kenaikan kelas (kelas_sekarang_id = kelas yang pernah ditempuh)
        $kenaikanList = KenaikanKelas::query()
            ->with(['kelasSekarang', 'tahunAjaran'])
            ->where('siswa_id', $siswa->id)
            ->orderBy('tahun_ajaran_id')
            ->orderBy('id')
            ->get();

        foreach ($kenaikanList as $kk) {
            $kelas = $kk->kelasSekarang;
            if (!$kelas) continue;

            $key = $kelas->id . '_' . ($kk->tahun_ajaran_id ?? 0);
            if ($riwayat->has($key)) continue;

            $riwayat->put($key, [
                'kelas_id'         => $kelas->id,
                'nama_kelas'       => $kelas->nama_kelas,
                'tingkat'          => (int) ($kelas->tingkat ?? 99),
                'tahun_ajaran_id'  => $kk->tahun_ajaran_id,
                'tahun_ajaran_nama'=> $kk->tahunAjaran?->nama ?? '-',
                'status'           => $kk->status ?? null,
            ]);
        }

        // ── Kelas saat ini (jika belum ada di riwayat)
        if ($siswa->kelas_id) {
            $kelas = $siswa->kelas;
            if ($kelas) {
                // Cari apakah sudah ada di riwayat
                $sudahAda = $riwayat->contains(fn ($r) => $r['kelas_id'] === $kelas->id);
                if (!$sudahAda) {
                    $tahunAjaran = TahunAjaran::query()->where('is_active', true)->first();
                    $riwayat->push([
                        'kelas_id'          => $kelas->id,
                        'nama_kelas'        => $kelas->nama_kelas,
                        'tingkat'           => (int) ($kelas->tingkat ?? 99),
                        'tahun_ajaran_id'   => $tahunAjaran?->id,
                        'tahun_ajaran_nama' => $tahunAjaran?->nama ?? '-',
                        'status'            => 'aktif',
                    ]);
                }
            }
        }

        // Jika tidak ada riwayat apapun, return empty
        return $riwayat->values();
    }

    private function nilaiToGrade(?float $nilai): ?string
    {
        if ($nilai === null) return null;
        if ($nilai >= 85) return 'A';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 60) return 'C';
        if ($nilai >= 50) return 'D';
        return 'E';
    }
}
