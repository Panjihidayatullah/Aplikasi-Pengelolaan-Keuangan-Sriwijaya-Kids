@extends('layouts.app')

@section('title', 'Edit Ruang')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Ruang</h1>
        <p class="text-gray-600 mt-1">Perbarui data ruang untuk manajemen jadwal.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-8 md:p-10 max-w-6xl w-full mx-auto">
        <form method="POST" action="{{ route('akademik.ruang.update', $ruang) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Ruang <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_ruang" value="{{ old('kode_ruang', $ruang->kode_ruang) }}" class="w-full px-4 py-2 border @error('kode_ruang') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                    @error('kode_ruang')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Ruang <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_ruang" value="{{ old('nama_ruang', $ruang->nama_ruang) }}" class="w-full px-4 py-2 border @error('nama_ruang') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                    @error('nama_ruang')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi</label>
                    <input type="text" name="lokasi" value="{{ old('lokasi', $ruang->lokasi) }}" class="w-full px-4 py-2 border @error('lokasi') border-red-400 @else border-gray-300 @enderror rounded-lg">
                    @error('lokasi')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kapasitas</label>
                    <input type="number" min="1" name="kapasitas" value="{{ old('kapasitas', $ruang->kapasitas) }}" class="w-full px-4 py-2 border @error('kapasitas') border-red-400 @else border-gray-300 @enderror rounded-lg">
                    @error('kapasitas')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border @error('keterangan') border-red-400 @else border-gray-300 @enderror rounded-lg">{{ old('keterangan', $ruang->keterangan) }}</textarea>
                @error('keterangan')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-end pb-2">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700 font-semibold">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $ruang->is_active))>
                    Ruang aktif
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">Simpan Perubahan</button>
                <a href="{{ route('akademik.ruang.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
