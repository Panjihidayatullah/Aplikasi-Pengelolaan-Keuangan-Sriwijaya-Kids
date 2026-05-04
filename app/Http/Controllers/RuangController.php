<?php

namespace App\Http\Controllers;

use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RuangController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($this->canManageRuang(), 403);

        $query = Ruang::query()->withCount('jadwalPelajaran');

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('kode_ruang', 'ilike', '%' . $keyword . '%')
                    ->orWhere('nama_ruang', 'ilike', '%' . $keyword . '%')
                    ->orWhere('lokasi', 'ilike', '%' . $keyword . '%')
                    ->orWhere('keterangan', 'ilike', '%' . $keyword . '%');
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            }

            if ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $ruangs = $query
            ->orderBy('nama_ruang')
            ->paginate(10)
            ->withQueryString();

        return view('akademik.ruang.index', [
            'ruangs' => $ruangs,
        ]);
    }

    public function create()
    {
        abort_unless($this->canManageRuang(), 403);

        return view('akademik.ruang.create');
    }

    public function store(Request $request)
    {
        abort_unless($this->canManageRuang(), 403);

        $validated = $this->validateInput($request);

        Ruang::create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('akademik.ruang.index')
            ->with('success', 'Data ruang berhasil ditambahkan.');
    }

    public function edit(Ruang $ruang)
    {
        abort_unless($this->canManageRuang(), 403);

        return view('akademik.ruang.edit', [
            'ruang' => $ruang,
        ]);
    }

    public function update(Request $request, Ruang $ruang)
    {
        abort_unless($this->canManageRuang(), 403);

        $validated = $this->validateInput($request, $ruang->id);

        $ruang->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('akademik.ruang.index')
            ->with('success', 'Data ruang berhasil diperbarui.');
    }

    public function destroy(Ruang $ruang)
    {
        abort_unless($this->canManageRuang(), 403);

        if ($ruang->jadwalPelajaran()->exists()) {
            return redirect()->route('akademik.ruang.index')
                ->withErrors(['ruang' => 'Ruang tidak bisa dihapus karena masih dipakai pada jadwal pelajaran.']);
        }

        $ruang->delete();

        return redirect()->route('akademik.ruang.index')
            ->with('success', 'Data ruang berhasil dihapus.');
    }

    private function validateInput(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'kode_ruang' => [
                'required',
                'string',
                'max:30',
                Rule::unique('ruang', 'kode_ruang')
                    ->ignore($ignoreId)
                    ->whereNull('deleted_at'),
            ],
            'nama_ruang' => 'required|string|max:100',
            'lokasi' => 'nullable|string|max:100',
            'kapasitas' => 'nullable|integer|min:1|max:2000',
            'keterangan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
    }

    private function canManageRuang(): bool
    {
        return is_admin();
    }
}
