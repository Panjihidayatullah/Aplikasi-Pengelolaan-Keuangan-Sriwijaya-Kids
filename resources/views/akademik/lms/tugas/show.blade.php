@extends('layouts.app')

@section('title', 'Detail Tugas LMS')

@section('content')
<div class="container mx-auto px-4 py-6">
    <a href="{{ route('akademik.lms.tugas.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-block mb-4"><- Kembali ke daftar tugas</a>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-800">{{ $tugas->judul }}</h1>
        <p class="text-gray-600 mt-2">{{ $tugas->deskripsi ?: '-' }}</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm text-gray-600">
            <p><span class="font-semibold text-gray-700">Kelas:</span> {{ $tugas->kelas->nama ?? '-' }}</p>
            <p><span class="font-semibold text-gray-700">Mapel:</span> {{ $tugas->mataPelajaran->nama ?? '-' }}</p>
            <p><span class="font-semibold text-gray-700">Deadline:</span> {{ $tugas->tanggal_deadline?->format('d M Y H:i') }}</p>
            <p><span class="font-semibold text-gray-700">Nilai Maks:</span> {{ $tugas->max_nilai }}</p>
            <p><span class="font-semibold text-gray-700">Semester:</span> {{ $tugas->semester->nama ?? '-' }}</p>
            <p><span class="font-semibold text-gray-700">Tahun Ajaran:</span> {{ $tugas->tahunAjaran->nama ?? '-' }}</p>
        </div>

        @if($tugas->lampiran_path)
        <a href="{{ asset('storage/' . $tugas->lampiran_path) }}" target="_blank" class="inline-block mt-4 px-5 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-semibold">Unduh Lampiran Tugas</a>
        @endif

        @if(is_admin() || auth()->user()->hasRole('Guru'))
        <a href="{{ route('akademik.lms.tugas.edit', $tugas) }}" class="inline-block mt-4 ml-2 px-5 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold">Edit Tugas</a>
        @endif
    </div>

    @if($isMandiriSiswa)
        @if(!$myPengumpulan)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Kumpulkan Jawaban Tugas</h2>
            <form method="POST" action="{{ route('akademik.lms.pengumpulan.store', $tugas) }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Siswa</label>
                    <input
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100"
                        value="{{ $currentSiswa?->nama ? $currentSiswa->nama . ' (' . ($currentSiswa->kelas->nama ?? '-') . ')' : auth()->user()->name }}"
                        readonly
                    >
                    <input type="hidden" name="siswa_id" value="{{ $currentSiswa?->id }}">
                    @error('siswa_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">File Jawaban</label>
                    <input type="file" name="file_jawaban" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    @error('file_jawaban')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan_siswa" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('catatan_siswa') }}</textarea>
                </div>
                @if(!$currentSiswa)
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm">
                    Profil siswa untuk akun ini belum tersambung. Hubungi admin untuk menghubungkan akun ke data siswa.
                </div>
                @endif
                <button class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold" @disabled(!$currentSiswa)>Kirim Jawaban</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-3">Nilai Saya</h2>
            <p class="text-sm text-gray-600">Kamu belum mengumpulkan jawaban untuk tugas ini.</p>
        </div>
    </div>
        @else
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Pengumpulan Tersimpan</h2>
        <p class="text-sm text-gray-600">Form input disembunyikan karena jawaban sudah dikumpulkan. Untuk memperbarui, gunakan aksi Edit/Hapus pada tabel di bawah.</p>
    </div>
        @endif
    @else
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-2">
            {{ ($isKepalaSekolahReadOnly ?? false) ? 'Panel Monitoring Kepala Sekolah' : 'Panel Penilaian Guru' }}
        </h2>
        <p class="text-sm text-gray-600">
            @if($isKepalaSekolahReadOnly ?? false)
                Kepala Sekolah hanya dapat melihat detail pengumpulan dan hasil penilaian.
            @else
                Guru menilai dari tabel pengumpulan di bawah ini. Form kirim jawaban hanya untuk akun siswa, sehingga guru tidak bisa mengisi tugas atas nama siswa.
            @endif
        </p>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    @if(!$isMandiriSiswa)
                    <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Siswa</th>
                    @endif
                    <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Waktu Submit</th>
                    <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">File</th>
                    <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Status Penilaian</th>
                    <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Aksi & Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengumpulan as $item)
                <tr class="border-b align-top">
                    @if(!$isMandiriSiswa)
                    <td class="px-5 py-4 text-gray-800 font-semibold">{{ $item->siswa->nama ?? '-' }}</td>
                    @endif
                    <td class="px-5 py-4 text-gray-600 text-sm">{{ $item->submitted_at?->format('d M Y H:i') ?? '-' }}</td>
                    <td class="px-5 py-4 text-sm">
                        @if($item->file_jawaban_path)
                        <a href="{{ asset('storage/' . $item->file_jawaban_path) }}" target="_blank" class="text-cyan-600 hover:text-cyan-800 font-semibold">Lihat File</a>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm">
                        @if($item->nilai === null)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Belum Dinilai</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Sudah Dinilai</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($isKepalaSekolahReadOnly ?? false)
                        <div class="text-sm space-y-1">
                            <p><span class="font-semibold text-gray-700">Status:</span> {{ $item->nilai !== null ? 'Sudah Dinilai' : 'Belum Dinilai' }}</p>
                            <p><span class="font-semibold text-gray-700">Nilai:</span> {{ $item->nilai !== null ? number_format((float) $item->nilai, 2, ',', '.') . ' / ' . number_format((float) $tugas->max_nilai, 2, ',', '.') : 'Belum dinilai' }}</p>
                            <p><span class="font-semibold text-gray-700">Feedback:</span> {{ $item->feedback ?: '-' }}</p>
                            <p class="text-xs text-gray-500"><span class="font-semibold">Dinilai:</span> {{ $item->graded_at?->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                        @elseif(is_admin() || auth()->user()->hasRole('Guru'))
                            @if($item->nilai === null)
                        <form method="POST" action="{{ route('akademik.lms.pengumpulan.grade', $item) }}" class="space-y-2">
                            @csrf
                            <input type="number" step="0.01" min="0" max="{{ $tugas->max_nilai }}" name="nilai" value="{{ $item->nilai }}" class="w-28 px-3 py-1 border border-gray-300 rounded" required>
                            <textarea name="feedback" rows="2" class="w-full px-3 py-1 border border-gray-300 rounded" placeholder="Feedback guru">{{ $item->feedback }}</textarea>
                            <button class="px-4 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded font-semibold text-sm">Simpan Nilai</button>
                        </form>
                            @else
                        <div class="space-y-2 text-sm">
                            <div class="rounded-lg border border-green-200 bg-green-50 px-3 py-2">
                                <p class="font-semibold text-green-800">Riwayat Penilaian Tersimpan</p>
                                <p class="text-green-700 mt-1">Nilai: {{ number_format((float) $item->nilai, 2, ',', '.') }} / {{ number_format((float) $tugas->max_nilai, 2, ',', '.') }}</p>
                                <p class="text-green-700">Feedback: {{ $item->feedback ?: '-' }}</p>
                                <p class="text-xs text-green-700 mt-1">Diisi pada: {{ $item->graded_at?->format('d M Y H:i') ?? '-' }}</p>
                            </div>

                            <details class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2">
                                <summary class="cursor-pointer font-semibold text-amber-800">Edit Nilai</summary>
                                <form method="POST" action="{{ route('akademik.lms.pengumpulan.grade', $item) }}" class="space-y-2 mt-3">
                                    @csrf
                                    <input type="number" step="0.01" min="0" max="{{ $tugas->max_nilai }}" name="nilai" value="{{ $item->nilai }}" class="w-28 px-3 py-1 border border-gray-300 rounded" required>
                                    <textarea name="feedback" rows="2" class="w-full px-3 py-1 border border-gray-300 rounded" placeholder="Feedback guru">{{ $item->feedback }}</textarea>
                                    <div class="pt-1">
                                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm shadow-sm">Simpan Perubahan Nilai</button>
                                    </div>
                                </form>
                            </details>

                            <form method="POST" action="{{ route('akademik.lms.pengumpulan.ungrade', $item) }}" onsubmit="return confirm('Hapus nilai untuk pengumpulan ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="px-4 py-1 bg-red-600 hover:bg-red-700 text-white rounded font-semibold text-sm">Hapus Nilai</button>
                            </form>
                        </div>
                            @endif
                        @elseif($isMandiriSiswa)
                        <div class="text-sm space-y-1">
                            <p><span class="font-semibold text-gray-700">Status:</span> {{ $item->nilai !== null ? 'Sudah Dinilai' : 'Belum Dinilai' }}</p>
                            <p><span class="font-semibold text-gray-700">Nilai:</span> {{ $item->nilai !== null ? number_format((float) $item->nilai, 2, ',', '.') . ' / ' . number_format((float) $tugas->max_nilai, 2, ',', '.') : 'Belum dinilai' }}</p>
                            <p><span class="font-semibold text-gray-700">Feedback:</span> {{ $item->feedback ?: '-' }}</p>
                            <p class="text-xs text-gray-500"><span class="font-semibold">Dinilai:</span> {{ $item->graded_at?->format('d M Y H:i') ?? '-' }}</p>

                            <details class="mt-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2">
                                <summary class="cursor-pointer font-semibold text-amber-800">Edit Pengumpulan</summary>
                                <form method="POST" action="{{ route('akademik.lms.pengumpulan.update', ['tugas' => $tugas, 'pengumpulan' => $item]) }}" enctype="multipart/form-data" class="space-y-2 mt-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="file" name="file_jawaban" class="w-full px-3 py-1 border border-gray-300 rounded">
                                    <textarea name="catatan_siswa" rows="2" class="w-full px-3 py-1 border border-gray-300 rounded" placeholder="Catatan siswa">{{ old('catatan_siswa', $item->catatan_siswa ?? $item->keterangan_siswa) }}</textarea>
                                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm">Simpan Perubahan Pengumpulan</button>
                                </form>
                            </details>

                            <form method="POST" action="{{ route('akademik.lms.pengumpulan.destroy', ['tugas' => $tugas, 'pengumpulan' => $item]) }}" onsubmit="return confirm('Hapus pengumpulan ini?')" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button class="px-4 py-1 bg-red-600 hover:bg-red-700 text-white rounded font-semibold text-sm">Hapus Pengumpulan</button>
                            </form>
                        </div>
                        @else
                        <div class="text-sm space-y-1">
                            <p><span class="font-semibold text-gray-700">Status:</span> {{ $item->nilai !== null ? 'Sudah Dinilai' : 'Belum Dinilai' }}</p>
                            <p><span class="font-semibold text-gray-700">Nilai:</span> {{ $item->nilai !== null ? number_format((float) $item->nilai, 2, ',', '.') . ' / ' . number_format((float) $tugas->max_nilai, 2, ',', '.') : 'Belum dinilai' }}</p>
                            <p><span class="font-semibold text-gray-700">Feedback:</span> {{ $item->feedback ?: '-' }}</p>
                            <p class="text-xs text-gray-500"><span class="font-semibold">Dinilai:</span> {{ $item->graded_at?->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $isMandiriSiswa ? 4 : 5 }}" class="px-5 py-8 text-center text-gray-500">Belum ada pengumpulan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pengumpulan->hasPages())
    <div class="mt-6">{{ $pengumpulan->links() }}</div>
    @endif
</div>
@endsection
