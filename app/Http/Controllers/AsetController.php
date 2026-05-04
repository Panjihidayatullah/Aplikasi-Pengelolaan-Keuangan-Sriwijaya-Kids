<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use Illuminate\Http\Request;

class AsetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Aset::query();

        // Filter by search (Nama Aset or Lokasi)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('lokasi', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        // Filter by Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', 'ILIKE', $request->kategori);
        }

        // Filter by Kondisi
        if ($request->filled('kondisi')) {
            $query->where('kondisi', 'ILIKE', $request->kondisi);
        }

        // Filter by Tanggal Range
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal_perolehan', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal_perolehan', '<=', $request->tanggal_akhir);
        }

        // Calculate statistics based on current filters (but before pagination)
        $statsQuery = clone $query;
        $stats = [
            'total' => $statsQuery->count(),
            'baik' => (clone $statsQuery)->where('kondisi', 'ILIKE', 'Baik')->count(),
            'rusak_ringan' => (clone $statsQuery)->where('kondisi', 'ILIKE', 'Rusak Ringan')->count(),
            'rusak_berat' => (clone $statsQuery)->where('kondisi', 'ILIKE', 'Rusak Berat')->count(),
        ];

        $aset = $query->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('aset.index', compact('aset', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('aset.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:200',
            'kategori' => 'required|in:elektronik,furniture,kendaraan,gedung,lainnya',
            'tanggal_perolehan' => 'required|date',
            'harga_perolehan' => 'required|numeric|min:0',
            'kondisi' => 'required|in:baik,rusak ringan,rusak berat',
            'lokasi' => 'nullable|string|max:200',
            'keterangan' => 'nullable|string',
        ]);

        // Map kondisi & kategori ke format yang konsisten sesuai data yang ada di database
        $kondisiMap = [
            'baik' => 'Baik',
            'rusak ringan' => 'Rusak Ringan',
            'rusak berat' => 'Rusak Berat'
        ];
        $kategoriMap = [
            'elektronik' => 'Elektronik',
            'furniture' => 'Furniture',
            'kendaraan' => 'Kendaraan',
            'gedung' => 'Bangunan',
            'lainnya' => 'Lainnya'
        ];
        
        $validated['kondisi'] = $kondisiMap[strtolower($validated['kondisi'])] ?? $validated['kondisi'];
        $validated['kategori'] = $kategoriMap[strtolower($validated['kategori'])] ?? $validated['kategori'];

        Aset::create($validated);

        return redirect()->route('aset.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $aset = Aset::findOrFail($id);

        return view('aset.show', compact('aset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $aset = Aset::findOrFail($id);

        return view('aset.edit', compact('aset'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $aset = Aset::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:200',
            'kategori' => 'required|in:elektronik,furniture,kendaraan,gedung,lainnya',
            'tanggal_perolehan' => 'required|date',
            'harga_perolehan' => 'required|numeric|min:0',
            'kondisi' => 'required|in:baik,rusak ringan,rusak berat',
            'lokasi' => 'nullable|string|max:200',
            'keterangan' => 'nullable|string',
        ]);

        // Map kondisi & kategori ke format yang konsisten sesuai data yang ada di database
        $kondisiMap = [
            'baik' => 'Baik',
            'rusak ringan' => 'Rusak Ringan',
            'rusak berat' => 'Rusak Berat'
        ];
        $kategoriMap = [
            'elektronik' => 'Elektronik',
            'furniture' => 'Furniture',
            'kendaraan' => 'Kendaraan',
            'gedung' => 'Bangunan',
            'lainnya' => 'Lainnya'
        ];
        
        $validated['kondisi'] = $kondisiMap[strtolower($validated['kondisi'])] ?? $validated['kondisi'];
        $validated['kategori'] = $kategoriMap[strtolower($validated['kategori'])] ?? $validated['kategori'];

        $aset->update($validated);

        return redirect()->route('aset.index')->with('success', 'Aset berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $aset = Aset::findOrFail($id);
        $aset->delete();

        return redirect()->route('aset.index')->with('success', 'Aset berhasil dihapus.');
    }
}
