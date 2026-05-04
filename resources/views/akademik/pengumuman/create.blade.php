@extends('layouts.app')

@section('title', 'Buat Pengumuman')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Buat Pengumuman</h1>
        <p class="text-gray-600 mt-1">Buat pengumuman baru untuk sekolah</p>
    </div>

    <!-- Form -->
    <div class="max-w-6xl w-full mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8 md:p-10">
            <form method="POST" action="{{ route('akademik.pengumuman.store') }}">
                @csrf

                <!-- Judul -->
                <div class="mb-6">
                    <label for="judul" class="block text-sm font-semibold text-gray-800 mb-2">
                        Judul <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul') }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Judul pengumuman..." required>
                    @error('judul')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Isi -->
                <div class="mb-6">
                    <label for="isi" class="block text-sm font-semibold text-gray-800 mb-2">
                        Isi Pengumuman <span class="text-red-500">*</span>
                    </label>
                    <textarea id="isi" name="isi" rows="6"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Tuliskan isi pengumuman..." required>{{ old('isi') }}</textarea>
                    @error('isi')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div class="mb-6">
                    <label for="kategori" class="block text-sm font-semibold text-gray-800 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="kategori" name="kategori" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="ujian" {{ old('kategori') == 'ujian' ? 'selected' : '' }}>Ujian</option>
                        <option value="libur" {{ old('kategori') == 'libur' ? 'selected' : '' }}>Libur</option>
                        <option value="kegiatan" {{ old('kategori') == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                        <option value="pengumuman" {{ old('kategori') == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                    </select>
                    @error('kategori')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Publikasi -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-800 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        @error('tanggal_mulai')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-semibold text-gray-800 mb-2">
                            Tanggal Selesai (Opsional)
                        </label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('tanggal_selesai')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Publish -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-500 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-semibold text-gray-800">Publikasikan pengumuman</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Jika dicentang, pengumuman akan langsung ditampilkan</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition font-semibold">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Pengumuman
                    </button>
                    <a href="{{ route('akademik.pengumuman.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-400 hover:bg-gray-500 text-white rounded-lg transition font-semibold">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
