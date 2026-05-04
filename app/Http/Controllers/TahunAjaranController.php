<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Models\Kurikulum;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunAjaran = TahunAjaran::with('kurikulum')->paginate(10);
        return view('akademik.tahun-ajaran.index', compact('tahunAjaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kurikulum = Kurikulum::where('is_active', true)->get();
        return view('akademik.tahun-ajaran.create', compact('kurikulum'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kurikulum_id' => 'required|exists:kurikulum,id',
            'nama' => 'required|string',
            'tahun_mulai' => 'required|numeric|min:2000',
            'tahun_selesai' => 'required|numeric|min:2000|gt:tahun_mulai',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
        ]);

        TahunAjaran::create($validated);
        return redirect()->route('akademik.tahun-ajaran.index')->with('success', 'Tahun Ajaran berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->load('kurikulum', 'semester');
        return view('akademik.tahun-ajaran.show', compact('tahunAjaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TahunAjaran $tahunAjaran)
    {
        $kurikulum = Kurikulum::all();
        return view('akademik.tahun-ajaran.edit', compact('tahunAjaran', 'kurikulum'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $validated = $request->validate([
            'kurikulum_id' => 'required|exists:kurikulum,id',
            'nama' => 'required|string',
            'tahun_mulai' => 'required|numeric|min:2000',
            'tahun_selesai' => 'required|numeric|min:2000|gt:tahun_mulai',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
        ]);

        $tahunAjaran->update($validated);
        return redirect()->route('akademik.tahun-ajaran.index')->with('success', 'Tahun Ajaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->delete();
        return redirect()->route('akademik.tahun-ajaran.index')->with('success', 'Tahun Ajaran berhasil dihapus');
    }

    /**
     * Set as active
     */
    public function setActive(TahunAjaran $tahunAjaran)
    {
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        $tahunAjaran->update(['is_active' => true]);
        return redirect()->route('akademik.tahun-ajaran.index')->with('success', 'Tahun Ajaran diaktifkan');
    }

    /**
     * Set as inactive
     */
    public function setInactive(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->update(['is_active' => false]);

        return redirect()->route('akademik.tahun-ajaran.index')
            ->with('success', 'Tahun Ajaran dinonaktifkan');
    }
}
