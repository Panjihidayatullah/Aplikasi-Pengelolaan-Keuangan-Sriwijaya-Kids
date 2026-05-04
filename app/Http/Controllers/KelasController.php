<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kelas::withCount('siswa');

        // Filter by search (Nama Kelas or Wali Kelas)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', '%' . $search . '%')
                  ->orWhere('wali_kelas', 'like', '%' . $search . '%');
            });
        }

        // Filter by Tingkat
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        $kelas = $query->orderByTingkat()
            ->orderBy('nama_kelas')
            ->paginate(10)
            ->withQueryString();

        $tingkatOptions = Kelas::query()
            ->select('tingkat')
            ->whereNotNull('tingkat')
            ->where('tingkat', '!=', '')
            ->orderByTingkat()
            ->pluck('tingkat')
            ->map(fn($item) => (int) $item)
            ->filter(fn($item) => $item > 0)
            ->unique()
            ->values();

        return view('kelas.index', compact('kelas', 'tingkatOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $occupiedTingkat = $this->getOccupiedTingkat();

        return view('kelas.create', compact('occupiedTingkat'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tingkat' => [
                'required',
                'integer',
                'min:1',
                'max:12',
                Rule::unique('kelas', 'tingkat')->whereNull('deleted_at'),
            ],
            'wali_kelas' => 'nullable|string|max:100',
            'is_tingkat_akhir' => 'nullable|boolean',
        ], [
            'tingkat.unique' => 'Tingkat ini sudah dipakai oleh kelas lain. Hapus kelas pada tingkat tersebut terlebih dahulu jika ingin digunakan.',
        ]);

        $payload = [
            'nama_kelas' => trim((string) $validated['nama_kelas']),
            'wali_kelas' => isset($validated['wali_kelas']) ? trim((string) $validated['wali_kelas']) : null,
            'is_tingkat_akhir' => $request->boolean('is_tingkat_akhir', false),
        ];

        $payload['tingkat'] = (int) $validated['tingkat'];

        Kelas::create($payload);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kelas = Kelas::query()->withCount('siswa')->findOrFail($id);

        $query = Siswa::query()
            ->where('kelas_id', $kelas->id)
            ->orderBy('nama');

        if (request()->filled('search')) {
            $search = trim((string) request('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', '%' . $search . '%')
                    ->orWhere('nama', 'like', '%' . $search . '%');
            });
        }

        if (request()->filled('status')) {
            $query->where('is_active', request('status') === 'aktif');
        }

        $siswas = $query->paginate(10, ['*'], 'siswa_page')->withQueryString();

        $candidateQuery = Siswa::query()
            ->with('kelas')
            ->orderBy('nama');

        if (request()->filled('candidate_search')) {
            $candidateSearch = trim((string) request('candidate_search'));
            $candidateQuery->where(function ($q) use ($candidateSearch) {
                $q->where('nis', 'like', '%' . $candidateSearch . '%')
                    ->orWhere('nama', 'like', '%' . $candidateSearch . '%');
            });
        }

        $kandidatSiswas = $candidateQuery->paginate(10, ['*'], 'candidate_page')->withQueryString();

        $totalSiswa = Siswa::query()->where('kelas_id', $kelas->id)->count();
        $totalAktif = Siswa::query()->where('kelas_id', $kelas->id)->where('is_active', true)->count();
        $totalTidakAktif = max(0, $totalSiswa - $totalAktif);

        return view('kelas.show', compact('kelas', 'siswas', 'kandidatSiswas', 'totalSiswa', 'totalAktif', 'totalTidakAktif'));
    }

    public function storeSiswa(Request $request, string $kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);

        $validated = $request->validate([
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'required|integer|exists:siswa,id',
        ]);

        $selectedIds = collect($validated['siswa_ids'])->map(fn ($id) => (int) $id)->unique()->values();

        $selectedSiswas = Siswa::query()
            ->with('kelas')
            ->whereIn('id', $selectedIds)
            ->get();

        $blocked = [];
        $toUpdateIds = [];

        foreach ($selectedSiswas as $siswa) {
            $hasValidKelas = !empty($siswa->kelas_id) && $siswa->kelas !== null;

            if ($hasValidKelas && (int) $siswa->kelas_id === (int) $kelas->id) {
                continue;
            }

            if ($hasValidKelas && (int) $siswa->kelas_id !== (int) $kelas->id) {
                $blocked[] = $siswa->nama;
            } else {
                $toUpdateIds[] = $siswa->id;
            }
        }

        if (!empty($blocked)) {
            return back()->withErrors([
                'siswa_ids' => 'Beberapa siswa masih terdaftar di kelas lain: ' . implode(', ', $blocked) . '. Keluarkan/pindahkan dulu dari kelas asal.',
            ])->withInput();
        }

        $updated = count($toUpdateIds);
        if ($updated > 0) {
            Siswa::whereIn('id', $toUpdateIds)->update(['kelas_id' => $kelas->id]);
        }

        return redirect()
            ->route('kelas.show', [
                $kelas->id,
                'search' => $request->input('search'),
                'status' => $request->input('status'),
                'candidate_search' => $request->input('candidate_search'),
            ])
            ->with('success', $updated > 0
                ? 'Siswa terpilih berhasil dimasukkan ke rombel kelas ini.'
                : 'Tidak ada perubahan data rombel.');
    }

    public function transferSiswa(Request $request, string $kelasId, string $siswaId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $siswa = Siswa::with('kelas')->findOrFail($siswaId);

        if ((int) $siswa->kelas_id === (int) $kelas->id) {
            return redirect()->route('kelas.show', [
                $kelas->id,
                'search' => $request->input('search'),
                'status' => $request->input('status'),
                'candidate_search' => $request->input('candidate_search'),
            ])
                ->with('success', 'Siswa sudah berada di kelas ini.');
        }

        $asal = $siswa->kelas->nama_kelas ?? 'kelas asal';

        $siswa->update([
            'kelas_id' => null,
        ]);

        return redirect()->route('kelas.show', [
            $kelas->id,
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'candidate_search' => $request->input('candidate_search'),
        ])
            ->with('success', 'Siswa berhasil dikeluarkan dari ' . $asal . '. Silakan ceklis siswa tersebut untuk menambahkannya ke rombel ini.');
    }

    public function updateSiswa(Request $request, string $kelasId, string $siswaId)
    {
        $kelas = Kelas::findOrFail($kelasId);

        $siswa = Siswa::query()
            ->where('kelas_id', $kelas->id)
            ->findOrFail($siswaId);

        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:50', Rule::unique('siswa', 'nis')->ignore($siswa->id)],
            'nama' => 'required|string|max:200',
            'jenis_kelamin' => 'required|in:L,P',
            'is_active' => 'nullable|boolean',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        $siswa->update([
            'kelas_id' => $kelas->id,
            'nis' => trim((string) $validated['nis']),
            'nama' => trim((string) $validated['nama']),
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'is_active' => $request->boolean('is_active', false),
            'telepon' => $validated['telepon'] ?? null,
            'email' => isset($validated['email']) ? mb_strtolower(trim((string) $validated['email'])) : null,
        ]);

        return redirect()
            ->route('kelas.show', $kelas->id)
            ->with('success', 'Data siswa pada rombel berhasil diperbarui.');
    }

    public function destroySiswa(string $kelasId, string $siswaId)
    {
        $kelas = Kelas::findOrFail($kelasId);

        $siswa = Siswa::query()
            ->where('kelas_id', $kelas->id)
            ->findOrFail($siswaId);

        $siswa->update(['kelas_id' => null]);

        return redirect()
            ->route('kelas.show', $kelas->id)
            ->with('success', 'Siswa berhasil dikeluarkan dari rombel kelas ini.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $kelas = Kelas::findOrFail($id);
        $occupiedTingkat = $this->getOccupiedTingkat($kelas->id);

        return view('kelas.edit', compact('kelas', 'occupiedTingkat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kelas = Kelas::findOrFail($id);

        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tingkat' => [
                'required',
                'integer',
                'min:1',
                'max:12',
                Rule::unique('kelas', 'tingkat')
                    ->whereNull('deleted_at')
                    ->ignore($kelas->id),
            ],
            'wali_kelas' => 'nullable|string|max:100',
            'is_tingkat_akhir' => 'nullable|boolean',
        ], [
            'tingkat.unique' => 'Tingkat ini sudah dipakai oleh kelas lain. Hapus kelas pada tingkat tersebut terlebih dahulu jika ingin digunakan.',
        ]);

        $payload = [
            'nama_kelas' => trim((string) $validated['nama_kelas']),
            'wali_kelas' => isset($validated['wali_kelas']) ? trim((string) $validated['wali_kelas']) : null,
            'is_tingkat_akhir' => $request->boolean('is_tingkat_akhir', false),
        ];

        $payload['tingkat'] = (int) $validated['tingkat'];

        $kelas->update($payload);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    private function syncTingkatFromNamaKelas(): void
    {
        $allKelas = Kelas::query()->select('id', 'nama_kelas', 'tingkat')->get();

        foreach ($allKelas as $kelas) {
            $inferred = Kelas::inferTingkatFromNama($kelas->nama_kelas);
            if ($inferred === null) {
                continue;
            }

            if ((int) $kelas->tingkat <= 0) {
                Kelas::query()->whereKey($kelas->id)->update(['tingkat' => $inferred]);
            }
        }
    }

    private function getOccupiedTingkat(?int $exceptKelasId = null)
    {
        $query = Kelas::query()
            ->select('tingkat')
            ->whereNotNull('tingkat')
            ->where('tingkat', '>', 0);

        if ($exceptKelasId) {
            $query->where('id', '!=', $exceptKelasId);
        }

        return $query
            ->pluck('tingkat')
            ->map(fn($item) => (int) $item)
            ->filter(fn($item) => $item > 0)
            ->unique()
            ->values();
    }
}
