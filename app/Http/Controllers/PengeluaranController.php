<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\JenisPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengeluaran::with(['jenis', 'user']);

        // Filter by search (Kode Transaksi or Keterangan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        // Filter by Tanggal Range
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal', '<=', $request->tanggal_akhir);
        }

        // Filter by Jenis Pengeluaran
        if ($request->filled('jenis_pengeluaran_id')) {
            $query->where('jenis_pengeluaran_id', $request->jenis_pengeluaran_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengeluaran = $query->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        $jenisPengeluaran = JenisPengeluaran::orderBy('nama')->get();

        // Statistics - dengan filter yang sama
        $statsQuery = Pengeluaran::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('tanggal_mulai')) {
            $statsQuery->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $statsQuery->where('tanggal', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('jenis_pengeluaran_id')) {
            $statsQuery->where('jenis_pengeluaran_id', $request->jenis_pengeluaran_id);
        }
        if ($request->filled('status')) {
            $statsQuery->where('status', $request->status);
        }

        $totalSemua = $statsQuery->sum('jumlah');
        $totalApproved = (clone $statsQuery)->where('status', 'Disetujui')->count();
        $totalPending = (clone $statsQuery)->where('status', 'Pending')->count();
        $totalRejected = (clone $statsQuery)->where('status', 'Ditolak')->count();

        return view('pengeluaran.index', compact(
            'pengeluaran', 
            'jenisPengeluaran',
            'totalSemua',
            'totalApproved',
            'totalPending',
            'totalRejected'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisPengeluaran = JenisPengeluaran::orderBy('nama')->get();

        return view('pengeluaran.create', compact('jenisPengeluaran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_pengeluaran_id' => 'required|exists:jenis_pengeluaran,id',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Generate kode transaksi unik
        $validated['kode_transaksi'] = 'OUT-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
        
        // Map deskripsi ke keterangan sesuai nama kolom di database
        $validated['keterangan'] = $validated['deskripsi'];
        unset($validated['deskripsi']);
        
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'Disetujui';

        if ($request->hasFile('bukti_file')) {
            $validated['bukti_file'] = $request->file('bukti_file')->store('pengeluaran', 'public');
        }

        Pengeluaran::create($validated);

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil dicatat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengeluaran = Pengeluaran::with(['jenis', 'user'])->findOrFail($id);

        return view('pengeluaran.show', compact('pengeluaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $jenisPengeluaran = JenisPengeluaran::orderBy('nama')->get();

        return view('pengeluaran.edit', compact('pengeluaran', 'jenisPengeluaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        $validated = $request->validate([
            'jenis_pengeluaran_id' => 'required|exists:jenis_pengeluaran,id',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Map deskripsi ke keterangan sesuai nama kolom di database
        $validated['keterangan'] = $validated['deskripsi'];
        unset($validated['deskripsi']);

        if ($request->hasFile('bukti_file')) {
            // Delete old file
            if ($pengeluaran->bukti_file) {
                Storage::disk('public')->delete($pengeluaran->bukti_file);
            }
            $validated['bukti_file'] = $request->file('bukti_file')->store('pengeluaran', 'public');
        }

        $pengeluaran->update($validated);

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        
        // Delete file if exists
        if ($pengeluaran->bukti_file) {
            Storage::disk('public')->delete($pengeluaran->bukti_file);
        }
        
        $pengeluaran->delete();

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
