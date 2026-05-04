<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\TranskripsNilai;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LmsPengumpulanController extends Controller
{
    public function store(Request $request, Tugas $tugas)
    {
        abort_if($this->isKepalaSekolahReadOnly(), 403, 'Kepala Sekolah hanya dapat melihat detail tugas.');

        $isMandiriSiswa = is_siswa() && !is_admin() && !auth()->user()->hasRole('Guru');
        abort_unless($isMandiriSiswa, 403);

        $validated = $request->validate([
            'siswa_id' => 'nullable|exists:siswa,id',
            'file_jawaban' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar,jpg,jpeg,png|max:51200',
            'catatan_siswa' => 'nullable|string',
        ]);

        $currentSiswa = $this->resolveCurrentSiswa();
        if (!$currentSiswa) {
            return back()->withErrors([
                'siswa_id' => 'Profil siswa untuk akun ini tidak ditemukan. Hubungi admin untuk sinkron data siswa.',
            ])->withInput();
        }

        $siswaId = (int) $currentSiswa->id;

        if ($tugas->kelas_id) {
            $existsInKelas = Siswa::where('id', $siswaId)
                ->where('kelas_id', $tugas->kelas_id)
                ->exists();

            if (!$existsInKelas) {
                return back()->withErrors(['siswa_id' => 'Siswa tidak terdaftar pada kelas tugas ini.'])->withInput();
            }
        }

        $path = $request->file('file_jawaban')->store('lms/pengumpulan', 'public');
        $deadline = $tugas->tanggal_deadline ?? $tugas->deadline;
        $status = $deadline && now()->gt($deadline) ? 'terlambat' : 'tepat_waktu';

        $existingPengumpulan = PengumpulanTugas::query()
            ->where('tugas_id', $tugas->id)
            ->where('siswa_id', $siswaId)
            ->first();

        if ($existingPengumpulan) {
            return back()->withErrors([
                'file_jawaban' => 'Jawaban sudah pernah dikumpulkan. Gunakan fitur edit jika ingin memperbarui.',
            ])->withInput();
        }

        PengumpulanTugas::create([
            'tugas_id' => $tugas->id,
            'siswa_id' => $siswaId,
            'file_jawaban' => $path,
            'file_jawaban_path' => $path,
            'keterangan_siswa' => $validated['catatan_siswa'] ?? null,
            'catatan_siswa' => $validated['catatan_siswa'] ?? null,
            'tanggal_kumpul' => now(),
            'submitted_at' => now(),
            'status' => $status,
        ]);

        return back()->with('success', 'Jawaban tugas berhasil dikumpulkan');
    }

    public function update(Request $request, Tugas $tugas, PengumpulanTugas $pengumpulan)
    {
        abort_if($this->isKepalaSekolahReadOnly(), 403, 'Kepala Sekolah hanya dapat melihat detail tugas.');

        $isMandiriSiswa = is_siswa() && !is_admin() && !auth()->user()->hasRole('Guru');
        abort_unless($isMandiriSiswa, 403);

        $currentSiswa = $this->resolveCurrentSiswa();
        if (!$currentSiswa) {
            abort(403);
        }

        abort_unless((int) $pengumpulan->tugas_id === (int) $tugas->id, 403);
        abort_unless((int) $pengumpulan->siswa_id === (int) $currentSiswa->id, 403);

        $validated = $request->validate([
            'file_jawaban' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar,jpg,jpeg,png|max:51200',
            'catatan_siswa' => 'nullable|string',
        ]);

        if (!$request->hasFile('file_jawaban') && empty($pengumpulan->file_jawaban_path)) {
            return back()->withErrors([
                'file_jawaban' => 'File jawaban wajib tersedia. Upload file jika file lama tidak ada.',
            ])->withInput();
        }

        $path = $pengumpulan->file_jawaban_path;
        if ($request->hasFile('file_jawaban')) {
            if (!empty($pengumpulan->file_jawaban_path)) {
                Storage::disk('public')->delete($pengumpulan->file_jawaban_path);
            }
            $path = $request->file('file_jawaban')->store('lms/pengumpulan', 'public');
        }

        $deadline = $tugas->tanggal_deadline ?? $tugas->deadline;
        $status = $deadline && now()->gt($deadline) ? 'terlambat' : 'tepat_waktu';

        $pengumpulan->update([
            'file_jawaban' => $path,
            'file_jawaban_path' => $path,
            'keterangan_siswa' => $validated['catatan_siswa'] ?? null,
            'catatan_siswa' => $validated['catatan_siswa'] ?? null,
            'tanggal_kumpul' => now(),
            'submitted_at' => now(),
            'status' => $status,
            'nilai' => null,
            'feedback' => null,
            'dinilai_oleh' => null,
            'graded_by_guru_id' => null,
            'tanggal_dinilai' => null,
            'graded_at' => null,
        ]);

        return back()->with('success', 'Pengumpulan berhasil diperbarui. Nilai sebelumnya direset dan menunggu penilaian ulang guru.');
    }

    public function destroy(Tugas $tugas, PengumpulanTugas $pengumpulan)
    {
        abort_if($this->isKepalaSekolahReadOnly(), 403, 'Kepala Sekolah hanya dapat melihat detail tugas.');

        $isMandiriSiswa = is_siswa() && !is_admin() && !auth()->user()->hasRole('Guru');
        abort_unless($isMandiriSiswa, 403);

        $currentSiswa = $this->resolveCurrentSiswa();
        if (!$currentSiswa) {
            abort(403);
        }

        abort_unless((int) $pengumpulan->tugas_id === (int) $tugas->id, 403);
        abort_unless((int) $pengumpulan->siswa_id === (int) $currentSiswa->id, 403);

        if (!empty($pengumpulan->file_jawaban_path)) {
            Storage::disk('public')->delete($pengumpulan->file_jawaban_path);
        }

        $pengumpulan->delete();

        return back()->with('success', 'Pengumpulan berhasil dihapus. Kamu bisa upload ulang jawaban jika diperlukan.');
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

    public function grade(Request $request, PengumpulanTugas $pengumpulan)
    {
        abort_if($this->isKepalaSekolahReadOnly(), 403, 'Kepala Sekolah hanya dapat melihat detail tugas.');
        abort_unless(can_access('grade lms-tugas') || is_admin() || auth()->user()->hasRole('Guru'), 403);

        $pengumpulan->load('tugas');

        $validated = $request->validate([
            'nilai' => 'required|numeric|min:0|max:' . ($pengumpulan->tugas->max_nilai ?? 100),
            'feedback' => 'nullable|string',
        ]);

        $pengumpulan->update([
            'nilai' => $validated['nilai'],
            'feedback' => $validated['feedback'] ?? null,
            'dinilai_oleh' => auth()->id(),
            'graded_by_guru_id' => optional(auth()->user()->guru)->id,
            'tanggal_dinilai' => now(),
            'graded_at' => now(),
        ]);

        $this->syncNilaiHarianToTranskrip($pengumpulan);
        $this->notifyNilaiDirilis($pengumpulan);

        return back()->with('success', 'Penilaian berhasil disimpan dan disinkronkan ke transkrip');
    }

    public function ungrade(PengumpulanTugas $pengumpulan)
    {
        abort_if($this->isKepalaSekolahReadOnly(), 403, 'Kepala Sekolah hanya dapat melihat detail tugas.');
        abort_unless(can_access('grade lms-tugas') || is_admin() || auth()->user()->hasRole('Guru'), 403);

        $pengumpulan->update([
            'nilai' => null,
            'feedback' => null,
            'dinilai_oleh' => null,
            'graded_by_guru_id' => null,
            'tanggal_dinilai' => null,
            'graded_at' => null,
        ]);

        $this->syncNilaiHarianToTranskrip($pengumpulan);

        return back()->with('success', 'Nilai pengumpulan berhasil dihapus.');
    }

    private function syncNilaiHarianToTranskrip(PengumpulanTugas $pengumpulan): void
    {
        $pengumpulan->load('tugas');
        $tugas = $pengumpulan->tugas;

        if (!$tugas || !$tugas->mata_pelajaran_id || !$tugas->semester_id || !$tugas->tahun_ajaran_id) {
            return;
        }

        $avgNilaiHarian = PengumpulanTugas::query()
            ->where('siswa_id', $pengumpulan->siswa_id)
            ->whereNotNull('nilai')
            ->whereHas('tugas', function ($q) use ($tugas) {
                $q->where('mata_pelajaran_id', $tugas->mata_pelajaran_id)
                    ->where('semester_id', $tugas->semester_id)
                    ->where('tahun_ajaran_id', $tugas->tahun_ajaran_id);
            })
            ->avg('nilai');

        $transkrip = TranskripsNilai::firstOrNew([
            'siswa_id' => $pengumpulan->siswa_id,
            'mata_pelajaran_id' => $tugas->mata_pelajaran_id,
            'semester_id' => $tugas->semester_id,
            'tahun_ajaran_id' => $tugas->tahun_ajaran_id,
        ]);

        $transkrip->nilai_harian = round((float) ($avgNilaiHarian ?? 0), 2);
        $transkrip->nilai_uts = $transkrip->nilai_uts ?? 0;
        $transkrip->nilai_uas = $transkrip->nilai_uas ?? 0;

        $hasilNilai = TranskripsNilai::hitungNilaiDanGrade(
            (float) $transkrip->nilai_harian,
            (float) $transkrip->nilai_uts,
            (float) $transkrip->nilai_uas
        );

        $transkrip->nilai_akhir = $hasilNilai['nilai_akhir'];
        $transkrip->grade = $hasilNilai['grade'];

        $transkrip->save();
    }

    private function notifyNilaiDirilis(PengumpulanTugas $pengumpulan): void
    {
        $pengumpulan->loadMissing(['siswa', 'tugas.guru']);

        $recipientIds = collect();

        if ($pengumpulan->siswa?->user_id) {
            $recipientIds->push((int) $pengumpulan->siswa->user_id);
        }

        $guruUserId = (int) ($pengumpulan->tugas?->guru?->user_id ?? 0);
        if ($guruUserId > 0) {
            $recipientIds->push($guruUserId);
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
                'judul' => 'Nilai Tugas Diperbarui',
                'isi' => 'Penilaian tugas telah diinput dan tersinkron ke transkrip nilai.',
                'tipe' => 'nilai',
                'terkait_dengan' => PengumpulanTugas::class,
                'terkait_id' => $pengumpulan->id,
                'is_read' => false,
            ]);
        }
    }

    private function isKepalaSekolahReadOnly(): bool
    {
        return auth()->user()?->hasRole('Kepala Sekolah') && !is_admin();
    }
}
