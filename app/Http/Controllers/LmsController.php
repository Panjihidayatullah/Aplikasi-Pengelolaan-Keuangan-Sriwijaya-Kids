<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiDetail;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\LmsPertemuan;
use App\Models\Materi;
use App\Models\PengumpulanTugas;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\Tugas;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class LmsController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($this->canViewLmsHub(), 403);

        $semesters = Semester::query()
            ->with('tahunAjaran')
            ->orderByDesc('is_active')
            ->orderByDesc('tanggal_mulai')
            ->get();
        $kelasOptions = $this->resolveKelasOptionsForCurrentUser();
        $selectedKelasId = $this->resolveSelectedKelasId($request, $kelasOptions);
        $isSiswaScope = $this->isSiswaScope();

        $selectedSemester = null;
        if ($request->filled('semester_id')) {
            $selectedSemester = $semesters->firstWhere('id', $request->integer('semester_id'));
        }

        if (!$selectedSemester) {
            $selectedSemester = $semesters->firstWhere('is_active', true) ?: $semesters->first();
        }

        if (!$selectedSemester) {
            return view('akademik.lms.index', [
                'semesters' => $semesters,
                'selectedSemester' => null,
                'monthOptions' => collect(),
                'selectedMonthKey' => null,
                'selectedMonthLabel' => null,
                'calendarWeeks' => collect(),
                'prevMonthKey' => null,
                'nextMonthKey' => null,
                'kelasOptions' => $kelasOptions,
                'selectedKelasId' => $selectedKelasId,
                'selectedKelas' => null,
                'isSiswaScope' => $isSiswaScope,
                'selectedMeetingCount' => 0,
                'riwayatPertemuans' => null,
                'riwayatSelectedByOptions' => collect(),
                'historyFromDate' => null,
                'historyToDate' => null,
                'historySelectedBy' => null,
                'historyMeetingMap' => [],
            ]);
        }

        $monthOptions = $this->buildMonthOptions($selectedSemester);
        $selectedMonthKey = $request->string('month_key')->toString();

        if (!isset($monthOptions[$selectedMonthKey])) {
            $nowMonthKey = now()->format('Y-m');
            $selectedMonthKey = isset($monthOptions[$nowMonthKey])
                ? $nowMonthKey
                : (string) array_key_first($monthOptions);
        }

        $selectedMonth = Carbon::createFromFormat('Y-m-d', $selectedMonthKey . '-01')->startOfDay();
        $semesterStart = Carbon::parse((string) $selectedSemester->tanggal_mulai)->startOfDay();
        $semesterEnd = Carbon::parse((string) $selectedSemester->tanggal_selesai)->startOfDay();
        $selectedMeetingMap = $selectedKelasId
            ? $this->buildMeetingMap((int) $selectedSemester->id, $selectedKelasId)
            : [];

        $historyFromDate = $request->string('history_from_date')->toString() ?: null;
        $historyToDate = $request->string('history_to_date')->toString() ?: null;
        $historySelectedBy = !$isSiswaScope && $request->filled('history_selected_by')
            ? $request->integer('history_selected_by')
            : null;

        $riwayatPertemuans = null;
        $riwayatSelectedByOptions = collect();
        if ($selectedKelasId) {
            $riwayatBaseQuery = LmsPertemuan::query()
                ->with(['selectedBy:id,name', 'kelas:id,nama_kelas'])
                ->where('semester_id', $selectedSemester->id)
                ->where('kelas_id', $selectedKelasId);

            $riwayatSelectedByOptions = (clone $riwayatBaseQuery)
                ->whereNotNull('selected_by')
                ->get()
                ->pluck('selectedBy')
                ->filter()
                ->unique('id')
                ->values();

            $riwayatPertemuans = (clone $riwayatBaseQuery)
                ->when($historyFromDate, fn ($q) => $q->whereDate('tanggal', '>=', $historyFromDate))
                ->when($historyToDate, fn ($q) => $q->whereDate('tanggal', '<=', $historyToDate))
                ->when($historySelectedBy && !$isSiswaScope, fn ($q) => $q->where('selected_by', $historySelectedBy))
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->paginate(10, ['*'], 'riwayat_page')
                ->withQueryString();
        }

        $startGrid = $selectedMonth->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $endGrid = $selectedMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $calendarDays = collect(CarbonPeriod::create($startGrid, $endGrid))
            ->map(function (Carbon $date) use ($selectedMonth, $selectedSemester, $semesterStart, $semesterEnd, $selectedMeetingMap, $selectedKelasId) {
                $inSemester = $date->betweenIncluded($semesterStart, $semesterEnd);
                $dateKey = $date->toDateString();
                $meetingNumber = $inSemester ? ($selectedMeetingMap[$dateKey] ?? null) : null;

                return [
                    'date' => $date->copy(),
                    'in_current_month' => $date->month === $selectedMonth->month,
                    'in_semester' => $inSemester,
                    'is_today' => $date->isToday(),
                    'meeting_number' => $meetingNumber,
                    'is_selected_pertemuan' => $meetingNumber !== null,
                    'url' => $inSemester
                        ? route('akademik.lms.pertemuan', array_filter([
                            'tanggal' => $date->toDateString(),
                            'semester_id' => $selectedSemester->id,
                            'kelas_id' => $selectedKelasId,
                        ], fn ($value) => $value !== null && $value !== ''))
                        : null,
                ];
            });

        $keys = array_keys($monthOptions);
        $currentIndex = array_search($selectedMonthKey, $keys, true);
        $prevMonthKey = $currentIndex !== false && $currentIndex > 0 ? $keys[$currentIndex - 1] : null;
        $nextMonthKey = $currentIndex !== false && $currentIndex < count($keys) - 1 ? $keys[$currentIndex + 1] : null;

        $meetingList = [];
        if ($selectedKelasId && $selectedSemester) {
            $meetingList = \App\Models\LmsPertemuan::query()
                ->where('semester_id', $selectedSemester->id)
                ->where('kelas_id', $selectedKelasId)
                ->orderBy('tanggal')
                ->get()
                ->map(function ($item, $idx) {
                    $item->nomor = $idx + 1;
                    return $item;
                });
        }

        return view('akademik.lms.index', [
            'semesters' => $semesters,
            'selectedSemester' => $selectedSemester,
            'monthOptions' => $monthOptions,
            'selectedMonthKey' => $selectedMonthKey,
            'selectedMonthLabel' => $monthOptions[$selectedMonthKey] ?? null,
            'calendarWeeks' => $calendarDays->chunk(7)->values(),
            'prevMonthKey' => $prevMonthKey,
            'nextMonthKey' => $nextMonthKey,
            'selectedMeetingCount' => count($selectedMeetingMap),
            'kelasOptions' => $kelasOptions,
            'selectedKelasId' => $selectedKelasId,
            'selectedKelas' => $selectedKelasId ? $kelasOptions->firstWhere('id', $selectedKelasId) : null,
            'isSiswaScope' => $isSiswaScope,
            'riwayatPertemuans' => $riwayatPertemuans,
            'riwayatSelectedByOptions' => $riwayatSelectedByOptions,
            'historyFromDate' => $historyFromDate,
            'historyToDate' => $historyToDate,
            'historySelectedBy' => $historySelectedBy,
            'historyMeetingMap' => $selectedMeetingMap,
            'meetingList' => $meetingList,
        ]);
    }

    public function pertemuan(Request $request, string $tanggal)
    {
        abort_unless($this->canViewLmsHub(), 403);

        $date = $this->parseDateOrAbort($tanggal);

        $semester = null;
        if ($request->filled('semester_id')) {
            $semester = Semester::query()->with('tahunAjaran')->find($request->integer('semester_id'));
        }

        if (!$semester || !$this->dateInsideSemester($date, $semester)) {
            $semester = Semester::query()
                ->with('tahunAjaran')
                ->whereDate('tanggal_mulai', '<=', $date->toDateString())
                ->whereDate('tanggal_selesai', '>=', $date->toDateString())
                ->orderByDesc('is_active')
                ->first();
        }

        if (!$semester) {
            $semester = Semester::query()->with('tahunAjaran')->where('is_active', true)->first();
        }

        $kelasOptions = $this->resolveKelasOptionsForCurrentUser();
        $selectedKelasId = $this->resolveSelectedKelasId($request, $kelasOptions);
        $isSiswaScope = $this->isSiswaScope();

        $semesterId = $semester?->id;
        $tahunAjaranId = $semester?->tahun_ajaran_id;
        $meetingNumber = null;

        if ($semester && $this->dateInsideSemester($date, $semester)) {
            if ($selectedKelasId && $this->canSelectPertemuan()) {
                LmsPertemuan::query()->firstOrCreate(
                    [
                        'semester_id' => $semester->id,
                        'kelas_id' => $selectedKelasId,
                        'tanggal' => $date->toDateString(),
                    ],
                    [
                        'selected_by' => Auth::id(),
                    ]
                );
            }

            $meetingMap = $selectedKelasId
                ? $this->buildMeetingMap((int) $semester->id, $selectedKelasId)
                : [];
            $meetingNumber = $meetingMap[$date->toDateString()] ?? null;
        }

        $materiHasPertemuan = Schema::hasColumn('materi', 'tanggal_pertemuan');
        $tugasHasPertemuan = Schema::hasColumn('tugas', 'tanggal_pertemuan');

        $materiCount = Materi::query()
            ->when($selectedKelasId, fn ($q) => $q->where('kelas_id', $selectedKelasId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->when($tahunAjaranId, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->when($materiHasPertemuan, fn ($q) => $q->whereDate('tanggal_pertemuan', $date->toDateString()))
            ->count();

        $tugasCount = Tugas::query()
            ->when($selectedKelasId, fn ($q) => $q->where('kelas_id', $selectedKelasId))
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->when($tahunAjaranId, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->when($tugasHasPertemuan, fn ($q) => $q->whereDate('tanggal_pertemuan', $date->toDateString()))
            ->count();

        $monthStart = $date->copy()->startOfMonth()->toDateString();
        $monthEnd = $date->copy()->endOfMonth()->toDateString();

        $absensiCount = Absensi::query()
            ->when($selectedKelasId, fn ($q) => $q->where('kelas_id', $selectedKelasId))
            ->whereDate('tanggal_absensi', $date->toDateString())
            ->count();

        $absensiDetailCount = AbsensiDetail::query()
            ->whereHas('absensi', function ($q) use ($selectedKelasId, $date) {
                $q->when($selectedKelasId, fn ($qq) => $qq->where('kelas_id', $selectedKelasId))
                    ->whereDate('tanggal_absensi', $date->toDateString());
            })
            ->count();

        $absensiCountFallback = Absensi::query()
            ->when($selectedKelasId, fn ($q) => $q->where('kelas_id', $selectedKelasId))
            ->when($semesterId, function ($q) use ($semester) {
                if (!$semester) {
                    return;
                }

                $q->whereDate('tanggal_absensi', '>=', (string) $semester->tanggal_mulai)
                    ->whereDate('tanggal_absensi', '<=', (string) $semester->tanggal_selesai);
            })
            ->whereDate('tanggal_absensi', '>=', $monthStart)
            ->whereDate('tanggal_absensi', '<=', $monthEnd)
            ->count();

        $absensiDetailCountFallback = AbsensiDetail::query()
            ->whereHas('absensi', function ($q) use ($selectedKelasId, $semesterId, $semester, $monthStart, $monthEnd) {
                $q->when($selectedKelasId, fn ($qq) => $qq->where('kelas_id', $selectedKelasId));

                if ($semesterId && $semester) {
                    $q->whereDate('tanggal_absensi', '>=', (string) $semester->tanggal_mulai)
                        ->whereDate('tanggal_absensi', '<=', (string) $semester->tanggal_selesai);
                }

                $q->whereDate('tanggal_absensi', '>=', $monthStart)
                    ->whereDate('tanggal_absensi', '<=', $monthEnd);
            })
            ->count();

        $absensiScope = 'tanggal';
        if ($absensiCount === 0 && $absensiCountFallback > 0) {
            $absensiCount = $absensiCountFallback;
            $absensiDetailCount = $absensiDetailCountFallback;
            $absensiScope = 'bulan';
        }

        $monitoringCount = PengumpulanTugas::query()
            ->whereHas('tugas', function ($q) use ($selectedKelasId, $semesterId, $tahunAjaranId, $tugasHasPertemuan, $date) {
                $q->when($selectedKelasId, fn ($qq) => $qq->where('kelas_id', $selectedKelasId))
                    ->when($semesterId, fn ($qq) => $qq->where('semester_id', $semesterId))
                    ->when($tahunAjaranId, fn ($qq) => $qq->where('tahun_ajaran_id', $tahunAjaranId));

                if ($tugasHasPertemuan) {
                    $q->whereDate('tanggal_pertemuan', $date->toDateString());
                }
            })
            ->count();

        $monitoringDinilaiCount = PengumpulanTugas::query()
            ->whereNotNull('nilai')
            ->whereHas('tugas', function ($q) use ($selectedKelasId, $semesterId, $tahunAjaranId, $tugasHasPertemuan, $date) {
                $q->when($selectedKelasId, fn ($qq) => $qq->where('kelas_id', $selectedKelasId))
                    ->when($semesterId, fn ($qq) => $qq->where('semester_id', $semesterId))
                    ->when($tahunAjaranId, fn ($qq) => $qq->where('tahun_ajaran_id', $tahunAjaranId));

                if ($tugasHasPertemuan) {
                    $q->whereDate('tanggal_pertemuan', $date->toDateString());
                }
            })
            ->count();

        $monitoringCountFallback = PengumpulanTugas::query()
            ->whereHas('tugas', function ($q) use ($selectedKelasId, $semesterId, $tahunAjaranId) {
                $q->when($selectedKelasId, fn ($qq) => $qq->where('kelas_id', $selectedKelasId))
                    ->when($semesterId, fn ($qq) => $qq->where('semester_id', $semesterId))
                    ->when($tahunAjaranId, fn ($qq) => $qq->where('tahun_ajaran_id', $tahunAjaranId));
            })
            ->count();

        $monitoringDinilaiCountFallback = PengumpulanTugas::query()
            ->whereNotNull('nilai')
            ->whereHas('tugas', function ($q) use ($selectedKelasId, $semesterId, $tahunAjaranId) {
                $q->when($selectedKelasId, fn ($qq) => $qq->where('kelas_id', $selectedKelasId))
                    ->when($semesterId, fn ($qq) => $qq->where('semester_id', $semesterId))
                    ->when($tahunAjaranId, fn ($qq) => $qq->where('tahun_ajaran_id', $tahunAjaranId));
            })
            ->count();

        $monitoringScope = 'tanggal';
        if ($monitoringCount === 0 && $monitoringCountFallback > 0) {
            $monitoringCount = $monitoringCountFallback;
            $monitoringDinilaiCount = $monitoringDinilaiCountFallback;
            $monitoringScope = 'periode';
        }

        return view('akademik.lms.pertemuan', [
            'date' => $date,
            'semester' => $semester,
            'semesterId' => $semesterId,
            'tahunAjaranId' => $tahunAjaranId,
            'kelasOptions' => $kelasOptions,
            'selectedKelasId' => $selectedKelasId,
            'selectedKelas' => $selectedKelasId ? $kelasOptions->firstWhere('id', $selectedKelasId) : null,
            'isSiswaScope' => $isSiswaScope,
            'meetingNumber' => $meetingNumber,
            'canSelectPertemuan' => $this->canSelectPertemuan(),
            'materiCount' => $materiCount,
            'tugasCount' => $tugasCount,
            'absensiCount' => $absensiCount,
            'absensiDetailCount' => $absensiDetailCount,
            'absensiScope' => $absensiScope,
            'monitoringCount' => $monitoringCount,
            'monitoringDinilaiCount' => $monitoringDinilaiCount,
            'monitoringScope' => $monitoringScope,
            'links' => [
                'materi' => route('akademik.lms.materi.index', array_filter([
                    'pertemuan_tanggal' => $date->toDateString(),
                    'semester_id' => $semesterId,
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'kelas_id' => $selectedKelasId,
                ], fn ($value) => $value !== null && $value !== '')),
                'tugas' => route('akademik.lms.tugas.index', array_filter([
                    'pertemuan_tanggal' => $date->toDateString(),
                    'semester_id' => $semesterId,
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'kelas_id' => $selectedKelasId,
                ], fn ($value) => $value !== null && $value !== '')),
                'absensi' => route('akademik.absensi.index', array_filter([
                    'pertemuan_tanggal' => $date->toDateString(),
                    'semester_id' => $semesterId,
                    'bulan' => (int) $date->month,
                    'kelas_id' => $selectedKelasId,
                ], fn ($value) => $value !== null && $value !== '')),
                'monitoring' => route('akademik.lms.monitoring.index', array_filter([
                    'pertemuan_tanggal' => $date->toDateString(),
                    'semester_id' => $semesterId,
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'kelas_id' => $selectedKelasId,
                ], fn ($value) => $value !== null && $value !== '')),
            ],
        ]);
    }

    public function storePertemuan(Request $request)
    {
        abort_unless($this->canSelectPertemuan(), 403);

        $validated = $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'kelas_id'    => 'required|exists:kelas,id',
            'tanggal'     => 'required|date',
        ]);

        $exists = LmsPertemuan::query()
            ->where('semester_id', $validated['semester_id'])
            ->where('kelas_id', $validated['kelas_id'])
            ->whereDate('tanggal', $validated['tanggal'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Tanggal pertemuan ini sudah ada.');
        }

        LmsPertemuan::create([
            'semester_id'  => $validated['semester_id'],
            'kelas_id'     => $validated['kelas_id'],
            'tanggal'      => $validated['tanggal'],
            'selected_by'  => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Pertemuan berhasil ditambahkan.');
    }

    public function updatePertemuan(Request $request, LmsPertemuan $lmsPertemuan)
    {
        abort_unless($this->canSelectPertemuan(), 403);

        $validated = $request->validate([
            'tanggal' => 'required|date',
        ]);

        $exists = LmsPertemuan::query()
            ->where('semester_id', $lmsPertemuan->semester_id)
            ->where('kelas_id', $lmsPertemuan->kelas_id)
            ->whereDate('tanggal', $validated['tanggal'])
            ->where('id', '!=', $lmsPertemuan->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Tanggal pertemuan ini sudah ada.');
        }

        $lmsPertemuan->update(['tanggal' => $validated['tanggal']]);

        return redirect()->back()->with('success', 'Tanggal pertemuan berhasil diperbarui.');
    }

    public function deletePertemuan(LmsPertemuan $lmsPertemuan)
    {
        abort_unless($this->canSelectPertemuan(), 403);
        $lmsPertemuan->delete();
        return redirect()->back()->with('success', 'Pertemuan berhasil dihapus.');
    }

    private function canViewLmsHub(): bool
    {
        $user = auth()->user();

        return is_admin()
            || is_siswa()
            || $user->hasRole('Guru')
            || $user->hasRole('Kepala Sekolah')
            || can_access('view lms-materi')
            || can_access('view lms-tugas')
            || can_access('view lms-monitoring')
            || can_access('view absensi');
    }

    private function canSelectPertemuan(): bool
    {
        return is_admin() || auth()->user()->hasRole('Guru');
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

    private function resolveKelasOptionsForCurrentUser()
    {
        $user = auth()->user();

        if ($this->isSiswaScope()) {
            $kelasId = $this->resolveSiswaKelasId();

            return $kelasId > 0
                ? Kelas::query()->whereKey($kelasId)->get()
                : collect();
        }

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

        return Kelas::query()->orderByTingkat()->orderBy('nama_kelas')->get();
    }

    private function resolveSelectedKelasId(Request $request, $kelasOptions): ?int
    {
        if ($this->isSiswaScope()) {
            return $kelasOptions->count() === 1
                ? (int) optional($kelasOptions->first())->id
                : null;
        }

        $selectedKelasId = $request->filled('kelas_id')
            ? $request->integer('kelas_id')
            : null;

        if (!$selectedKelasId) {
            return $kelasOptions->count() === 1
                ? (int) optional($kelasOptions->first())->id
                : null;
        }

        $available = $kelasOptions->contains(fn ($kelas) => (int) $kelas->id === (int) $selectedKelasId);

        return $available ? (int) $selectedKelasId : null;
    }

    private function isSiswaScope(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if (is_admin() || $user->hasRole('Guru') || $user->hasRole('Kepala Sekolah')) {
            return false;
        }

        if ($user->hasRole('Siswa')) {
            return true;
        }

        return $user->siswa()->exists();
    }

    private function resolveSiswaKelasId(): int
    {
        $user = auth()->user();
        if (!$user) {
            return 0;
        }

        $kelasId = (int) optional($user->siswa)->kelas_id;
        if ($kelasId > 0) {
            return $kelasId;
        }

        $normalizedEmail = mb_strtolower(trim((string) ($user->email ?? '')));
        if ($normalizedEmail === '') {
            return 0;
        }

        $siswaByEmail = Siswa::query()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
            ->first();

        if (!$siswaByEmail) {
            return 0;
        }

        if ((int) ($siswaByEmail->user_id ?? 0) === 0) {
            Siswa::query()->whereKey((int) $siswaByEmail->id)->update(['user_id' => (int) $user->id]);
        }

        return (int) ($siswaByEmail->kelas_id ?? 0);
    }

    private function buildMonthOptions(Semester $semester): array
    {
        $start = Carbon::parse((string) $semester->tanggal_mulai)->startOfMonth();
        $end = Carbon::parse((string) $semester->tanggal_selesai)->startOfMonth();

        $options = [];
        foreach (CarbonPeriod::create($start, '1 month', $end) as $date) {
            $options[$date->format('Y-m')] = $date->translatedFormat('F Y');
        }

        return $options;
    }

    private function parseDateOrAbort(string $tanggal): Carbon
    {
        try {
            return Carbon::parse($tanggal)->startOfDay();
        } catch (\Throwable $e) {
            abort(404);
        }
    }

    private function dateInsideSemester(Carbon $date, Semester $semester): bool
    {
        $start = Carbon::parse((string) $semester->tanggal_mulai)->startOfDay();
        $end = Carbon::parse((string) $semester->tanggal_selesai)->startOfDay();

        return $date->betweenIncluded($start, $end);
    }
}
