@extends('layouts.app')

@section('title', 'Tambah Kelas - ' . config('app.name'))
@section('page-title', 'Tambah Kelas')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="px-8 py-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
            <h3 class="text-2xl font-bold text-slate-800">Form Tambah Kelas</h3>
            <p class="mt-1 text-sm text-slate-500">Lengkapi informasi kelas baru di bawah ini</p>
        </div>

        <form action="{{ route('kelas.store') }}" method="POST" class="p-8">
            @csrf

            <div class="space-y-6">
                <!-- Nama Kelas -->
                <div>
                    <label for="nama_kelas" class="block text-sm font-semibold text-slate-700 mb-2">
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama_kelas" 
                           id="nama_kelas" 
                           value="{{ old('nama_kelas') }}" 
                           required
                           placeholder="Contoh: 7A, 8B, 9C"
                           class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('nama_kelas') border-red-500 @enderror">
                    <p class="mt-2 text-xs text-slate-500 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Format: [Tingkat][Rombel], misal: 7A, 8B, 9C
                    </p>
                    @error('nama_kelas')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Tingkat -->
                <div>
                    <label for="tingkat" class="block text-sm font-semibold text-slate-700 mb-2">
                        Tingkat <span class="text-red-500">*</span>
                    </label>
                    <select name="tingkat" 
                            id="tingkat" 
                            required
                            class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 bg-white @error('tingkat') border-red-500 @enderror">
                        <option value="">Pilih Tingkat Kelas</option>
                        <option value="7" {{ old('tingkat') == 7 ? 'selected' : '' }}>Tingkat 7 (SMP Kelas 1)</option>
                        <option value="8" {{ old('tingkat') == 8 ? 'selected' : '' }}>Tingkat 8 (SMP Kelas 2)</option>
                        <option value="9" {{ old('tingkat') == 9 ? 'selected' : '' }}>Tingkat 9 (SMP Kelas 3)</option>
                        <option value="10" {{ old('tingkat') == 10 ? 'selected' : '' }}>Tingkat 10 (SMA Kelas 1)</option>
                        <option value="11" {{ old('tingkat') == 11 ? 'selected' : '' }}>Tingkat 11 (SMA Kelas 2)</option>
                        <option value="12" {{ old('tingkat') == 12 ? 'selected' : '' }}>Tingkat 12 (SMA Kelas 3)</option>
                    </select>
                    @error('tingkat')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Wali Kelas -->
                <div>
                    <label for="wali_kelas" class="block text-sm font-semibold text-slate-700 mb-2">
                        Wali Kelas
                    </label>
                    <input type="text" 
                           name="wali_kelas" 
                           id="wali_kelas" 
                           value="{{ old('wali_kelas') }}"
                           placeholder="Nama wali kelas (opsional)"
                           class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('wali_kelas') border-red-500 @enderror">
                    @error('wali_kelas')
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
                <a href="{{ route('kelas.index') }}" 
                   class="px-6 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-slate-200 transition-all duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/40 hover:shadow-xl hover:shadow-blue-500/50 focus:outline-none focus:ring-4 focus:ring-blue-500/50 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Kelas</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
