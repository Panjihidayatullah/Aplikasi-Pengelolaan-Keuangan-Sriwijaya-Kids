<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Pengeluaran;
use App\Models\Kelas;
use App\Models\Aset;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    /**
     * Display activity history with filters
     */
    public function index(Request $request)
    {
        $activities = collect();

        // Get filter parameters
        $type = $request->input('type', 'all');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('search');

        // Pembayaran Activities
        if ($type === 'all' || $type === 'pembayaran') {
            $pembayaranQuery = Pembayaran::with(['siswa', 'jenis'])
                ->when($dateFrom, function($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->when($search, function($q) use ($search) {
                    $q->where(function($query) use ($search) {
                        $query->where('kode_transaksi', 'ILIKE', "%{$search}%")
                              ->orWhereHas('siswa', function($q) use ($search) {
                                  $q->where('nama', 'ILIKE', "%{$search}%");
                              });
                    });
                })
                ->latest()
                ->take(100)
                ->get()
                ->map(function ($payment) {
                    return [
                        'type' => 'pembayaran',
                        'type_label' => 'Pembayaran',
                        'title' => 'Pembayaran Baru Diterima',
                        'description' => ($payment->siswa->nama ?? 'Siswa') . ' - ' . ($payment->jenis->nama ?? 'Pembayaran'),
                        'amount' => 'Rp ' . number_format($payment->jumlah, 0, ',', '.'),
                        'time' => $payment->created_at,
                        'url' => route('pembayaran.show', $payment->id),
                        'icon_bg' => 'bg-green-100',
                        'icon_color' => 'text-green-600',
                        'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        'user' => $payment->user->name ?? 'System'
                    ];
                });
            $activities = $activities->merge($pembayaranQuery);
        }

        // Siswa Activities
        if ($type === 'all' || $type === 'siswa') {
            $siswaQuery = Siswa::with('kelas')
                ->when($dateFrom, function($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->when($search, function($q) use ($search) {
                    $q->where(function($query) use ($search) {
                        $query->where('nama', 'ILIKE', "%{$search}%")
                              ->orWhere('nis', 'ILIKE', "%{$search}%");
                    });
                })
                ->latest()
                ->take(100)
                ->get()
                ->map(function ($student) {
                    return [
                        'type' => 'siswa',
                        'type_label' => 'Siswa',
                        'title' => 'Siswa Baru Terdaftar',
                        'description' => $student->nama . ' - ' . ($student->kelas->nama_kelas ?? 'Belum ada kelas'),
                        'amount' => 'NIS: ' . $student->nis,
                        'time' => $student->created_at,
                        'url' => route('siswa.show', $student->id),
                        'icon_bg' => 'bg-blue-100',
                        'icon_color' => 'text-blue-600',
                        'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                        'user' => 'System'
                    ];
                });
            $activities = $activities->merge($siswaQuery);
        }

        // Pengeluaran Activities
        if ($type === 'all' || $type === 'pengeluaran') {
            $pengeluaranQuery = Pengeluaran::with(['jenis', 'user'])
                ->when($dateFrom, function($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->when($search, function($q) use ($search) {
                    $q->where(function($query) use ($search) {
                        $query->where('keterangan', 'ILIKE', "%{$search}%")
                              ->orWhere('kode_transaksi', 'ILIKE', "%{$search}%");
                    });
                })
                ->latest()
                ->take(100)
                ->get()
                ->map(function ($expense) {
                    return [
                        'type' => 'pengeluaran',
                        'type_label' => 'Pengeluaran',
                        'title' => 'Pengeluaran Baru Dicatat',
                        'description' => $expense->keterangan ?? 'Pengeluaran',
                        'amount' => 'Rp ' . number_format($expense->jumlah, 0, ',', '.'),
                        'time' => $expense->created_at,
                        'url' => route('pengeluaran.show', $expense->id),
                        'icon_bg' => 'bg-red-100',
                        'icon_color' => 'text-red-600',
                        'icon' => 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6',
                        'user' => $expense->user->name ?? 'System'
                    ];
                });
            $activities = $activities->merge($pengeluaranQuery);
        }

        // Kelas Activities
        if ($type === 'all' || $type === 'kelas') {
            $kelasQuery = Kelas::query()
                ->when($dateFrom, function($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->when($search, function($q) use ($search) {
                    $q->where('nama_kelas', 'ILIKE', "%{$search}%");
                })
                ->latest()
                ->take(100)
                ->get()
                ->map(function ($kelas) {
                    return [
                        'type' => 'kelas',
                        'type_label' => 'Kelas',
                        'title' => 'Kelas Baru Dibuat',
                        'description' => $kelas->nama_kelas . ' - Wali Kelas: ' . ($kelas->wali_kelas ?? '-'),
                        'amount' => 'Tingkat ' . $kelas->tingkat,
                        'time' => $kelas->created_at,
                        'url' => route('kelas.show', $kelas->id),
                        'icon_bg' => 'bg-purple-100',
                        'icon_color' => 'text-purple-600',
                        'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        'user' => 'System'
                    ];
                });
            $activities = $activities->merge($kelasQuery);
        }

        // Aset Activities
        if ($type === 'all' || $type === 'aset') {
            $asetQuery = Aset::query()
                ->when($dateFrom, function($q) use ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function($q) use ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
                })
                ->when($search, function($q) use ($search) {
                    $q->where(function($query) use ($search) {
                        $query->where('nama', 'ILIKE', "%{$search}%")
                              ->orWhere('lokasi', 'ILIKE', "%{$search}%");
                    });
                })
                ->latest()
                ->take(100)
                ->get()
                ->map(function ($aset) {
                    return [
                        'type' => 'aset',
                        'type_label' => 'Aset',
                        'title' => 'Aset Baru Ditambahkan',
                        'description' => $aset->nama . ' - ' . $aset->lokasi,
                        'amount' => 'Rp ' . number_format($aset->nilai_perolehan, 0, ',', '.'),
                        'time' => $aset->created_at,
                        'url' => route('aset.show', $aset->id),
                        'icon_bg' => 'bg-yellow-100',
                        'icon_color' => 'text-yellow-600',
                        'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        'user' => 'System'
                    ];
                });
            $activities = $activities->merge($asetQuery);
        }

        // Sort by time and paginate
        $activities = $activities
            ->sortByDesc('time')
            ->values();

        // Manual pagination
        $perPage = 20;
        $currentPage = $request->input('page', 1);
        $total = $activities->count();
        $activities = $activities->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $pagination = new \Illuminate\Pagination\LengthAwarePaginator(
            $activities,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('riwayat.index', [
            'activities' => $pagination,
            'type' => $type,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search
        ]);
    }
}
