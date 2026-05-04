<?php

namespace App\Http\Controllers;

use App\Models\KenaikanKelas;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\TranskripsNilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KenaikanKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->syncKelasTingkatFromNama();

        $query = KenaikanKelas::with('siswa', 'kelasSekarang', 'kelasTujuan', 'tahunAjaran');

        $guruKelasIds = $this->getGuruKelasIds();
        if ($guruKelasIds !== null) {
            $query->whereIn('kelas_sekarang_id', $guruKelasIds);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->whereHas('siswa', function ($sq) use ($search) {
                    $sq->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('nis', 'like', '%' . $search . '%');
                })->orWhereHas('kelasSekarang', function ($kq) use ($search) {
                    $kq->where('nama_kelas', 'like', '%' . $search . '%');
                })->orWhereHas('kelasTujuan', function ($kq) use ($search) {
                    $kq->where('nama_kelas', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('tingkat')) {
            $tingkat = (int) $request->input('tingkat');
            $query->whereHas('kelasSekarang', function ($kq) use ($tingkat) {
                $kq->where('tingkat', $tingkat);
            });
        }

        if ($request->filled('rombel')) {
            $query->where('kelas_sekarang_id', (int) $request->input('rombel'));
        }

        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', (int) $request->input('tahun_ajaran_id'));
        }

        $kenaikans = $query
            ->orderBy('tahun_ajaran_id', 'desc')
            ->orderBy('status', 'asc')
            ->paginate(10)
            ->withQueryString();

        $statsQuery = clone $query;
        $totalNaik = (clone $statsQuery)->where('status', 'naik')->count();
        $totalTidakNaik = (clone $statsQuery)->where('status', 'tidak_naik')->count();
        $totalLulus = (clone $statsQuery)->where('status', 'lulus')->count();

        $tingkatOptions = Kelas::query()
            ->whereNotNull('tingkat')
            ->where('tingkat', '>', 0)
            ->select('tingkat')
            ->distinct()
            ->orderBy('tingkat')
            ->pluck('tingkat');

        $rombelQuery = Kelas::query()
            ->select('id', 'nama_kelas', 'tingkat')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas');
            
        if ($guruKelasIds !== null) {
            $rombelQuery->whereIn('id', $guruKelasIds);
        }
        $rombelOptions = $rombelQuery->get();

        $tahunAjarans = TahunAjaran::query()->orderBy('nama', 'desc')->get();

        return view('akademik.kenaikan-kelas.index', compact(
            'kenaikans',
            'totalNaik',
            'totalTidakNaik',
            'totalLulus',
            'tingkatOptions',
            'rombelOptions',
            'tahunAjarans'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->abortIfKepalaSekolahReadOnly();
        $this->syncKelasTingkatFromNama();

        $guruKelasIds = $this->getGuruKelasIds();
        $siswasQuery = Siswa::with('kelas')->where('is_active', true)->orderBy('nama');
        if ($guruKelasIds !== null) {
            $siswasQuery->whereIn('kelas_id', $guruKelasIds);
        }
        $siswas = $siswasQuery->get();
        $kelases = $this->orderedKelasQuery()->get();
        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();

        return view('akademik.kenaikan-kelas.create', compact('siswas', 'kelases', 'tahunAjarans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->abortIfKepalaSekolahReadOnly();
        $this->syncKelasTingkatFromNama();

        
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'kelas_sekarang_id' => 'required|exists:kelas,id',
            'kelas_tujuan_id' => 'nullable|exists:kelas,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'status' => 'required|in:naik,tidak_naik,lulus',
            'rata_rata_nilai' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $kelasSekarang = Kelas::findOrFail((int) $validated['kelas_sekarang_id']);
        
        $guruKelasIds = $this->getGuruKelasIds();
        if ($guruKelasIds !== null && !in_array($kelasSekarang->id, $guruKelasIds, true)) {
            return back()->withInput()->withErrors([
                'kelas_sekarang_id' => 'Anda tidak memiliki akses untuk mengatur kenaikan dari kelas ini.',
            ]);
        }
        $resolvedKelasTujuanId = $this->resolveKelasTujuanId(
            $kelasSekarang,
            (string) $validated['status'],
            $validated['kelas_tujuan_id'] ?? null
        );

        if ($validated['status'] === 'naik' && !$resolvedKelasTujuanId) {
            return back()->withInput()->withErrors([
                'kelas_tujuan_id' => 'Kelas tujuan tidak ditemukan. Pastikan kelas tingkat berikutnya tersedia.',
            ]);
        }

        if ($validated['status'] === 'tidak_naik' && !$resolvedKelasTujuanId) {
            return back()->withInput()->withErrors([
                'kelas_tujuan_id' => 'Kelas tujuan tidak ditemukan. Pastikan kelas tingkat sebelumnya tersedia.',
            ]);
        }

        if ($validated['status'] === 'naik' && (int) $resolvedKelasTujuanId === (int) $validated['kelas_sekarang_id']) {
            return back()->withInput()->withErrors([
                'kelas_tujuan_id' => 'Kelas tujuan kenaikan harus lebih tinggi dari kelas saat ini.',
            ]);
        }

        try {
            $this->validateKelasTujuanByStatus(
                $kelasSekarang,
                (string) $validated['status'],
                $resolvedKelasTujuanId
            );
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        $validated['kelas_tujuan_id'] = $resolvedKelasTujuanId;
        $validated['tanggal_penetapan'] = now()->toDateString();

        KenaikanKelas::create($validated);
        
        return redirect()->route('akademik.kenaikan-kelas.index')
            ->with('success', 'Data kenaikan kelas berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(KenaikanKelas $kenaikanKelas)
    {
        $kenaikanKelas->load('siswa.kelas', 'kelasSekarang', 'kelasTujuan', 'tahunAjaran');

        return view('akademik.kenaikan-kelas.show', compact('kenaikanKelas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KenaikanKelas $kenaikanKelas)
    {
        $this->abortIfKepalaSekolahReadOnly();
        $this->syncKelasTingkatFromNama();

        $guruKelasIds = $this->getGuruKelasIds();
        if ($guruKelasIds !== null && !in_array((int) $kenaikanKelas->kelas_sekarang_id, $guruKelasIds, true)) {
            return redirect()->route('akademik.kenaikan-kelas.index')->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        $siswasQuery = Siswa::with('kelas')->where('is_active', true)->orderBy('nama');
        if ($guruKelasIds !== null) {
            $siswasQuery->whereIn('kelas_id', $guruKelasIds);
        }
        $siswas = $siswasQuery->get();
        $kelases = $this->orderedKelasQuery()->get();
        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();

        return view('akademik.kenaikan-kelas.edit', compact('kenaikanKelas', 'siswas', 'kelases', 'tahunAjarans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KenaikanKelas $kenaikanKelas)
    {
        $this->abortIfKepalaSekolahReadOnly();
        $this->syncKelasTingkatFromNama();

        
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'kelas_sekarang_id' => 'required|exists:kelas,id',
            'kelas_tujuan_id' => 'nullable|exists:kelas,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'status' => 'required|in:naik,tidak_naik,lulus',
            'rata_rata_nilai' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $kelasSekarang = Kelas::findOrFail((int) $validated['kelas_sekarang_id']);
        
        $guruKelasIds = $this->getGuruKelasIds();
        if ($guruKelasIds !== null && (!in_array($kelasSekarang->id, $guruKelasIds, true) || !in_array((int) $kenaikanKelas->kelas_sekarang_id, $guruKelasIds, true))) {
            return back()->withInput()->withErrors([
                'kelas_sekarang_id' => 'Anda tidak memiliki akses untuk mengatur kenaikan dari kelas ini.',
            ]);
        }
        $resolvedKelasTujuanId = $this->resolveKelasTujuanId(
            $kelasSekarang,
            (string) $validated['status'],
            $validated['kelas_tujuan_id'] ?? null
        );

        if ($validated['status'] === 'naik' && !$resolvedKelasTujuanId) {
            return back()->withInput()->withErrors([
                'kelas_tujuan_id' => 'Kelas tujuan tidak ditemukan. Pastikan kelas tingkat berikutnya tersedia.',
            ]);
        }

        if ($validated['status'] === 'tidak_naik' && !$resolvedKelasTujuanId) {
            return back()->withInput()->withErrors([
                'kelas_tujuan_id' => 'Kelas tujuan tidak ditemukan. Pastikan kelas tingkat sebelumnya tersedia.',
            ]);
        }

        if ($validated['status'] === 'naik' && (int) $resolvedKelasTujuanId === (int) $validated['kelas_sekarang_id']) {
            return back()->withInput()->withErrors([
                'kelas_tujuan_id' => 'Kelas tujuan kenaikan harus lebih tinggi dari kelas saat ini.',
            ]);
        }

        try {
            $this->validateKelasTujuanByStatus(
                $kelasSekarang,
                (string) $validated['status'],
                $resolvedKelasTujuanId
            );
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        $validated['kelas_tujuan_id'] = $resolvedKelasTujuanId;
        $validated['tanggal_penetapan'] = now()->toDateString();

        $kenaikanKelas->update($validated);
        
        return redirect()->route('akademik.kenaikan-kelas.index')
            ->with('success', 'Data kenaikan kelas berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KenaikanKelas $kenaikanKelas)
    {
        $this->abortIfKepalaSekolahReadOnly();
        
        $guruKelasIds = $this->getGuruKelasIds();
        if ($guruKelasIds !== null && !in_array((int) $kenaikanKelas->kelas_sekarang_id, $guruKelasIds, true)) {
            return redirect()->route('akademik.kenaikan-kelas.index')->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $kenaikanKelas->delete();
        
        return redirect()->route('akademik.kenaikan-kelas.index')
            ->with('success', 'Data kenaikan kelas berhasil dihapus');
    }

    /**
     * Approve process
     */
    public function approve(Request $request, KenaikanKelas $kenaikanKelas)
    {
        $this->abortIfKepalaSekolahReadOnly();
        $this->syncKelasTingkatFromNama();

        $guruKelasIds = $this->getGuruKelasIds();
        if ($guruKelasIds !== null && !in_array((int) $kenaikanKelas->kelas_sekarang_id, $guruKelasIds, true)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menyetujui kenaikan kelas ini.');
        }

        $kenaikanKelas->loadMissing('siswa', 'kelasSekarang', 'kelasTujuan');

        if (!$kenaikanKelas->siswa) {
            throw ValidationException::withMessages([
                'siswa_id' => 'Data siswa untuk proses kenaikan tidak ditemukan.',
            ]);
        }

        DB::transaction(function () use ($kenaikanKelas) {
            $siswa = $kenaikanKelas->siswa;
            $status = (string) $kenaikanKelas->status;

            if ($status === 'naik') {
                $kelasTujuan = $kenaikanKelas->kelasTujuan;
                if (!$kelasTujuan) {
                    $kelasTujuan = $this->findNextKelas($kenaikanKelas->kelasSekarang);
                }

                if (!$kelasTujuan) {
                    throw ValidationException::withMessages([
                        'kelas_tujuan_id' => 'Tidak ada kelas tingkat berikutnya untuk kenaikan siswa ini.',
                    ]);
                }

                $siswa->update([
                    'kelas_id' => $kelasTujuan->id,
                    'is_active' => true,
                ]);

                $kenaikanKelas->update([
                    'kelas_tujuan_id' => $kelasTujuan->id,
                    'tanggal_penetapan' => now()->toDateString(),
                    'is_applied' => true,
                ]);

                return;
            }

            if ($status === 'tidak_naik') {
                $kelasTujuan = $kenaikanKelas->kelasTujuan;
                if (!$kelasTujuan) {
                    $kelasTujuan = $kenaikanKelas->kelasSekarang;
                }

                if (!$kelasTujuan) {
                    throw ValidationException::withMessages([
                        'kelas_tujuan_id' => 'Tidak ada kelas tingkat sebelumnya untuk status tidak naik siswa ini.',
                    ]);
                }

                $siswa->update([
                    'kelas_id' => $kelasTujuan->id,
                    'is_active' => true,
                ]);

                $kenaikanKelas->update([
                    'kelas_tujuan_id' => $kelasTujuan->id,
                    'tanggal_penetapan' => now()->toDateString(),
                    'is_applied' => true,
                ]);

                return;
            }

            // Status lulus: siswa dinonaktifkan dari daftar siswa aktif.
            $siswa->update(['is_active' => false]);
            $kenaikanKelas->update([
                'kelas_tujuan_id' => null,
                'tanggal_penetapan' => now()->toDateString(),
                'is_applied' => true,
            ]);
        });

        return back()->with('success', 'Proses kenaikan kelas berhasil diterapkan ke data siswa.');
    }

    /**
     * Bulk approve process
     */
    public function bulkApprove(Request $request)
    {
        $this->abortIfKepalaSekolahReadOnly();

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:kenaikan_kelas,id',
        ]);

        $query = KenaikanKelas::with('siswa', 'kelasSekarang', 'kelasTujuan')
            ->whereIn('id', $validated['ids']);
            
        $guruKelasIds = $this->getGuruKelasIds();
        if ($guruKelasIds !== null) {
            $query->whereIn('kelas_sekarang_id', $guruKelasIds);
        }
        
        $kenaikans = $query->get();

        DB::transaction(function () use ($kenaikans) {
            foreach ($kenaikans as $kenaikanKelas) {
                $siswa = $kenaikanKelas->siswa;
                if (!$siswa) continue;

                $status = (string) $kenaikanKelas->status;

                if ($status === 'naik') {
                    $kelasTujuan = $kenaikanKelas->kelasTujuan;
                    if (!$kelasTujuan) {
                        $kelasTujuan = $this->findNextKelas($kenaikanKelas->kelasSekarang);
                    }

                    if (!$kelasTujuan) {
                        throw ValidationException::withMessages([
                            'bulk_approve' => "Tidak ada kelas tingkat berikutnya untuk kenaikan siswa {$siswa->nama}.",
                        ]);
                    }

                    $siswa->update([
                        'kelas_id' => $kelasTujuan->id,
                        'is_active' => true,
                    ]);

                    $kenaikanKelas->update([
                        'kelas_tujuan_id' => $kelasTujuan->id,
                        'tanggal_penetapan' => now()->toDateString(),
                        'is_applied' => true,
                    ]);
                } elseif ($status === 'tidak_naik') {
                    $kelasTujuan = $kenaikanKelas->kelasTujuan;
                    if (!$kelasTujuan) {
                        $kelasTujuan = $kenaikanKelas->kelasSekarang;
                    }

                    if (!$kelasTujuan) {
                        throw ValidationException::withMessages([
                            'bulk_approve' => "Tidak ada kelas tingkat sebelumnya untuk status tidak naik siswa {$siswa->nama}.",
                        ]);
                    }

                    $siswa->update([
                        'kelas_id' => $kelasTujuan->id,
                        'is_active' => true,
                    ]);

                    $kenaikanKelas->update([
                        'kelas_tujuan_id' => $kelasTujuan->id,
                        'tanggal_penetapan' => now()->toDateString(),
                        'is_applied' => true,
                    ]);
                } elseif ($status === 'lulus') {
                    $siswa->update(['is_active' => false]);
                    $kenaikanKelas->update([
                        'kelas_tujuan_id' => null,
                        'tanggal_penetapan' => now()->toDateString(),
                        'is_applied' => true,
                    ]);
                }
            }
        });

        return back()->with('success', 'Proses kenaikan kelas massal berhasil diterapkan ke data siswa.');
    }

    /**
     * Halaman proses massal kenaikan kelas berdasarkan rombel.
     */
    public function prosesRombel(Request $request)
    {
        $this->abortIfKepalaSekolahReadOnly();
        $this->syncKelasTingkatFromNama();

        $tahunAjarans = TahunAjaran::query()->orderBy('nama', 'desc')->get();
        $kelases = $this->orderedKelasQuery()->get();

        $selectedTahunAjaranId = $request->integer('tahun_ajaran_id')
            ?: (int) (TahunAjaran::query()->where('is_active', true)->value('id') ?: 0)
            ?: (int) ($tahunAjarans->first()->id ?? 0);

        $selectedKelasId = $request->integer('kelas_id');
        $selectedKelas = $selectedKelasId ? Kelas::query()->find($selectedKelasId) : null;

        $siswaRows = collect();
        $isLastGrade = false;
        if ($selectedKelas && $selectedTahunAjaranId > 0) {
            $isLastGrade = $this->isLastTingkat($selectedKelas);
            $siswas = Siswa::query()
                ->with('kartuPelajar')
                ->where('is_active', true)
                ->where('kelas_id', $selectedKelas->id)
                ->orderBy('nama')
                ->get();

            $rataNilaiBySiswa = TranskripsNilai::query()
                ->selectRaw('siswa_id, AVG(nilai_akhir) as rata_rata_nilai')
                ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                ->whereIn('siswa_id', $siswas->pluck('id'))
                ->groupBy('siswa_id')
                ->pluck('rata_rata_nilai', 'siswa_id');

            $existingKenaikan = KenaikanKelas::query()
                ->with('kelasTujuan')
                ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                ->whereIn('siswa_id', $siswas->pluck('id'))
                ->get()
                ->keyBy('siswa_id');

            $kelasNaikDefault = $this->findNextKelas($selectedKelas);
            $kelasTurunDefault = $selectedKelas;

            $siswaRows = $siswas->map(function ($siswa) use ($rataNilaiBySiswa, $existingKenaikan, $selectedKelas, $kelasNaikDefault, $kelasTurunDefault, $isLastGrade) {
                $rata = $rataNilaiBySiswa->has($siswa->id)
                    ? round((float) $rataNilaiBySiswa->get($siswa->id), 2)
                    : null;

                $existing = $existingKenaikan->get($siswa->id);
                $statusDefault = $existing
                    ? (string) $existing->status
                    : ($rata !== null ? KenaikanKelas::determineStatus($rata, $isLastGrade) : 'tidak_naik');

                return [
                    'siswa' => $siswa,
                    'nis' => $siswa->nis ?? optional($siswa->kartuPelajar->first())->nis_otomatis ?? '-',
                    'rata_rata_nilai' => $rata,
                    'status_default' => $statusDefault,
                    'catatan' => (string) ($existing->catatan ?? ''),
                    'existing_status' => $existing?->status,
                    'kelas_tujuan_preview' => $existing?->kelasTujuan?->nama
                        ?? (($statusDefault === 'naik' && $kelasNaikDefault)
                            ? $kelasNaikDefault->nama
                            : (($statusDefault === 'tidak_naik' && $kelasTurunDefault)
                                ? $kelasTurunDefault->nama
                                : '-')),
                ];
            });
        }

        return view('akademik.kenaikan-kelas.bulk-rombel', [
            'tahunAjarans' => $tahunAjarans,
            'kelases' => $kelases,
            'selectedTahunAjaranId' => $selectedTahunAjaranId,
            'selectedKelasId' => $selectedKelasId,
            'selectedKelas' => $selectedKelas,
            'siswaRows' => $siswaRows,
            'isLastGrade' => $isLastGrade,
        ]);
    }

    /**
     * Simpan hasil proses massal kenaikan kelas berdasarkan rombel.
     */
    public function simpanProsesRombel(Request $request)
    {
        $this->abortIfKepalaSekolahReadOnly();
        $this->syncKelasTingkatFromNama();

        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'selected' => 'nullable|array',
            'selected.*' => 'nullable|in:1',
            'rows' => 'nullable|array',
            'rows.*.status' => 'required|in:naik,tidak_naik,lulus',
            'rows.*.catatan' => 'nullable|string',
        ]);

        $selectedIds = collect(array_keys((array) ($validated['selected'] ?? [])))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values();

        if ($selectedIds->isEmpty()) {
            return back()->withInput()->withErrors([
                'selected' => 'Pilih minimal satu siswa untuk diproses.',
            ]);
        }

        $kelas = Kelas::query()->findOrFail((int) $validated['kelas_id']);
        
        $guruKelasIds = $this->getGuruKelasIds();
        if ($guruKelasIds !== null && !in_array($kelas->id, $guruKelasIds, true)) {
            return back()->withInput()->withErrors([
                'kelas_id' => 'Anda tidak memiliki akses untuk memproses kelas ini.',
            ]);
        }
        
        $tahunAjaranId = (int) $validated['tahun_ajaran_id'];
        $rows = (array) ($validated['rows'] ?? []);

        $siswas = Siswa::query()
            ->where('kelas_id', $kelas->id)
            ->whereIn('id', $selectedIds)
            ->get()
            ->keyBy('id');

        if ($siswas->count() !== $selectedIds->count()) {
            return back()->withInput()->withErrors([
                'selected' => 'Ada siswa yang tidak sesuai dengan rombel terpilih.',
            ]);
        }

        $rataNilaiBySiswa = TranskripsNilai::query()
            ->selectRaw('siswa_id, AVG(nilai_akhir) as rata_rata_nilai')
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->whereIn('siswa_id', $selectedIds)
            ->groupBy('siswa_id')
            ->pluck('rata_rata_nilai', 'siswa_id');

        $processed = 0;

        DB::transaction(function () use ($selectedIds, $rows, $kelas, $tahunAjaranId, $rataNilaiBySiswa, &$processed) {
            foreach ($selectedIds as $siswaId) {
                $row = (array) ($rows[$siswaId] ?? []);
                $status = (string) ($row['status'] ?? 'tidak_naik');
                $catatan = isset($row['catatan']) ? trim((string) $row['catatan']) : null;

                $resolvedKelasTujuanId = $this->resolveKelasTujuanId($kelas, $status, null);

                if ($status === 'naik' && !$resolvedKelasTujuanId) {
                    throw ValidationException::withMessages([
                        'rows.' . $siswaId . '.status' => 'Kelas tujuan naik untuk salah satu siswa tidak ditemukan. Pastikan tingkat berikutnya tersedia.',
                    ]);
                }

                if ($status === 'tidak_naik' && !$resolvedKelasTujuanId) {
                    throw ValidationException::withMessages([
                        'rows.' . $siswaId . '.status' => 'Kelas tujuan turun untuk salah satu siswa tidak ditemukan. Pastikan tingkat sebelumnya tersedia.',
                    ]);
                }

                $this->validateKelasTujuanByStatus($kelas, $status, $resolvedKelasTujuanId);

                KenaikanKelas::query()->updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'tahun_ajaran_id' => $tahunAjaranId,
                    ],
                    [
                        'kelas_sekarang_id' => $kelas->id,
                        'kelas_tujuan_id' => $resolvedKelasTujuanId,
                        'status' => $status,
                        'rata_rata_nilai' => $rataNilaiBySiswa->has($siswaId)
                            ? round((float) $rataNilaiBySiswa->get($siswaId), 2)
                            : null,
                        'catatan' => $catatan,
                        'tanggal_penetapan' => now()->toDateString(),
                        'is_applied' => false,
                    ]
                );

                $processed++;
            }
        });

        return redirect()->route('akademik.kenaikan-kelas.index', [
            'tahun_ajaran_id' => $tahunAjaranId,
            'status' => null,
        ])->with('success', 'Proses kenaikan kelas per rombel berhasil disimpan untuk ' . $processed . ' siswa.');
    }

    private function orderedKelasQuery()
    {
        $query = Kelas::query()->orderByTingkat()->orderBy('nama_kelas');
        $guruKelasIds = $this->getGuruKelasIds();
        if ($guruKelasIds !== null) {
            $query->whereIn('id', $guruKelasIds);
        }
        return $query;
    }

    private function getGuruKelasIds(): ?array
    {
        $user = auth()->user();
        if ($user && $user->hasRole('Guru')) {
            $guru = $user->guru;
            if ($guru) {
                $jadwalKelasIds = \App\Models\JadwalPelajaran::where('guru_id', $guru->id)->pluck('kelas_id')->toArray();
                $waliKelasIds = \App\Models\GuruWaliKelas::where('guru_id', $guru->id)->pluck('kelas_id')->toArray();
                return array_unique(array_merge($jadwalKelasIds, $waliKelasIds));
            }
            return [];
        }
        return null;
    }

    private function abortIfKepalaSekolahReadOnly(): void
    {
        abort_if($this->isKepalaSekolahRole(), 403, 'Kepala Sekolah hanya dapat melihat detail pada modul Kenaikan Kelas.');
    }

    private function isKepalaSekolahRole(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        $normalizedRoles = $user->getRoleNames()->map(function ($role) {
            $normalized = strtolower((string) $role);

            return preg_replace('/[^a-z0-9]/', '', $normalized);
        });

        return $normalizedRoles->contains('kepalasekolah');
    }

    private function resolveKelasTujuanId(Kelas $kelasSekarang, string $status, $kelasTujuanId): ?int
    {
        if ($status === 'naik') {
            if ($kelasTujuanId) {
                return (int) $kelasTujuanId;
            }

            return $this->findNextKelas($kelasSekarang)?->id;
        }

        if ($status === 'tidak_naik') {
            if ($kelasTujuanId) {
                return (int) $kelasTujuanId;
            }

            return $kelasSekarang->id;
        }

        return null;
    }

    private function findNextKelas(?Kelas $kelasSekarang): ?Kelas
    {
        if (!$kelasSekarang) {
            return null;
        }

        $tingkatSaatIni = (int) ($kelasSekarang->tingkat ?: Kelas::inferTingkatFromNama($kelasSekarang->nama_kelas));
        if ($tingkatSaatIni <= 0) {
            return null;
        }

        $targetTingkat = $tingkatSaatIni + 1;
        $candidates = $this->orderedKelasQuery()
            ->where('tingkat', $targetTingkat)
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        $suffixSaatIni = $this->extractRombelSuffix($kelasSekarang->nama_kelas);
        if ($suffixSaatIni !== '') {
            $matched = $candidates->first(function ($kelas) use ($suffixSaatIni) {
                return $this->extractRombelSuffix($kelas->nama_kelas) === $suffixSaatIni;
            });

            if ($matched) {
                return $matched;
            }
        }

        return $candidates->first();
    }

    private function findPreviousKelas(?Kelas $kelasSekarang): ?Kelas
    {
        if (!$kelasSekarang) {
            return null;
        }

        $tingkatSaatIni = (int) ($kelasSekarang->tingkat ?: Kelas::inferTingkatFromNama($kelasSekarang->nama_kelas));
        if ($tingkatSaatIni <= 1) {
            return null;
        }

        $targetTingkat = $tingkatSaatIni - 1;
        $candidates = $this->orderedKelasQuery()
            ->where('tingkat', $targetTingkat)
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        $suffixSaatIni = $this->extractRombelSuffix($kelasSekarang->nama_kelas);
        if ($suffixSaatIni !== '') {
            $matched = $candidates->first(function ($kelas) use ($suffixSaatIni) {
                return $this->extractRombelSuffix($kelas->nama_kelas) === $suffixSaatIni;
            });

            if ($matched) {
                return $matched;
            }
        }

        return $candidates->first();
    }

    private function extractRombelSuffix(?string $namaKelas): string
    {
        if (!$namaKelas) {
            return '';
        }

        if (preg_match('/([A-Za-z]+)$/', trim($namaKelas), $matches)) {
            return strtoupper($matches[1]);
        }

        return '';
    }

    private function syncKelasTingkatFromNama(): void
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

    private function isLastTingkat(Kelas $kelas): bool
    {
        return (bool) $kelas->is_tingkat_akhir;
    }

    private function validateKelasTujuanByStatus(Kelas $kelasSekarang, string $status, ?int $kelasTujuanId): void
    {
        if ($status === 'lulus') {
            if ($kelasTujuanId !== null) {
                throw ValidationException::withMessages([
                    'kelas_tujuan_id' => 'Status lulus tidak memerlukan kelas tujuan.',
                ]);
            }
            
            if (!$kelasSekarang->is_tingkat_akhir) {
                throw ValidationException::withMessages([
                    'status' => 'Siswa belum bisa diluluskan karena kelas saat ini bukan merupakan Kelas Kelulusan (Tingkat Akhir).',
                ]);
            }

            return;
        }

        if ($kelasTujuanId === null) {
            throw ValidationException::withMessages([
                'kelas_tujuan_id' => 'Kelas tujuan wajib diisi untuk status ini.',
            ]);
        }

        $kelasTujuan = Kelas::query()->find($kelasTujuanId);
        if (!$kelasTujuan) {
            throw ValidationException::withMessages([
                'kelas_tujuan_id' => 'Kelas tujuan tidak ditemukan.',
            ]);
        }

        $tingkatSaatIni = (int) ($kelasSekarang->tingkat ?: Kelas::inferTingkatFromNama($kelasSekarang->nama_kelas));
        $tingkatTujuan = (int) ($kelasTujuan->tingkat ?: Kelas::inferTingkatFromNama($kelasTujuan->nama_kelas));

        if ($tingkatSaatIni <= 0 || $tingkatTujuan <= 0) {
            throw ValidationException::withMessages([
                'kelas_tujuan_id' => 'Tingkat kelas belum valid. Pastikan kelas saat ini dan kelas tujuan memiliki tingkatan yang benar.',
            ]);
        }

        if ($status === 'naik' && $tingkatTujuan !== ($tingkatSaatIni + 1)) {
            throw ValidationException::withMessages([
                'kelas_tujuan_id' => 'Untuk status naik, kelas tujuan harus tingkat berikutnya dari kelas saat ini.',
            ]);
        }

        if ($status === 'tidak_naik' && $tingkatTujuan !== $tingkatSaatIni) {
            throw ValidationException::withMessages([
                'kelas_tujuan_id' => 'Untuk status tidak naik, kelas tujuan harus sama dengan tingkat kelas saat ini.',
            ]);
        }
    }
}
