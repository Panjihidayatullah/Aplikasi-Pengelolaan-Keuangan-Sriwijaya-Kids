<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Absensi;
use App\Models\AbsensiDetail;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\LmsPertemuan;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\Siswa;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can_access('view absensi') || auth()->user()->hasRole('Guru') || is_admin() || $this->isSiswaScoped(), 403);

        $currentSiswa = $this->resolveCurrentSiswa();

        $isKepalaSekolahReadOnly = $this->isKepalaSekolahReadOnly();
        $canManageAbsensi = !$isKepalaSekolahReadOnly
            && (can_access('create absensi') || auth()->user()->hasRole('Guru') || is_admin());

        $semesters = Semester::query()->with('tahunAjaran')->orderByDesc('id')->get();
        $selectedSemesterId = $request->filled('semester_id')
            ? $request->integer('semester_id')
            : optional($semesters->firstWhere('is_active', true))->id;
        $selectedSemester = $selectedSemesterId
            ? Semester::query()->with('tahunAjaran')->find($selectedSemesterId)
            : null;

        if (!$selectedSemester && $semesters->isNotEmpty()) {
            $selectedSemester = $semesters->firstWhere('is_active', true) ?: $semesters->first();
            $selectedSemesterId = $selectedSemester?->id;
        }

        $availableBulanOptions = $this->getAvailableBulanOptions($selectedSemester);
        $monthAdjusted = false;
        $pertemuanTanggalFocus = null;

        if ($request->filled('pertemuan_tanggal')) {
            try {
                $pertemuanTanggalFocus = Carbon::parse((string) $request->input('pertemuan_tanggal'))->startOfDay();
            } catch (\Throwable $e) {
                $pertemuanTanggalFocus = null;
            }
        }

        if (!empty($availableBulanOptions)) {
            $defaultBulan = array_key_exists((int) now()->month, $availableBulanOptions)
                ? (int) now()->month
                : (int) array_key_first($availableBulanOptions);

            if ($pertemuanTanggalFocus && array_key_exists((int) $pertemuanTanggalFocus->month, $availableBulanOptions)) {
                $defaultBulan = (int) $pertemuanTanggalFocus->month;
            }

            $selectedBulan = $request->filled('bulan')
                ? (int) $request->integer('bulan')
                : $defaultBulan;

            if (!array_key_exists($selectedBulan, $availableBulanOptions)) {
                $selectedBulan = $defaultBulan;
                $monthAdjusted = true;
            }
        } else {
            $availableBulanOptions = $this->getAllBulanOptions();
            $selectedBulan = max(1, min(12, $request->integer('bulan', (int) now()->month)));
        }

        $guruFilterId = auth()->user()->hasRole('Guru') && !is_admin()
            ? $this->resolveGuruId()
            : null;

        $jadwalOptions = JadwalPelajaran::query()
            ->with(['kelas', 'mataPelajaran', 'guru'])
            ->where('is_active', true)
            ->where('is_istirahat', false)
            ->when($guruFilterId, fn ($q) => $q->where('guru_id', $guruFilterId))
            ->orderBy('kelas_id')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $kelasOptions = $jadwalOptions
            ->pluck('kelas')
            ->filter()
            ->unique('id')
            ->sortBy(fn ($kelas) => sprintf('%03d-%s', (int) ($kelas->tingkat ?? 0), (string) ($kelas->nama_kelas ?? $kelas->nama ?? '')))
            ->values();

        $isSiswaScope = $this->isSiswaScoped($currentSiswa);
        if ($isSiswaScope) {
            $siswaKelasId = (int) ($currentSiswa?->kelas_id ?? 0);
            $kelasOptions = $siswaKelasId
                ? Kelas::query()->whereKey($siswaKelasId)->get()
                : collect();

            $selectedKelasId = $siswaKelasId ?: null;
        } else {
            $selectedKelasId = $request->filled('kelas_id')
                ? $request->integer('kelas_id')
                : null;

            if ($selectedKelasId && !$kelasOptions->contains(fn ($kelas) => (int) $kelas->id === (int) $selectedKelasId)) {
                $selectedKelasId = null;
            }

            if (!$selectedKelasId && $request->filled('jadwal_pelajaran_id')) {
                $legacySelectedJadwal = $jadwalOptions->firstWhere('id', $request->integer('jadwal_pelajaran_id'));
                $selectedKelasId = (int) ($legacySelectedJadwal?->kelas_id ?? 0) ?: null;
            }
        }

        $selectedKelas = $selectedKelasId
            ? $kelasOptions->firstWhere('id', $selectedKelasId)
            : null;

        $selectedJadwal = null;
        $selectedJadwalId = null;

        if ($selectedKelasId) {
            $jadwalPerKelas = $jadwalOptions
                ->where('kelas_id', $selectedKelasId)
                ->values();

            if ($pertemuanTanggalFocus) {
                $targetHari = $this->dayNameIndonesia($pertemuanTanggalFocus);
                $selectedJadwal = $jadwalPerKelas->firstWhere('hari', $targetHari);
            }

            $selectedJadwal = $selectedJadwal ?: $jadwalPerKelas->first();
            $selectedJadwalId = $selectedJadwal?->id;
        }

        $selectedMapelId = $selectedJadwal?->mata_pelajaran_id;

        $siswas = collect();
        $tanggalKolom = collect();
        $statusMatrix = [];
        $monthStart = null;
        $monthEnd = null;
        $siswaAbsensiRows = null;
        $siswaAbsensiSummary = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'total' => 0,
        ];

        if ($selectedSemester) {
            [$monthStart, $monthEnd] = $this->resolveMonthRange($selectedSemester, $selectedBulan);
        }

        if ($selectedJadwal && $selectedSemester) {
            $siswas = Siswa::query()
                ->where('is_active', true)
                ->where('kelas_id', $selectedJadwal->kelas_id)
                ->orderBy('nama')
                ->get();

            if ($monthStart && $monthEnd) {
                $tanggalKolom = $this->buildTanggalKolom($monthStart, $monthEnd, $selectedJadwal->hari);

                $tanggalKeys = $tanggalKolom
                    ->map(fn (Carbon $date) => $date->toDateString())
                    ->all();

                $existingAbsensi = empty($tanggalKeys)
                    ? collect()
                    : Absensi::query()
                        ->with('details')
                        ->where('kelas_id', $selectedJadwal->kelas_id)
                        ->where('mata_pelajaran_id', $selectedJadwal->mata_pelajaran_id)
                        ->whereIn('tanggal_absensi', $tanggalKeys)
                        ->when($guruFilterId, fn ($q) => $q->where('guru_id', $guruFilterId))
                        ->get();

                foreach ($siswas as $siswa) {
                    foreach ($tanggalKeys as $dateKey) {
                        $statusMatrix[(int) $siswa->id][$dateKey] = 'hadir';
                    }
                }

                foreach ($existingAbsensi as $absensi) {
                    $dateKey = optional($absensi->tanggal_absensi)->toDateString();
                    if (!$dateKey) {
                        continue;
                    }

                    foreach ($absensi->details as $detail) {
                        $statusMatrix[(int) $detail->siswa_id][$dateKey] = (string) $detail->status;
                    }
                }
            }
        }

        if ($isSiswaScope) {
            if ($currentSiswa && $selectedSemester && $selectedKelasId && $monthStart && $monthEnd) {
                $meetingMap = $this->buildMeetingMap((int) $selectedSemester->id, (int) $selectedKelasId);
                $fallbackMeetingMap = $this->buildFallbackMeetingMapFromAbsensi(
                    (int) $selectedKelasId,
                    Carbon::parse((string) $selectedSemester->tanggal_mulai)->startOfDay(),
                    Carbon::parse((string) $selectedSemester->tanggal_selesai)->endOfDay()
                );

                $siswaAbsensiBase = AbsensiDetail::query()
                    ->with(['absensi.kelas', 'absensi.mataPelajaran', 'absensi.guru'])
                    ->where('siswa_id', (int) $currentSiswa->id)
                    ->whereHas('absensi', function ($q) use ($selectedKelasId, $monthStart, $monthEnd) {
                        $q->where('kelas_id', (int) $selectedKelasId)
                            ->whereDate('tanggal_absensi', '>=', $monthStart->toDateString())
                            ->whereDate('tanggal_absensi', '<=', $monthEnd->toDateString());
                    });

                $siswaAbsensiSummary = [
                    'hadir' => (clone $siswaAbsensiBase)->where('status', 'hadir')->count(),
                    'izin' => (clone $siswaAbsensiBase)->where('status', 'izin')->count(),
                    'sakit' => (clone $siswaAbsensiBase)->where('status', 'sakit')->count(),
                    'alpa' => (clone $siswaAbsensiBase)->where('status', 'alpa')->count(),
                    'total' => (clone $siswaAbsensiBase)->count(),
                ];

                $siswaAbsensiRows = (clone $siswaAbsensiBase)
                    ->orderByDesc(
                        Absensi::query()
                            ->select('tanggal_absensi')
                            ->whereColumn('absensi.id', 'absensi_detail.absensi_id')
                            ->limit(1)
                    )
                    ->orderByDesc('id')
                    ->paginate(15, ['*'], 'siswa_page')
                    ->withQueryString();

                $siswaAbsensiRows->setCollection(
                    $siswaAbsensiRows->getCollection()->map(function (AbsensiDetail $detail) use ($meetingMap, $fallbackMeetingMap) {
                        $dateKey = optional($detail->absensi?->tanggal_absensi)->toDateString();
                        $detail->pertemuan_ke = $dateKey
                            ? ($meetingMap[$dateKey] ?? $fallbackMeetingMap[$dateKey] ?? null)
                            : null;

                        return $detail;
                    })
                );
            }
        }

        $riwayatQuery = Absensi::query()
            ->with(['kelas', 'mataPelajaran', 'guru', 'details'])
            ->when($guruFilterId, fn ($q) => $q->where('guru_id', $guruFilterId));

        if ($selectedKelasId && $selectedSemester && $monthStart && $monthEnd) {
            $riwayatQuery
                ->where('kelas_id', $selectedKelasId)
                ->whereDate('tanggal_absensi', '>=', $monthStart->toDateString())
                ->whereDate('tanggal_absensi', '<=', $monthEnd->toDateString());
        } else {
            $riwayatQuery->whereRaw('1 = 0');
        }

        $riwayatAbsensi = $riwayatQuery
            ->latest('tanggal_absensi')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('akademik.absensi.index', compact(
            'kelasOptions',
            'jadwalOptions',
            'semesters',
            'canManageAbsensi',
            'isSiswaScope',
            'currentSiswa',
            'siswaAbsensiRows',
            'siswaAbsensiSummary',
            'isKepalaSekolahReadOnly',
            'selectedJadwalId',
            'selectedJadwal',
            'selectedKelas',
            'selectedSemesterId',
            'selectedSemester',
            'selectedBulan',
            'availableBulanOptions',
            'monthAdjusted',
            'pertemuanTanggalFocus',
            'selectedKelasId',
            'selectedMapelId',
            'siswas',
            'tanggalKolom',
            'statusMatrix',
            'monthStart',
            'monthEnd',
            'riwayatAbsensi'
        ));
    }

    public function exportSiswaPdf(Request $request)
    {
        abort_unless($this->isSiswaScoped(), 403);

        $currentSiswa = $this->resolveCurrentSiswa();
        abort_unless($currentSiswa instanceof Siswa, 404);

        $validated = $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'bulan' => 'nullable|integer|min:1|max:12',
            'mode' => 'nullable|in:preview,download',
        ]);

        $semester = Semester::query()->with('tahunAjaran')->findOrFail($validated['semester_id']);
        $availableBulanOptions = $this->getAvailableBulanOptions($semester);

        $defaultBulan = array_key_exists((int) now()->month, $availableBulanOptions)
            ? (int) now()->month
            : (int) array_key_first($availableBulanOptions);

        $selectedBulan = (int) ($validated['bulan'] ?? $defaultBulan);
        if (!array_key_exists($selectedBulan, $availableBulanOptions)) {
            $selectedBulan = $defaultBulan;
        }

        [$monthStart, $monthEnd] = $this->resolveMonthRange($semester, $selectedBulan);
        if (!$monthStart || !$monthEnd) {
            return back()->withErrors([
                'bulan' => 'Bulan yang dipilih berada di luar periode semester.',
            ]);
        }

        $kelasId = (int) ($currentSiswa->kelas_id ?? 0);
        abort_unless($kelasId > 0, 404);

        $meetingMap = $this->buildMeetingMap((int) $semester->id, $kelasId);
        $fallbackMeetingMap = $this->buildFallbackMeetingMapFromAbsensi(
            $kelasId,
            Carbon::parse((string) $semester->tanggal_mulai)->startOfDay(),
            Carbon::parse((string) $semester->tanggal_selesai)->endOfDay()
        );

        $rows = AbsensiDetail::query()
            ->with(['absensi.kelas', 'absensi.mataPelajaran', 'absensi.guru'])
            ->where('siswa_id', (int) $currentSiswa->id)
            ->whereHas('absensi', function ($q) use ($kelasId, $monthStart, $monthEnd) {
                $q->where('kelas_id', $kelasId)
                    ->whereDate('tanggal_absensi', '>=', $monthStart->toDateString())
                    ->whereDate('tanggal_absensi', '<=', $monthEnd->toDateString());
            })
            ->orderBy(
                Absensi::query()
                    ->select('tanggal_absensi')
                    ->whereColumn('absensi.id', 'absensi_detail.absensi_id')
                    ->limit(1)
            )
            ->orderBy('id')
            ->get()
            ->map(function (AbsensiDetail $detail) use ($meetingMap, $fallbackMeetingMap) {
                $dateKey = optional($detail->absensi?->tanggal_absensi)->toDateString();
                $detail->pertemuan_ke = $dateKey
                    ? ($meetingMap[$dateKey] ?? $fallbackMeetingMap[$dateKey] ?? null)
                    : null;

                return $detail;
            })
            ->values();

        if ($rows->isEmpty()) {
            return back()->withErrors([
                'semester_id' => 'Data absensi siswa belum tersedia untuk filter semester/bulan yang dipilih.',
            ]);
        }

        $summary = [
            'hadir' => $rows->where('status', 'hadir')->count(),
            'izin' => $rows->where('status', 'izin')->count(),
            'sakit' => $rows->where('status', 'sakit')->count(),
            'alpa' => $rows->where('status', 'alpa')->count(),
            'total' => $rows->count(),
        ];

        $pdf = Pdf::loadView('akademik.absensi.siswa-pdf', [
            'siswa' => $currentSiswa,
            'kelas' => $rows->first()?->absensi?->kelas,
            'semester' => $semester,
            'bulanLabel' => $this->monthNameIndonesia($selectedBulan),
            'rows' => $rows,
            'summary' => $summary,
            'dicetakPada' => now(),
        ])->setPaper('a4', 'portrait');

        $filename = 'absensi-saya-'
            . Str::slug((string) ($currentSiswa->nama ?? 'siswa'))
            . '-semester-' . (string) ($semester->nomor_semester ?? '-')
            . '-bulan-' . $selectedBulan
            . '.pdf';

        $mode = strtolower((string) ($validated['mode'] ?? 'download'));
        if ($mode === 'preview') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    public function exportPdf(Request $request)
    {
        abort_unless(can_access('view absensi') || auth()->user()->hasRole('Guru') || is_admin(), 403);

        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'semester_id' => 'required|exists:semester,id',
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajaran,id',
        ]);

        if ($this->isSiswaScoped()) {
            $siswaKelasId = $this->resolveSiswaKelasId();
            abort_unless($siswaKelasId && $siswaKelasId === (int) $validated['kelas_id'], 403);
        }

        $kelas = Kelas::query()->findOrFail($validated['kelas_id']);
        $semester = Semester::query()->with('tahunAjaran')->findOrFail($validated['semester_id']);
        $mapelFilter = !empty($validated['mata_pelajaran_id'])
            ? MataPelajaran::query()->findOrFail($validated['mata_pelajaran_id'])
            : null;

        $guruFilterId = auth()->user()->hasRole('Guru') && !is_admin()
            ? $this->resolveGuruId()
            : null;

        $absensiList = Absensi::query()
            ->with(['kelas', 'mataPelajaran', 'guru', 'details.siswa'])
            ->where('kelas_id', $kelas->id)
            ->whereDate('tanggal_absensi', '>=', $semester->tanggal_mulai)
            ->whereDate('tanggal_absensi', '<=', $semester->tanggal_selesai)
            ->when($mapelFilter, fn ($q) => $q->where('mata_pelajaran_id', $mapelFilter->id))
            ->when($guruFilterId, fn ($q) => $q->where('guru_id', $guruFilterId))
            ->orderBy('tanggal_absensi')
            ->orderBy('id')
            ->get();

        if ($absensiList->isEmpty()) {
            $scopeLabel = $mapelFilter
                ? ('kelas, semester, dan mata pelajaran ' . $mapelFilter->nama)
                : 'kelas dan semester';

            return back()->withErrors([
                'history_kelas_id' => 'Data absensi untuk ' . $scopeLabel . ' yang dipilih belum tersedia.',
            ]);
        }

        $totalHadir = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;
        $rekapPerSiswa = [];

        foreach ($absensiList as $absensi) {
            $totalHadir += $absensi->details->where('status', 'hadir')->count();
            $totalIzin += $absensi->details->where('status', 'izin')->count();
            $totalSakit += $absensi->details->where('status', 'sakit')->count();
            $totalAlpa += $absensi->details->where('status', 'alpa')->count();

            foreach ($absensi->details as $detail) {
                $siswaId = (int) $detail->siswa_id;

                if (!isset($rekapPerSiswa[$siswaId])) {
                    $rekapPerSiswa[$siswaId] = [
                        'nama' => $detail->siswa->nama ?? 'Siswa',
                        'nis' => $detail->siswa->nis ?? '-',
                        'hadir' => 0,
                        'izin' => 0,
                        'sakit' => 0,
                        'alpa' => 0,
                        'total' => 0,
                    ];
                }

                $status = (string) $detail->status;
                if (in_array($status, ['hadir', 'izin', 'sakit', 'alpa'], true)) {
                    $rekapPerSiswa[$siswaId][$status]++;
                    $rekapPerSiswa[$siswaId]['total']++;
                }
            }
        }

        $rekapPerSiswa = collect($rekapPerSiswa)
            ->map(function (array $item): array {
                $item['persentase_hadir'] = $item['total'] > 0
                    ? round(($item['hadir'] / $item['total']) * 100, 2)
                    : 0;

                return $item;
            })
            ->sortBy('nama')
            ->values();

        $pdf = Pdf::loadView('akademik.absensi.pdf', [
            'kelas' => $kelas,
            'semester' => $semester,
            'mapelFilter' => $mapelFilter,
            'absensiList' => $absensiList,
            'totalHadir' => $totalHadir,
            'totalIzin' => $totalIzin,
            'totalSakit' => $totalSakit,
            'totalAlpa' => $totalAlpa,
            'rekapPerSiswa' => $rekapPerSiswa,
            'dicetakPada' => now(),
        ]);

        $pdf->setPaper('a4', 'portrait');

        $kelasSlug = Str::slug((string) ($kelas->nama ?? 'kelas'));
        $mapelSuffix = $mapelFilter
            ? ('-mapel-' . Str::slug((string) $mapelFilter->nama))
            : '';
        $filename = 'rekap-absensi-' . $kelasSlug . '-semester-' . $semester->nomor_semester . $mapelSuffix . '.pdf';

        $mode = strtolower((string) $request->input('mode', 'download'));
        if ($mode === 'preview') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    public function store(Request $request)
    {
        abort_if($this->isKepalaSekolahReadOnly(), 403, 'Kepala Sekolah hanya dapat melihat data absensi.');
        abort_unless(can_access('create absensi') || auth()->user()->hasRole('Guru') || is_admin(), 403);

        $validated = $request->validate([
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
            'semester_id'         => 'required|exists:semester,id',
            'bulan'               => 'required|integer|min:1|max:12',
            'status'              => 'required|array|min:1',
            'status.*'            => 'required|array',
            'status.*.*'          => 'required|in:hadir,izin,sakit,alpa',
            'catatan'             => 'nullable|array',
            'catatan.*'           => 'nullable|string|max:500',
        ]);

        $jadwal = JadwalPelajaran::query()->findOrFail($validated['jadwal_pelajaran_id']);

        if ($this->isGuruScoped() && (int) $jadwal->guru_id !== $this->resolveGuruId()) {
            abort(403, 'Anda tidak memiliki akses untuk jadwal ini.');
        }

        if (!$jadwal->is_active || $jadwal->is_istirahat) {
            throw ValidationException::withMessages([
                'jadwal_pelajaran_id' => 'Jadwal tidak valid untuk input absensi.',
            ]);
        }

        $semester = Semester::query()->findOrFail($validated['semester_id']);
        $availableBulanOptions = $this->getAvailableBulanOptions($semester);

        if (!array_key_exists((int) $validated['bulan'], $availableBulanOptions)) {
            throw ValidationException::withMessages([
                'bulan' => 'Bulan yang dipilih berada di luar periode semester.',
            ]);
        }

        [$monthStart, $monthEnd] = $this->resolveMonthRange($semester, (int) $validated['bulan']);

        if (!$monthStart || !$monthEnd) {
            throw ValidationException::withMessages([
                'bulan' => 'Bulan yang dipilih berada di luar periode semester.',
            ]);
        }

        $tanggalKolom = $this->buildTanggalKolom($monthStart, $monthEnd, (string) $jadwal->hari);
        $tanggalKeys = $tanggalKolom->map(fn (Carbon $date) => $date->toDateString())->all();

        if (empty($tanggalKeys)) {
            throw ValidationException::withMessages([
                'jadwal_pelajaran_id' => 'Tidak ada tanggal pada bulan ini yang sesuai dengan hari jadwal.',
            ]);
        }

        $siswaIdsKelas = Siswa::query()
            ->where('is_active', true)
            ->where('kelas_id', $jadwal->kelas_id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($siswaIdsKelas)) {
            return back()->withErrors([
                'jadwal_pelajaran_id' => 'Belum ada siswa aktif pada kelas dari jadwal yang dipilih.',
            ])->withInput();
        }

        $submittedStatus = $validated['status'];
        $submittedSiswaIds = collect(array_keys($submittedStatus))->map(fn ($id) => (int) $id)->all();

        $invalidSiswaIds = array_diff($submittedSiswaIds, $siswaIdsKelas);
        if (!empty($invalidSiswaIds)) {
            throw ValidationException::withMessages([
                'status' => 'Ditemukan data siswa yang tidak valid untuk kelas ini.',
            ]);
        }

        $allowedDateKeyLookup = array_fill_keys($tanggalKeys, true);

        foreach ($submittedStatus as $siswaId => $statusPerTanggal) {
            foreach (array_keys($statusPerTanggal) as $dateKey) {
                if (!isset($allowedDateKeyLookup[$dateKey])) {
                    throw ValidationException::withMessages([
                        'status' => 'Ditemukan tanggal absensi yang tidak sesuai jadwal/semester/bulan.',
                    ]);
                }
            }
        }

        $guruId = $jadwal->guru_id ?: $this->resolveGuruId();

        foreach ($tanggalKeys as $dateKey) {
            $absensi = Absensi::query()->updateOrCreate(
                [
                    'kelas_id'           => $jadwal->kelas_id,
                    'mata_pelajaran_id'  => $jadwal->mata_pelajaran_id,
                    'tanggal_absensi'    => $dateKey,
                    'guru_id'            => $guruId,
                ],
                [
                    'keterangan'  => null,
                    'created_by'  => auth()->id(),
                ]
            );

            foreach ($siswaIdsKelas as $siswaId) {
                $status  = $submittedStatus[$siswaId][$dateKey] ?? 'hadir';
                $catatan = $validated['catatan'][$siswaId] ?? null;

                AbsensiDetail::query()->updateOrCreate(
                    [
                        'absensi_id' => $absensi->id,
                        'siswa_id'   => $siswaId,
                    ],
                    [
                        'status'  => $status,
                        'catatan' => $catatan,
                    ]
                );
            }
        }

        return redirect()->route('akademik.absensi.index', [
            'kelas_id' => $jadwal->kelas_id,
            'semester_id' => $validated['semester_id'],
            'bulan' => $validated['bulan'],
        ])->with('success', 'Absensi siswa per bulan berhasil disimpan.');
    }

    public function show(Absensi $absensi)
    {
        abort_unless(can_access('view absensi') || auth()->user()->hasRole('Guru') || is_admin() || $this->isSiswaScoped(), 403);
        $absensi->load(['kelas', 'mataPelajaran', 'guru', 'details.siswa']);
        return view('akademik.absensi.show', compact('absensi'));
    }

    public function destroy(Absensi $absensi)
    {
        abort_if($this->isKepalaSekolahReadOnly(), 403);
        abort_unless(can_access('create absensi') || auth()->user()->hasRole('Guru') || is_admin(), 403);

        // Guru hanya bisa hapus absensi miliknya sendiri
        if ($this->isGuruScoped()) {
            $guruId = $this->resolveGuruId();
            if ((int) $absensi->guru_id !== $guruId) {
                abort(403, 'Anda tidak memiliki akses untuk menghapus absensi ini.');
            }
        }

        $absensi->details()->delete();
        $absensi->delete();

        return back()->with('success', 'Data absensi tanggal ' . optional($absensi->tanggal_absensi)->format('d M Y') . ' berhasil dihapus.');
    }

    public function edit(Absensi $absensi)
    {
        abort_if($this->isKepalaSekolahReadOnly(), 403);
        abort_unless(can_access('create absensi') || auth()->user()->hasRole('Guru') || is_admin(), 403);

        if ($this->isGuruScoped()) {
            $guruId = $this->resolveGuruId();
            if ((int) $absensi->guru_id !== $guruId) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit absensi ini.');
            }
        }

        $absensi->load(['kelas', 'mataPelajaran', 'guru', 'details.siswa']);

        // Siswa aktif di kelas tersebut
        $siswas = Siswa::query()
            ->where('is_active', true)
            ->where('kelas_id', $absensi->kelas_id)
            ->orderBy('nama')
            ->get();

        // Map siswa_id => [status, catatan]
        $detailMap = [];
        foreach ($absensi->details as $det) {
            $detailMap[(int) $det->siswa_id] = [
                'status'  => $det->status,
                'catatan' => $det->catatan,
            ];
        }

        return view('akademik.absensi.edit', compact('absensi', 'siswas', 'detailMap'));
    }

    public function update(Request $request, Absensi $absensi)
    {
        abort_if($this->isKepalaSekolahReadOnly(), 403);
        abort_unless(can_access('create absensi') || auth()->user()->hasRole('Guru') || is_admin(), 403);

        if ($this->isGuruScoped()) {
            $guruId = $this->resolveGuruId();
            if ((int) $absensi->guru_id !== $guruId) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit absensi ini.');
            }
        }

        $validated = $request->validate([
            'status'   => 'required|array',
            'status.*' => 'required|in:hadir,izin,sakit,alpa',
            'catatan'  => 'nullable|array',
            'catatan.*'=> 'nullable|string|max:500',
        ]);

        $siswaIds = Siswa::query()
            ->where('is_active', true)
            ->where('kelas_id', $absensi->kelas_id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($siswaIds as $siswaId) {
            $status  = $validated['status'][$siswaId]  ?? 'hadir';
            $catatan = $validated['catatan'][$siswaId]  ?? null;

            AbsensiDetail::query()->updateOrCreate(
                ['absensi_id' => $absensi->id, 'siswa_id' => $siswaId],
                ['status' => $status, 'catatan' => $catatan]
            );
        }

        return redirect()
            ->route('akademik.absensi.show', $absensi)
            ->with('success', 'Absensi tanggal ' . optional($absensi->tanggal_absensi)->format('d M Y') . ' berhasil diperbarui.');
    }

    private function resolveGuruId(): int

    {
        $user = auth()->user();
        $guruId = optional($user?->guru)->id;

        if ($guruId) {
            return (int) $guruId;
        }

        if ($user) {
            $guru = Guru::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name ?: ('Guru #' . $user->id),
                    'jenis_kelamin' => 'L',
                    'email' => $user->email,
                    'is_active' => true,
                ]
            );

            return (int) $guru->id;
        }

        $fallbackGuruId = Guru::query()->withTrashed()->orderBy('id')->value('id');

        if ($fallbackGuruId) {
            return (int) $fallbackGuruId;
        }

        throw ValidationException::withMessages([
            'guru_id' => 'Data guru tidak ditemukan. Silakan buat data guru terlebih dahulu.',
        ]);
    }

    private function isKepalaSekolahReadOnly(): bool
    {
        return auth()->user()?->hasRole('Kepala Sekolah') && !is_admin();
    }

    private function isGuruScoped(): bool
    {
        return auth()->user()?->hasRole('Guru') && !is_admin();
    }

    private function isSiswaScoped(?Siswa $currentSiswa = null): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if (is_admin() || $user->hasRole('Guru')) {
            return false;
        }

        if ($user->hasRole('Siswa')) {
            return true;
        }

        if ($user->siswa()->exists()) {
            return true;
        }

        if ($currentSiswa instanceof Siswa) {
            return true;
        }

        return $this->resolveCurrentSiswa() instanceof Siswa;
    }

    private function resolveSiswaKelasId(): ?int
    {
        $currentSiswa = $this->resolveCurrentSiswa();
        $kelasId = (int) ($currentSiswa?->kelas_id ?? 0);

        return $kelasId > 0 ? $kelasId : null;
    }

    private function resolveCurrentSiswa(): ?Siswa
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        $siswaByUser = Siswa::query()
            ->where('is_active', true)
            ->where('user_id', $user->id)
            ->first();

        if ($siswaByUser instanceof Siswa) {
            return $siswaByUser;
        }

        $normalizedEmail = mb_strtolower(trim((string) ($user->email ?? '')));
        if ($normalizedEmail === '') {
            return null;
        }

        $siswaByEmail = Siswa::query()
            ->where('is_active', true)
            ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
            ->first();

        if (!$siswaByEmail instanceof Siswa) {
            return null;
        }

        if ((int) ($siswaByEmail->user_id ?? 0) === 0) {
            Siswa::query()->whereKey((int) $siswaByEmail->id)->update(['user_id' => (int) $user->id]);
        }

        return $siswaByEmail;
    }

    private function buildMeetingMap(int $semesterId, int $kelasId): array
    {
        $dates = LmsPertemuan::query()
            ->where('semester_id', $semesterId)
            ->where('kelas_id', $kelasId)
            ->orderBy('tanggal')
            ->pluck('tanggal');

        $map = [];
        $number = 1;

        foreach ($dates as $date) {
            $key = Carbon::parse((string) $date)->toDateString();
            $map[$key] = $number;
            $number++;
        }

        return $map;
    }

    private function buildFallbackMeetingMapFromAbsensi(int $kelasId, Carbon $startDate, Carbon $endDate): array
    {
        $dates = Absensi::query()
            ->where('kelas_id', $kelasId)
            ->whereDate('tanggal_absensi', '>=', $startDate->toDateString())
            ->whereDate('tanggal_absensi', '<=', $endDate->toDateString())
            ->orderBy('tanggal_absensi')
            ->pluck('tanggal_absensi')
            ->map(fn ($date) => Carbon::parse((string) $date)->toDateString())
            ->unique()
            ->values();

        $map = [];
        $number = 1;
        foreach ($dates as $dateKey) {
            $map[(string) $dateKey] = $number;
            $number++;
        }

        return $map;
    }

    private function resolveSemesterIdByDate(?string $date): ?int
    {
        if (!$date) {
            return null;
        }

        $semester = Semester::query()
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date)
            ->orderByDesc('id')
            ->first();

        return $semester?->id;
    }

    private function resolveMonthRange(Semester $semester, int $bulan): array
    {
        $start = Carbon::parse($semester->tanggal_mulai)->startOfDay();
        $end = Carbon::parse($semester->tanggal_selesai)->endOfDay();

        $monthStart = null;
        $monthEnd = null;

        $cursor = $start->copy()->startOfMonth();
        while ($cursor->lte($end)) {
            if ((int) $cursor->month === (int) $bulan) {
                $candidateStart = $cursor->copy()->startOfMonth();
                $candidateEnd = $cursor->copy()->endOfMonth();

                $monthStart = $candidateStart->lt($start) ? $start->copy() : $candidateStart;
                $monthEnd = $candidateEnd->gt($end) ? $end->copy() : $candidateEnd;
                break;
            }

            $cursor->addMonth();
        }

        return [$monthStart, $monthEnd];
    }

    private function buildTanggalKolom(Carbon $monthStart, Carbon $monthEnd, string $hariJadwal)
    {
        $result = collect();

        foreach (CarbonPeriod::create($monthStart, $monthEnd) as $date) {
            $dateCarbon = Carbon::instance($date);

            if ($this->dayNameIndonesia($dateCarbon) === $hariJadwal) {
                $result->push($dateCarbon->copy());
            }
        }

        return $result->values();
    }

    private function dayNameIndonesia(Carbon $date): string
    {
        $map = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        return $map[$date->dayOfWeekIso] ?? '';
    }

    private function getAvailableBulanOptions(?Semester $semester): array
    {
        if (!$semester) {
            return $this->getAllBulanOptions();
        }

        $start = Carbon::parse($semester->tanggal_mulai)->startOfMonth();
        $end = Carbon::parse($semester->tanggal_selesai)->startOfMonth();

        if ($start->gt($end)) {
            return $this->getAllBulanOptions();
        }

        $result = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $month = (int) $cursor->month;
            $result[$month] = $this->monthNameIndonesia($month);
            $cursor->addMonth();
        }

        return $result;
    }

    private function getAllBulanOptions(): array
    {
        $all = [];
        for ($month = 1; $month <= 12; $month++) {
            $all[$month] = $this->monthNameIndonesia($month);
        }

        return $all;
    }

    private function monthNameIndonesia(int $month): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $months[$month] ?? '-';
    }
}
