<?php

namespace App\Http\Controllers;

use App\Models\Ujian;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;

class UjianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless($this->canViewUjian(), 403);

        $ujian = Ujian::with('mataPelajaran', 'kelas', 'semester')
            ->orderBy('tanggal_ujian', 'asc')
            ->paginate(10);
        
        return view('akademik.ujian.index', compact('ujian'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless($this->canManageUjian(), 403);

        $mataPelajarans = MataPelajaran::dropdownOptions();
        $kelases = Kelas::orderBy('nama_kelas')->get();
        $semesters = Semester::with('tahunAjaran')->orderBy('nomor_semester')->get();
        $ruangs = \App\Models\Ruang::where('is_active', true)->orderBy('nama_ruang')->get();
        
        return view('akademik.ujian.create', compact('mataPelajarans', 'kelases', 'semesters', 'ruangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless($this->canManageUjian(), 403);

        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'semester_id' => 'required|exists:semester,id',
            'jenis_ujian' => 'required|in:UTS,UAS,Quiz',
            'tanggal_ujian' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang' => 'required|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        // Combine date and time for time fields
        $validated['jam_mulai'] = date('H:i:s', strtotime($validated['jam_mulai']));
        $validated['jam_selesai'] = date('H:i:s', strtotime($validated['jam_selesai']));

        Ujian::create($validated);
        
        return redirect()->route('akademik.ujian.index')
            ->with('success', 'Jadwal ujian berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ujian $ujian)
    {
        abort_unless($this->canViewUjian(), 403);

        $ujian->load('mataPelajaran', 'kelas', 'semester.tahunAjaran');

        return view('akademik.ujian.show', compact('ujian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ujian $ujian)
    {
        abort_unless($this->canManageUjian(), 403);

        $mataPelajarans = MataPelajaran::dropdownOptions();
        $kelases = Kelas::orderBy('nama_kelas')->get();
        $semesters = Semester::with('tahunAjaran')->orderBy('nomor_semester')->get();
        $ruangs = \App\Models\Ruang::where('is_active', true)->orderBy('nama_ruang')->get();

        return view('akademik.ujian.edit', compact('ujian', 'mataPelajarans', 'kelases', 'semesters', 'ruangs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ujian $ujian)
    {
        abort_unless($this->canManageUjian(), 403);

        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'semester_id' => 'required|exists:semester,id',
            'jenis_ujian' => 'required|in:UTS,UAS,Quiz',
            'tanggal_ujian' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang' => 'required|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        // Combine date and time for time fields
        $validated['jam_mulai'] = date('H:i:s', strtotime($validated['jam_mulai']));
        $validated['jam_selesai'] = date('H:i:s', strtotime($validated['jam_selesai']));

        $ujian->update($validated);
        
        return redirect()->route('akademik.ujian.index')
            ->with('success', 'Jadwal ujian berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ujian $ujian)
    {
        abort_unless($this->canManageUjian(), 403);

        $ujian->delete();
        
        return redirect()->route('akademik.ujian.index')
            ->with('success', 'Jadwal ujian berhasil dihapus');
    }

    private function canViewUjian(): bool
    {
        return can_access('view ujian')
            || is_admin()
            || auth()->user()?->hasRole('Guru')
            || is_siswa();
    }

    private function canManageUjian(): bool
    {
        return can_access('create ujian')
            || can_access('edit ujian')
            || can_access('delete ujian')
            || is_admin()
            || auth()->user()?->hasRole('Guru');
    }
}
