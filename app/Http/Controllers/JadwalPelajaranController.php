<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Ruang;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($this->canViewJadwal(), 403);

        $user = auth()->user();
        $currentSiswa = $this->resolveCurrentSiswa();
        $isAdmin = is_admin();
        $isGuruScope = !$isAdmin && $user->hasRole('Guru');
        $isSiswaScope = !$isAdmin && is_siswa() && !$isGuruScope;
        $selectedKelasId = null;
        $selectedKelasNama = null;

        $query = JadwalPelajaran::query()->with(['kelas', 'mataPelajaran', 'guru', 'ruangan']);

        if ($isGuruScope) {
            $guruId = optional($user->guru)->id;
            if ($guruId) {
                $query->where('guru_id', $guruId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($isSiswaScope) {
            $kelasId = (int) ($currentSiswa?->kelas_id ?? 0);
            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $hariOrder = "CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 ELSE 7 END";
        $groupedJadwal = collect();
        $kelasCards = collect();
        $kelasCardCounts = collect();
        $popupScheduleByClass = collect();
        $popupStudentsByClass = collect();

        if ($isSiswaScope) {
            $selectedKelasId = (int) ($currentSiswa?->kelas_id ?? 0) ?: null;
            $selectedKelasNama = optional($currentSiswa?->kelas)->nama;

            $jadwal = $query
                ->orderByRaw($hariOrder)
                ->orderBy('jam_mulai')
                ->get();

            $groupedJadwal = collect($this->hariOptions())
                ->mapWithKeys(fn (string $hari) => [
                    $hari => $jadwal->where('hari', $hari)->values(),
                ]);
        } else {
            $kelasCardCounts = (clone $query)
                ->selectRaw('kelas_id, count(*) as total')
                ->groupBy('kelas_id')
                ->pluck('total', 'kelas_id');

            $kelasCards = Kelas::query()
                ->whereIn('id', $kelasCardCounts->keys())
                ->orderBy('nama_kelas')
                ->get();

            $jadwal = $query
                ->orderByRaw($hariOrder)
                ->orderBy('jam_mulai')
                ->get();

            $popupScheduleByClass = $jadwal
                ->groupBy('kelas_id')
                ->map(function ($kelasJadwal) {
                    return collect($this->hariOptions())
                        ->mapWithKeys(function (string $hari) use ($kelasJadwal) {
                            $rows = $kelasJadwal
                                ->where('hari', $hari)
                                ->values()
                                ->map(fn (JadwalPelajaran $item) => [
                                    'id' => $item->id,
                                    'jam_mulai' => substr((string) $item->jam_mulai, 0, 5),
                                    'jam_selesai' => substr((string) $item->jam_selesai, 0, 5),
                                    'kelas' => $item->kelas->nama ?? '-',
                                    'mata_pelajaran' => $item->is_istirahat ? 'Ishoma / Istirahat' : ($item->mataPelajaran->nama ?? '-'),
                                    'guru' => $item->is_istirahat ? '-' : ($item->guru->nama ?? '-'),
                                    'ruang' => $item->is_istirahat ? '-' : ($item->ruangan->nama ?? $item->ruang ?? '-'),
                                    'is_active' => (bool) $item->is_active,
                                ]);

                            return [$hari => $rows];
                        });
                });

            $popupStudentsByClass = Siswa::query()
                ->select(['id', 'kelas_id', 'nis', 'nama', 'jenis_kelamin', 'is_active'])
                ->whereIn('kelas_id', $kelasCards->pluck('id')->all())
                ->orderBy('nama')
                ->get()
                ->groupBy('kelas_id')
                ->map(function ($rows) {
                    return $rows->values()->map(fn (Siswa $siswa) => [
                        'id' => (int) $siswa->id,
                        'nis' => (string) ($siswa->nis ?? '-'),
                        'nama' => (string) ($siswa->nama ?? '-'),
                        'jenis_kelamin' => (string) ($siswa->jenis_kelamin ?? '-'),
                        'is_active' => (bool) $siswa->is_active,
                    ]);
                });
        }

        $kelases = Kelas::query()->orderBy('nama_kelas')->get();
        return view('akademik.jadwal-pelajaran.index', [
            'jadwal' => $jadwal,
            'kelases' => $kelases,
            'kelasCards' => $kelasCards,
            'kelasCardCounts' => $kelasCardCounts,
            'selectedKelasId' => $selectedKelasId,
            'selectedKelasNama' => $selectedKelasNama,
            'popupScheduleByClass' => $popupScheduleByClass,
            'popupStudentsByClass' => $popupStudentsByClass,
            'isAdmin' => $isAdmin,
            'isGuruScope' => $isGuruScope,
            'isSiswaScope' => $isSiswaScope,
            'groupedJadwal' => $groupedJadwal,
            'kelasSiswa' => optional($currentSiswa?->kelas)->nama,
            'hariOptions' => $this->hariOptions(),
        ]);
    }

    public function exportPdf(Request $request)
    {
        abort_unless($this->canViewJadwal(), 403);

        $user = auth()->user();
        $currentSiswa = $this->resolveCurrentSiswa();
        $isAdmin = is_admin();
        $isGuruScope = !$isAdmin && $user->hasRole('Guru');
        $isSiswaScope = !$isAdmin && is_siswa() && !$isGuruScope;

        $query = JadwalPelajaran::query()->with(['kelas', 'mataPelajaran', 'guru', 'ruangan']);

        if ($isGuruScope) {
            $guruId = optional($user->guru)->id;
            if ($guruId) {
                $query->where('guru_id', $guruId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($isSiswaScope) {
            $kelasId = (int) ($currentSiswa?->kelas_id ?? 0);
            if ($kelasId > 0) {
                $query->where('kelas_id', $kelasId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $hariOrder = "CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 ELSE 7 END";

        $jadwal = $query
            ->orderByRaw($hariOrder)
            ->orderBy('jam_mulai')
            ->get();

        if ($jadwal->isEmpty()) {
            return back()->withErrors([
                'jadwal_export' => 'Data jadwal belum tersedia untuk diekspor.',
            ]);
        }

        $scopeLabel = 'Semua Kelas';
        $detailLabel = null;

        if ($isSiswaScope) {
            $scopeLabel = 'Jadwal Siswa';
            $detailLabel = 'Kelas: ' . (string) (optional($currentSiswa?->kelas)->nama ?? '-');
        } elseif ($isGuruScope) {
            $scopeLabel = 'Jadwal Guru';
            $detailLabel = 'Guru: ' . (string) (optional($user->guru)->nama ?? $user->name ?? '-');
        }

        $filename = 'jadwal-pelajaran-' . Str::slug((string) $scopeLabel) . '.pdf';
        $mode = strtolower((string) $request->input('mode', 'download'));

        $pdf = Pdf::loadView('akademik.jadwal-pelajaran.pdf', [
            'jadwal' => $jadwal,
            'scopeLabel' => $scopeLabel,
            'detailLabel' => $detailLabel,
            'isSiswaScope' => $isSiswaScope,
            'dicetakPada' => now(),
        ])->setPaper('a4', 'landscape');

        if ($mode === 'preview') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    private function resolveCurrentSiswa(): ?Siswa
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        $siswaByUser = Siswa::query()
            ->with('kelas')
            ->where('is_active', true)
            ->where('user_id', $user->id)
            ->first();

        if ($siswaByUser instanceof Siswa) {
            return $siswaByUser;
        }

        $normalizedEmail = mb_strtolower(trim((string) ($user->email ?? '')));
        if ($normalizedEmail === '') {
            return null;
        }

        $siswaByEmail = Siswa::query()
            ->with('kelas')
            ->where('is_active', true)
            ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
            ->first();

        if (!$siswaByEmail instanceof Siswa) {
            return null;
        }

        if ((int) ($siswaByEmail->user_id ?? 0) === 0) {
            Siswa::query()
                ->whereKey((int) $siswaByEmail->id)
                ->update(['user_id' => (int) $user->id]);
        }

        return $siswaByEmail;
    }

    public function create()
    {
        abort_unless($this->canManageJadwal(), 403);

        return view('akademik.jadwal-pelajaran.create', [
            'kelases' => Kelas::query()->orderBy('nama_kelas')->get(),
            'mataPelajarans' => MataPelajaran::dropdownOptions(),
            'gurus' => Guru::query()->where('is_active', true)->orderBy('nama')->get(),
            'ruangs' => Ruang::query()->where('is_active', true)->orderBy('nama_ruang')->get(),
            'hariOptions' => $this->hariOptions(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($this->canManageJadwal(), 403);

        $validated = $this->validateInput($request);
        $this->validateBentrok($validated);

        $ruang = Ruang::query()->find($validated['ruang_id']);

        JadwalPelajaran::create([
            ...$validated,
            'ruang' => $ruang?->nama,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('akademik.jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function edit(JadwalPelajaran $jadwalPelajaran)
    {
        abort_unless($this->canManageJadwal(), 403);

        return view('akademik.jadwal-pelajaran.edit', [
            'jadwalPelajaran' => $jadwalPelajaran,
            'kelases' => Kelas::query()->orderBy('nama_kelas')->get(),
            'mataPelajarans' => MataPelajaran::dropdownOptions(),
            'gurus' => Guru::query()->where('is_active', true)->orderBy('nama')->get(),
            'ruangs' => Ruang::query()->where('is_active', true)->orderBy('nama_ruang')->get(),
            'hariOptions' => $this->hariOptions(),
        ]);
    }

    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        abort_unless($this->canManageJadwal(), 403);

        $validated = $this->validateInput($request);
        $this->validateBentrok($validated, $jadwalPelajaran->id);

        $ruang = Ruang::query()->find($validated['ruang_id']);

        $jadwalPelajaran->update([
            ...$validated,
            'ruang' => $ruang?->nama,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('akademik.jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(JadwalPelajaran $jadwalPelajaran)
    {
        abort_unless($this->canManageJadwal(), 403);

        $jadwalPelajaran->delete();

        return redirect()->route('akademik.jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }

    private function validateInput(Request $request): array
    {
        $isIstirahat = $request->boolean('is_istirahat');

        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => ($isIstirahat ? 'nullable' : 'required') . '|exists:mata_pelajaran,id',
            'guru_id' => ($isIstirahat ? 'nullable' : 'required') . '|exists:guru,id',
            'ruang_id' => ($isIstirahat ? 'nullable' : 'required') . '|exists:ruang,id',
            'is_istirahat' => 'nullable|boolean',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_istirahat'] = $isIstirahat;

        if ($isIstirahat) {
            $validated['mata_pelajaran_id'] = null;
            $validated['guru_id'] = null;
            $validated['ruang_id'] = null;

            if (!filled($validated['keterangan'] ?? null)) {
                $validated['keterangan'] = 'Sesi Ishoma / Istirahat';
            }
        }

        return $validated;
    }

    private function validateBentrok(array $data, ?int $ignoreId = null): void
    {
        $kelasBentrok = JadwalPelajaran::query()
            ->where('kelas_id', $data['kelas_id'])
            ->where('hari', $data['hari'])
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if ($kelasBentrok) {
            throw ValidationException::withMessages([
                'jam_mulai' => 'Jadwal bentrok: kelas tersebut sudah memiliki jadwal pada rentang jam yang sama.',
            ]);
        }

        if (filled($data['guru_id'] ?? null)) {
            $guruBentrok = JadwalPelajaran::query()
                ->where('guru_id', $data['guru_id'])
                ->where('hari', $data['hari'])
                ->where('jam_mulai', '<', $data['jam_selesai'])
                ->where('jam_selesai', '>', $data['jam_mulai'])
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists();

            if ($guruBentrok) {
                throw ValidationException::withMessages([
                    'jam_mulai' => 'Jadwal bentrok: guru tersebut sudah mengajar pada rentang jam yang sama.',
                ]);
            }
        }

        if (filled($data['ruang_id'] ?? null)) {
            $ruangBentrok = JadwalPelajaran::query()
                ->where('ruang_id', $data['ruang_id'])
                ->where('hari', $data['hari'])
                ->where('jam_mulai', '<', $data['jam_selesai'])
                ->where('jam_selesai', '>', $data['jam_mulai'])
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists();

            if ($ruangBentrok) {
                throw ValidationException::withMessages([
                    'jam_mulai' => 'Jadwal bentrok: ruangan sudah dipakai pada rentang jam yang sama.',
                ]);
            }
        }
    }

    private function canViewJadwal(): bool
    {
        return is_admin()
            || auth()->user()->hasRole('Guru')
            || is_siswa()
            || auth()->user()->hasRole('Kepala Sekolah');
    }

    private function canManageJadwal(): bool
    {
        return is_admin();
    }

    private function hariOptions(): array
    {
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    }
}
