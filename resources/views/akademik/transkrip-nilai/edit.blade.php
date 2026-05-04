@extends('layouts.app')

@section('title', 'Edit Nilai Siswa')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Nilai Siswa</h1>
        <p class="text-gray-600 mt-1">Perbarui nilai untuk {{ $transkripNilai->siswa->nama ?? '-' }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-8 md:p-10 max-w-6xl w-full mx-auto">
        <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            Bobot aktif: Tugas {{ number_format((float) $bobot->bobot_tugas, 2) }}% | UTS {{ number_format((float) $bobot->bobot_uts, 2) }}% | UAS {{ number_format((float) $bobot->bobot_uas, 2) }}%
        </div>

        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Siswa</p>
                <p class="font-semibold text-gray-800">{{ $transkripNilai->siswa->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Mata Pelajaran</p>
                <p class="font-semibold text-gray-800">{{ $transkripNilai->mataPelajaran->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Semester</p>
                <p class="font-semibold text-gray-800">{{ $transkripNilai->semester->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tahun Ajaran</p>
                <p class="font-semibold text-gray-800">{{ $transkripNilai->tahunAjaran->nama ?? '-' }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('akademik.transkrip-nilai.update', $transkripNilai) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="mb-6">
                    <label for="nilai_harian" class="block text-sm font-semibold text-gray-700 mb-2">Nilai Tugas</label>
                    <input type="number" step="0.01" min="0" max="100" id="nilai_harian" name="nilai_harian" value="{{ old('nilai_harian', $transkripNilai->nilai_harian) }}" class="w-full px-4 py-2 border @error('nilai_harian') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                </div>
                <div class="mb-6">
                    <label for="nilai_uts" class="block text-sm font-semibold text-gray-700 mb-2">Nilai UTS</label>
                    <input type="number" step="0.01" min="0" max="100" id="nilai_uts" name="nilai_uts" value="{{ old('nilai_uts', $transkripNilai->nilai_uts) }}" class="w-full px-4 py-2 border @error('nilai_uts') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                </div>
                <div class="mb-6">
                    <label for="nilai_uas" class="block text-sm font-semibold text-gray-700 mb-2">Nilai UAS</label>
                    <input type="number" step="0.01" min="0" max="100" id="nilai_uas" name="nilai_uas" value="{{ old('nilai_uas', $transkripNilai->nilai_uas) }}" class="w-full px-4 py-2 border @error('nilai_uas') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">Simpan Perubahan</button>
                <a href="{{ route('akademik.transkrip-nilai.kelas-mapel', ['kelas' => $transkripNilai->siswa->kelas_id, 'mataPelajaran' => $transkripNilai->mata_pelajaran_id, 'semester_id' => $transkripNilai->semester_id, 'tahun_ajaran_id' => $transkripNilai->tahun_ajaran_id]) }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
