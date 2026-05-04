<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\JenisPembayaran;
use App\Models\JenisPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

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

        $requestedGroupBy = strtolower((string) $request->input('group_by', ''));
        $groupBy = in_array($requestedGroupBy, ['day', 'month', 'year'], true)
            ? $requestedGroupBy
            : ($daysDiff <= 31 ? 'day' : 'month');

        if ($groupBy === 'day') {
            // Grouping per hari
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
        } elseif ($groupBy === 'month') {
            // Grouping per bulan
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
        } else {
            // Grouping per tahun
            $pemasukanRaw = Pembayaran::select(
                    DB::raw('DATE_TRUNC(\'year\', tanggal_bayar) as periode'),
                    DB::raw('SUM(jumlah) as total')
                )
                ->whereBetween('tanggal_bayar', [$startDate, $endDate])
                ->groupBy(DB::raw('DATE_TRUNC(\'year\', tanggal_bayar)'))
                ->orderBy(DB::raw('DATE_TRUNC(\'year\', tanggal_bayar)'))
                ->get()
                ->keyBy(function($item) {
                    return \Carbon\Carbon::parse($item->periode)->format('Y-01-01');
                });

            $pengeluaranRaw = Pengeluaran::select(
                    DB::raw('DATE_TRUNC(\'year\', tanggal) as periode'),
                    DB::raw('SUM(jumlah) as total')
                )
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->groupBy(DB::raw('DATE_TRUNC(\'year\', tanggal)'))
                ->orderBy(DB::raw('DATE_TRUNC(\'year\', tanggal)'))
                ->get()
                ->keyBy(function($item) {
                    return \Carbon\Carbon::parse($item->periode)->format('Y-01-01');
                });

            // Generate semua tahun dalam range
            $allPeriods = [];
            $current = $start->copy()->startOfYear();
            $endYear = $end->copy()->startOfYear();
            while ($current->lte($endYear)) {
                $periodKey = $current->format('Y-01-01');
                $allPeriods[] = [
                    'periode' => $periodKey,
                    'pemasukan' => $pemasukanRaw->has($periodKey) ? $pemasukanRaw[$periodKey]->total : 0,
                    'pengeluaran' => $pengeluaranRaw->has($periodKey) ? $pengeluaranRaw[$periodKey]->total : 0,
                ];
                $current->addYear();
            }

            $pemasukanData = collect($allPeriods)->map(fn($item) => (object)['periode' => $item['periode'], 'total' => $item['pemasukan']]);
            $pengeluaranData = collect($allPeriods)->map(fn($item) => (object)['periode' => $item['periode'], 'total' => $item['pengeluaran']]);
        }

        // Get detailed transactions
        $transactions = collect();
        
        // Get pemasukan
        $pemasukanTransactions = Pembayaran::with(['siswa', 'jenis'])
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->get()
            ->map(function($item) {
                $keterangan = $item->keterangan ?: 'Pembayaran';
                $namaSiswa = data_get($item, 'siswa.nama', 'Siswa tidak ditemukan');
                $namaJenis = JenisPembayaran::normalizeNama((string) data_get($item, 'jenis.nama', ''));

                return [
                    'tanggal' => $item->tanggal_bayar,
                    'keterangan' => $keterangan . ' - ' . $namaSiswa . ' (' . $namaJenis . ')',
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
                $keterangan = $item->keterangan ?: 'Pengeluaran';
                $namaJenis = JenisPengeluaran::normalizeNama((string) data_get($item, 'jenis.nama', ''));

                return [
                    'tanggal' => $item->tanggal,
                    'keterangan' => $keterangan . ' (' . $namaJenis . ')',
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

        // Filter by jenis pembayaran (supports legacy ID and normalized name key)
        $this->applyJenisPembayaranFilter($query, $request);

        // Filter by metode_bayar
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $pembayaran = $query->orderBy('tanggal_bayar', 'desc')->get();

        $totalPerJenis = $pembayaran
            ->groupBy(function ($item) {
                return JenisPembayaran::normalizeNama((string) data_get($item, 'jenis.nama', ''));
            })
            ->map(function ($items, $kategori) {
                return [
                    'nama' => (string) $kategori,
                    'total' => (float) $items->sum('jumlah'),
                ];
            })
            ->values();

        $grandTotal = $pembayaran->sum('jumlah');
        $jenisPembayaran = JenisPembayaran::dropdownOptions();

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

        // Filter by jenis pengeluaran (normalized)
        $this->applyJenisPengeluaranFilter($query, $request);

        $pengeluaran = $query->orderBy('tanggal', 'desc')->get();

        $totalPerJenis = $pengeluaran
            ->groupBy(function ($item) {
                return JenisPengeluaran::normalizeNama((string) data_get($item, 'jenis.nama', ''));
            })
            ->map(function ($items, $kategori) {
                return [
                    'nama' => (string) $kategori,
                    'total' => (float) $items->sum('jumlah'),
                ];
            })
            ->values();

        $grandTotal = $pengeluaran->sum('jumlah');
        $jenisPengeluaran = JenisPengeluaran::dropdownOptions();

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
        $pemasukanPerJenis = $pemasukan->groupBy(function ($item) {
            return JenisPembayaran::normalizeNama((string) data_get($item, 'jenis.nama', ''));
        })->map(function ($items, $kategori) {
            return [
                'nama' => (string) $kategori,
                'items' => $items,
                'total' => $items->sum('jumlah')
            ];
        });

        $pengeluaranPerJenis = $pengeluaran->groupBy(function ($item) {
            return JenisPengeluaran::normalizeNama((string) data_get($item, 'jenis.nama', ''));
        })->map(function ($items, $kategori) {
            return [
                'nama' => (string) $kategori,
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

        // Filter by jenis pembayaran (supports legacy ID and normalized name key)
        $this->applyJenisPembayaranFilter($query, $request);

        // Filter by metode_bayar
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $pembayaran = $query->orderBy('tanggal_bayar', 'asc')->get();

        // Group by jenis
        $pembayaranPerJenis = $pembayaran->groupBy(function ($item) {
            return JenisPembayaran::normalizeNama((string) data_get($item, 'jenis.nama', ''));
        })->map(function ($items, $kategori) {
            return [
                'nama' => (string) $kategori,
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

        // Filter by jenis pengeluaran (normalized)
        $this->applyJenisPengeluaranFilter($query, $request);

        $pengeluaran = $query->orderBy('tanggal', 'asc')->get();

        // Group by normalized kategori
        $pengeluaranPerJenis = $pengeluaran->groupBy(function ($item) {
            return JenisPengeluaran::normalizeNama((string) data_get($item, 'jenis.nama', ''));
        })->map(function ($items, $kategori) {
            return [
                'nama' => (string) $kategori,
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
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        if ($type === 'cashflow') {
            return $this->exportCashflowExcel($request, $startDate, $endDate);
        }

        if ($type === 'pemasukan') {
            return $this->exportPemasukanExcel($request, $startDate, $endDate);
        }

        if ($type === 'pengeluaran') {
            return $this->exportPengeluaranExcel($request, $startDate, $endDate);
        }

        return abort(404);
    }

    private function exportCashflowExcel(Request $request, string $startDate, string $endDate)
    {
        $pemasukanTransactions = Pembayaran::with(['siswa', 'jenis'])
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->orderBy('tanggal_bayar')
            ->get()
            ->map(function ($item) {
                $keterangan = $item->keterangan ?: 'Pembayaran';
                $namaSiswa = data_get($item, 'siswa.nama', 'Siswa tidak ditemukan');
                $namaJenis = JenisPembayaran::normalizeNama((string) data_get($item, 'jenis.nama', ''));

                return [
                    'tanggal' => $item->tanggal_bayar,
                    'keterangan' => $keterangan . ' - ' . $namaSiswa . ' (' . $namaJenis . ')',
                    'pemasukan' => (float) $item->jumlah,
                    'pengeluaran' => 0.0,
                ];
            })
            ->values()
            ->all();

        $pengeluaranTransactions = Pengeluaran::with(['jenis'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->get()
            ->map(function ($item) {
                $keterangan = $item->keterangan ?: 'Pengeluaran';
                $namaJenis = JenisPengeluaran::normalizeNama((string) data_get($item, 'jenis.nama', ''));

                return [
                    'tanggal' => $item->tanggal,
                    'keterangan' => $keterangan . ' (' . $namaJenis . ')',
                    'pemasukan' => 0.0,
                    'pengeluaran' => (float) $item->jumlah,
                ];
            })
            ->values()
            ->all();

        $transactions = collect($pemasukanTransactions)
            ->merge($pengeluaranTransactions)
            ->sortBy('tanggal')
            ->values();

        $runningBalance = 0.0;
        $rows = $transactions->map(function ($item) use (&$runningBalance) {
            $runningBalance += ((float) $item['pemasukan'] - (float) $item['pengeluaran']);

            return [
                \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y'),
                (string) $item['keterangan'],
                (float) $item['pemasukan'],
                (float) $item['pengeluaran'],
                (float) $runningBalance,
            ];
        })->all();

        $headings = ['Tanggal', 'Keterangan', 'Pemasukan', 'Pengeluaran', 'Saldo'];
        $filename = 'Laporan_Cashflow_' . date('Y-m-d', strtotime($startDate)) . '_sd_' . date('Y-m-d', strtotime($endDate)) . '.xlsx';

        return $this->downloadExcel($headings, $rows, $filename);
    }

    private function exportPemasukanExcel(Request $request, string $startDate, string $endDate)
    {
        $query = Pembayaran::with(['siswa', 'jenis', 'user'])
            ->whereBetween('tanggal_bayar', [$startDate, $endDate]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                    ->orWhereHas('siswa', function ($q2) use ($search) {
                        $q2->where('nama', 'like', '%' . $search . '%')
                            ->orWhere('nis', 'like', '%' . $search . '%');
                    });
            });
        }

        $this->applyJenisPembayaranFilter($query, $request);

        if ($request->filled('metode_bayar')) {
            $metodeMap = [
                'tunai' => 'Tunai',
                'transfer' => 'Transfer',
                'qris' => 'QRIS',
            ];
            $metode = $metodeMap[strtolower((string) $request->metode_bayar)] ?? $request->metode_bayar;
            $query->where('metode_bayar', $metode);
        }

        $rows = $query->orderBy('tanggal_bayar', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y'),
                    (string) ($item->kode_transaksi ?? '-'),
                    (string) data_get($item, 'siswa.nis', '-'),
                    (string) data_get($item, 'siswa.nama', '-'),
                    (string) JenisPembayaran::normalizeNama((string) data_get($item, 'jenis.nama', '')),
                    (string) ($item->metode_bayar ?? '-'),
                    (float) ($item->jumlah ?? 0),
                    (string) ($item->keterangan ?? '-'),
                    (string) data_get($item, 'user.name', '-'),
                ];
            })
            ->all();

        $headings = ['Tanggal', 'Kode Transaksi', 'NIS', 'Nama Siswa', 'Jenis Pembayaran', 'Metode Bayar', 'Jumlah', 'Keterangan', 'Petugas'];
        $filename = 'Laporan_Pemasukan_' . date('Y-m-d', strtotime($startDate)) . '_sd_' . date('Y-m-d', strtotime($endDate)) . '.xlsx';

        return $this->downloadExcel($headings, $rows, $filename);
    }

    private function exportPengeluaranExcel(Request $request, string $startDate, string $endDate)
    {
        $query = Pengeluaran::with(['jenis', 'user'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                    ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        $this->applyJenisPengeluaranFilter($query, $request);

        $rows = $query->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y'),
                    (string) ($item->kode_transaksi ?? '-'),
                    (string) JenisPengeluaran::normalizeNama((string) data_get($item, 'jenis.nama', '')),
                    (float) ($item->jumlah ?? 0),
                    (string) ($item->keterangan ?? '-'),
                    (string) data_get($item, 'user.name', '-'),
                ];
            })
            ->all();

        $headings = ['Tanggal', 'Kode Transaksi', 'Jenis Pengeluaran', 'Jumlah', 'Keterangan', 'Petugas'];
        $filename = 'Laporan_Pengeluaran_' . date('Y-m-d', strtotime($startDate)) . '_sd_' . date('Y-m-d', strtotime($endDate)) . '.xlsx';

        return $this->downloadExcel($headings, $rows, $filename);
    }

    private function applyJenisPembayaranFilter($query, Request $request): void
    {
        if (!$request->filled('jenis_pembayaran_id')) {
            return;
        }

        $filterValue = trim((string) $request->input('jenis_pembayaran_id'));
        if ($filterValue === '') {
            return;
        }

        // Backward compatibility for existing URLs that still pass numeric ID.
        if (ctype_digit($filterValue)) {
            $ids = JenisPembayaran::equivalentIds((int) $filterValue);

            if (empty($ids)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('jenis_pembayaran_id', $ids);
            }

            return;
        }

        $normalized = JenisPembayaran::normalizeNama($filterValue);
        $ids = JenisPembayaran::query()
            ->withTrashed()
            ->get(['id', 'nama'])
            ->filter(fn ($item) => JenisPembayaran::normalizeNama((string) $item->nama) === $normalized)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (empty($ids)) {
            $query->whereRaw('1 = 0');
            return;
        }

        $query->whereIn('jenis_pembayaran_id', $ids);
    }

    private function applyJenisPengeluaranFilter($query, Request $request): void
    {
        if (!$request->filled('jenis_pengeluaran_id')) {
            return;
        }

        $filterValue = trim((string) $request->input('jenis_pengeluaran_id'));
        if ($filterValue === '') {
            return;
        }

        if (ctype_digit($filterValue)) {
            $ids = JenisPengeluaran::equivalentIds((int) $filterValue);

            if (empty($ids)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('jenis_pengeluaran_id', $ids);
            }

            return;
        }

        $normalized = JenisPengeluaran::normalizeNama($filterValue);
        $ids = JenisPengeluaran::query()
            ->withTrashed()
            ->get(['id', 'nama'])
            ->filter(fn ($item) => JenisPengeluaran::normalizeNama((string) $item->nama) === $normalized)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (empty($ids)) {
            $query->whereRaw('1 = 0');
            return;
        }

        $query->whereIn('jenis_pengeluaran_id', $ids);
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
}
