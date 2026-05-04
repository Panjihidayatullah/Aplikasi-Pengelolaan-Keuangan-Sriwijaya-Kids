@extends('layouts.app')

@section('title', 'Tambah Tahun Ajaran')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Tambah Tahun Ajaran</h1>
        <p class="text-gray-600 mt-1">Buat tahun akademik baru</p>
    </div>

    <!-- Form -->
    <div class="max-w-6xl w-full mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8 md:p-10">
            <form method="POST" action="{{ route('akademik.tahun-ajaran.store') }}">
                @csrf

                <!-- Kurikulum -->
                <div class="mb-6">
                    <label for="kurikulum_id" class="block text-sm font-semibold text-gray-800 mb-2">
                        Kurikulum <span class="text-red-500">*</span>
                    </label>
                    <select id="kurikulum_id" name="kurikulum_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Pilih Kurikulum --</option>
                        @foreach($kurikulum as $k)
                        <option value="{{ $k->id }}" {{ old('kurikulum_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('kurikulum_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama -->
                <div class="mb-6">
                    <label for="nama" class="block text-sm font-semibold text-gray-800 mb-2">
                        Nama Tahun Ajaran <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: 2025/2026" required>
                    @error('nama')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun Mulai & Selesai -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="tahun_mulai" class="block text-sm font-semibold text-gray-800 mb-2">
                            Tahun Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="tahun_mulai" name="tahun_mulai" value="{{ old('tahun_mulai') }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="2025" required>
                        @error('tahun_mulai')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="tahun_selesai" class="block text-sm font-semibold text-gray-800 mb-2">
                            Tahun Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="tahun_selesai" name="tahun_selesai" value="{{ old('tahun_selesai') }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="2026" required>
                        @error('tahun_selesai')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tanggal Mulai & Selesai -->
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
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        @error('tanggal_selesai')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Is Active -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-500 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-semibold text-gray-800">Aktifkan tahun ajaran ini</span>
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
                    <a href="{{ route('akademik.tahun-ajaran.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-400 hover:bg-gray-500 text-white rounded-lg transition font-semibold">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
