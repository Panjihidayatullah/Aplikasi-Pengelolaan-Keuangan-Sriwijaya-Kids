@extends('layouts.app')

@section('title', 'Tambah Nilai Siswa')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Tambah Nilai Siswa</h1>
        <p class="text-gray-600 mt-1">Input nilai tugas, UTS, dan UAS untuk transkrip siswa</p>
    </div>

    <div class="bg-white rounded-lg shadow p-8 md:p-10 max-w-6xl w-full mx-auto">
        <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            Bobot aktif: Tugas {{ number_format((float) $bobot->bobot_tugas, 2) }}% | UTS {{ number_format((float) $bobot->bobot_uts, 2) }}% | UAS {{ number_format((float) $bobot->bobot_uas, 2) }}%
        </div>

        <form method="POST" action="{{ route('akademik.transkrip-nilai.store') }}">
            @csrf

            @if(old('redirect_mapel_id', $selectedMapelId))
                <input type="hidden" name="redirect_mapel_id" value="{{ old('redirect_mapel_id', $selectedMapelId) }}">
            @endif
            @if(old('redirect_kelas_id', $selectedKelasId))
                <input type="hidden" name="redirect_kelas_id" value="{{ old('redirect_kelas_id', $selectedKelasId) }}">
            @endif
            @if(old('redirect_semester_id', $selectedSemesterId))
                <input type="hidden" name="redirect_semester_id" value="{{ old('redirect_semester_id', $selectedSemesterId) }}">
            @endif
            @if(old('redirect_tahun_ajaran_id', $selectedTahunAjaranId))
                <input type="hidden" name="redirect_tahun_ajaran_id" value="{{ old('redirect_tahun_ajaran_id', $selectedTahunAjaranId) }}">
            @endif

            <div class="mb-6">
                <label for="siswa_id" class="block text-sm font-semibold text-gray-700 mb-2">Siswa</label>
                <select id="siswa_id" name="siswa_id" class="w-full px-4 py-2 border @error('siswa_id') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswas as $siswa)
                    <option value="{{ $siswa->id }}" @selected((string) old('siswa_id', $selectedSiswaId ?? null) === (string) $siswa->id)>{{ $siswa->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-6">
                    <label for="mata_pelajaran_id" class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran</label>
                    <select id="mata_pelajaran_id" name="mata_pelajaran_id" class="w-full px-4 py-2 border @error('mata_pelajaran_id') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mataPelajarans as $mp)
                        <option value="{{ $mp->id }}" @selected((string) old('mata_pelajaran_id', $selectedMapelId) === (string) $mp->id)>{{ $mp->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-6">
                    <label for="semester_id" class="block text-sm font-semibold text-gray-700 mb-2">Semester</label>
                    <select id="semester_id" name="semester_id" class="w-full px-4 py-2 border @error('semester_id') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                        <option value="">-- Pilih Semester --</option>
                        @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected((string) old('semester_id', $selectedSemesterId) === (string) $semester->id)>{{ $semester->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label for="tahun_ajaran_id" class="block text-sm font-semibold text-gray-700 mb-2">Tahun Ajaran</label>
                <select id="tahun_ajaran_id" name="tahun_ajaran_id" class="w-full px-4 py-2 border @error('tahun_ajaran_id') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" @selected((string) old('tahun_ajaran_id', $selectedTahunAjaranId) === (string) $ta->id)>{{ $ta->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="mb-6">
                    <label for="nilai_harian" class="block text-sm font-semibold text-gray-700 mb-2">Nilai Tugas</label>
                    <input type="number" step="0.01" min="0" max="100" id="nilai_harian" name="nilai_harian" value="{{ old('nilai_harian') }}" class="w-full px-4 py-2 border @error('nilai_harian') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                </div>
                <div class="mb-6">
                    <label for="nilai_uts" class="block text-sm font-semibold text-gray-700 mb-2">Nilai UTS</label>
                    <input type="number" step="0.01" min="0" max="100" id="nilai_uts" name="nilai_uts" value="{{ old('nilai_uts') }}" class="w-full px-4 py-2 border @error('nilai_uts') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                </div>
                <div class="mb-6">
                    <label for="nilai_uas" class="block text-sm font-semibold text-gray-700 mb-2">Nilai UAS</label>
                    <input type="number" step="0.01" min="0" max="100" id="nilai_uas" name="nilai_uas" value="{{ old('nilai_uas') }}" class="w-full px-4 py-2 border @error('nilai_uas') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">Simpan Nilai</button>
                @php
                    $cancelMapelId = old('redirect_mapel_id', $selectedMapelId);
                    $cancelKelasId = old('redirect_kelas_id', $selectedKelasId);
                    $cancelSemesterId = old('redirect_semester_id', $selectedSemesterId);
                    $cancelTahunAjaranId = old('redirect_tahun_ajaran_id', $selectedTahunAjaranId);
                @endphp
                <a href="{{ ($cancelKelasId && $cancelMapelId) ? route('akademik.transkrip-nilai.kelas-mapel', ['kelas' => $cancelKelasId, 'mataPelajaran' => $cancelMapelId, 'semester_id' => $cancelSemesterId, 'tahun_ajaran_id' => $cancelTahunAjaranId]) : route('akademik.transkrip-nilai.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
