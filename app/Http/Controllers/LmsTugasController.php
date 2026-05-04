<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KelasMataPelajaran;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\MataPelajaran;
use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LmsTugasController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can_access('view lms-tugas'), 403);

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

        $tugas = Tugas::with(['kelas', 'mataPelajaran', 'guru', 'semester', 'tahunAjaran'])
            ->withCount('pengumpulanTugas')
            ->when($this->isGuruScope(), function ($q) use ($allowedKelasIds) {
                if (empty($allowedKelasIds)) {
                    $q->whereRaw('1 = 0');

                    return;
                }

                $q->whereIn('kelas_id', $allowedKelasIds);
            })
            ->when($isSiswaScope && !$selectedKelasId, fn ($q) => $q->whereRaw('1 = 0'))
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
            ->when($pertemuanTanggal && Schema::hasColumn('tugas', 'tanggal_pertemuan'), fn ($q) => $q->whereDate('tanggal_pertemuan', $pertemuanTanggal))
            ->latest('tanggal_deadline')
            ->paginate(10)
            ->withQueryString();

        $mataPelajarans = collect();
        if ($selectedKelasId) {
            $mapelIds = Tugas::query()
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

        return view('akademik.lms.tugas.index', compact('tugas', 'kelases', 'mataPelajarans', 'selectedKelasId', 'isSiswaScope'));
    }

    public function create(Request $request)
    {
        abort_unless(can_access('create lms-tugas') || auth()->user()->hasRole('Guru'), 403);

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

        return view('akademik.lms.tugas.create', compact(
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
        abort_unless(can_access('create lms-tugas') || auth()->user()->hasRole('Guru'), 403);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'instruksi' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:51200',
            'tanggal_deadline' => 'required|date|after:now',
            'max_nilai' => 'required|numeric|min:1|max:100',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'semester_id' => 'nullable|exists:semester,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'tanggal_pertemuan' => 'nullable|date',
            'is_published' => 'nullable|boolean',
        ]);

        $this->ensureKelasAccessible((int) $validated['kelas_id']);

        $path = null;
        if ($request->hasFile('lampiran')) {
            $path = $request->file('lampiran')->store('lms/tugas', 'public');
        }

        $guruId = $this->resolveGuruId();
        $kelasMataPelajaranId = $this->resolveKelasMataPelajaranId($validated, $guruId);
        $deskripsiLegacy = $validated['deskripsi'] ?? $validated['instruksi'] ?? '-';

        $payload = [
            'kelas_mata_pelajaran_id' => $kelasMataPelajaranId,
            'judul_tugas' => $validated['judul'],
            'deskripsi_tugas' => $deskripsiLegacy,
            'deadline' => $validated['tanggal_deadline'],
            'created_by' => auth()->id(),
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'instruksi' => $validated['instruksi'] ?? null,
            'lampiran_path' => $path,
            'tanggal_deadline' => $validated['tanggal_deadline'],
            'max_nilai' => $validated['max_nilai'],
            'kelas_id' => $validated['kelas_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'semester_id' => $validated['semester_id'] ?? null,
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'] ?? null,
            'guru_id' => $guruId,
            'is_published' => (bool) ($validated['is_published'] ?? true),
        ];

        if (Schema::hasColumn('tugas', 'tanggal_pertemuan')) {
            $payload['tanggal_pertemuan'] = $validated['tanggal_pertemuan'] ?? null;
        }

        $tugas = Tugas::create($payload);

        $this->notifyTugasBaru($tugas);

        $redirectPertemuanTanggal = optional($tugas->tanggal_pertemuan)->toDateString();
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

        return redirect()->route('akademik.lms.tugas.index', array_filter([
            'kelas_id' => $validated['kelas_id'] ?? null,
            'semester_id' => $redirectSemesterId,
            'tahun_ajaran_id' => $redirectTahunAjaranId,
            'pertemuan_tanggal' => $redirectPertemuanTanggal,
        ], fn ($value) => $value !== null && $value !== ''))->with('success', 'Tugas berhasil dibuat');
    }

    public function show(Tugas $tugas)
    {
        abort_unless(can_access('view lms-tugas'), 403);

        $tugas->load(['kelas', 'mataPelajaran', 'guru', 'semester', 'tahunAjaran']);
        $currentSiswa = $this->resolveCurrentSiswa();
        $isKepalaSekolahReadOnly = auth()->user()->hasRole('Kepala Sekolah') && !is_admin();
        $isMandiriSiswa = !$isKepalaSekolahReadOnly && is_siswa() && !is_admin() && !auth()->user()->hasRole('Guru');

        $pengumpulanQuery = PengumpulanTugas::with(['siswa.kelas', 'dinilaiOleh'])
            ->where('tugas_id', $tugas->id);

        if ($isMandiriSiswa) {
            if ($currentSiswa) {
                $pengumpulanQuery->where('siswa_id', $currentSiswa->id);
            } else {
                $pengumpulanQuery->whereRaw('1 = 0');
            }
        }

        $pengumpulan = $pengumpulanQuery
            ->latest('submitted_at')
            ->paginate(10);

        $myPengumpulan = null;
        if ($isMandiriSiswa && $currentSiswa) {
            $myPengumpulan = PengumpulanTugas::query()
                ->where('tugas_id', $tugas->id)
                ->where('siswa_id', $currentSiswa->id)
                ->latest('submitted_at')
                ->first();
        }

        $siswas = collect();
        if (!$isMandiriSiswa) {
            $siswas = Siswa::where('is_active', true)
                ->when($tugas->kelas_id, fn ($q) => $q->where('kelas_id', $tugas->kelas_id))
                ->orderBy('nama')
                ->get();
        }

        return view('akademik.lms.tugas.show', compact(
            'tugas',
            'pengumpulan',
            'siswas',
            'currentSiswa',
            'isMandiriSiswa',
            'myPengumpulan',
            'isKepalaSekolahReadOnly'
        ));
    }

    public function edit(Tugas $tugas)
    {
        abort_unless(can_access('create lms-tugas') || auth()->user()->hasRole('Guru'), 403);

        $kelases = $this->resolveKelasOptionsForCurrentUser();
        $mataPelajarans = MataPelajaran::dropdownOptions();
        $semesters = Semester::with('tahunAjaran')->orderBy('nomor_semester')->get();
        $tahunAjarans = TahunAjaran::orderBy('nama', 'desc')->get();

        return view('akademik.lms.tugas.edit', compact('tugas', 'kelases', 'mataPelajarans', 'semesters', 'tahunAjarans'));
    }

    public function update(Request $request, Tugas $tugas)
    {
        abort_unless(can_access('create lms-tugas') || auth()->user()->hasRole('Guru'), 403);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'instruksi' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:51200',
            'tanggal_deadline' => 'required|date',
            'max_nilai' => 'required|numeric|min:1|max:100',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'semester_id' => 'nullable|exists:semester,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'tanggal_pertemuan' => 'nullable|date',
            'is_published' => 'nullable|boolean',
        ]);

        $this->ensureKelasAccessible((int) $validated['kelas_id']);

        $path = $tugas->lampiran_path;
        if ($request->hasFile('lampiran')) {
            if ($tugas->lampiran_path) {
                Storage::disk('public')->delete($tugas->lampiran_path);
            }

            $path = $request->file('lampiran')->store('lms/tugas', 'public');
        }

        $guruId = $tugas->guru_id ?: $this->resolveGuruId();
        $kelasMataPelajaranId = $this->resolveKelasMataPelajaranId($validated, $guruId);
        $deskripsiLegacy = $validated['deskripsi'] ?? $validated['instruksi'] ?? '-';

        $payload = [
            'kelas_mata_pelajaran_id' => $kelasMataPelajaranId,
            'judul_tugas' => $validated['judul'],
            'deskripsi_tugas' => $deskripsiLegacy,
            'deadline' => $validated['tanggal_deadline'],
            'created_by' => $tugas->created_by ?: auth()->id(),
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'instruksi' => $validated['instruksi'] ?? null,
            'lampiran_path' => $path,
            'tanggal_deadline' => $validated['tanggal_deadline'],
            'max_nilai' => $validated['max_nilai'],
            'kelas_id' => $validated['kelas_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'semester_id' => $validated['semester_id'] ?? null,
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'] ?? null,
            'guru_id' => $guruId,
            'is_published' => (bool) ($validated['is_published'] ?? true),
        ];

        if (Schema::hasColumn('tugas', 'tanggal_pertemuan')) {
            $payload['tanggal_pertemuan'] = $validated['tanggal_pertemuan'] ?? $tugas->tanggal_pertemuan;
        }

        $tugas->update($payload);

        return redirect()->route('akademik.lms.tugas.show', $tugas)->with('success', 'Tugas berhasil diperbarui');
    }

    public function destroy(Tugas $tugas)
    {
        abort_unless(can_access('delete lms-tugas') || is_admin(), 403);

        if ($tugas->lampiran_path) {
            Storage::disk('public')->delete($tugas->lampiran_path);
        }

        $tugas->delete();

        return redirect()->route('akademik.lms.tugas.index')->with('success', 'Tugas berhasil dihapus');
    }

    private function notifyTugasBaru(Tugas $tugas): void
    {
        $recipientIds = collect();

        if ($tugas->kelas_id) {
            $recipientIds = $recipientIds->merge(
                Siswa::query()
                    ->where('is_active', true)
                    ->where('kelas_id', $tugas->kelas_id)
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
            );
        }

        $recipientIds = $recipientIds
            ->merge(
                User::query()
                    ->whereHas('roles', function ($q) {
                        $q->whereIn('name', ['Admin', 'Kepala Sekolah']);
                    })
                    ->pluck('id')
            )
            ->unique()
            ->filter();

        foreach ($recipientIds as $recipientId) {
            Notifikasi::create([
                'user_id' => (int) $recipientId,
                'judul' => 'Tugas Baru LMS',
                'isi' => 'Tugas baru tersedia: ' . $tugas->judul,
                'tipe' => 'tugas',
                'terkait_dengan' => Tugas::class,
                'terkait_id' => $tugas->id,
                'is_read' => false,
            ]);
        }
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

        if (is_admin() || $user->hasRole('Guru')) {
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
        $currentSiswa = $this->resolveCurrentSiswa();
        $kelasId = (int) ($currentSiswa?->kelas_id ?? 0);

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

    private function resolveCurrentSiswa(): ?Siswa
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        $siswaByUser = Siswa::query()
            ->where('is_active', true)
            ->where('user_id', $user->id)
            ->first();

        if ($siswaByUser) {
            return $siswaByUser;
        }

        if (!empty($user->email)) {
            $normalizedEmail = mb_strtolower(trim((string) $user->email));

            $siswaByEmail = Siswa::query()
                ->where('is_active', true)
                ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
                ->first();

            if ($siswaByEmail instanceof Siswa) {
                if (!$siswaByEmail->user_id) {
                    $siswaByEmail->update(['user_id' => $user->id]);
                }

                return $siswaByEmail;
            }
        }

        if (!empty($user->name)) {
            $siswaByName = Siswa::query()
                ->where('is_active', true)
                ->where(function ($q) use ($user) {
                    $q->whereNull('user_id')->orWhere('user_id', $user->id);
                })
                ->whereRaw('LOWER(nama) = ?', [mb_strtolower(trim($user->name))])
                ->first();

            if ($siswaByName instanceof Siswa && !$siswaByName->user_id) {
                $siswaByName->update(['user_id' => $user->id]);
            }

            return $siswaByName instanceof Siswa ? $siswaByName : null;
        }

        return null;
    }
}
