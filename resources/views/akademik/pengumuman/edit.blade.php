@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Pengumuman</h1>
        <p class="text-gray-600 mt-1">Ubah informasi pengumuman</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-8 md:p-10 max-w-6xl w-full mx-auto">
        <form method="POST" action="{{ route('akademik.pengumuman.update', $pengumuman->id) }}">
            @csrf
            @method('PUT')

            <!-- Judul -->
            <div class="mb-6">
                <label for="judul" class="block text-sm font-semibold text-gray-700 mb-2">Judul</label>
                <input type="text" id="judul" name="judul" value="{{ old('judul', $pengumuman->judul) }}" class="w-full px-4 py-2 border @error('judul') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" placeholder="Judul pengumuman" required>
                @error('judul')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Isi -->
            <div class="mb-6">
                <label for="isi" class="block text-sm font-semibold text-gray-700 mb-2">Isi Pengumuman</label>
                <textarea id="isi" name="isi" rows="6" class="w-full px-4 py-2 border @error('isi') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" placeholder="Isi pengumuman..." required>{{ old('isi', $pengumuman->isi) }}</textarea>
                @error('isi')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori -->
            <div class="mb-6">
                <label for="kategori" class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                <select id="kategori" name="kategori" class="w-full px-4 py-2 border @error('kategori') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="ujian" @selected(old('kategori', $pengumuman->kategori) == 'ujian')>Ujian</option>
                    <option value="libur" @selected(old('kategori', $pengumuman->kategori) == 'libur')>Libur</option>
                    <option value="kegiatan" @selected(old('kategori', $pengumuman->kategori) == 'kegiatan')>Kegiatan</option>
                    <option value="pengumuman" @selected(old('kategori', $pengumuman->kategori) == 'pengumuman')>Pengumuman</option>
                </select>
                @error('kategori')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Mulai -->
            <div class="mb-6">
                <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="datetime-local" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $pengumuman->tanggal_mulai?->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2 border @error('tanggal_mulai') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                @error('tanggal_mulai')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Selesai -->
            <div class="mb-6">
                <label for="tanggal_selesai" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="datetime-local" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $pengumuman->tanggal_selesai?->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2 border @error('tanggal_selesai') border-red-400 @else border-gray-300 @enderror rounded-lg focus:outline-none focus:border-blue-500" required>
                @error('tanggal_selesai')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Published -->
            <div class="mb-6 flex items-center gap-3">
                <input type="checkbox" id="is_published" name="is_published" value="1" @checked(old('is_published', $pengumuman->is_published)) class="w-5 h-5 rounded">
                <label for="is_published" class="text-sm font-semibold text-gray-700">Publikasikan Segera</label>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('akademik.pengumuman.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
