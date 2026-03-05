<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\JenisPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with(['siswa.kelas', 'jenis', 'user']);

        // Filter by search (Kode Transaksi or Nama Siswa)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhereHas('siswa', function($q2) use ($search) {
                      $q2->where('nama', 'like', '%' . $search . '%')
                         ->orWhere('nis', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by Tanggal Range
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal_bayar', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_bayar', '<=', $request->tanggal_akhir);
        }

        // Filter by Jenis Pembayaran
        if ($request->filled('jenis_pembayaran_id')) {
            $query->where('jenis_pembayaran_id', $request->jenis_pembayaran_id);
        }

        // Filter by Metode Bayar
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembayaran = $query->orderBy('tanggal_bayar', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        $jenisPembayaran = JenisPembayaran::orderBy('nama')->get();

        // Statistics - dengan filter yang sama
        $statsQuery = Pembayaran::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhereHas('siswa', function($q2) use ($search) {
                      $q2->where('nama', 'like', '%' . $search . '%')
                         ->orWhere('nis', 'like', '%' . $search . '%');
                  });
            });
        }
        if ($request->filled('tanggal_mulai')) {
            $statsQuery->where('tanggal_bayar', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $statsQuery->where('tanggal_bayar', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('jenis_pembayaran_id')) {
            $statsQuery->where('jenis_pembayaran_id', $request->jenis_pembayaran_id);
        }
        if ($request->filled('metode_bayar')) {
            $statsQuery->where('metode_bayar', $request->metode_bayar);
        }
        if ($request->filled('status')) {
            $statsQuery->where('status', $request->status);
        }

        $totalSemua = $statsQuery->sum('jumlah');
        $totalTunai = (clone $statsQuery)->where('metode_bayar', 'Tunai')->count();
        $totalTransfer = (clone $statsQuery)->where('metode_bayar', 'Transfer')->count();
        $totalQRIS = (clone $statsQuery)->where('metode_bayar', 'QRIS')->count();

        return view('pembayaran.index', compact(
            'pembayaran', 
            'jenisPembayaran',
            'totalSemua',
            'totalTunai',
            'totalTransfer',
            'totalQRIS'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $siswa = Siswa::where('is_active', true)->orderBy('nama')->get();
        $jenisPembayaran = JenisPembayaran::orderBy('nama')->get();

        return view('pembayaran.create', compact('siswa', 'jenisPembayaran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris',
            'keterangan' => 'nullable|string',
        ]);

        // Generate kode transaksi unik
        $validated['kode_transaksi'] = 'PAY-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
        
        // Map metode_pembayaran ke metode_bayar dengan format yang sesuai enum database
        $metodeMap = [
            'tunai' => 'Tunai',
            'transfer' => 'Transfer',
            'qris' => 'QRIS'
        ];
        $validated['metode_bayar'] = $metodeMap[$validated['metode_pembayaran']] ?? 'Tunai';
        unset($validated['metode_pembayaran']);
        
        $validated['user_id'] = Auth::id();

        Pembayaran::create($validated);

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dicatat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pembayaran = Pembayaran::with(['siswa', 'jenis', 'user'])->findOrFail($id);

        return view('pembayaran.show', compact('pembayaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $siswa = Siswa::where('is_active', true)->orderBy('nama')->get();
        $jenisPembayaran = JenisPembayaran::orderBy('nama')->get();

        return view('pembayaran.edit', compact('pembayaran', 'siswa', 'jenisPembayaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris',
            'keterangan' => 'nullable|string',
        ]);

        // Map metode_pembayaran ke metode_bayar dengan format yang sesuai enum database
        $metodeMap = [
            'tunai' => 'Tunai',
            'transfer' => 'Transfer',
            'qris' => 'QRIS'
        ];
        $validated['metode_bayar'] = $metodeMap[$validated['metode_pembayaran']] ?? 'Tunai';
        unset($validated['metode_pembayaran']);

        $pembayaran->update($validated);

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->delete();

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dihapus.');
    }
}
