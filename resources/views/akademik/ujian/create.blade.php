@extends('layouts.app')

@section('title', 'Buat Jadwal Ujian')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Buat Jadwal Ujian</h1>
        <p class="text-gray-600 mt-1">Tambah jadwal ujian baru</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-8 md:p-10 max-w-6xl w-full mx-auto">
        <form method="POST" action="{{ route('akademik.ujian.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-8">
                <!-- Mata Pelajaran -->
                <div>
                    <label for="mata_pelajaran_id" class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran <span class="text-red-500">*</span></label>
                    <select id="mata_pelajaran_id" name="mata_pelajaran_id" class="w-full px-4 py-2 border @error('mata_pelajaran_id') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @forelse($mataPelajarans as $mp)
                        <option value="{{ $mp->id }}" @selected(old('mata_pelajaran_id') == $mp->id)>{{ $mp->nama }}</option>
                        @empty
                        <option value="" disabled>Tidak ada mata pelajaran</option>
                        @endforelse
                    </select>
                    @error('mata_pelajaran_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kelas -->
                <div>
                    <label for="kelas_id" class="block text-sm font-semibold text-gray-700 mb-2">Kelas <span class="text-red-500">*</span></label>
                    <select id="kelas_id" name="kelas_id" class="w-full px-4 py-2 border @error('kelas_id') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                        <option value="">-- Pilih Kelas --</option>
                        @forelse($kelases as $kelas)
                        <option value="{{ $kelas->id }}" @selected(old('kelas_id') == $kelas->id)>{{ $kelas->nama }}</option>
                        @empty
                        <option value="" disabled>Tidak ada kelas</option>
                        @endforelse
                    </select>
                    @error('kelas_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Semester -->
                <div>
                    <label for="semester_id" class="block text-sm font-semibold text-gray-700 mb-2">Semester <span class="text-red-500">*</span></label>
                    <select id="semester_id" name="semester_id" class="w-full px-4 py-2 border @error('semester_id') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                        <option value="">-- Pilih Semester --</option>
                        @forelse($semesters as $sem)
                        <option value="{{ $sem->id }}" @selected(old('semester_id') == $sem->id)>{{ $sem->nama }} ({{ $sem->tahunAjaran->nama ?? '' }})</option>
                        @empty
                        <option value="" disabled>Tidak ada semester</option>
                        @endforelse
                    </select>
                    @error('semester_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Ujian -->
                <div>
                    <label for="jenis_ujian" class="block text-sm font-semibold text-gray-700 mb-2">Jenis Ujian <span class="text-red-500">*</span></label>
                    <select id="jenis_ujian" name="jenis_ujian" class="w-full px-4 py-2 border @error('jenis_ujian') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                        <option value="">-- Pilih Jenis Ujian --</option>
                        <option value="UTS" @selected(old('jenis_ujian') == 'UTS')>Ujian Tengah Semester (UTS)</option>
                        <option value="UAS" @selected(old('jenis_ujian') == 'UAS')>Ujian Akhir Semester (UAS)</option>
                        <option value="Quiz" @selected(old('jenis_ujian') == 'Quiz')>Quiz</option>
                    </select>
                    @error('jenis_ujian')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Ujian -->
                <div>
                    <label for="tanggal_ujian" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Ujian <span class="text-red-500">*</span></label>
                    <input type="date" id="tanggal_ujian" name="tanggal_ujian" value="{{ old('tanggal_ujian') }}" class="w-full px-4 py-2 border @error('tanggal_ujian') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                    @error('tanggal_ujian')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ruang -->
                <div>
                    <label for="ruang" class="block text-sm font-semibold text-gray-700 mb-2">Ruang <span class="text-red-500">*</span></label>
                    <select id="ruang" name="ruang" class="w-full px-4 py-2 border @error('ruang') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                        <option value="">-- Pilih Ruangan --</option>
                        @foreach($ruangs as $ruangItem)
                        <option value="{{ $ruangItem->nama }}" @selected(old('ruang') == $ruangItem->nama)>{{ $ruangItem->nama }} (Kapasitas: {{ $ruangItem->kapasitas }})</option>
                        @endforeach
                    </select>
                    @error('ruang')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jam Mulai -->
                <div>
                    <label for="jam_mulai" class="block text-sm font-semibold text-gray-700 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', '08:00') }}" class="w-full px-4 py-2 border @error('jam_mulai') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                    @error('jam_mulai')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jam Selesai -->
                <div>
                    <label for="jam_selesai" class="block text-sm font-semibold text-gray-700 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', '10:00') }}" class="w-full px-4 py-2 border @error('jam_selesai') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                    @error('jam_selesai')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Catatan -->
            <div class="mb-6">
                <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                <textarea id="catatan" name="catatan" rows="3" class="w-full px-4 py-2 border @error('catatan') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" placeholder="Catatan tambahan untuk ujian ini...">{{ old('catatan') }}</textarea>
                @error('catatan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                    Buat Jadwal Ujian
                </button>
                <a href="{{ route('akademik.ujian.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
