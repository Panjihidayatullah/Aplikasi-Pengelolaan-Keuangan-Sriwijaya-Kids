<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Siswa::with('kelas');

        // Filter by search (NIS or Nama)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nis', 'like', '%' . $search . '%')
                  ->orWhere('nama', 'like', '%' . $search . '%')
                  ->orWhere('nisn', 'like', '%' . $search . '%');
            });
        }

        // Filter by Kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter by Jenis Kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $siswa = $query->orderBy('nama')->paginate(15)->withQueryString();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        return view('students.index', compact('siswa', 'kelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('students.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'nis' => 'required|string|max:50|unique:siswa',
            'nama' => 'required|string|max:200',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'nama_ayah' => 'nullable|string|max:200',
            'telepon_ayah' => 'nullable|string|max:20',
            'nama_ibu' => 'nullable|string|max:200',
            'telepon_ibu' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        $validated['is_active'] = true;

        Siswa::create($validated);

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $siswa = Siswa::with('kelas')->findOrFail($id);
        return view('students.show', compact('siswa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $siswa = Siswa::findOrFail($id);
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('students.edit', compact('siswa', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'nis' => 'required|string|max:50|unique:siswa,nis,' . $id,
            'nama' => 'required|string|max:200',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'nama_ayah' => 'nullable|string|max:200',
            'telepon_ayah' => 'nullable|string|max:20',
            'nama_ibu' => 'nullable|string|max:200',
            'telepon_ibu' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $validated['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        $siswa->update($validated);

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $siswa = Siswa::findOrFail($id);
        
        // Delete photo if exists
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }
        
        $siswa->delete();

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
