@extends('layouts.app')

@section('title', 'Tambah Kurikulum')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Tambah Kurikulum</h1>
        <p class="text-gray-600 mt-1">Buat kurikulum baru untuk sekolah</p>
    </div>

    <!-- Form -->
    <div class="max-w-6xl w-full mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8 md:p-10">
            <form method="POST" action="{{ route('akademik.kurikulum.store') }}">
                @csrf

                <!-- Nama -->
                <div class="mb-6">
                    <label for="nama" class="block text-sm font-semibold text-gray-800 mb-2">
                        Nama Kurikulum <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: K-13 Revisi 2020" required>
                    @error('nama')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <label for="deskripsi" class="block text-sm font-semibold text-gray-800 mb-2">
                        Deskripsi
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Deskripsi kurikulum...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun Berlaku -->
                <div class="mb-6">
                    <label for="tahun_berlaku" class="block text-sm font-semibold text-gray-800 mb-2">
                        Tahun Berlaku <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="tahun_berlaku" name="tahun_berlaku" value="{{ old('tahun_berlaku') }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="2025" min="2020" max="2050" required>
                    @error('tahun_berlaku')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-500 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-semibold text-gray-800">Aktifkan kurikulum ini</span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition font-semibold">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Simpan
                    </button>
                    <a href="{{ route('akademik.kurikulum.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-400 hover:bg-gray-500 text-white rounded-lg transition font-semibold">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
