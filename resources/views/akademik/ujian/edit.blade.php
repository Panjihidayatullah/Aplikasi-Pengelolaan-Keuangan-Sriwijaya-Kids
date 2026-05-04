@extends('layouts.app')

@section('title', 'Edit Jadwal Ujian')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Jadwal Ujian</h1>
        <p class="text-gray-600 mt-1">Perbarui data jadwal ujian</p>
    </div>

    <div class="bg-white rounded-lg shadow p-8 md:p-10 max-w-6xl w-full mx-auto">
        <form method="POST" action="{{ route('akademik.ujian.update', $ujian) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-8">
                <!-- Mata Pelajaran -->
                <div>
                    <label for="mata_pelajaran_id" class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran</label>
                    <select id="mata_pelajaran_id" name="mata_pelajaran_id" class="w-full px-4 py-2 border @error('mata_pelajaran_id') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                        @foreach($mataPelajarans as $mp)
                        <option value="{{ $mp->id }}" @selected(old('mata_pelajaran_id', $ujian->mata_pelajaran_id) == $mp->id)>{{ $mp->nama }}</option>
                        @endforeach
                    </select>
                    @error('mata_pelajaran_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kelas -->
                <div>
                    <label for="kelas_id" class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                    <select id="kelas_id" name="kelas_id" class="w-full px-4 py-2 border @error('kelas_id') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                        @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}" @selected(old('kelas_id', $ujian->kelas_id) == $kelas->id)>{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Semester -->
                <div>
                    <label for="semester_id" class="block text-sm font-semibold text-gray-700 mb-2">Semester</label>
                    <select id="semester_id" name="semester_id" class="w-full px-4 py-2 border @error('semester_id') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                        @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" @selected(old('semester_id', $ujian->semester_id) == $sem->id)>{{ $sem->nama }} ({{ $sem->tahunAjaran->nama ?? '-' }})</option>
                        @endforeach
                    </select>
                    @error('semester_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Ujian -->
                <div>
                    <label for="jenis_ujian" class="block text-sm font-semibold text-gray-700 mb-2">Jenis Ujian</label>
                    <select id="jenis_ujian" name="jenis_ujian" class="w-full px-4 py-2 border @error('jenis_ujian') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                        <option value="UTS" @selected(old('jenis_ujian', $ujian->jenis_ujian) == 'UTS')>UTS</option>
                        <option value="UAS" @selected(old('jenis_ujian', $ujian->jenis_ujian) == 'UAS')>UAS</option>
                        <option value="Quiz" @selected(old('jenis_ujian', $ujian->jenis_ujian) == 'Quiz')>Quiz</option>
                    </select>
                    @error('jenis_ujian')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Ujian -->
                <div>
                    <label for="tanggal_ujian" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Ujian</label>
                    <input type="date" id="tanggal_ujian" name="tanggal_ujian" value="{{ old('tanggal_ujian', optional($ujian->tanggal_ujian)->format('Y-m-d')) }}" class="w-full px-4 py-2 border @error('tanggal_ujian') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                    @error('tanggal_ujian')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ruang -->
                <div>
                    <label for="ruang" class="block text-sm font-semibold text-gray-700 mb-2">Ruang</label>
                    <select id="ruang" name="ruang" class="w-full px-4 py-2 border @error('ruang') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                        <option value="">-- Pilih Ruangan --</option>
                        @foreach($ruangs as $ruangItem)
                        <option value="{{ $ruangItem->nama }}" @selected(old('ruang', $ujian->ruang) == $ruangItem->nama)>{{ $ruangItem->nama }} (Kapasitas: {{ $ruangItem->kapasitas }})</option>
                        @endforeach
                    </select>
                    @error('ruang')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jam Mulai -->
                <div>
                    <label for="jam_mulai" class="block text-sm font-semibold text-gray-700 mb-2">Jam Mulai</label>
                    <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', \Illuminate\Support\Str::substr((string) $ujian->jam_mulai, 0, 5)) }}" class="w-full px-4 py-2 border @error('jam_mulai') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                    @error('jam_mulai')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jam Selesai -->
                <div>
                    <label for="jam_selesai" class="block text-sm font-semibold text-gray-700 mb-2">Jam Selesai</label>
                    <input type="time" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', \Illuminate\Support\Str::substr((string) $ujian->jam_selesai, 0, 5)) }}" class="w-full px-4 py-2 border @error('jam_selesai') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                    @error('jam_selesai')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                <textarea id="catatan" name="catatan" rows="3" class="w-full px-4 py-2 border @error('catatan') border-red-400 @else border-gray-300 @enderror rounded-lg">{{ old('catatan', $ujian->catatan) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">Simpan Perubahan</button>
                <a href="{{ route('akademik.ujian.show', $ujian) }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
