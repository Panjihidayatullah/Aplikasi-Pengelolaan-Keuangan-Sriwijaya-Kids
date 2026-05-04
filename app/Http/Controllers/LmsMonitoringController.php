<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Absensi;
use App\Models\AbsensiDetail;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\PengumpulanTugas;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TranskripsNilai;
use App\Models\Tugas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LmsMonitoringController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can_access('view lms-monitoring'), 403);

        $kelases = $this->resolveKelasOptionsForCurrentUser();
        $semesters = Semester::query()->with('tahunAjaran')->orderBy('nomor_semester')->get();
        $tahunAjarans = TahunAjaran::query()->orderBy('nama', 'desc')->get();

        $selectedKelasId = $this->resolveSelectedKelasId($request, $kelases);
        $selectedSemesterId = $this->resolveSelectedSemesterId($request, $semesters);
        $selectedTahunAjaranId = $this->resolveSelectedTahunAjaranId($request, $selectedSemesterId, $semesters, $tahunAjarans);

        $selectedKelas = $selectedKelasId
            ? $kelases->firstWhere('id', $selectedKelasId)
            : null;
        $selectedSemester = $selectedSemesterId
            ? $semesters->firstWhere('id', $selectedSemesterId)
            : null;
        $selectedTahunAjaran = $selectedTahunAjaranId
            ? $tahunAjarans->firstWhere('id', $selectedTahunAjaranId)
            : null;

        [$periodStart, $periodEnd] = $this->resolvePeriodRange($selectedSemester, $selectedTahunAjaran);

        $siswaSummary = Siswa::query()
            ->whereRaw('1 = 0')
            ->paginate(10)
            ->withQueryString();

        $totalAbsensiClass = 0;
        $totalTugasClass = 0;
        $canEditTranskrip = is_admin() || auth()->user()->hasRole('Guru');

        if ($selectedKelasId) {
            $siswaSummary = Siswa::query()
                ->with('kartuPelajar')
                ->where('kelas_id', $selectedKelasId)
                ->where('is_active', true)
                ->orderBy('nama')
                ->paginate(10)
                ->withQueryString();

            $siswaIds = $siswaSummary->getCollection()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $totalAbsensiClassQuery = Absensi::query()
                ->where('kelas_id', $selectedKelasId);
            $this->applyPeriodFilter($totalAbsensiClassQuery, $periodStart, $periodEnd, 'tanggal_absensi');
            $totalAbsensiClass = (int) $totalAbsensiClassQuery->count();

            $totalTugasClass = (int) Tugas::query()
                ->where('kelas_id', $selectedKelasId)
                ->when($selectedSemesterId, fn (Builder $q) => $q->where('semester_id', $selectedSemesterId))
                ->when($selectedTahunAjaranId, fn (Builder $q) => $q->where('tahun_ajaran_id', $selectedTahunAjaranId))
                ->count();

            $absensiAgg = collect();
            $pengumpulanAgg = collect();
            $nilaiAgg = collect();

            if (!empty($siswaIds)) {
                $absensiAggQuery = AbsensiDetail::query()
                    ->selectRaw("absensi_detail.siswa_id, count(*) as total_absensi_detail, sum(case when absensi_detail.status = 'hadir' then 1 else 0 end) as hadir_count")
                    ->join('absensi', 'absensi.id', '=', 'absensi_detail.absensi_id')
                    ->whereIn('absensi_detail.siswa_id', $siswaIds)
                    ->where('absensi.kelas_id', $selectedKelasId)
                    ->whereNull('absensi.deleted_at')
                    ->groupBy('absensi_detail.siswa_id');
                $this->applyPeriodFilter($absensiAggQuery, $periodStart, $periodEnd, 'absensi.tanggal_absensi');
                $absensiAgg = $absensiAggQuery->get()->keyBy(fn ($row) => (int) $row->siswa_id);

                $pengumpulanAgg = PengumpulanTugas::query()
                    ->selectRaw("pengumpulan_tugas.siswa_id, count(distinct pengumpulan_tugas.tugas_id) as submit_count, count(distinct case when pengumpulan_tugas.nilai is not null then pengumpulan_tugas.tugas_id end) as graded_count")
                    ->join('tugas', 'tugas.id', '=', 'pengumpulan_tugas.tugas_id')
                    ->whereIn('pengumpulan_tugas.siswa_id', $siswaIds)
                    ->where('tugas.kelas_id', $selectedKelasId)
                    ->whereNull('pengumpulan_tugas.deleted_at')
                    ->whereNull('tugas.deleted_at')
                    ->when($selectedSemesterId, fn (Builder $q) => $q->where('tugas.semester_id', $selectedSemesterId))
                    ->when($selectedTahunAjaranId, fn (Builder $q) => $q->where('tugas.tahun_ajaran_id', $selectedTahunAjaranId))
                    ->groupBy('pengumpulan_tugas.siswa_id')
                    ->get()
                    ->keyBy(fn ($row) => (int) $row->siswa_id);

                $nilaiAgg = TranskripsNilai::query()
                    ->selectRaw('siswa_id, avg(nilai_uts) as avg_uts, avg(nilai_uas) as avg_uas, avg(nilai_akhir) as avg_total_nilai, max(id) as latest_transkrip_id')
                    ->whereIn('siswa_id', $siswaIds)
                    ->when($selectedSemesterId, fn (Builder $q) => $q->where('semester_id', $selectedSemesterId))
                    ->when($selectedTahunAjaranId, fn (Builder $q) => $q->where('tahun_ajaran_id', $selectedTahunAjaranId))
                    ->groupBy('siswa_id')
                    ->get()
                    ->keyBy(fn ($row) => (int) $row->siswa_id);
            }

            $siswaSummary->setCollection(
                $siswaSummary->getCollection()->map(function (Siswa $siswa) use ($absensiAgg, $pengumpulanAgg, $nilaiAgg, $totalAbsensiClass, $totalTugasClass) {
                    $siswaId = (int) $siswa->id;
                    $absensiRow = $absensiAgg->get($siswaId);
                    $pengumpulanRow = $pengumpulanAgg->get($siswaId);
                    $nilaiRow = $nilaiAgg->get($siswaId);

                    return [
                        'id' => $siswaId,
                        'nisn' => $this->resolveStudentNisn($siswa),
                        'nama' => (string) $siswa->nama,
                        'absensi_hadir' => (int) ($absensiRow->hadir_count ?? 0),
                        'absensi_total' => $totalAbsensiClass,
                        'tugas_submit' => (int) ($pengumpulanRow->submit_count ?? 0),
                        'tugas_total' => $totalTugasClass,
                        'nilai_isi' => (int) ($pengumpulanRow->graded_count ?? 0),
                        'nilai_total' => $totalTugasClass,
                        'uts' => $nilaiRow ? round((float) $nilaiRow->avg_uts, 2) : null,
                        'uas' => $nilaiRow ? round((float) $nilaiRow->avg_uas, 2) : null,
                        'total_nilai' => $nilaiRow ? round((float) $nilaiRow->avg_total_nilai, 2) : null,
                        'latest_transkrip_id' => (int) ($nilaiRow->latest_transkrip_id ?? 0) ?: null,
                    ];
                })
            );
        }

        return view('akademik.lms.monitoring.index', compact(
            'kelases',
            'semesters',
            'tahunAjarans',
            'selectedKelasId',
            'selectedSemesterId',
            'selectedTahunAjaranId',
            'selectedKelas',
            'selectedSemester',
            'selectedTahunAjaran',
            'siswaSummary',
            'totalAbsensiClass',
            'totalTugasClass',
            'canEditTranskrip'
        ));
    }

    public function show(Request $request, Siswa $siswa)
    {
        abort_unless(can_access('view lms-monitoring'), 403);

        $kelases = $this->resolveKelasOptionsForCurrentUser();
        $allowedClassIds = $kelases->pluck('id')->map(fn ($id) => (int) $id)->all();

        $siswa->load(['kelas', 'kartuPelajar']);
        abort_unless(in_array((int) $siswa->kelas_id, $allowedClassIds, true), 403);

        $semesters = Semester::query()->with('tahunAjaran')->orderBy('nomor_semester')->get();
        $tahunAjarans = TahunAjaran::query()->orderBy('nama', 'desc')->get();

        $selectedSemesterId = $this->resolveSelectedSemesterId($request, $semesters);
        $selectedTahunAjaranId = $this->resolveSelectedTahunAjaranId($request, $selectedSemesterId, $semesters, $tahunAjarans);

        $selectedSemester = $selectedSemesterId
            ? $semesters->firstWhere('id', $selectedSemesterId)
            : null;
        $selectedTahunAjaran = $selectedTahunAjaranId
            ? $tahunAjarans->firstWhere('id', $selectedTahunAjaranId)
            : null;

        [$periodStart, $periodEnd] = $this->resolvePeriodRange($selectedSemester, $selectedTahunAjaran);
        $detailPayload = $this->buildStudentMapelSummary($siswa, $selectedSemesterId, $selectedTahunAjaranId, $periodStart, $periodEnd);

        $canEditTranskrip = is_admin() || auth()->user()->hasRole('Guru');

        return view('akademik.lms.monitoring.show', [
            'siswa' => $siswa,
            'studentNisn' => $this->resolveStudentNisn($siswa),
            'selectedKelas' => $siswa->kelas,
            'selectedSemesterId' => $selectedSemesterId,
            'selectedTahunAjaranId' => $selectedTahunAjaranId,
            'selectedSemester' => $selectedSemester,
            'selectedTahunAjaran' => $selectedTahunAjaran,
            'detailRows' => $detailPayload['rows'],
            'detailTotals' => $detailPayload['totals'],
            'canEditTranskrip' => $canEditTranskrip,
        ]);
    }

    public function exportPdf(Request $request, Siswa $siswa)
    {
        abort_unless(can_access('view lms-monitoring'), 403);

        $kelases = $this->resolveKelasOptionsForCurrentUser();
        $allowedClassIds = $kelases->pluck('id')->map(fn ($id) => (int) $id)->all();

        $siswa->load(['kelas', 'kartuPelajar']);
        abort_unless(in_array((int) $siswa->kelas_id, $allowedClassIds, true), 403);

        $semesters = Semester::query()->with('tahunAjaran')->orderBy('nomor_semester')->get();
        $tahunAjarans = TahunAjaran::query()->orderBy('nama', 'desc')->get();

        $selectedSemesterId = $this->resolveSelectedSemesterId($request, $semesters);
        $selectedTahunAjaranId = $this->resolveSelectedTahunAjaranId($request, $selectedSemesterId, $semesters, $tahunAjarans);

        $selectedSemester = $selectedSemesterId
            ? $semesters->firstWhere('id', $selectedSemesterId)
            : null;
        $selectedTahunAjaran = $selectedTahunAjaranId
            ? $tahunAjarans->firstWhere('id', $selectedTahunAjaranId)
            : null;

        [$periodStart, $periodEnd] = $this->resolvePeriodRange($selectedSemester, $selectedTahunAjaran);
        $detailPayload = $this->buildStudentMapelSummary($siswa, $selectedSemesterId, $selectedTahunAjaranId, $periodStart, $periodEnd);

        $namaFile = 'monitoring-raport-' . str_replace(' ', '-', strtolower((string) $siswa->nama)) . '.pdf';
        $cacheKey = implode(':', [
            'lms-monitoring-pdf',
            (int) $siswa->id,
            (int) ($selectedSemesterId ?? 0),
            (int) ($selectedTahunAjaranId ?? 0),
            $periodStart ?: 'na',
            $periodEnd ?: 'na',
        ]);

        $pdfBinary = Cache::remember($cacheKey, now()->addSeconds(90), function () use ($siswa, $selectedSemester, $selectedTahunAjaran, $detailPayload) {
            return Pdf::loadView('akademik.lms.monitoring.pdf', [
                'siswa' => $siswa,
                'studentNisn' => $this->resolveStudentNisn($siswa),
                'selectedKelas' => $siswa->kelas,
                'selectedSemester' => $selectedSemester,
                'selectedTahunAjaran' => $selectedTahunAjaran,
                'detailRows' => $detailPayload['rows'],
                'detailTotals' => $detailPayload['totals'],
                'dicetakPada' => now(),
            ])
                ->setPaper('a4', 'portrait')
                ->output();
        });

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $namaFile . '"',
            'Cache-Control' => 'private, max-age=90',
        ]);
    }

    private function resolveKelasOptionsForCurrentUser()
    {
        $user = auth()->user();

        if (is_admin() || $user->hasRole('Kepala Sekolah')) {
            return Kelas::query()->orderByTingkat()->orderBy('nama_kelas')->get();
        }

        if ($user->hasRole('Guru')) {
            $guruId = (int) optional($user->guru)->id;
            if ($guruId <= 0) {
                return collect();
            }

            $kelasIds = JadwalPelajaran::query()
                ->where('guru_id', $guruId)
                ->where('is_active', true)
                ->where('is_istirahat', false)
                ->whereNotNull('kelas_id')
                ->pluck('kelas_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($kelasIds->isEmpty()) {
                return collect();
            }

            return Kelas::query()
                ->whereIn('id', $kelasIds)
                ->orderByTingkat()
                ->orderBy('nama_kelas')
                ->get();
        }

        if (is_siswa()) {
            $kelasId = (int) optional($user->siswa)->kelas_id;

            return $kelasId > 0
                ? Kelas::query()->whereKey($kelasId)->get()
                : collect();
        }

        return Kelas::query()->orderByTingkat()->orderBy('nama_kelas')->get();
    }

    private function resolveSelectedKelasId(Request $request, $kelasOptions): ?int
    {
        $kelasId = $request->filled('kelas_id')
            ? $request->integer('kelas_id')
            : null;

        if (!$kelasId) {
            return $kelasOptions->count() === 1
                ? (int) optional($kelasOptions->first())->id
                : null;
        }

        $exists = $kelasOptions->contains(fn ($kelas) => (int) $kelas->id === (int) $kelasId);

        return $exists ? (int) $kelasId : null;
    }

    private function resolveSelectedSemesterId(Request $request, Collection $semesters): ?int
    {
        $requestedSemesterId = $request->filled('semester_id')
            ? $request->integer('semester_id')
            : null;

        if ($requestedSemesterId) {
            return $semesters->contains(fn ($semester) => (int) $semester->id === (int) $requestedSemesterId)
                ? (int) $requestedSemesterId
                : null;
        }

        $activeSemester = $semesters->firstWhere('is_active', true);

        return $activeSemester ? (int) $activeSemester->id : null;
    }

    private function resolveSelectedTahunAjaranId(
        Request $request,
        ?int $selectedSemesterId,
        Collection $semesters,
        Collection $tahunAjarans
    ): ?int {
        $requestedTahunAjaranId = $request->filled('tahun_ajaran_id')
            ? $request->integer('tahun_ajaran_id')
            : null;

        if ($requestedTahunAjaranId) {
            return $tahunAjarans->contains(fn ($tahun) => (int) $tahun->id === (int) $requestedTahunAjaranId)
                ? (int) $requestedTahunAjaranId
                : null;
        }

        if ($selectedSemesterId) {
            $semester = $semesters->firstWhere('id', $selectedSemesterId);
            $tahunAjaranId = (int) ($semester?->tahun_ajaran_id ?? 0);

            if ($tahunAjaranId > 0) {
                return $tahunAjaranId;
            }
        }

        $activeTahunAjaran = $tahunAjarans->firstWhere('is_active', true);

        return $activeTahunAjaran ? (int) $activeTahunAjaran->id : null;
    }

    private function resolvePeriodRange($selectedSemester, $selectedTahunAjaran): array
    {
        if ($selectedSemester && $selectedSemester->tanggal_mulai && $selectedSemester->tanggal_selesai) {
            return [
                $selectedSemester->tanggal_mulai->toDateString(),
                $selectedSemester->tanggal_selesai->toDateString(),
            ];
        }

        if ($selectedTahunAjaran && $selectedTahunAjaran->tanggal_mulai && $selectedTahunAjaran->tanggal_selesai) {
            return [
                $selectedTahunAjaran->tanggal_mulai->toDateString(),
                $selectedTahunAjaran->tanggal_selesai->toDateString(),
            ];
        }

        return [null, null];
    }

    private function applyPeriodFilter(Builder|QueryBuilder $query, ?string $startDate, ?string $endDate, string $column): void
    {
        if ($startDate && $endDate) {
            $query->where($column, '>=', $startDate)
                ->where($column, '<=', $endDate);

            return;
        }

        if ($startDate) {
            $query->where($column, '>=', $startDate);
        }

        if ($endDate) {
            $query->where($column, '<=', $endDate);
        }
    }

    private function resolveStudentNisn(Siswa $siswa): string
    {
        return (string) ($siswa->nis ?: optional($siswa->kartuPelajar->first())->nis_otomatis ?: '-');
    }

    private function buildStudentMapelSummary(
        Siswa $siswa,
        ?int $selectedSemesterId,
        ?int $selectedTahunAjaranId,
        ?string $periodStart,
        ?string $periodEnd
    ): array {
        $kelasId = (int) $siswa->kelas_id;

        $absensiTotalsPerMapelQuery = Absensi::query()
            ->selectRaw('mata_pelajaran_id, count(*) as total_absensi')
            ->where('kelas_id', $kelasId)
            ->whereNotNull('mata_pelajaran_id')
            ->groupBy('mata_pelajaran_id');
        $this->applyPeriodFilter($absensiTotalsPerMapelQuery, $periodStart, $periodEnd, 'tanggal_absensi');
        $absensiTotalsPerMapel = $absensiTotalsPerMapelQuery->pluck('total_absensi', 'mata_pelajaran_id');

        $absensiSiswaPerMapelQuery = AbsensiDetail::query()
            ->selectRaw("absensi.mata_pelajaran_id as mapel_id, count(*) as total_absensi_detail, sum(case when absensi_detail.status = 'hadir' then 1 else 0 end) as hadir_count")
            ->join('absensi', 'absensi.id', '=', 'absensi_detail.absensi_id')
            ->where('absensi_detail.siswa_id', (int) $siswa->id)
            ->where('absensi.kelas_id', $kelasId)
            ->whereNotNull('absensi.mata_pelajaran_id')
            ->whereNull('absensi.deleted_at')
            ->groupBy('absensi.mata_pelajaran_id');
        $this->applyPeriodFilter($absensiSiswaPerMapelQuery, $periodStart, $periodEnd, 'absensi.tanggal_absensi');
        $absensiSiswaPerMapel = $absensiSiswaPerMapelQuery
            ->get()
            ->keyBy(fn ($row) => (int) $row->mapel_id);

        $tugasTotalsPerMapel = Tugas::query()
            ->selectRaw('mata_pelajaran_id, count(*) as total_tugas')
            ->where('kelas_id', $kelasId)
            ->whereNotNull('mata_pelajaran_id')
            ->when($selectedSemesterId, fn (Builder $q) => $q->where('semester_id', $selectedSemesterId))
            ->when($selectedTahunAjaranId, fn (Builder $q) => $q->where('tahun_ajaran_id', $selectedTahunAjaranId))
            ->groupBy('mata_pelajaran_id')
            ->pluck('total_tugas', 'mata_pelajaran_id');

        $pengumpulanSiswaPerMapel = PengumpulanTugas::query()
            ->selectRaw("tugas.mata_pelajaran_id as mapel_id, count(distinct pengumpulan_tugas.tugas_id) as submit_count, count(distinct case when pengumpulan_tugas.nilai is not null then pengumpulan_tugas.tugas_id end) as graded_count")
            ->join('tugas', 'tugas.id', '=', 'pengumpulan_tugas.tugas_id')
            ->where('pengumpulan_tugas.siswa_id', (int) $siswa->id)
            ->where('tugas.kelas_id', $kelasId)
            ->whereNotNull('tugas.mata_pelajaran_id')
            ->whereNull('pengumpulan_tugas.deleted_at')
            ->whereNull('tugas.deleted_at')
            ->when($selectedSemesterId, fn (Builder $q) => $q->where('tugas.semester_id', $selectedSemesterId))
            ->when($selectedTahunAjaranId, fn (Builder $q) => $q->where('tugas.tahun_ajaran_id', $selectedTahunAjaranId))
            ->groupBy('tugas.mata_pelajaran_id')
            ->get()
            ->keyBy(fn ($row) => (int) $row->mapel_id);

        $transkripPerMapel = TranskripsNilai::query()
            ->with('mataPelajaran')
            ->where('siswa_id', (int) $siswa->id)
            ->when($selectedSemesterId, fn (Builder $q) => $q->where('semester_id', $selectedSemesterId))
            ->when($selectedTahunAjaranId, fn (Builder $q) => $q->where('tahun_ajaran_id', $selectedTahunAjaranId))
            ->get()
            ->keyBy(fn ($row) => (int) $row->mata_pelajaran_id);

        $mapelIds = collect()
            ->merge($absensiTotalsPerMapel->keys())
            ->merge($absensiSiswaPerMapel->keys())
            ->merge($tugasTotalsPerMapel->keys())
            ->merge($pengumpulanSiswaPerMapel->keys())
            ->merge($transkripPerMapel->keys())
            ->filter(fn ($id) => (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $mapelMap = MataPelajaran::query()
            ->whereIn('id', $mapelIds)
            ->get()
            ->keyBy('id');

        $rawRows = $mapelIds
            ->map(function (int $mapelId) use ($mapelMap, $absensiTotalsPerMapel, $absensiSiswaPerMapel, $tugasTotalsPerMapel, $pengumpulanSiswaPerMapel, $transkripPerMapel) {
                $mapel = $mapelMap->get($mapelId);
                $absensiSiswa = $absensiSiswaPerMapel->get($mapelId);
                $pengumpulanSiswa = $pengumpulanSiswaPerMapel->get($mapelId);
                $transkrip = $transkripPerMapel->get($mapelId);
                $totalTugasMapel = (int) ($tugasTotalsPerMapel->get($mapelId) ?? 0);

                return [
                    'mapel_id' => $mapelId,
                    'mapel_nama' => (string) ($mapel->nama ?? optional($transkrip?->mataPelajaran)->nama ?? '-'),
                    'absensi_hadir' => (int) ($absensiSiswa->hadir_count ?? 0),
                    'absensi_total' => (int) ($absensiTotalsPerMapel->get($mapelId) ?? 0),
                    'tugas_submit' => (int) ($pengumpulanSiswa->submit_count ?? 0),
                    'tugas_total' => $totalTugasMapel,
                    'nilai_isi' => (int) ($pengumpulanSiswa->graded_count ?? 0),
                    'nilai_total' => $totalTugasMapel,
                    'uts' => $transkrip ? round((float) $transkrip->nilai_uts, 2) : null,
                    'uas' => $transkrip ? round((float) $transkrip->nilai_uas, 2) : null,
                    'total_nilai' => $transkrip ? round((float) $transkrip->nilai_akhir, 2) : null,
                    'grade' => (string) ($transkrip->grade ?? '-'),
                    'transkrip_id' => (int) ($transkrip->id ?? 0) ?: null,
                ];
            })
            ->values();

        $rows = $rawRows
            ->groupBy(function (array $row): string {
                $normalizedName = strtolower(trim((string) ($row['mapel_nama'] ?? '')));

                if ($normalizedName === '' || $normalizedName === '-') {
                    return 'mapel-id-' . (string) ($row['mapel_id'] ?? 0);
                }

                return $normalizedName;
            })
            ->map(function (Collection $group): array {
                $first = $group->first();

                if ($group->count() === 1) {
                    return $first;
                }

                $avgUts = $group->pluck('uts')->filter(fn ($nilai) => $nilai !== null)->avg();
                $avgUas = $group->pluck('uas')->filter(fn ($nilai) => $nilai !== null)->avg();
                $avgTotalNilai = $group->pluck('total_nilai')->filter(fn ($nilai) => $nilai !== null)->avg();

                return [
                    'mapel_id' => (int) ($group->pluck('mapel_id')->first() ?? 0),
                    'mapel_nama' => (string) ($first['mapel_nama'] ?? '-'),
                    'absensi_hadir' => (int) $group->sum('absensi_hadir'),
                    'absensi_total' => (int) $group->sum('absensi_total'),
                    'tugas_submit' => (int) $group->sum('tugas_submit'),
                    'tugas_total' => (int) $group->sum('tugas_total'),
                    'nilai_isi' => (int) $group->sum('nilai_isi'),
                    'nilai_total' => (int) $group->sum('nilai_total'),
                    'uts' => $avgUts !== null ? round((float) $avgUts, 2) : null,
                    'uas' => $avgUas !== null ? round((float) $avgUas, 2) : null,
                    'total_nilai' => $avgTotalNilai !== null ? round((float) $avgTotalNilai, 2) : null,
                    'grade' => (string) ($first['grade'] ?? '-'),
                    'transkrip_id' => (int) ($group->pluck('transkrip_id')->filter()->sortDesc()->first() ?? 0) ?: null,
                ];
            })
            ->sortBy('mapel_nama', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $avgUts = $rows->pluck('uts')->filter(fn ($nilai) => $nilai !== null)->avg();
        $avgUas = $rows->pluck('uas')->filter(fn ($nilai) => $nilai !== null)->avg();
        $avgTotalNilai = $rows->pluck('total_nilai')->filter(fn ($nilai) => $nilai !== null)->avg();

        $totals = [
            'absensi_hadir' => (int) $rows->sum('absensi_hadir'),
            'absensi_total' => (int) $rows->sum('absensi_total'),
            'tugas_submit' => (int) $rows->sum('tugas_submit'),
            'tugas_total' => (int) $rows->sum('tugas_total'),
            'nilai_isi' => (int) $rows->sum('nilai_isi'),
            'nilai_total' => (int) $rows->sum('nilai_total'),
            'avg_uts' => $avgUts !== null ? round((float) $avgUts, 2) : null,
            'avg_uas' => $avgUas !== null ? round((float) $avgUas, 2) : null,
            'avg_total_nilai' => $avgTotalNilai !== null ? round((float) $avgTotalNilai, 2) : null,
        ];

        return [
            'rows' => $rows,
            'totals' => $totals,
        ];
    }
}
