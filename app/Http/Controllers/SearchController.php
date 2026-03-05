<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\Aset;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handle global search
     */
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return redirect()->route('dashboard');
        }

        // Search Siswa
        $siswa = Siswa::where('nama', 'ILIKE', "%{$query}%")
            ->orWhere('nis', 'ILIKE', "%{$query}%")
            ->orWhere('email', 'ILIKE', "%{$query}%")
            ->with('kelas')
            ->limit(5)
            ->get();

        // Search Kelas
        $kelas = Kelas::where('nama_kelas', 'ILIKE', "%{$query}%")
            ->orWhere('wali_kelas', 'ILIKE', "%{$query}%")
            ->limit(5)
            ->get();

        // Search Pembayaran
        $pembayaran = Pembayaran::whereHas('siswa', function($q) use ($query) {
                $q->where('nama', 'ILIKE', "%{$query}%")
                  ->orWhere('nis', 'ILIKE', "%{$query}%");
            })
            ->orWhere('keterangan', 'ILIKE', "%{$query}%")
            ->with('siswa', 'jenisPembayaran')
            ->limit(5)
            ->get();

        // Search Pengeluaran
        $pengeluaran = Pengeluaran::where('keterangan', 'ILIKE', "%{$query}%")
            ->orWhere('kode_transaksi', 'ILIKE', "%{$query}%")
            ->with('jenisPengeluaran')
            ->limit(5)
            ->get();

        // Search Aset
        $aset = Aset::where('nama', 'ILIKE', "%{$query}%")
            ->orWhere('lokasi', 'ILIKE', "%{$query}%")
            ->orWhere('kategori', 'ILIKE', "%{$query}%")
            ->limit(5)
            ->get();

        $totalResults = $siswa->count() + $kelas->count() + $pembayaran->count() + $pengeluaran->count() + $aset->count();

        return view('search.results', compact(
            'query',
            'siswa',
            'kelas',
            'pembayaran',
            'pengeluaran',
            'aset',
            'totalResults'
        ));
    }
}
