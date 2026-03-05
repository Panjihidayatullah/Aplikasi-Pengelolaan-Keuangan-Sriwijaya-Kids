<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
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
    public function index()
    {
        // Data untuk dashboard
        $totalSiswa = Siswa::where('is_active', true)->count();
        $totalPembayaran = Pembayaran::whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('jumlah');
        $totalPengeluaran = Pengeluaran::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');
        $saldo = $totalPembayaran - $totalPengeluaran;
        
        // Data untuk chart (6 bulan terakhir)
        $startDate = now()->subMonths(5)->startOfMonth();
        $endDate = now()->endOfMonth();
        
        $pemasukanBulanan = Pembayaran::select(
                DB::raw('DATE_TRUNC(\'month\', tanggal_bayar) as bulan'),
                DB::raw('SUM(jumlah) as total')
            )
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE_TRUNC(\'month\', tanggal_bayar)'))
            ->orderBy(DB::raw('DATE_TRUNC(\'month\', tanggal_bayar)'))
            ->get();
        
        $pengeluaranBulanan = Pengeluaran::select(
                DB::raw('DATE_TRUNC(\'month\', tanggal) as bulan'),
                DB::raw('SUM(jumlah) as total')
            )
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE_TRUNC(\'month\', tanggal)'))
            ->orderBy(DB::raw('DATE_TRUNC(\'month\', tanggal)'))
            ->get();
        
        return view('dashboard.index', compact(
            'totalSiswa',
            'totalPembayaran',
            'totalPengeluaran',
            'saldo',
            'pemasukanBulanan',
            'pengeluaranBulanan'
        ));
    }
}
