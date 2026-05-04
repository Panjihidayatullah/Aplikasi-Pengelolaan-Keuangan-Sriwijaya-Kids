<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use Illuminate\Http\Request;

class KurikulumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kurikulum = Kurikulum::paginate(10);
        return view('akademik.kurikulum.index', compact('kurikulum'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('akademik.kurikulum.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|unique:kurikulum',
            'deskripsi' => 'nullable|string',
            'tahun_berlaku' => 'required|numeric|min:2000|max:'.date('Y'),
            'is_active' => 'boolean',
        ]);

        Kurikulum::create($validated);
        return redirect()->route('akademik.kurikulum.index')->with('success', 'Kurikulum berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kurikulum $kurikulum)
    {
        $tahunAjaran = $kurikulum->tahunAjaran()->paginate(10);
        return view('akademik.kurikulum.show', compact('kurikulum', 'tahunAjaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kurikulum $kurikulum)
    {
        return view('akademik.kurikulum.edit', compact('kurikulum'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kurikulum $kurikulum)
    {
        $validated = $request->validate([
            'nama' => 'required|string|unique:kurikulum,nama,'.$kurikulum->id,
            'deskripsi' => 'nullable|string',
            'tahun_berlaku' => 'required|numeric|min:2000|max:'.date('Y'),
            'is_active' => 'boolean',
        ]);

        $kurikulum->update($validated);
        return redirect()->route('akademik.kurikulum.index')->with('success', 'Kurikulum berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kurikulum $kurikulum)
    {
        $kurikulum->delete();
        return redirect()->route('akademik.kurikulum.index')->with('success', 'Kurikulum berhasil dihapus');
    }
}
