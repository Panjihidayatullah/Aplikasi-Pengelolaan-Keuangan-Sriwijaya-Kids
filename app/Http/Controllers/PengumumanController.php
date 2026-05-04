<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pengumuman = Pengumuman::with('user')
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);
        return view('akademik.pengumuman.index', compact('pengumuman'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('akademik.pengumuman.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|in:ujian,libur,kegiatan,pengumuman',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'is_published' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();

        Pengumuman::create($validated);
        return redirect()->route('akademik.pengumuman.index')->with('success', 'Pengumuman berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengumuman $pengumuman)
    {
        return view('akademik.pengumuman.show', compact('pengumuman'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pengumuman $pengumuman)
    {
        return view('akademik.pengumuman.edit', compact('pengumuman'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|in:ujian,libur,kegiatan,pengumuman',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'is_published' => 'boolean',
        ]);

        $pengumuman->update($validated);
        return redirect()->route('akademik.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();
        return redirect()->route('akademik.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus');
    }

    /**
     * View active announcements for public
     */
    public function public()
    {
        $pengumuman = Pengumuman::active()
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);
        return view('akademik.pengumuman.public', compact('pengumuman'));
    }
}
