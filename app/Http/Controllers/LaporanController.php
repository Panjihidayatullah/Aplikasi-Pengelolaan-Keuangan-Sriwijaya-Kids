<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\JenisPembayaran;
use App\Models\JenisPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function cashflow(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $pemasukan = Pembayaran::whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->sum('jumlah');

        $pengeluaran = Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        $saldo = $pemasukan - $pengeluaran;

        // Hitung selisih hari untuk menentukan grouping
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $daysDiff = $start->diffInDays($end);

        // Tentukan grouping berdasarkan range tanggal
        if ($daysDiff <= 31) {
            // Grouping per hari untuk range <= 31 hari
            $pemasukanData = Pembayaran::select(
                    DB::raw('tanggal_bayar::date as periode'),
                    DB::raw('SUM(jumlah) as total')
                )
                ->whereBetween('tanggal_bayar', [$startDate, $endDate])
                ->groupBy(DB::raw('tanggal_bayar::date'))
                ->orderBy(DB::raw('tanggal_bayar::date'))
                ->get();

            $pengeluaranData = Pengeluaran::select(
                    DB::raw('tanggal::date as periode'),
                    DB::raw('SUM(jumlah) as total')
                )
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->groupBy(DB::raw('tanggal::date'))
                ->orderBy(DB::raw('tanggal::date'))
                ->get();
            
            $groupBy = 'day';
        } else {
            // Grouping per bulan untuk range > 31 hari
            $pemasukanData = Pembayaran::select(
                    DB::raw('DATE_TRUNC(\'month\', tanggal_bayar) as periode'),
                    DB::raw('SUM(jumlah) as total')
                )
                ->whereBetween('tanggal_bayar', [$startDate, $endDate])
                ->groupBy(DB::raw('DATE_TRUNC(\'month\', tanggal_bayar)'))
                ->orderBy(DB::raw('DATE_TRUNC(\'month\', tanggal_bayar)'))
                ->get();

            $pengeluaranData = Pengeluaran::select(
                    DB::raw('DATE_TRUNC(\'month\', tanggal) as periode'),
                    DB::raw('SUM(jumlah) as total')
                )
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->groupBy(DB::raw('DATE_TRUNC(\'month\', tanggal)'))
                ->orderBy(DB::raw('DATE_TRUNC(\'month\', tanggal)'))
                ->get();
            
            $groupBy = 'month';
        }

        // Get detailed transactions
        $transactions = collect();
        
        // Get pemasukan
        $pemasukanTransactions = Pembayaran::with(['siswa', 'jenis'])
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->get()
            ->map(function($item) {
                return [
                    'tanggal' => $item->tanggal_bayar,
                    'keterangan' => $item->keterangan . ' - ' . $item->siswa->nama . ' (' . $item->jenis->nama . ')',
                    'pemasukan' => $item->jumlah,
                    'pengeluaran' => 0,
                    'type' => 'pemasukan'
                ];
            })
            ->values()
            ->all();
        
        // Get pengeluaran
        $pengeluaranTransactions = Pengeluaran::with(['jenis'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->map(function($item) {
                return [
                    'tanggal' => $item->tanggal,
                    'keterangan' => $item->keterangan . ' (' . $item->jenis->nama . ')',
                    'pemasukan' => 0,
                    'pengeluaran' => $item->jumlah,
                    'type' => 'pengeluaran'
                ];
            })
            ->values()
            ->all();
        
        // Merge and sort by date
        $transactions = collect($pemasukanTransactions)
            ->merge($pengeluaranTransactions)
            ->sortBy('tanggal')
            ->values();
        
        // Calculate running balance
        $saldoAwal = 0; // You can get this from database if you have opening balance
        $runningBalance = $saldoAwal;
        
        $transactions = $transactions->map(function($item) use (&$runningBalance) {
            $runningBalance += $item['pemasukan'] - $item['pengeluaran'];
            $item['saldo'] = $runningBalance;
            return $item;
        });

        return view('laporan.cashflow', compact(
            'pemasukan',
            'pengeluaran',
            'saldo',
            'pemasukanData',
            'pengeluaranData',
            'startDate',
            'endDate',
            'groupBy',
            'transactions'
        ));
    }

    public function pemasukan(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $query = Pembayaran::with(['siswa', 'jenis', 'user']);

        // Filter by date range
        $query->whereBetween('tanggal_bayar', [$startDate, $endDate]);

        // Filter by search (student name, NIS, or kode_transaksi)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhereHas('siswa', function($q) use ($search) {
                      $q->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('nis', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by jenis_pembayaran_id
        if ($request->filled('jenis_pembayaran_id')) {
            $query->where('jenis_pembayaran_id', $request->jenis_pembayaran_id);
        }

        // Filter by metode_bayar
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $pembayaran = $query->orderBy('tanggal_bayar', 'desc')->get();

        $totalPerJenis = Pembayaran::select('jenis_pembayaran_id', DB::raw('SUM(jumlah) as total'))
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->groupBy('jenis_pembayaran_id')
            ->with('jenis')
            ->get();

        $grandTotal = $pembayaran->sum('jumlah');
        $jenisPembayaran = JenisPembayaran::all();

        return view('laporan.pemasukan', compact(
            'pembayaran',
            'totalPerJenis',
            'grandTotal',
            'startDate',
            'endDate',
            'jenisPembayaran'
        ));
    }

    public function pengeluaran(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $query = Pengeluaran::with(['jenis', 'user']);

        // Filter by date range
        $query->whereBetween('tanggal', [$startDate, $endDate]);

        // Filter by search (kode_transaksi or keterangan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        // Filter by jenis_pengeluaran_id
        if ($request->filled('jenis_pengeluaran_id')) {
            $query->where('jenis_pengeluaran_id', $request->jenis_pengeluaran_id);
        }

        $pengeluaran = $query->orderBy('tanggal', 'desc')->get();

        $totalPerJenis = Pengeluaran::select('jenis_pengeluaran_id', DB::raw('SUM(jumlah) as total'))
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('jenis_pengeluaran_id')
            ->with('jenis')
            ->get();

        $grandTotal = $pengeluaran->sum('jumlah');
        $jenisPengeluaran = JenisPengeluaran::all();

        return view('laporan.pengeluaran', compact(
            'pengeluaran',
            'totalPerJenis',
            'grandTotal',
            'startDate',
            'endDate',
            'jenisPengeluaran'
        ));
    }

    public function exportCashflow(Request $request)
    {
        // Placeholder untuk export Excel/PDF
        return response()->json(['message' => 'Export cashflow akan segera tersedia']);
    }

    public function exportPemasukan(Request $request)
    {
        // Placeholder untuk export Excel/PDF
        return response()->json(['message' => 'Export pemasukan akan segera tersedia']);
    }

    public function exportPengeluaran(Request $request)
    {
        // Placeholder untuk export Excel/PDF
        return response()->json(['message' => 'Export pengeluaran akan segera tersedia']);
    }

    public function exportPdf(Request $request, $type)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        if ($type === 'cashflow') {
            return $this->exportCashflowPdf($request, $startDate, $endDate);
        } elseif ($type === 'pemasukan') {
            return $this->exportPemasukanPdf($request, $startDate, $endDate);
        } elseif ($type === 'pengeluaran') {
            return $this->exportPengeluaranPdf($request, $startDate, $endDate);
        }

        return abort(404);
    }

    private function exportCashflowPdf(Request $request, $startDate, $endDate)
    {
        // Ambil data pemasukan dengan detail
        $pemasukan = Pembayaran::with(['siswa', 'jenis'])
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->orderBy('tanggal_bayar', 'asc')
            ->get();

        // Ambil data pengeluaran dengan detail
        $pengeluaran = Pengeluaran::with(['jenis'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();

        // Kelompokkan berdasarkan jenis
        $pemasukanPerJenis = $pemasukan->groupBy('jenis_pembayaran_id')->map(function ($items) {
            return [
                'nama' => $items->first()->jenis->nama ?? 'Lainnya',
                'items' => $items,
                'total' => $items->sum('jumlah')
            ];
        });

        $pengeluaranPerJenis = $pengeluaran->groupBy('jenis_pengeluaran_id')->map(function ($items) {
            return [
                'nama' => $items->first()->jenis->nama ?? 'Lainnya',
                'items' => $items,
                'total' => $items->sum('jumlah')
            ];
        });

        $totalPemasukan = $pemasukan->sum('jumlah');
        $totalPengeluaran = $pengeluaran->sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $pdf = Pdf::loadView('laporan.pdf.cashflow', compact(
            'pemasukan',
            'pengeluaran',
            'pemasukanPerJenis',
            'pengeluaranPerJenis',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo',
            'startDate',
            'endDate'
        ));

        $pdf->setPaper('a4', 'portrait');
        
        $filename = 'Laporan_Cashflow_' . date('Y-m-d', strtotime($startDate)) . '_sd_' . date('Y-m-d', strtotime($endDate)) . '.pdf';
        
        return $pdf->download($filename);
    }

    private function exportPemasukanPdf(Request $request, $startDate, $endDate)
    {
        $query = Pembayaran::with(['siswa', 'jenis', 'user']);

        // Filter by date range
        $query->whereBetween('tanggal_bayar', [$startDate, $endDate]);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhereHas('siswa', function($q) use ($search) {
                      $q->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('nis', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by jenis_pembayaran_id
        if ($request->filled('jenis_pembayaran_id')) {
            $query->where('jenis_pembayaran_id', $request->jenis_pembayaran_id);
        }

        // Filter by metode_bayar
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $pembayaran = $query->orderBy('tanggal_bayar', 'asc')->get();

        // Group by jenis
        $pembayaranPerJenis = $pembayaran->groupBy('jenis_pembayaran_id')->map(function ($items) {
            return [
                'nama' => $items->first()->jenis->nama ?? 'Lainnya',
                'items' => $items,
                'total' => $items->sum('jumlah')
            ];
        });

        $grandTotal = $pembayaran->sum('jumlah');

        $pdf = Pdf::loadView('laporan.pdf.pemasukan', compact(
            'pembayaran',
            'pembayaranPerJenis',
            'grandTotal',
            'startDate',
            'endDate'
        ));

        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'Laporan_Pemasukan_' . date('Y-m-d', strtotime($startDate)) . '_sd_' . date('Y-m-d', strtotime($endDate)) . '.pdf';
        
        return $pdf->download($filename);
    }

    private function exportPengeluaranPdf(Request $request, $startDate, $endDate)
    {
        $query = Pengeluaran::with(['jenis', 'user']);

        // Filter by date range
        $query->whereBetween('tanggal', [$startDate, $endDate]);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        // Filter by jenis_pengeluaran_id
        if ($request->filled('jenis_pengeluaran_id')) {
            $query->where('jenis_pengeluaran_id', $request->jenis_pengeluaran_id);
        }

        $pengeluaran = $query->orderBy('tanggal', 'asc')->get();

        // Group by jenis
        $pengeluaranPerJenis = $pengeluaran->groupBy('jenis_pengeluaran_id')->map(function ($items) {
            return [
                'nama' => $items->first()->jenis->nama ?? 'Lainnya',
                'items' => $items,
                'total' => $items->sum('jumlah')
            ];
        });

        $grandTotal = $pengeluaran->sum('jumlah');

        $pdf = Pdf::loadView('laporan.pdf.pengeluaran', compact(
            'pengeluaran',
            'pengeluaranPerJenis',
            'grandTotal',
            'startDate',
            'endDate'
        ));

        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'Laporan_Pengeluaran_' . date('Y-m-d', strtotime($startDate)) . '_sd_' . date('Y-m-d', strtotime($endDate)) . '.pdf';
        
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request, $type)
    {
        // Implementasi export Excel
        return response()->json(['message' => 'Export Excel sedang dalam pengembangan']);
    }
}
