<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use App\Models\Ujian;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display the dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $dashboardRole = $this->resolveDashboardRole($user);

        if ($dashboardRole === 'guru') {
            $guruData = $this->buildGuruDashboardData($user);

            return view('dashboard.index', array_merge([
                'dashboardRole' => 'guru',
                'dashboardSubtitle' => $this->dashboardSubtitleForRole('guru'),
            ], $guruData));
        }

        if ($dashboardRole === 'siswa') {
            $siswaData = $this->buildSiswaDashboardData($user);

            return view('dashboard.index', array_merge([
                'dashboardRole' => 'siswa',
                'dashboardSubtitle' => $this->dashboardSubtitleForRole('siswa'),
            ], $siswaData));
        }

        $defaultDashboardAccess = $this->buildDefaultDashboardAccess($user);

        // Data untuk dashboard
        $totalSiswa = Siswa::where('is_active', true)->count();
        $totalPembayaran = Pembayaran::whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('jumlah');
        $totalPengeluaran = Pengeluaran::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');
        $saldo = $totalPembayaran - $totalPengeluaran;

        // Ringkasan gabungan modul Akademik + LMS + Keuangan
        $totalKelas = Kelas::query()->count();
        $totalJadwalAktif = JadwalPelajaran::query()->where('is_active', true)->count();
        $totalUjianMendatang = Ujian::query()->whereDate('tanggal_ujian', '>=', now()->toDateString())->count();

        $totalMateri = Materi::query()->count();
        $totalTugas = Tugas::query()->count();
        $totalPengumpulan = PengumpulanTugas::query()->count();

        $transaksiKeuanganBulanIni = Pembayaran::query()
            ->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->count()
            + Pengeluaran::query()
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->count();

        $expenseComposition = Pengeluaran::query()
            ->leftJoin('jenis_pengeluaran', 'pengeluaran.jenis_pengeluaran_id', '=', 'jenis_pengeluaran.id')
            ->whereMonth('pengeluaran.tanggal', now()->month)
            ->whereYear('pengeluaran.tanggal', now()->year)
            ->selectRaw("COALESCE(jenis_pengeluaran.nama, 'Lainnya') as kategori")
            ->selectRaw('SUM(pengeluaran.jumlah) as total')
            ->groupBy('jenis_pengeluaran.nama')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn ($item) => [
                'kategori' => (string) $item->kategori,
                'total' => (float) $item->total,
            ])
            ->values();

        $pembayaranTerbaru = Pembayaran::query()
            ->with(['siswa:id,nama', 'jenis:id,nama'])
            ->select('id', 'tanggal_bayar', 'jumlah', 'status', 'siswa_id', 'jenis_pembayaran_id')
            ->latest('tanggal_bayar')
            ->limit(8)
            ->get()
            ->map(fn ($item) => [
                'tanggal' => optional($item->tanggal_bayar)?->format('Y-m-d'),
                'sort_at' => optional($item->tanggal_bayar)?->timestamp ?? 0,
                'deskripsi' => trim(
                    sprintf(
                        '%s%s',
                        $item->jenis?->nama ? 'Pembayaran ' . $item->jenis->nama : 'Pembayaran',
                        $item->siswa?->nama ? ' - ' . $item->siswa->nama : ''
                    )
                ),
                'tipe' => 'Pemasukan',
                'jumlah' => (float) $item->jumlah,
                'status' => $item->status ?: 'Lunas',
                'url' => route('pembayaran.index'),
            ]);

        $pengeluaranTerbaru = Pengeluaran::query()
            ->with(['jenis:id,nama'])
            ->select('id', 'tanggal', 'jumlah', 'status', 'jenis_pengeluaran_id', 'keterangan')
            ->latest('tanggal')
            ->limit(8)
            ->get()
            ->map(fn ($item) => [
                'tanggal' => optional($item->tanggal)?->format('Y-m-d'),
                'sort_at' => optional($item->tanggal)?->timestamp ?? 0,
                'deskripsi' => trim(
                    sprintf(
                        '%s%s',
                        $item->jenis?->nama ? $item->jenis->nama : 'Pengeluaran',
                        $item->keterangan ? ' - ' . $item->keterangan : ''
                    )
                ),
                'tipe' => 'Pengeluaran',
                'jumlah' => (float) $item->jumlah,
                'status' => $item->status ?: 'Disetujui',
                'url' => route('pengeluaran.index'),
            ]);

        $recentTransactions = $pembayaranTerbaru
            ->concat($pengeluaranTerbaru)
            ->sortByDesc('sort_at')
            ->take(8)
            ->values();
        
        // Data untuk chart dengan filter
        $startDate = $request->input('chart_start_date', now()->subMonths(5)->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('chart_end_date', now()->endOfMonth()->format('Y-m-d'));
        
        // Hitung selisih hari untuk menentukan grouping
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $daysDiff = $start->diffInDays($end);

        // Tentukan grouping berdasarkan range tanggal
        if ($daysDiff <= 31) {
            // Grouping per hari untuk range <= 31 hari
            $pemasukanRaw = Pembayaran::select(
                    DB::raw('tanggal_bayar::date as periode'),
                    DB::raw('SUM(jumlah) as total')
                )
                ->whereBetween('tanggal_bayar', [$startDate, $endDate])
                ->groupBy(DB::raw('tanggal_bayar::date'))
                ->orderBy(DB::raw('tanggal_bayar::date'))
                ->get()
                ->keyBy('periode');

            $pengeluaranRaw = Pengeluaran::select(
                    DB::raw('tanggal::date as periode'),
                    DB::raw('SUM(jumlah) as total')
                )
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->groupBy(DB::raw('tanggal::date'))
                ->orderBy(DB::raw('tanggal::date'))
                ->get()
                ->keyBy('periode');
            
            // Generate semua tanggal dalam range
            $allPeriods = [];
            $current = $start->copy();
            while ($current->lte($end)) {
                $periodKey = $current->format('Y-m-d');
                $allPeriods[] = [
                    'periode' => $periodKey,
                    'pemasukan' => $pemasukanRaw->has($periodKey) ? $pemasukanRaw[$periodKey]->total : 0,
                    'pengeluaran' => $pengeluaranRaw->has($periodKey) ? $pengeluaranRaw[$periodKey]->total : 0,
                ];
                $current->addDay();
            }
            
            $pemasukanData = collect($allPeriods)->map(fn($item) => (object)['periode' => $item['periode'], 'total' => $item['pemasukan']]);
            $pengeluaranData = collect($allPeriods)->map(fn($item) => (object)['periode' => $item['periode'], 'total' => $item['pengeluaran']]);
            
            $groupBy = 'day';
        } else {
            // Grouping per bulan untuk range > 31 hari
            $pemasukanRaw = Pembayaran::select(
                    DB::raw('DATE_TRUNC(\'month\', tanggal_bayar) as periode'),
                    DB::raw('SUM(jumlah) as total')
                )
                ->whereBetween('tanggal_bayar', [$startDate, $endDate])
                ->groupBy(DB::raw('DATE_TRUNC(\'month\', tanggal_bayar)'))
                ->orderBy(DB::raw('DATE_TRUNC(\'month\', tanggal_bayar)'))
                ->get()
                ->keyBy(function($item) {
                    return \Carbon\Carbon::parse($item->periode)->format('Y-m-01');
                });

            $pengeluaranRaw = Pengeluaran::select(
                    DB::raw('DATE_TRUNC(\'month\', tanggal) as periode'),
                    DB::raw('SUM(jumlah) as total')
                )
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->groupBy(DB::raw('DATE_TRUNC(\'month\', tanggal)'))
                ->orderBy(DB::raw('DATE_TRUNC(\'month\', tanggal)'))
                ->get()
                ->keyBy(function($item) {
                    return \Carbon\Carbon::parse($item->periode)->format('Y-m-01');
                });
            
            // Generate semua bulan dalam range
            $allPeriods = [];
            $current = $start->copy()->startOfMonth();
            $endMonth = $end->copy()->startOfMonth();
            while ($current->lte($endMonth)) {
                $periodKey = $current->format('Y-m-01');
                $allPeriods[] = [
                    'periode' => $periodKey,
                    'pemasukan' => $pemasukanRaw->has($periodKey) ? $pemasukanRaw[$periodKey]->total : 0,
                    'pengeluaran' => $pengeluaranRaw->has($periodKey) ? $pengeluaranRaw[$periodKey]->total : 0,
                ];
                $current->addMonth();
            }
            
            $pemasukanData = collect($allPeriods)->map(fn($item) => (object)['periode' => $item['periode'], 'total' => $item['pemasukan']]);
            $pengeluaranData = collect($allPeriods)->map(fn($item) => (object)['periode' => $item['periode'], 'total' => $item['pengeluaran']]);
            
            $groupBy = 'month';
        }
        
        return view('dashboard.index', array_merge(compact(
            'dashboardRole',
            'totalSiswa',
            'totalPembayaran',
            'totalPengeluaran',
            'saldo',
            'totalKelas',
            'totalJadwalAktif',
            'totalUjianMendatang',
            'totalMateri',
            'totalTugas',
            'totalPengumpulan',
            'transaksiKeuanganBulanIni',
            'expenseComposition',
            'recentTransactions',
            'pemasukanData',
            'pengeluaranData',
            'groupBy',
            'startDate',
            'endDate'
        ), $defaultDashboardAccess, [
            'dashboardSubtitle' => $this->dashboardSubtitleForRole($dashboardRole),
        ]));
    }

    private function resolveDashboardRole(User $user): string
    {
        if ($user->hasRole('Admin')) {
            return 'admin';
        }

        if ($user->hasRole('Bendahara')) {
            return 'bendahara';
        }

        if ($user->hasRole('Kepala Sekolah')) {
            return 'kepala_sekolah';
        }

        if ($user->hasRole('Guru') || $user->guru()->exists()) {
            return 'guru';
        }

        if ($user->hasRole('Siswa') || $user->siswa()->exists()) {
            return 'siswa';
        }

        return 'default';
    }

    private function dashboardSubtitleForRole(string $dashboardRole): string
    {
        $schoolName = config('finance.school.name', 'Sekolah');

        return match ($dashboardRole) {
            'admin' => $schoolName . ' - Ringkasan Operasional Lengkap',
            'bendahara' => $schoolName . ' - Ringkasan Keuangan dan Monitoring',
            'kepala_sekolah' => $schoolName . ' - Monitoring Akademik dan Keuangan',
            'guru' => $schoolName . ' - Ringkasan Akademik dan LMS untuk Guru',
            'siswa' => $schoolName . ' - Ringkasan Belajar dan LMS untuk Siswa',
            default => $schoolName . ' - Dashboard Terpadu Akademik, LMS, dan Keuangan',
        };
    }

    private function buildDefaultDashboardAccess(User $user): array
    {
        $isAdmin = $user->hasRole('Admin');
        $isKepalaSekolah = $user->hasRole('Kepala Sekolah');

        $canViewKelas = can_access('view kelas');
        $canViewUjian = can_access('view ujian');
        $canViewJadwal = $isAdmin || $user->hasRole('Kepala Sekolah');

        $canViewLmsMateri = can_access('view lms-materi');
        $canViewLmsTugas = can_access('view lms-tugas');
        $canViewLmsMonitoring = can_access('view lms-monitoring');

        // Kepala Sekolah pada dashboard hanya diarahkan ke Monitoring LMS.
        if ($isKepalaSekolah && !$isAdmin) {
            $canViewLmsMateri = false;
            $canViewLmsTugas = false;
            $canViewLmsIndex = false;
        } else {
            $canViewLmsIndex = $canViewLmsMateri || $canViewLmsTugas || $canViewLmsMonitoring || $isAdmin;
        }

        $canViewPembayaran = can_access('view pembayaran');
        $canViewPengeluaran = can_access('view pengeluaran');
        $canViewCashflow = can_access('view laporan cashflow');
        $canViewSiswa = can_access('view siswa');
        $canViewRiwayat = can_access('view riwayat');

        $canCreateSiswa = can_access('create siswa');
        $canCreatePembayaran = can_access('create pembayaran');
        $canCreatePengeluaran = can_access('create pengeluaran');
        $canExportLaporan = can_access('export laporan');

        return compact(
            'canViewKelas',
            'canViewUjian',
            'canViewJadwal',
            'canViewLmsIndex',
            'canViewLmsMateri',
            'canViewLmsTugas',
            'canViewLmsMonitoring',
            'canViewPembayaran',
            'canViewPengeluaran',
            'canViewCashflow',
            'canViewSiswa',
            'canViewRiwayat',
            'canCreateSiswa',
            'canCreatePembayaran',
            'canCreatePengeluaran',
            'canExportLaporan'
        );
    }

    private function buildGuruDashboardData(User $user): array
    {
        $guru = Guru::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        $guruMetrics = [
            'kelas_diampu' => 0,
            'jadwal_mengajar' => 0,
            'ujian_mendatang' => 0,
            'materi' => 0,
            'tugas' => 0,
            'pengumpulan' => 0,
            'belum_dinilai' => 0,
        ];

        $guruMonthlyLabels = [];
        $guruMonthlyTugas = [];
        $guruMonthlyPengumpulan = [];
        $guruStatusLabels = ['Dinilai', 'Belum Dinilai'];
        $guruStatusValues = [0, 0];
        $guruUpcomingTugas = collect();

        if (!$guru) {
            return compact(
                'guruMetrics',
                'guruMonthlyLabels',
                'guruMonthlyTugas',
                'guruMonthlyPengumpulan',
                'guruStatusLabels',
                'guruStatusValues',
                'guruUpcomingTugas'
            );
        }

        $jadwalAktif = JadwalPelajaran::query()
            ->where('guru_id', $guru->id)
            ->where('is_active', true);

        $mapelIds = (clone $jadwalAktif)
            ->pluck('mata_pelajaran_id')
            ->filter()
            ->unique()
            ->values();

        $kelasIds = (clone $jadwalAktif)
            ->pluck('kelas_id')
            ->filter()
            ->unique()
            ->values();

        $pengumpulanQuery = PengumpulanTugas::query()
            ->whereHas('tugas', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            });

        $guruMetrics = [
            'kelas_diampu' => (clone $jadwalAktif)->distinct('kelas_id')->count('kelas_id'),
            'jadwal_mengajar' => (clone $jadwalAktif)->count(),
            'ujian_mendatang' => Ujian::query()
                ->whereDate('tanggal_ujian', '>=', now()->toDateString())
                ->when($mapelIds->isNotEmpty(), fn ($q) => $q->whereIn('mata_pelajaran_id', $mapelIds))
                ->when($kelasIds->isNotEmpty(), fn ($q) => $q->whereIn('kelas_id', $kelasIds))
                ->count(),
            'materi' => Materi::query()->where('guru_id', $guru->id)->count(),
            'tugas' => Tugas::query()->where('guru_id', $guru->id)->count(),
            'pengumpulan' => (clone $pengumpulanQuery)->count(),
            'belum_dinilai' => (clone $pengumpulanQuery)->whereNull('nilai')->count(),
        ];

        $startMonth = now()->copy()->subMonths(5)->startOfMonth();
        $cursor = $startMonth->copy();

        while ($cursor->lte(now())) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();

            $guruMonthlyLabels[] = $cursor->translatedFormat('M Y');
            $guruMonthlyTugas[] = Tugas::query()
                ->where('guru_id', $guru->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $guruMonthlyPengumpulan[] = PengumpulanTugas::query()
                ->whereHas('tugas', function ($q) use ($guru) {
                    $q->where('guru_id', $guru->id);
                })
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $cursor = $cursor->addMonth();
        }

        $guruStatusValues = [
            (clone $pengumpulanQuery)->whereNotNull('nilai')->count(),
            (clone $pengumpulanQuery)->whereNull('nilai')->count(),
        ];

        $guruUpcomingTugas = Tugas::query()
            ->with(['kelas:id,nama_kelas', 'mataPelajaran:id,nama_mapel'])
            ->where('guru_id', $guru->id)
            ->where(function ($q) {
                $q->whereDate('tanggal_deadline', '>=', now()->toDateString())
                    ->orWhereDate('deadline', '>=', now()->toDateString());
            })
            ->orderByRaw('COALESCE(tanggal_deadline, deadline) ASC')
            ->limit(6)
            ->get();

        return compact(
            'guruMetrics',
            'guruMonthlyLabels',
            'guruMonthlyTugas',
            'guruMonthlyPengumpulan',
            'guruStatusLabels',
            'guruStatusValues',
            'guruUpcomingTugas'
        );
    }

    private function buildSiswaDashboardData(User $user): array
    {
        $siswa = Siswa::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->with('kelas:id,nama_kelas')
            ->first();

        $siswaMetrics = [
            'kelas' => '-',
            'jadwal_aktif' => 0,
            'ujian_mendatang' => 0,
            'materi' => 0,
            'total_tugas' => 0,
            'tugas_selesai' => 0,
            'tugas_belum' => 0,
        ];

        $siswaScheduleLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $siswaScheduleValues = [0, 0, 0, 0, 0, 0];
        $siswaProgressLabels = ['Selesai', 'Belum Dikumpulkan'];
        $siswaProgressValues = [0, 0];
        $siswaUpcomingTugas = collect();

        if (!$siswa || !$siswa->kelas_id) {
            return compact(
                'siswaMetrics',
                'siswaScheduleLabels',
                'siswaScheduleValues',
                'siswaProgressLabels',
                'siswaProgressValues',
                'siswaUpcomingTugas'
            );
        }

        $kelasId = (int) $siswa->kelas_id;
        $totalTugas = Tugas::query()->where('kelas_id', $kelasId)->count();

        $submittedTaskIds = PengumpulanTugas::query()
            ->where('siswa_id', $siswa->id)
            ->pluck('tugas_id')
            ->filter()
            ->unique();

        $tugasSelesai = Tugas::query()
            ->where('kelas_id', $kelasId)
            ->whereIn('id', $submittedTaskIds)
            ->count();

        $siswaMetrics = [
            'kelas' => $siswa->kelas?->nama_kelas ?? '-',
            'jadwal_aktif' => JadwalPelajaran::query()
                ->where('kelas_id', $kelasId)
                ->where('is_active', true)
                ->count(),
            'ujian_mendatang' => Ujian::query()
                ->where('kelas_id', $kelasId)
                ->whereDate('tanggal_ujian', '>=', now()->toDateString())
                ->count(),
            'materi' => Materi::query()->where('kelas_id', $kelasId)->count(),
            'total_tugas' => $totalTugas,
            'tugas_selesai' => $tugasSelesai,
            'tugas_belum' => max(0, $totalTugas - $tugasSelesai),
        ];

        $jadwalByHari = JadwalPelajaran::query()
            ->select('hari', DB::raw('COUNT(*) as total'))
            ->where('kelas_id', $kelasId)
            ->where('is_active', true)
            ->groupBy('hari')
            ->get()
            ->mapWithKeys(function ($item) {
                return [mb_strtolower((string) $item->hari) => (int) $item->total];
            });

        $siswaScheduleValues = array_map(function ($hari) use ($jadwalByHari) {
            return (int) ($jadwalByHari[mb_strtolower($hari)] ?? 0);
        }, $siswaScheduleLabels);

        $siswaProgressValues = [
            (int) $siswaMetrics['tugas_selesai'],
            (int) $siswaMetrics['tugas_belum'],
        ];

        $siswaUpcomingTugas = Tugas::query()
            ->with(['mataPelajaran:id,nama_mapel'])
            ->where('kelas_id', $kelasId)
            ->where(function ($q) {
                $q->whereDate('tanggal_deadline', '>=', now()->toDateString())
                    ->orWhereDate('deadline', '>=', now()->toDateString());
            })
            ->orderByRaw('COALESCE(tanggal_deadline, deadline) ASC')
            ->limit(6)
            ->get();

        return compact(
            'siswaMetrics',
            'siswaScheduleLabels',
            'siswaScheduleValues',
            'siswaProgressLabels',
            'siswaProgressValues',
            'siswaUpcomingTugas'
        );
    }
}
