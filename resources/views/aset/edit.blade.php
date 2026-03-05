@extends('layouts.app')

@section('title', 'Edit Aset - ' . config('app.name'))
@section('page-title', 'Edit Aset')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="px-8 py-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
            <h3 class="text-2xl font-bold text-slate-800">Form Edit Aset</h3>
            <p class="mt-1 text-sm text-slate-500">Perbarui informasi aset</p>
        </div>

        <form action="{{ route('aset.update', $aset->id) }}" method="POST" class="p-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Nama Aset -->
                <div>
                    <label for="nama" class="block text-sm font-semibold text-slate-700 mb-2">
                        Nama Aset <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama" 
                           id="nama" 
                           value="{{ old('nama', $aset->nama) }}" 
                           required
                           placeholder="Contoh: Laptop Dell Latitude 7420"
                           class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kategori -->
                    <div>
                        <label for="kategori" class="block text-sm font-semibold text-slate-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="kategori" 
                                id="kategori" 
                                required
                                class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 bg-white @error('kategori') border-red-500 @enderror">
                            <option value="">Pilih Kategori</option>
                            <option value="elektronik" {{ old('kategori', $aset->kategori) == 'elektronik' ? 'selected' : '' }}>Elektronik</option>
                            <option value="furniture" {{ old('kategori', $aset->kategori) == 'furniture' ? 'selected' : '' }}>Furniture</option>
                            <option value="kendaraan" {{ old('kategori', $aset->kategori) == 'kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                            <option value="gedung" {{ old('kategori', $aset->kategori) == 'gedung' ? 'selected' : '' }}>Gedung</option>
                            <option value="lainnya" {{ old('kategori', $aset->kategori) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('kategori')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Kondisi -->
                    <div>
                        <label for="kondisi" class="block text-sm font-semibold text-slate-700 mb-2">
                            Kondisi <span class="text-red-500">*</span>
                        </label>
                        <select name="kondisi" 
                                id="kondisi" 
                                required
                                class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 bg-white @error('kondisi') border-red-500 @enderror">
                            <option value="baik" {{ old('kondisi', strtolower($aset->kondisi)) == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak ringan" {{ old('kondisi', strtolower($aset->kondisi)) == 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak berat" {{ old('kondisi', strtolower($aset->kondisi)) == 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                        @error('kondisi')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tanggal Perolehan -->
                    <div>
                        <label for="tanggal_perolehan" class="block text-sm font-semibold text-slate-700 mb-2">
                            Tanggal Perolehan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="tanggal_perolehan" 
                               id="tanggal_perolehan" 
                               value="{{ old('tanggal_perolehan', $aset->tanggal_perolehan->format('Y-m-d')) }}" 
                               required
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('tanggal_perolehan') border-red-500 @enderror">
                        @error('tanggal_perolehan')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Harga Perolehan -->
                    <div>
                        <label for="harga_perolehan" class="block text-sm font-semibold text-slate-700 mb-2">
                            Harga Perolehan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">Rp</span>
                            <input type="number" 
                                   name="harga_perolehan" 
                                   id="harga_perolehan" 
                                   value="{{ old('harga_perolehan', $aset->harga_perolehan) }}" 
                                   required
                                   min="0"
                                   step="1000"
                                   placeholder="0"
                                   class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('harga_perolehan') border-red-500 @enderror">
                        </div>
                        @error('harga_perolehan')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Lokasi -->
                <div>
                    <label for="lokasi" class="block text-sm font-semibold text-slate-700 mb-2">
                        Lokasi
                    </label>
                    <input type="text" 
                           name="lokasi" 
                           id="lokasi" 
                           value="{{ old('lokasi', $aset->lokasi) }}"
                           placeholder="Contoh: Ruang Guru, Lab Komputer, Perpustakaan"
                           class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('lokasi') border-red-500 @enderror">
                    <p class="mt-2 text-xs text-slate-500 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Sebutkan lokasi penempatan aset untuk memudahkan pelacakan
                    </p>
                    @error('lokasi')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-semibold text-slate-700 mb-2">
                        Keterangan
                    </label>
                    <textarea name="keterangan" 
                              id="keterangan" 
                              rows="4"
                              placeholder="Catatan tambahan tentang aset ini..."
                              class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $aset->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-8 mt-8 border-t border-slate-100">
                <a href="{{ route('aset.index') }}" 
                   class="px-6 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-slate-200 transition-all duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/40 hover:shadow-xl hover:shadow-blue-500/50 focus:outline-none focus:ring-4 focus:ring-blue-500/50 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Update Aset</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
