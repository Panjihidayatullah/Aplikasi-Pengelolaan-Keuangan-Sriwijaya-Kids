<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\JadwalPelajaran;
use App\Models\KenaikanKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TranskripsNilai;
use App\Models\TranskripNilaiPengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class TranskripsNilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->isSiswaScope()) {
            return redirect()->route('akademik.transkrip-nilai.saya');
        }

        $kelasSearch = trim((string) $request->input('kelas_search'));
        $mapelSearch = trim((string) $request->input('mapel_search'));
        $isGuruScope = $this->isGuruScope();

        $guruAssignments = collect();
        $allowedKelasIds = collect();

        if ($isGuruScope) {
            $guruAssignments = $this->getGuruAssignments($this->currentGuruId());
            $allowedKelasIds = $guruAssignments->pluck('kelas_id')->filter()->unique()->values();
        }

        $kelasesQuery = Kelas::query()
            ->when($kelasSearch !== '', function ($query) use ($kelasSearch) {
                $query->where('nama_kelas', 'ilike', '%' . $kelasSearch . '%');
            })
            ->orderBy('nama_kelas');

        if ($isGuruScope) {
            if ($allowedKelasIds->isEmpty()) {
                $kelasesQuery->whereRaw('1 = 0');
            } else {
                $kelasesQuery->whereIn('id', $allowedKelasIds->all());
            }
        }

        $kelases = $kelasesQuery->get();

        $selectedKelas = null;
        $mapels = collect();
        $transkripCounts = collect();

        if ($request->filled('kelas_id')) {
            $selectedKelasId = $request->integer('kelas_id');

            if (!$isGuruScope || $allowedKelasIds->contains($selectedKelasId)) {
                $selectedKelas = Kelas::query()->find($selectedKelasId);
            }

            if ($selectedKelas) {
                $mapelsQuery = MataPelajaran::query()
                    ->when($mapelSearch !== '', function ($query) use ($mapelSearch) {
                        $query->where(function ($q) use ($mapelSearch) {
                            $q->where('nama_mapel', 'ilike', '%' . $mapelSearch . '%')
                                ->orWhere('kode_mapel', 'ilike', '%' . $mapelSearch . '%');
                        });
                    });

                if ($isGuruScope) {
                    $allowedMapelIds = $guruAssignments
                        ->where('kelas_id', $selectedKelas->id)
                        ->pluck('mata_pelajaran_id')
                        ->filter()
                        ->unique()
                        ->values();

                    if ($allowedMapelIds->isEmpty()) {
                        $mapelsQuery->whereRaw('1 = 0');
                    } else {
                        $mapelsQuery->whereIn('id', $allowedMapelIds->all());
                    }
                }

                $mapels = MataPelajaran::dropdownOptions($mapelsQuery);

                $countsQuery = TranskripsNilai::query()
                    ->selectRaw('mata_pelajaran_id, count(*) as total')
                    ->whereHas('siswa', fn ($q) => $q->where('kelas_id', $selectedKelas->id))
                    ->groupBy('mata_pelajaran_id');

                if ($isGuruScope) {
                    $countsQuery->whereIn('mata_pelajaran_id', $mapels->pluck('id')->all());
                }

                $transkripCounts = $countsQuery->pluck('total', 'mata_pelajaran_id');
            }
        }

        return view('akademik.transkrip-nilai.index', [
            'kelases' => $kelases,
            'selectedKelas' => $selectedKelas,
            'mapels' => $mapels,
            'transkripCounts' => $transkripCounts,
        ]);
    }

    public function byMapel(Request $request, Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        $this->authorizeGuruMapelKelas($kelas->id, $mataPelajaran->id);

        $semesters = Semester::query()->orderBy('nomor_semester')->get();
        $tahunAjarans = TahunAjaran::query()->orderBy('nama', 'desc')->get();

        $selectedSemesterId = $request->integer('semester_id')
            ?: (int) (Semester::query()->where('is_active', true)->value('id') ?: 0)
            ?: (int) (Semester::query()->value('id') ?: 0);

        $selectedTahunAjaranId = $request->integer('tahun_ajaran_id')
            ?: (int) (TahunAjaran::query()->where('is_active', true)->value('id') ?: 0)
            ?: (int) (TahunAjaran::query()->value('id') ?: 0);

        $studentsQuery = Siswa::query()
            ->with(['kelas', 'kartuPelajar'])
            ->where('kelas_id', $kelas->id)
            ->where('is_active', true);

        if ($request->filled('search')) {
            $keyword = trim((string) $request->input('search'));
            $studentsQuery->where(function ($q) use ($keyword) {
                $q->where('nama', 'ilike', '%' . $keyword . '%')
                    ->orWhere('nis', 'ilike', '%' . $keyword . '%')
                    ->orWhereHas('kelas', fn ($kelasQ) => $kelasQ->where('nama_kelas', 'ilike', '%' . $keyword . '%'))
                    ->orWhereHas('kartuPelajar', fn ($kpQ) => $kpQ->where('nis_otomatis', 'ilike', '%' . $keyword . '%'));
            });
        }

        $students = $studentsQuery->orderBy('nama')->get();

        $transkripBySiswa = collect();
        if ($selectedSemesterId && $selectedTahunAjaranId && $students->isNotEmpty()) {
            $transkripBySiswa = TranskripsNilai::query()
                ->where('mata_pelajaran_id', $mataPelajaran->id)
                ->where('semester_id', $selectedSemesterId)
                ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                ->whereIn('siswa_id', $students->pluck('id'))
                ->get()
                ->keyBy('siswa_id');
        }

        return view('akademik.transkrip-nilai.by-mapel', [
            'kelas' => $kelas,
            'mataPelajaran' => $mataPelajaran,
            'students' => $students,
            'transkripBySiswa' => $transkripBySiswa,
            'semesters' => $semesters,
            'tahunAjarans' => $tahunAjarans,
            'selectedSemesterId' => $selectedSemesterId,
            'selectedTahunAjaranId' => $selectedTahunAjaranId,
        ]);
    }

    public function pengaturan(Request $request)
    {
        abort_unless($this->canManagePengaturan(), 403);

        $pengaturan = TranskripNilaiPengaturan::getOrCreateDefault();

        return view('akademik.transkrip-nilai.pengaturan', [
            'pengaturan' => $pengaturan,
            'returnKelasId' => $request->integer('kelas_id') ?: null,
            'returnMapelId' => $request->integer('mapel_id') ?: null,
            'returnSemesterId' => $request->integer('semester_id') ?: null,
            'returnTahunAjaranId' => $request->integer('tahun_ajaran_id') ?: null,
            'returnSearch' => trim((string) $request->input('search')),
            'backUrl' => $this->pengaturanBackUrl($request),
        ]);
    }

    public function updatePengaturan(Request $request)
    {
        abort_unless($this->canManagePengaturan(), 403);

        $validated = $request->validate([
            'bobot_tugas' => 'required|numeric|min:0|max:100',
            'bobot_uts' => 'required|numeric|min:0|max:100',
            'bobot_uas' => 'required|numeric|min:0|max:100',
            'grade_a_min' => 'required|numeric|min:0|max:100',
            'grade_a_max' => 'required|numeric|min:0|max:100',
            'grade_b_min' => 'required|numeric|min:0|max:100',
            'grade_b_max' => 'required|numeric|min:0|max:100',
            'grade_c_min' => 'required|numeric|min:0|max:100',
            'grade_c_max' => 'required|numeric|min:0|max:100',
            'grade_d_min' => 'required|numeric|min:0|max:100',
            'grade_d_max' => 'required|numeric|min:0|max:100',
            'grade_e_min' => 'required|numeric|min:0|max:100',
            'grade_e_max' => 'required|numeric|min:0|max:100',
            'kelas_id' => 'nullable|integer',
            'mapel_id' => 'nullable|integer',
            'semester_id' => 'nullable|integer',
            'tahun_ajaran_id' => 'nullable|integer',
            'search' => 'nullable|string|max:100',
        ]);

        $total = (float) $validated['bobot_tugas'] + (float) $validated['bobot_uts'] + (float) $validated['bobot_uas'];
        if (abs($total - 100) > 0.01) {
            return back()
                ->withInput()
                ->withErrors(['bobot_tugas' => 'Total bobot harus tepat 100%.']);
        }

        $gradeValidationMessage = $this->validateGradeRanges($validated);
        if ($gradeValidationMessage !== null) {
            return back()
                ->withInput()
                ->withErrors(['grade_a_min' => $gradeValidationMessage]);
        }

        TranskripNilaiPengaturan::getOrCreateDefault()->update([
            'bobot_tugas' => $validated['bobot_tugas'],
            'bobot_uts' => $validated['bobot_uts'],
            'bobot_uas' => $validated['bobot_uas'],
            'grade_a_min' => $validated['grade_a_min'],
            'grade_a_max' => $validated['grade_a_max'],
            'grade_b_min' => $validated['grade_b_min'],
            'grade_b_max' => $validated['grade_b_max'],
            'grade_c_min' => $validated['grade_c_min'],
            'grade_c_max' => $validated['grade_c_max'],
            'grade_d_min' => $validated['grade_d_min'],
            'grade_d_max' => $validated['grade_d_max'],
            'grade_e_min' => $validated['grade_e_min'],
            'grade_e_max' => $validated['grade_e_max'],
        ]);

        $this->recalculateNilaiAkhir();

        $contextRequest = Request::create('/', 'GET', [
            'kelas_id' => $validated['kelas_id'] ?? null,
            'mapel_id' => $validated['mapel_id'] ?? null,
            'semester_id' => $validated['semester_id'] ?? null,
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'] ?? null,
            'search' => $validated['search'] ?? null,
        ]);

        return redirect($this->pengaturanBackUrl($contextRequest))
            ->with('success', 'Pengaturan bobot dan rentang grade berhasil diperbarui. Nilai akhir sudah direkalkulasi.');
    }

    public function saveNilaiMapel(Request $request, Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        $this->authorizeGuruMapelKelas($kelas->id, $mataPelajaran->id);

        $validated = $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'single_siswa_id' => 'nullable|exists:siswa,id',
            'nilai' => 'nullable|array',
            'nilai.*.nilai_harian' => 'nullable|numeric|min:0|max:100',
            'nilai.*.nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilai.*.nilai_uas' => 'nullable|numeric|min:0|max:100',
        ]);

        $allowedSiswaIds = Siswa::query()
            ->where('kelas_id', $kelas->id)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $nilaiRows = (array) ($validated['nilai'] ?? []);
        $singleSiswaId = isset($validated['single_siswa_id'])
            ? (string) $validated['single_siswa_id']
            : null;

        if ($singleSiswaId !== null) {
            $nilaiRows = array_key_exists($singleSiswaId, $nilaiRows)
                ? [$singleSiswaId => $nilaiRows[$singleSiswaId]]
                : [];
        }

        foreach ($nilaiRows as $siswaId => $nilai) {
            if (!in_array((string) $siswaId, $allowedSiswaIds, true)) {
                continue;
            }

            $nilaiTugas = isset($nilai['nilai_harian']) && $nilai['nilai_harian'] !== '' ? (float) $nilai['nilai_harian'] : null;
            $nilaiUts = isset($nilai['nilai_uts']) && $nilai['nilai_uts'] !== '' ? (float) $nilai['nilai_uts'] : null;
            $nilaiUas = isset($nilai['nilai_uas']) && $nilai['nilai_uas'] !== '' ? (float) $nilai['nilai_uas'] : null;

            if ($nilaiTugas === null && $nilaiUts === null && $nilaiUas === null) {
                continue;
            }

            $record = TranskripsNilai::firstOrNew([
                'siswa_id' => (int) $siswaId,
                'mata_pelajaran_id' => $mataPelajaran->id,
                'semester_id' => (int) $validated['semester_id'],
                'tahun_ajaran_id' => (int) $validated['tahun_ajaran_id'],
            ]);

            $record->nilai_harian = $nilaiTugas ?? 0;
            $record->nilai_uts = $nilaiUts ?? 0;
            $record->nilai_uas = $nilaiUas ?? 0;

            $hasilNilai = TranskripsNilai::hitungNilaiDanGrade(
                (float) $record->nilai_harian,
                (float) $record->nilai_uts,
                (float) $record->nilai_uas
            );

            $record->nilai_akhir = $hasilNilai['nilai_akhir'];
            $record->grade = $hasilNilai['grade'];
            $record->save();
        }

        return redirect()->route('akademik.transkrip-nilai.kelas-mapel', [
            'kelas' => $kelas,
            'mataPelajaran' => $mataPelajaran,
            'semester_id' => $validated['semester_id'],
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            'search' => $request->input('search'),
        ])->with('success', $singleSiswaId ? 'Nilai siswa berhasil disimpan per baris.' : 'Nilai siswa berhasil disimpan.');
    }

    public function exportByMapel(Request $request, Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        $this->authorizeGuruMapelKelas($kelas->id, $mataPelajaran->id);

        $payload = $this->buildByMapelExportPayload($request, $kelas, $mataPelajaran);
        $format = strtolower(trim((string) $request->input('format', 'excel')));

        if ($format === 'pdf') {
            $rows = $payload['students']->map(function ($student, $index) use ($payload) {
                $record = $payload['transkripBySiswa']->get($student->id);

                return [
                    'no' => $index + 1,
                    'nis' => (string) ($student->nis ?? optional($student->kartuPelajar->first())->nis_otomatis ?? '-'),
                    'nama_siswa' => (string) ($student->nama ?? '-'),
                    'nilai_tugas' => $record && $record->nilai_harian !== null ? number_format((float) $record->nilai_harian, 2, ',', '.') : '-',
                    'nilai_uts' => $record && $record->nilai_uts !== null ? number_format((float) $record->nilai_uts, 2, ',', '.') : '-',
                    'nilai_uas' => $record && $record->nilai_uas !== null ? number_format((float) $record->nilai_uas, 2, ',', '.') : '-',
                    'nilai_akhir' => $record && $record->nilai_akhir !== null ? number_format((float) $record->nilai_akhir, 2, ',', '.') : '-',
                    'grade' => (string) ($record->grade ?? '-'),
                ];
            });

            $filename = 'transkrip-nilai-'
                . Str::slug((string) ($kelas->nama ?? 'kelas'))
                . '-'
                . Str::slug((string) ($mataPelajaran->nama ?? 'mapel'))
                . '-semester-' . $payload['selectedSemesterId']
                . '-tahun-' . $payload['selectedTahunAjaranId']
                . '.pdf';

            $pdf = Pdf::loadView('akademik.transkrip-nilai.by-mapel-pdf', [
                'kelas' => $kelas,
                'mataPelajaran' => $mataPelajaran,
                'rows' => $rows,
                'selectedSemester' => $payload['selectedSemester'],
                'selectedTahunAjaran' => $payload['selectedTahunAjaran'],
                'dicetakPada' => now(),
            ])->setPaper('a4', 'landscape');

            return $pdf->download($filename);
        }

        $rows = $payload['students']->map(function ($student) use ($payload) {
            $record = $payload['transkripBySiswa']->get($student->id);

            return [
                (int) $student->id,
                (string) ($student->nis ?? optional($student->kartuPelajar->first())->nis_otomatis ?? ''),
                (string) ($student->nama ?? ''),
                $record ? (float) ($record->nilai_harian ?? 0) : null,
                $record ? (float) ($record->nilai_uts ?? 0) : null,
                $record ? (float) ($record->nilai_uas ?? 0) : null,
                $record ? (float) ($record->nilai_akhir ?? 0) : null,
                (string) ($record->grade ?? ''),
            ];
        })->all();

        $filename = 'transkrip-nilai-'
            . Str::slug((string) ($kelas->nama ?? 'kelas'))
            . '-'
            . Str::slug((string) ($mataPelajaran->nama ?? 'mapel'))
            . '-semester-' . $payload['selectedSemesterId']
            . '-tahun-' . $payload['selectedTahunAjaranId']
            . '.xlsx';

        $headings = [
            'siswa_id',
            'nis',
            'nama_siswa',
            'nilai_tugas',
            'nilai_uts',
            'nilai_uas',
            'nilai_akhir',
            'grade',
        ];

        return $this->downloadExcel($headings, $rows, $filename);
    }

    public function exportExcelByMapel(Request $request, Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        $request->query->set('format', 'excel');

        return $this->exportByMapel($request, $kelas, $mataPelajaran);
    }

    public function importExcelByMapel(Request $request, Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        $this->authorizeGuruMapelKelas($kelas->id, $mataPelajaran->id);

        $validated = $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'search' => 'nullable|string|max:100',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        // Ambil semua ID siswa aktif di kelas ini untuk validasi cepat
        $allowedStudentIds = Siswa::query()
            ->where('kelas_id', $kelas->id)
            ->where('is_active', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->flip() // [id => index] untuk lookup O(1)
            ->all();

        // Build NIS lookup: [nis_string => siswa_id]
        $allowedByNis = Siswa::query()
            ->where('kelas_id', $kelas->id)
            ->where('is_active', true)
            ->get(['id', 'nis'])
            ->mapWithKeys(fn ($s) => $s->nis ? [trim((string) $s->nis) => (int) $s->id] : [])
            ->all();

        // Baca semua baris secara raw (tanpa WithHeadingRow) agar key tidak di-slug
        $allRows = $this->readExcelAllRowsRaw($validated['excel_file']);

        if ($allRows->count() < 2) {
            return redirect()->route('akademik.transkrip-nilai.kelas-mapel', [
                'kelas' => $kelas,
                'mataPelajaran' => $mataPelajaran,
                'semester_id' => $validated['semester_id'],
                'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                'search' => $validated['search'] ?? null,
            ])->withErrors(['excel_file' => 'File Excel tidak berisi data yang bisa diimpor (minimal 2 baris: header + data).']);
        }

        // Baris pertama = header, baris selanjutnya = data
        $headerRow = $allRows->first()->values()->toArray();
        $dataRows  = $allRows->skip(1)->values();

        // Buat mapping: kolom index => nama key yang dinormalisasi
        $colMap = [];
        foreach ($headerRow as $colIdx => $colName) {
            if ($colName === null || trim((string) $colName) === '') continue;
            $normalized = strtolower(trim(preg_replace('/[\s\-\.]+/', '_', (string) $colName)));
            $colMap[$colIdx] = $normalized;
        }

        $processed = 0;
        $skipped = 0;
        $failed = 0;
        $rowErrors = [];

        foreach ($dataRows->values() as $index => $rawRow) {
            $line = $index + 2;

            // Map kolom berdasarkan posisi (immune terhadap key normalization)
            $row = [];
            $rawArr = $rawRow->values()->toArray();
            foreach ($rawArr as $colIdx => $value) {
                $key = $colMap[$colIdx] ?? ('col_' . $colIdx);
                $row[$key] = $value;
            }

            // Baca siswa_id dari kolom A (index 0), juga coba key alternatif
            $siswaId = 0;
            foreach (['siswa_id', 'siswa-id', 'id', 'student_id'] as $idKey) {
                $rawId = $row[$idKey] ?? null;
                if ($rawId !== null && trim((string) $rawId) !== '') {
                    $parsed = (int) round((float) $rawId);
                    if ($parsed > 0) { $siswaId = $parsed; break; }
                }
            }
            // Fallback: ambil langsung dari kolom pertama (index 0) jika key tidak cocok
            if ($siswaId <= 0 && isset($rawArr[0]) && $rawArr[0] !== null) {
                $parsed = (int) round((float) $rawArr[0]);
                if ($parsed > 0) $siswaId = $parsed;
            }

            // Jika siswa_id tidak valid atau tidak ada di kelas, coba via NIS
            if ($siswaId <= 0 || !array_key_exists($siswaId, $allowedStudentIds)) {
                $rawNis = $row['nis'] ?? '';
                // Handle float/scientific notation dari Excel
                if (is_float($rawNis) || is_int($rawNis)) {
                    $candidateNis = rtrim(number_format((float) $rawNis, 0, '.', ''), '.');
                } else {
                    $candidateNis = trim((string) $rawNis);
                    if (preg_match('/^[\d.]+[eE][+\-]?\d+$/', $candidateNis)) {
                        $candidateNis = rtrim(number_format((float) $candidateNis, 0, '.', ''), '.');
                    }
                }

                if ($candidateNis !== '' && isset($allowedByNis[$candidateNis])) {
                    $siswaId = $allowedByNis[$candidateNis];
                }
            }

            // Fallback terakhir: query langsung ke DB berdasarkan siswa_id dari Excel
            if ($siswaId > 0 && !array_key_exists($siswaId, $allowedStudentIds)) {
                $exists = Siswa::query()
                    ->where('id', $siswaId)
                    ->where('kelas_id', $kelas->id)
                    ->where('is_active', true)
                    ->exists();
                if ($exists) {
                    $allowedStudentIds[$siswaId] = 1; // tambahkan ke cache
                } else {
                    $failed++;
                    $rowErrors[] = 'Baris ' . $line . ': siswa dengan ID ' . $siswaId . ' tidak ditemukan di kelas ini.';
                    continue;
                }
            }

            if ($siswaId <= 0) {
                $failed++;
                $rowErrors[] = 'Baris ' . $line . ': siswa tidak dapat diidentifikasi (siswa_id atau NIS tidak valid).';
                continue;
            }


            // Fallback NIS dari kolom index 1 jika key 'nis' tidak ada di $row
            if (!array_key_exists('nis', $row) && isset($rawArr[1])) {
                $row['nis'] = $rawArr[1];
            }
            // Fallback nilai dari kolom index 3, 4, 5
            if (!array_key_exists('nilai_tugas', $row) && isset($rawArr[3])) {
                $row['nilai_tugas'] = $rawArr[3];
            }
            if (!array_key_exists('nilai_uts', $row) && isset($rawArr[4])) {
                $row['nilai_uts'] = $rawArr[4];
            }
            if (!array_key_exists('nilai_uas', $row) && isset($rawArr[5])) {
                $row['nilai_uas'] = $rawArr[5];
            }

            $nilaiTugas = $this->extractImportScore($row, ['nilai_tugas', 'nilai_harian', 'tugas']);
            $nilaiUts = $this->extractImportScore($row, ['nilai_uts', 'uts']);
            $nilaiUas = $this->extractImportScore($row, ['nilai_uas', 'uas']);

            if ($nilaiTugas === 'invalid' || $nilaiUts === 'invalid' || $nilaiUas === 'invalid') {
                $failed++;
                $rowErrors[] = 'Baris ' . $line . ': nilai harus angka di rentang 0 sampai 100.';
                continue;
            }

            if ($nilaiTugas === null && $nilaiUts === null && $nilaiUas === null) {
                $skipped++;
                continue;
            }

            $record = TranskripsNilai::firstOrNew([
                'siswa_id' => $siswaId,
                'mata_pelajaran_id' => $mataPelajaran->id,
                'semester_id' => (int) $validated['semester_id'],
                'tahun_ajaran_id' => (int) $validated['tahun_ajaran_id'],
            ]);

            $record->nilai_harian = $nilaiTugas ?? 0;
            $record->nilai_uts = $nilaiUts ?? 0;
            $record->nilai_uas = $nilaiUas ?? 0;

            $hasilNilai = TranskripsNilai::hitungNilaiDanGrade(
                (float) $record->nilai_harian,
                (float) $record->nilai_uts,
                (float) $record->nilai_uas
            );

            $record->nilai_akhir = $hasilNilai['nilai_akhir'];
            $record->grade = $hasilNilai['grade'];
            $record->save();
            $processed++;
        }

        $message = 'Import Excel selesai. Berhasil: ' . $processed
            . ' baris, dilewati: ' . $skipped
            . ' baris, gagal: ' . $failed . ' baris.';

        $redirect = redirect()->route('akademik.transkrip-nilai.kelas-mapel', [
            'kelas' => $kelas,
            'mataPelajaran' => $mataPelajaran,
            'semester_id' => $validated['semester_id'],
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            'search' => $validated['search'] ?? null,
        ]);

        if ($failed > 0) {
            return $redirect
                ->with('warning', $message)
                ->with('import_errors', array_slice($rowErrors, 0, 30));
        }

        return $redirect->with('success', $message);
    }

    public function downloadTemplate(Request $request, Kelas $kelas, MataPelajaran $mataPelajaran)
    {
        $this->authorizeGuruMapelKelas($kelas->id, $mataPelajaran->id);

        // Ambil siswa aktif di kelas ini
        $students = Siswa::query()
            ->with('kartuPelajar')
            ->where('kelas_id', $kelas->id)
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $headings = [
            'siswa_id',
            'nis',
            'nama_siswa',
            'nilai_tugas',
            'nilai_uts',
            'nilai_uas',
        ];

        // Baris data siswa (nilai dikosongkan agar siap diisi)
        // NIS disimpan sebagai string dengan prefix tab-trick (\t) agar Excel tidak konversi ke scientific notation
        $rows = $students->map(function ($student) {
            $nis = trim((string) ($student->nis ?? optional($student->kartuPelajar->first())->nis_otomatis ?? ''));
            return [
                (int) $student->id,  // siswa_id — jangan diubah
                $nis,                // nis — sebagai text
                (string) ($student->nama ?? ''),
                '', // nilai_tugas — isi angka 0-100
                '', // nilai_uts   — isi angka 0-100
                '', // nilai_uas   — isi angka 0-100
            ];
        })->toArray();

        $filename = 'template-nilai-'
            . \Illuminate\Support\Str::slug((string) ($kelas->nama ?? 'kelas'))
            . '-'
            . \Illuminate\Support\Str::slug((string) ($mataPelajaran->nama ?? 'mapel'))
            . '.xlsx';

        // Export dengan format TEXT pada kolom NIS agar tidak berubah ke scientific notation
        $export = new class($headings, $rows) implements
            \Maatwebsite\Excel\Concerns\FromArray,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\ShouldAutoSize,
            \Maatwebsite\Excel\Concerns\WithColumnFormatting
        {
            public function __construct(private array $headings, private array $rows) {}

            public function headings(): array { return $this->headings; }

            public function array(): array { return $this->rows; }

            /** Kolom B (nis) diformat TEXT agar angka panjang tidak jadi scientific notation */
            public function columnFormats(): array
            {
                return [
                    'A' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER,
                    'B' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
                ];
            }
        };

        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }


    public function create(Request $request)
    {
        if ($this->isSiswaScope()) {
            return redirect()->route('akademik.transkrip-nilai.saya');
        }

        $selectedKelasId = $request->integer('kelas_id');
        $selectedMapelId = $request->integer('mapel_id');
        $selectedSemesterId = $request->integer('semester_id');
        $selectedTahunAjaranId = $request->integer('tahun_ajaran_id');
        $selectedSiswaId = $request->integer('siswa_id');

        $siswasQuery = Siswa::with('kartuPelajar')
            ->where('is_active', true)
            ->when($selectedKelasId, fn ($q) => $q->where('kelas_id', $selectedKelasId));

        $mataPelajaransQuery = MataPelajaran::query();

        if ($this->isGuruScope()) {
            $guruAssignments = $this->getGuruAssignments($this->currentGuruId());
            $allowedKelasIds = $guruAssignments->pluck('kelas_id')->filter()->unique()->values();
            $allowedMapelIds = $guruAssignments->pluck('mata_pelajaran_id')->filter()->unique()->values();

            if ($selectedKelasId && $selectedMapelId) {
                $this->authorizeGuruMapelKelas($selectedKelasId, $selectedMapelId);
            }

            if ($allowedKelasIds->isEmpty()) {
                $siswasQuery->whereRaw('1 = 0');
            } else {
                $siswasQuery->whereIn('kelas_id', $allowedKelasIds->all());
            }

            if ($selectedKelasId) {
                $mapelPerKelas = $guruAssignments
                    ->where('kelas_id', $selectedKelasId)
                    ->pluck('mata_pelajaran_id')
                    ->filter()
                    ->unique()
                    ->values();

                if ($mapelPerKelas->isEmpty()) {
                    $mataPelajaransQuery->whereRaw('1 = 0');
                } else {
                    $mataPelajaransQuery->whereIn('id', $mapelPerKelas->all());
                }
            } elseif ($allowedMapelIds->isEmpty()) {
                $mataPelajaransQuery->whereRaw('1 = 0');
            } else {
                $mataPelajaransQuery->whereIn('id', $allowedMapelIds->all());
            }
        }

        $siswas = $siswasQuery
            ->orderBy('nama')
            ->get();

        $mataPelajarans = MataPelajaran::dropdownOptions($mataPelajaransQuery);
        $semesters = Semester::with('tahunAjaran')->orderBy('nomor_semester')->get();
        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();
        $bobot = TranskripNilaiPengaturan::getOrCreateDefault();

        return view('akademik.transkrip-nilai.create', compact(
            'siswas',
            'mataPelajarans',
            'semesters',
            'tahunAjarans',
            'selectedMapelId',
            'selectedSiswaId',
            'selectedKelasId',
            'selectedSemesterId',
            'selectedTahunAjaranId',
            'bobot'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($this->isSiswaScope()) {
            abort(403);
        }

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'semester_id' => 'required|exists:semester,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai_uas' => 'required|numeric|min:0|max:100',
            'nilai_harian' => 'required|numeric|min:0|max:100',
            'redirect_mapel_id' => 'nullable|exists:mata_pelajaran,id',
            'redirect_kelas_id' => 'nullable|exists:kelas,id',
            'redirect_semester_id' => 'nullable|exists:semester,id',
            'redirect_tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
        ]);

        if ($this->isGuruScope()) {
            $kelasId = (int) Siswa::query()->whereKey($validated['siswa_id'])->value('kelas_id');
            $this->authorizeGuruMapelKelas($kelasId, (int) $validated['mata_pelajaran_id']);
        }

        $hasilNilai = TranskripsNilai::hitungNilaiDanGrade(
            (float) $validated['nilai_harian'],
            (float) $validated['nilai_uts'],
            (float) $validated['nilai_uas']
        );

        $redirectMapelId = (int) ($validated['redirect_mapel_id'] ?? $validated['mata_pelajaran_id']);
        $redirectKelasId = (int) ($validated['redirect_kelas_id'] ?? Siswa::query()->whereKey($validated['siswa_id'])->value('kelas_id'));
        $redirectSemesterId = $validated['redirect_semester_id'] ?? $validated['semester_id'];
        $redirectTahunAjaranId = $validated['redirect_tahun_ajaran_id'] ?? $validated['tahun_ajaran_id'];

        unset(
            $validated['redirect_mapel_id'],
            $validated['redirect_kelas_id'],
            $validated['redirect_semester_id'],
            $validated['redirect_tahun_ajaran_id']
        );

        TranskripsNilai::create([
            ...$validated,
            ...$hasilNilai,
        ]);

        if ($redirectKelasId && $redirectMapelId) {
            return redirect()->route('akademik.transkrip-nilai.kelas-mapel', [
                'kelas' => $redirectKelasId,
                'mataPelajaran' => $redirectMapelId,
                'semester_id' => $redirectSemesterId,
                'tahun_ajaran_id' => $redirectTahunAjaranId,
            ])->with('success', 'Nilai siswa berhasil ditambahkan');
        }

        return redirect()->route('akademik.transkrip-nilai.index')
            ->with('success', 'Nilai siswa berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(TranskripsNilai $transkripNilai)
    {
        $this->authorizeTranskripAccess($transkripNilai);

        $siswa = $transkripNilai->siswa()->with(['kelas', 'kartuPelajar'])->firstOrFail();

        // Get all grades for this student grouped by semester
        $transkrip = TranskripsNilai::where('siswa_id', $siswa->id)
            ->with('semester', 'mataPelajaran', 'tahunAjaran')
            ->orderBy('tahun_ajaran_id')
            ->orderBy('semester_id')
            ->orderBy('mata_pelajaran_id')
            ->get()
            ->groupBy(function($item) {
                $semester = $item->semester->nama ?? 'Semester';
                $tahunAjaran = $item->tahunAjaran->nama ?? 'Tahun Ajaran';
                return $semester . ' - ' . $tahunAjaran;
            });
        
        return view('akademik.transkrip-nilai.show', compact('siswa', 'transkrip', 'transkripNilai'));
    }

    /**
     * Print transcript sheet in document layout.
     */
    public function print(TranskripsNilai $transkripNilai)
    {
        $this->authorizeTranskripAccess($transkripNilai);

        $siswa = $transkripNilai->siswa()->with(['kelas', 'kartuPelajar'])->firstOrFail();

        $nilais = TranskripsNilai::where('siswa_id', $siswa->id)
            ->with('mataPelajaran', 'semester', 'tahunAjaran')
            ->orderBy('tahun_ajaran_id')
            ->orderBy('semester_id')
            ->orderBy('mata_pelajaran_id')
            ->get();

        $nomorDokumen = sprintf(
            'TR/%s/%s/%s',
            str_pad((string) $siswa->id, 4, '0', STR_PAD_LEFT),
            now()->format('Ymd'),
            str_pad((string) $transkripNilai->id, 4, '0', STR_PAD_LEFT)
        );

        return view('akademik.transkrip-nilai.print', compact('siswa', 'nilais', 'nomorDokumen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TranskripsNilai $transkripNilai)
    {
        $this->authorizeTranskripAccess($transkripNilai);

        $transkripNilai->load('siswa', 'mataPelajaran', 'semester', 'tahunAjaran');

        return view('akademik.transkrip-nilai.edit', [
            'transkripNilai' => $transkripNilai,
            'bobot' => TranskripNilaiPengaturan::getOrCreateDefault(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TranskripsNilai $transkripNilai)
    {
        $this->authorizeTranskripAccess($transkripNilai);

        $validated = $request->validate([
            'nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai_uas' => 'required|numeric|min:0|max:100',
            'nilai_harian' => 'required|numeric|min:0|max:100',
        ]);

        $hasilNilai = TranskripsNilai::hitungNilaiDanGrade(
            (float) $validated['nilai_harian'],
            (float) $validated['nilai_uts'],
            (float) $validated['nilai_uas']
        );

        $transkripNilai->update([
            ...$validated,
            ...$hasilNilai,
        ]);

        return $this->redirectBackToContext($transkripNilai, 'Nilai siswa berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TranskripsNilai $transkripNilai)
    {
        $this->authorizeTranskripAccess($transkripNilai);

        $transkripNilai->delete();

        return $this->redirectBackToContext($transkripNilai, 'Nilai siswa berhasil dihapus');
    }

    public function siswaIndex(Request $request)
    {
        abort_unless($this->isSiswaScope(), 403);

        $siswa = $this->resolveCurrentSiswa($request);
        if (!$siswa) {
            return redirect()->route('dashboard')
                ->with('error', 'Profil siswa belum terhubung. Silakan hubungi admin untuk sinkronisasi akun siswa.');
        }

        $transkrip = TranskripsNilai::query()
            ->where('siswa_id', $siswa->id)
            ->with('semester', 'mataPelajaran', 'tahunAjaran')
            ->orderBy('tahun_ajaran_id')
            ->orderBy('semester_id')
            ->orderBy('mata_pelajaran_id')
            ->get()
            ->groupBy(function ($item) {
                $semester = $item->semester->nama ?? 'Semester';
                $tahunAjaran = $item->tahunAjaran->nama ?? 'Tahun Ajaran';

                return $semester . ' - ' . $tahunAjaran;
            });

        $kenaikanTerbaru = $this->latestKenaikanForSiswa((int) $siswa->id);

        return view('akademik.transkrip-nilai.siswa-index', [
            'siswa' => $siswa,
            'transkrip' => $transkrip,
            'kenaikanTerbaru' => $kenaikanTerbaru,
        ]);
    }

    public function siswaDownloadPdf(Request $request)
    {
        abort_unless($this->isSiswaScope(), 403);

        $siswa = $this->resolveCurrentSiswa($request);
        if (!$siswa) {
            return redirect()->route('akademik.transkrip-nilai.saya')
                ->with('error', 'Profil siswa belum terhubung. Export belum bisa diproses.');
        }

        $nilais = TranskripsNilai::query()
            ->where('siswa_id', $siswa->id)
            ->with('semester', 'mataPelajaran', 'tahunAjaran')
            ->orderBy('tahun_ajaran_id')
            ->orderBy('semester_id')
            ->orderBy('mata_pelajaran_id')
            ->get();

        $nomorDokumen = sprintf(
            'TR-SISWA/%s/%s',
            str_pad((string) $siswa->id, 4, '0', STR_PAD_LEFT),
            now()->format('Ymd')
        );

        $kenaikanTerbaru = $this->latestKenaikanForSiswa((int) $siswa->id);

        $pdf = Pdf::loadView('akademik.transkrip-nilai.siswa-pdf', [
            'siswa' => $siswa,
            'nilais' => $nilais,
            'nomorDokumen' => $nomorDokumen,
            'kenaikanTerbaru' => $kenaikanTerbaru,
        ])->setPaper('a4', 'portrait');

        $namaFile = 'transkrip-nilai-' . str_replace(' ', '-', strtolower((string) $siswa->nama)) . '.pdf';

        return $pdf->download($namaFile);
    }

    private function latestKenaikanForSiswa(int $siswaId): ?KenaikanKelas
    {
        $activeTahunAjaranId = (int) (TahunAjaran::query()->where('is_active', true)->value('id') ?: 0);

        if ($activeTahunAjaranId > 0) {
            /** @var KenaikanKelas|null $activeRecord */
            $activeRecord = KenaikanKelas::query()
                ->with('kelasSekarang', 'kelasTujuan', 'tahunAjaran')
                ->where('siswa_id', $siswaId)
                ->where('tahun_ajaran_id', $activeTahunAjaranId)
                ->orderByDesc('tanggal_penetapan')
                ->orderByDesc('id')
                ->first();

            if ($activeRecord) {
                return $activeRecord;
            }
        }

        /** @var KenaikanKelas|null $latestRecord */
        $latestRecord = KenaikanKelas::query()
            ->with('kelasSekarang', 'kelasTujuan', 'tahunAjaran')
            ->where('siswa_id', $siswaId)
            ->orderByDesc('tanggal_penetapan')
            ->orderByDesc('id')
            ->first();

        return $latestRecord;
    }

    private function downloadExcel(array $headings, array $rows, string $filename)
    {
        $export = new class($headings, $rows) implements FromArray, WithHeadings, ShouldAutoSize {
            public function __construct(private array $headings, private array $rows)
            {
            }

            public function headings(): array
            {
                return $this->headings;
            }

            public function array(): array
            {
                return $this->rows;
            }
        };

        return Excel::download($export, $filename);
    }

    private function readExcelHeadingRows($file): Collection
    {
        $import = new class implements ToCollection, WithHeadingRow {
            public Collection $rows;

            public function __construct()
            {
                $this->rows = collect();
            }

            public function collection(Collection $collection): void
            {
                $this->rows = $collection;
            }
        };

        Excel::import($import, $file);

        return $import->rows;
    }

    /**
     * Baca semua baris Excel sebagai array numerik (tanpa WithHeadingRow).
     * Row pertama biasanya adalah header, baris selanjutnya adalah data.
     */
    private function readExcelAllRowsRaw($file): Collection
    {
        $import = new class implements ToCollection {
            public Collection $rows;

            public function __construct()
            {
                $this->rows = collect();
            }

            public function collection(Collection $collection): void
            {
                $this->rows = $collection;
            }
        };

        Excel::import($import, $file);

        return $import->rows;
    }

    private function extractImportScore(array $row, array $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (!array_key_exists($key, $row)) {
                continue;
            }

            $rawValue = trim((string) ($row[$key] ?? ''));
            if ($rawValue === '') {
                return null;
            }

            $normalized = str_replace(',', '.', $rawValue);
            if (!is_numeric($normalized)) {
                return 'invalid';
            }

            $value = round((float) $normalized, 2);
            if ($value < 0 || $value > 100) {
                return 'invalid';
            }

            return $value;
        }

        return null;
    }

    private function buildByMapelExportPayload(Request $request, Kelas $kelas, MataPelajaran $mataPelajaran): array
    {
        $selectedSemesterId = $request->integer('semester_id')
            ?: (int) (Semester::query()->where('is_active', true)->value('id') ?: 0)
            ?: (int) (Semester::query()->value('id') ?: 0);

        $selectedTahunAjaranId = $request->integer('tahun_ajaran_id')
            ?: (int) (TahunAjaran::query()->where('is_active', true)->value('id') ?: 0)
            ?: (int) (TahunAjaran::query()->value('id') ?: 0);

        $search = trim((string) $request->input('search'));

        $studentsQuery = Siswa::query()
            ->with(['kelas', 'kartuPelajar'])
            ->where('kelas_id', $kelas->id)
            ->where('is_active', true);

        if ($search !== '') {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('nama', 'ilike', '%' . $search . '%')
                    ->orWhere('nis', 'ilike', '%' . $search . '%')
                    ->orWhereHas('kelas', fn ($kelasQ) => $kelasQ->where('nama_kelas', 'ilike', '%' . $search . '%'))
                    ->orWhereHas('kartuPelajar', fn ($kpQ) => $kpQ->where('nis_otomatis', 'ilike', '%' . $search . '%'));
            });
        }

        $students = $studentsQuery->orderBy('nama')->get();

        $transkripBySiswa = collect();
        if ($selectedSemesterId && $selectedTahunAjaranId && $students->isNotEmpty()) {
            $transkripBySiswa = TranskripsNilai::query()
                ->where('mata_pelajaran_id', $mataPelajaran->id)
                ->where('semester_id', $selectedSemesterId)
                ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                ->whereIn('siswa_id', $students->pluck('id'))
                ->get()
                ->keyBy('siswa_id');
        }

        return [
            'selectedSemesterId' => $selectedSemesterId,
            'selectedTahunAjaranId' => $selectedTahunAjaranId,
            'selectedSemester' => $selectedSemesterId ? Semester::query()->find($selectedSemesterId) : null,
            'selectedTahunAjaran' => $selectedTahunAjaranId ? TahunAjaran::query()->find($selectedTahunAjaranId) : null,
            'students' => $students,
            'transkripBySiswa' => $transkripBySiswa,
        ];
    }

    private function recalculateNilaiAkhir(?int $mataPelajaranId = null): void
    {
        $bobot = TranskripsNilai::bobotPersenAktif();

        TranskripsNilai::query()
            ->when($mataPelajaranId, fn ($q) => $q->where('mata_pelajaran_id', $mataPelajaranId))
            ->chunkById(200, function ($records) use ($bobot) {
                foreach ($records as $record) {
                    $hasilNilai = TranskripsNilai::hitungNilaiDanGrade(
                        (float) ($record->nilai_harian ?? 0),
                        (float) ($record->nilai_uts ?? 0),
                        (float) ($record->nilai_uas ?? 0),
                        $bobot
                    );

                    TranskripsNilai::query()
                        ->whereKey($record->id)
                        ->update([
                            'nilai_akhir' => $hasilNilai['nilai_akhir'],
                            'grade' => $hasilNilai['grade'],
                        ]);
                }
            });
    }

    private function validateGradeRanges(array $validated): ?string
    {
        $gradeOrder = ['A', 'B', 'C', 'D', 'E'];
        $normalized = collect($gradeOrder)
            ->mapWithKeys(function (string $grade) use ($validated) {
                $lower = strtolower($grade);

                return [
                    $grade => [
                        'min' => (float) $validated['grade_' . $lower . '_min'],
                        'max' => (float) $validated['grade_' . $lower . '_max'],
                    ],
                ];
            });

        foreach ($normalized as $grade => $range) {
            if ($range['min'] > $range['max']) {
                return 'Rentang grade ' . $grade . ' tidak valid: nilai minimum harus lebih kecil atau sama dengan maksimum.';
            }
        }

        foreach ($gradeOrder as $index => $grade) {
            if ($index === 0) {
                continue;
            }

            $prevGrade = $gradeOrder[$index - 1];
            $current = $normalized[$grade];
            $previous = $normalized[$prevGrade];

            if ($current['max'] >= $previous['min']) {
                return 'Rentang grade harus menurun dan tidak boleh tumpang tindih. Batas grade ' . $grade . ' bertabrakan dengan grade ' . $prevGrade . '.';
            }
        }

        if ($normalized['A']['max'] < 100) {
            return 'Batas maksimum grade A harus mencakup nilai 100.';
        }

        if ($normalized['E']['min'] > 0) {
            return 'Batas minimum grade E harus mencakup nilai 0.';
        }

        return null;
    }

    private function pengaturanBackUrl(Request $request): string
    {
        $kelasId = (int) $request->input('kelas_id');
        $mapelId = (int) $request->input('mapel_id');

        if ($kelasId > 0 && $mapelId > 0) {
            return route('akademik.transkrip-nilai.kelas-mapel', [
                'kelas' => $kelasId,
                'mataPelajaran' => $mapelId,
                'semester_id' => $request->input('semester_id') ?: null,
                'tahun_ajaran_id' => $request->input('tahun_ajaran_id') ?: null,
                'search' => $request->input('search') ?: null,
            ]);
        }

        return route('akademik.transkrip-nilai.index');
    }

    private function redirectBackToContext(TranskripsNilai $transkripNilai, string $message)
    {
        $kelasId = (int) optional($transkripNilai->siswa)->kelas_id;

        if ($kelasId && $transkripNilai->mata_pelajaran_id) {
            return redirect()->route('akademik.transkrip-nilai.kelas-mapel', [
                'kelas' => $kelasId,
                'mataPelajaran' => $transkripNilai->mata_pelajaran_id,
                'semester_id' => $transkripNilai->semester_id,
                'tahun_ajaran_id' => $transkripNilai->tahun_ajaran_id,
            ])->with('success', $message);
        }

        return redirect()->route('akademik.transkrip-nilai.index')->with('success', $message);
    }

    private function isGuruScope(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Guru');
    }

    private function isSiswaScope(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();
        if ($user->hasRole('Siswa')) {
            return true;
        }

        return $user->siswa()->exists();
    }

    private function canManagePengaturan(): bool
    {
        return can_access('create transkrip-nilai')
            || can_access('edit transkrip-nilai')
            || is_admin()
            || $this->isGuruScope();
    }

    private function resolveCurrentSiswa(?Request $request = null): ?Siswa
    {
        $user = $request?->user() ?? auth()->user();
        if (!$user) {
            return null;
        }

        $siswa = $user->siswa()->with(['kelas', 'kartuPelajar'])->first();
        if ($siswa) {
            return $siswa;
        }

        $email = mb_strtolower(trim((string) ($user->email ?? '')));
        if ($email === '') {
            return null;
        }

        $fallbackSiswa = Siswa::query()
            ->with(['kelas', 'kartuPelajar'])
            ->whereNotNull('email')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (!$fallbackSiswa) {
            return null;
        }

        if ((int) ($fallbackSiswa->user_id ?? 0) === 0) {
            $fallbackSiswa->update(['user_id' => (int) $user->id]);
        }

        return $fallbackSiswa;
    }

    private function currentGuruId(): ?int
    {
        return optional(auth()->user()?->guru)->id;
    }

    private function getGuruAssignments(?int $guruId)
    {
        if (!$guruId) {
            return collect();
        }

        return JadwalPelajaran::query()
            ->select('kelas_id', 'mata_pelajaran_id')
            ->where('guru_id', $guruId)
            ->whereNotNull('kelas_id')
            ->whereNotNull('mata_pelajaran_id')
            ->where('is_active', true)
            ->distinct()
            ->get();
    }

    private function authorizeGuruMapelKelas(int $kelasId, int $mataPelajaranId): void
    {
        if (!$this->isGuruScope()) {
            return;
        }

        $allowed = $this->getGuruAssignments($this->currentGuruId())
            ->contains(function ($row) use ($kelasId, $mataPelajaranId) {
                return (int) $row->kelas_id === $kelasId
                    && (int) $row->mata_pelajaran_id === $mataPelajaranId;
            });

        abort_unless($allowed, 403);
    }

    private function authorizeTranskripAccess(TranskripsNilai $transkripNilai): void
    {
        if ($this->isSiswaScope()) {
            $siswaId = (int) optional($this->resolveCurrentSiswa())->id;
            abort_unless($siswaId && $siswaId === (int) $transkripNilai->siswa_id, 403);

            return;
        }

        if ($this->isGuruScope()) {
            $kelasId = (int) optional($transkripNilai->siswa)->kelas_id;
            $mataPelajaranId = (int) $transkripNilai->mata_pelajaran_id;
            $this->authorizeGuruMapelKelas($kelasId, $mataPelajaranId);
        }
    }
}
