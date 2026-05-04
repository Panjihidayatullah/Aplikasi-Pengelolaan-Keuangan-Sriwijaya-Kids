<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KelasMataPelajaran;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Materi;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LmsMateriController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can_access('view lms-materi'), 403);

        $pertemuanTanggal = null;
        if ($request->filled('pertemuan_tanggal')) {
            try {
                $pertemuanTanggal = Carbon::parse((string) $request->input('pertemuan_tanggal'))->toDateString();
            } catch (\Throwable $e) {
                $pertemuanTanggal = null;
            }
        }

        $kelases = $this->resolveKelasOptionsForCurrentUser();
        $selectedKelasId = $this->resolveSelectedKelasId($request, $kelases);
        $isSiswaScope = $this->isSiswaScope();
        $allowedKelasIds = $kelases->pluck('id')->map(fn ($id) => (int) $id)->all();
        $selectedMapelIds = $request->filled('mata_pelajaran_id')
            ? MataPelajaran::equivalentIds($request->integer('mata_pelajaran_id'))
            : [];

        $materi = Materi::with(['kelas', 'mataPelajaran', 'guru', 'semester', 'tahunAjaran'])
            ->when($this->isGuruScope(), function ($q) use ($allowedKelasIds) {
                if (empty($allowedKelasIds)) {
                    $q->whereRaw('1 = 0');

                    return;
                }

                $q->whereIn('kelas_id', $allowedKelasIds);
            })
            ->when(!$selectedKelasId, fn ($q) => $q->whereRaw('1 = 0'))
            ->when($selectedKelasId, fn ($q) => $q->where('kelas_id', $selectedKelasId))
            ->when($request->filled('semester_id'), fn ($q) => $q->where('semester_id', $request->integer('semester_id')))
            ->when($request->filled('tahun_ajaran_id'), fn ($q) => $q->where('tahun_ajaran_id', $request->integer('tahun_ajaran_id')))
            ->when($request->filled('mata_pelajaran_id'), function ($q) use ($selectedMapelIds) {
                if (empty($selectedMapelIds)) {
                    $q->whereRaw('1 = 0');

                    return;
                }

                $q->whereIn('mata_pelajaran_id', $selectedMapelIds);
            })
            ->when($request->filled('q'), fn ($q) => $q->where('judul', 'ilike', '%' . $request->string('q') . '%'))
            ->when($pertemuanTanggal && Schema::hasColumn('materi', 'tanggal_pertemuan'), fn ($q) => $q->whereDate('tanggal_pertemuan', $pertemuanTanggal))
            ->when(!(auth()->user()->hasRole('Guru') || is_admin()), fn ($q) => $q->where('is_published', true))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $mataPelajarans = collect();
        if ($selectedKelasId) {
            $mapelIds = Materi::query()
                ->where('kelas_id', $selectedKelasId)
                ->whereNotNull('mata_pelajaran_id')
                ->pluck('mata_pelajaran_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $mataPelajarans = MataPelajaran::dropdownOptions(MataPelajaran::query()
                ->when($mapelIds->isNotEmpty(), fn ($q) => $q->whereIn('id', $mapelIds->all()), fn ($q) => $q->whereRaw('1 = 0'))
            );
        }

        return view('akademik.lms.materi.index', compact('materi', 'kelases', 'mataPelajarans', 'selectedKelasId', 'isSiswaScope'));
    }

    public function create(Request $request)
    {
        abort_unless(can_access('create lms-materi') || auth()->user()->hasRole('Guru'), 403);

        $kelases = $this->resolveKelasOptionsForCurrentUser();
        $mataPelajarans = MataPelajaran::dropdownOptions();
        $semesters = Semester::with('tahunAjaran')->orderBy('nomor_semester')->get();
        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();

        $prefillPertemuanTanggal = null;
        if ($request->filled('pertemuan_tanggal')) {
            try {
                $prefillPertemuanTanggal = Carbon::parse((string) $request->input('pertemuan_tanggal'))->toDateString();
            } catch (\Throwable $e) {
                $prefillPertemuanTanggal = null;
            }
        }

        $prefillSemesterId = $request->filled('semester_id') ? $request->integer('semester_id') : null;
        $prefillTahunAjaranId = $request->filled('tahun_ajaran_id') ? $request->integer('tahun_ajaran_id') : null;

        if ($prefillSemesterId && !$prefillTahunAjaranId) {
            $prefillTahunAjaranId = optional($semesters->firstWhere('id', $prefillSemesterId))->tahun_ajaran_id;
        }

        $prefillKelasId = $this->resolveSelectedKelasId($request, $kelases);

        return view('akademik.lms.materi.create', compact(
            'kelases',
            'mataPelajarans',
            'semesters',
            'tahunAjarans',
            'prefillPertemuanTanggal',
            'prefillSemesterId',
            'prefillTahunAjaranId',
            'prefillKelasId'
        ));
    }

    public function store(Request $request)
    {
        abort_unless(can_access('create lms-materi') || auth()->user()->hasRole('Guru'), 403);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:pdf,video,ppt,link',
            'file' => 'nullable|file|mimes:pdf,ppt,pptx,mp4,mov,avi|max:51200',
            'video_url' => 'nullable|url|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'semester_id' => 'nullable|exists:semester,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'tanggal_pertemuan' => 'nullable|date',
            'is_published' => 'nullable|boolean',
        ]);

        $this->ensureKelasAccessible((int) $validated['kelas_id']);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('lms/materi', 'public');
        }

        $guruId = $this->resolveGuruId();
        $kelasMataPelajaranId = $this->resolveKelasMataPelajaranId($validated, $guruId);

        $payload = [
            'kelas_mata_pelajaran_id' => $kelasMataPelajaranId,
            'judul_materi' => $validated['judul'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tipe_materi' => $this->mapLegacyTipeMateri($validated['tipe']),
            'tipe' => $validated['tipe'],
            'file_path' => $path,
            'link_url' => $validated['video_url'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'kelas_id' => $validated['kelas_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'semester_id' => $validated['semester_id'] ?? null,
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'] ?? null,
            'guru_id' => $guruId,
            'created_by' => auth()->id(),
            'is_published' => (bool) ($validated['is_published'] ?? true),
            'published_at' => now(),
        ];

        if (Schema::hasColumn('materi', 'tanggal_pertemuan')) {
            $payload['tanggal_pertemuan'] = $validated['tanggal_pertemuan'] ?? null;
        }

        $createdMateri = Materi::create($payload);

        $redirectPertemuanTanggal = optional($createdMateri->tanggal_pertemuan)->toDateString();
        if (!$redirectPertemuanTanggal && $request->filled('pertemuan_tanggal_context')) {
            try {
                $redirectPertemuanTanggal = Carbon::parse((string) $request->input('pertemuan_tanggal_context'))->toDateString();
            } catch (\Throwable $e) {
                $redirectPertemuanTanggal = null;
            }
        }

        $redirectSemesterId = $validated['semester_id'] ?? null;
        if (!$redirectSemesterId && $request->filled('semester_id_context')) {
            $redirectSemesterId = $request->integer('semester_id_context');
        }

        $redirectTahunAjaranId = $validated['tahun_ajaran_id'] ?? null;
        if (!$redirectTahunAjaranId && $request->filled('tahun_ajaran_id_context')) {
            $redirectTahunAjaranId = $request->integer('tahun_ajaran_id_context');
        }

        return redirect()->route('akademik.lms.materi.index', array_filter([
            'kelas_id' => $validated['kelas_id'] ?? null,
            'semester_id' => $redirectSemesterId,
            'tahun_ajaran_id' => $redirectTahunAjaranId,
            'pertemuan_tanggal' => $redirectPertemuanTanggal,
        ], fn ($value) => $value !== null && $value !== ''))->with('success', 'Materi berhasil diupload');
    }

    public function show(Materi $materi)
    {
        abort_unless(can_access('view lms-materi'), 403);

        $materi->load(['kelas', 'mataPelajaran', 'guru', 'semester', 'tahunAjaran']);

        return view('akademik.lms.materi.show', compact('materi'));
    }

    public function viewFile(Materi $materi)
    {
        abort_unless(can_access('view lms-materi'), 403);

        if (!$materi->file_path) {
            abort(404, 'File materi tidak ditemukan.');
        }

        $relativePath = ltrim($materi->file_path, '/\\');
        if (!Storage::disk('public')->exists($relativePath)) {
            abort(404, 'File materi tidak ditemukan di penyimpanan.');
        }

        $absolutePath = storage_path('app/public/' . $relativePath);
        $filename = basename($absolutePath);

        return response()->file($absolutePath, [
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function downloadFile(Materi $materi)
    {
        abort_unless(can_access('view lms-materi'), 403);

        if (!$materi->file_path) {
            abort(404, 'File materi tidak ditemukan.');
        }

        $relativePath = ltrim($materi->file_path, '/\\');
        if (!Storage::disk('public')->exists($relativePath)) {
            abort(404, 'File materi tidak ditemukan di penyimpanan.');
        }

        $absolutePath = storage_path('app/public/' . $relativePath);

        return response()->download($absolutePath, basename($absolutePath));
    }

    public function edit(Materi $materi)
    {
        abort_unless(can_access('create lms-materi') || auth()->user()->hasRole('Guru'), 403);

        $kelases = $this->resolveKelasOptionsForCurrentUser();
        $mataPelajarans = MataPelajaran::dropdownOptions();
        $semesters = Semester::with('tahunAjaran')->orderBy('nomor_semester')->get();
        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();

        return view('akademik.lms.materi.edit', compact('materi', 'kelases', 'mataPelajarans', 'semesters', 'tahunAjarans'));
    }

    public function update(Request $request, Materi $materi)
    {
        abort_unless(can_access('create lms-materi') || auth()->user()->hasRole('Guru'), 403);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:pdf,video,ppt,link',
            'file' => 'nullable|file|mimes:pdf,ppt,pptx,mp4,mov,avi|max:51200',
            'video_url' => 'nullable|url|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'semester_id' => 'nullable|exists:semester,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'tanggal_pertemuan' => 'nullable|date',
            'is_published' => 'nullable|boolean',
        ]);

        $this->ensureKelasAccessible((int) $validated['kelas_id']);

        $path = $materi->file_path;
        if ($request->hasFile('file')) {
            if ($materi->file_path) {
                Storage::disk('public')->delete($materi->file_path);
            }

            $path = $request->file('file')->store('lms/materi', 'public');
        }

        $guruId = $materi->guru_id ?: $this->resolveGuruId();
        $kelasMataPelajaranId = $this->resolveKelasMataPelajaranId($validated, $guruId);

        $payload = [
            'kelas_mata_pelajaran_id' => $kelasMataPelajaranId,
            'judul_materi' => $validated['judul'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tipe_materi' => $this->mapLegacyTipeMateri($validated['tipe']),
            'tipe' => $validated['tipe'],
            'file_path' => $path,
            'link_url' => $validated['video_url'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'kelas_id' => $validated['kelas_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'semester_id' => $validated['semester_id'] ?? null,
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'] ?? null,
            'guru_id' => $guruId,
            'created_by' => $materi->created_by ?: auth()->id(),
            'is_published' => (bool) ($validated['is_published'] ?? true),
        ];

        if (Schema::hasColumn('materi', 'tanggal_pertemuan')) {
            $payload['tanggal_pertemuan'] = $validated['tanggal_pertemuan'] ?? $materi->tanggal_pertemuan;
        }

        $materi->update($payload);

        return redirect()->route('akademik.lms.materi.show', $materi)->with('success', 'Materi berhasil diperbarui');
    }

    public function destroy(Materi $materi)
    {
        abort_unless(can_access('delete lms-materi') || is_admin(), 403);

        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return redirect()->route('akademik.lms.materi.index')->with('success', 'Materi berhasil dihapus');
    }

    private function resolveGuruId(): int
    {
        $user = auth()->user();
        $guruId = optional($user?->guru)->id;

        if ($guruId) {
            return (int) $guruId;
        }

        if ($user) {
            $guru = Guru::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->name ?: ('Guru #' . $user->id),
                    'jenis_kelamin' => 'L',
                    'email' => $user->email,
                    'is_active' => true,
                ]
            );

            return (int) $guru->id;
        }

        $fallbackGuruId = Guru::query()->withTrashed()->orderBy('id')->value('id');

        if ($fallbackGuruId) {
            return (int) $fallbackGuruId;
        }

        throw ValidationException::withMessages([
            'guru_id' => 'Data guru tidak ditemukan. Silakan buat data guru terlebih dahulu.',
        ]);
    }

    private function resolveKelasMataPelajaranId(array $validated, int $guruId): int
    {
        $tahunAjaran = !empty($validated['tahun_ajaran_id'])
            ? TahunAjaran::query()->withTrashed()->find($validated['tahun_ajaran_id'])
            : TahunAjaran::query()->withTrashed()->orderByDesc('id')->first();
        $semester = !empty($validated['semester_id'])
            ? Semester::query()->withTrashed()->find($validated['semester_id'])
            : Semester::query()->withTrashed()->orderByDesc('id')->first();

        $tahunAjaranText = $tahunAjaran?->nama
            ?: now()->format('Y') . '/' . now()->addYear()->format('Y');
        $semesterText = $this->mapSemesterUntukKelasMapel($semester);

        $kelasMataPelajaran = KelasMataPelajaran::query()->firstOrCreate(
            [
                'kelas_id' => $validated['kelas_id'],
                'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
                'tahun_ajaran' => $tahunAjaranText,
                'semester' => $semesterText,
            ],
            [
                'guru_id' => $guruId,
                'is_active' => true,
            ]
        );

        return (int) $kelasMataPelajaran->id;
    }

    private function mapLegacyTipeMateri(string $tipe): string
    {
        return in_array($tipe, ['video', 'link'], true) ? $tipe : 'dokumen';
    }

    private function mapSemesterUntukKelasMapel(?Semester $semester): string
    {
        return ((int) ($semester?->nomor_semester ?? 1)) === 2 ? 'Genap' : 'Ganjil';
    }

    private function isGuruScope(): bool
    {
        return auth()->user()->hasRole('Guru') && !is_admin();
    }

    private function isSiswaScope(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if ($user->hasRole('Siswa')) {
            return true;
        }

        return $user->siswa()->exists();
    }

    private function resolveKelasOptionsForCurrentUser()
    {
        if ($this->isSiswaScope()) {
            $kelasId = $this->resolveSiswaKelasId();
            if (!$kelasId) {
                return collect();
            }

            return Kelas::query()
                ->whereKey($kelasId)
                ->orderByTingkat()
                ->orderBy('nama_kelas')
                ->get();
        }

        if (!$this->isGuruScope()) {
            return Kelas::query()->orderByTingkat()->orderBy('nama_kelas')->get();
        }

        $guruId = $this->resolveGuruId();

        $kelasIds = JadwalPelajaran::query()
            ->where('guru_id', $guruId)
            ->where('is_active', true)
            ->where('is_istirahat', false)
            ->whereNotNull('kelas_id')
            ->pluck('kelas_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($kelasIds->isEmpty()) {
            return collect();
        }

        return Kelas::query()
            ->whereIn('id', $kelasIds)
            ->orderByTingkat()
            ->orderBy('nama_kelas')
            ->get();
    }

    private function resolveSelectedKelasId(Request $request, $kelasOptions): ?int
    {
        if ($this->isSiswaScope()) {
            return $kelasOptions->count() > 0
                ? (int) optional($kelasOptions->first())->id
                : null;
        }

        $kelasId = $request->filled('kelas_id')
            ? $request->integer('kelas_id')
            : null;

        if (!$kelasId) {
            return $kelasOptions->count() === 1
                ? (int) optional($kelasOptions->first())->id
                : null;
        }

        $exists = $kelasOptions->contains(fn ($kelas) => (int) $kelas->id === (int) $kelasId);

        return $exists ? (int) $kelasId : null;
    }

    private function resolveSiswaKelasId(): ?int
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        $directKelasId = (int) optional($user->siswa)->kelas_id;
        if ($directKelasId > 0) {
            return $directKelasId;
        }

        $email = mb_strtolower(trim((string) ($user->email ?? '')));
        if ($email === '') {
            return null;
        }

        $siswa = Siswa::query()
            ->whereNotNull('email')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (!$siswa) {
            return null;
        }

        if ((int) ($siswa->user_id ?? 0) === 0) {
            Siswa::query()->whereKey((int) $siswa->id)->update(['user_id' => (int) $user->id]);
        }

        $kelasId = (int) ($siswa->kelas_id ?? 0);

        return $kelasId > 0 ? $kelasId : null;
    }

    private function ensureKelasAccessible(int $kelasId): void
    {
        if (!$this->isGuruScope()) {
            return;
        }

        $allowed = $this->resolveKelasOptionsForCurrentUser()
            ->contains(fn ($kelas) => (int) $kelas->id === $kelasId);

        if ($allowed) {
            return;
        }

        throw ValidationException::withMessages([
            'kelas_id' => 'Kelas yang dipilih tidak termasuk kelas yang Anda ampu.',
        ]);
    }
}
