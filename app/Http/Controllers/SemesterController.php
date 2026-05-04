<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index()
    {
        abort_unless(can_access('view semester'), 403);

        $semesters = Semester::with('tahunAjaran')
            ->orderByDesc('is_active')
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('akademik.semester.index', compact('semesters'));
    }

    public function create()
    {
        abort_unless(can_access('create semester'), 403);

        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();

        return view('akademik.semester.create', compact('tahunAjarans'));
    }

    public function store(Request $request)
    {
        abort_unless(can_access('create semester'), 403);

        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'nomor_semester' => 'required|integer|in:1,2',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tanggal_uts' => 'nullable|date|after_or_equal:tanggal_mulai|before_or_equal:tanggal_selesai',
            'tanggal_uas' => 'nullable|date|after_or_equal:tanggal_mulai|before_or_equal:tanggal_selesai',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        if ($validated['is_active']) {
            $this->deactivateOtherSemesters($validated['tahun_ajaran_id']);
        }

        Semester::create($validated);

        return redirect()->route('akademik.semester.index')->with('success', 'Semester berhasil ditambahkan');
    }

    public function show(Semester $semester)
    {
        abort_unless(can_access('view semester'), 403);

        $semester->load('tahunAjaran');

        return view('akademik.semester.show', compact('semester'));
    }

    public function edit(Semester $semester)
    {
        abort_unless(can_access('edit semester'), 403);

        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();

        return view('akademik.semester.edit', compact('semester', 'tahunAjarans'));
    }

    public function update(Request $request, Semester $semester)
    {
        abort_unless(can_access('edit semester'), 403);

        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'nomor_semester' => 'required|integer|in:1,2',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tanggal_uts' => 'nullable|date|after_or_equal:tanggal_mulai|before_or_equal:tanggal_selesai',
            'tanggal_uas' => 'nullable|date|after_or_equal:tanggal_mulai|before_or_equal:tanggal_selesai',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        if ($validated['is_active']) {
            $this->deactivateOtherSemesters($validated['tahun_ajaran_id'], $semester->id);
        }

        $semester->update($validated);

        return redirect()->route('akademik.semester.index')->with('success', 'Semester berhasil diperbarui');
    }

    public function destroy(Semester $semester)
    {
        abort_unless(can_access('delete semester'), 403);

        $semester->delete();

        return redirect()->route('akademik.semester.index')->with('success', 'Semester berhasil dihapus');
    }

    private function deactivateOtherSemesters(int $tahunAjaranId, ?int $exceptId = null): void
    {
        Semester::query()
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->update(['is_active' => false]);
    }
}
