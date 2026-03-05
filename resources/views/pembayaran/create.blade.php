@extends('layouts.app')

@section('title', 'Input Pembayaran - ' . config('app.name'))
@section('page-title', 'Input Pembayaran')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="px-8 py-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
            <h3 class="text-2xl font-bold text-slate-800">Form Input Pembayaran</h3>
            <p class="mt-1 text-sm text-slate-500">Catat pembayaran siswa dengan lengkap</p>
        </div>

        <form action="{{ route('pembayaran.store') }}" method="POST" class="p-8">
            @csrf

            <div class="space-y-6">
                <!-- Siswa -->
                <div>
                    <label for="siswa_id" class="block text-sm font-semibold text-slate-700 mb-2">
                        Siswa <span class="text-red-500">*</span>
                    </label>
                    <select name="siswa_id" 
                            id="siswa_id" 
                            required
                            class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 bg-white @error('siswa_id') border-red-500 @enderror">
                        <option value="">Pilih Siswa</option>
                        @foreach(\App\Models\Siswa::with('kelas')->orderBy('nama')->get() as $siswa)
                            <option value="{{ $siswa->id }}" {{ old('siswa_id') == $siswa->id ? 'selected' : '' }}>
                                {{ $siswa->nis }} - {{ $siswa->nama }} ({{ $siswa->kelas->nama_kelas ?? 'Tanpa Kelas' }})
                            </option>
                        @endforeach
                    </select>
                    @error('siswa_id')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Jenis Pembayaran -->
                <div>
                    <label for="jenis_pembayaran_id" class="block text-sm font-semibold text-slate-700 mb-2">
                        Jenis Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_pembayaran_id" 
                            id="jenis_pembayaran_id" 
                            required
                            class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 bg-white @error('jenis_pembayaran_id') border-red-500 @enderror">
                        <option value="">Pilih Jenis Pembayaran</option>
                        @foreach(\App\Models\JenisPembayaran::orderBy('nama')->get() as $jenis)
                            <option value="{{ $jenis->id }}" 
                                    data-jumlah="{{ $jenis->nominal_default }}" 
                                    {{ old('jenis_pembayaran_id') == $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->nama }} - Rp {{ number_format($jenis->nominal_default, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_pembayaran_id')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Jumlah -->
                <div>
                    <label for="jumlah" class="block text-sm font-semibold text-slate-700 mb-2">
                        Jumlah Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">Rp</span>
                        <input type="number" 
                               name="jumlah" 
                               id="jumlah" 
                               value="{{ old('jumlah') }}" 
                               required
                               min="0"
                               step="1000"
                               placeholder="0"
                               class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('jumlah') border-red-500 @enderror">
                    </div>
                    <p class="mt-2 text-xs text-slate-500 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Jumlah akan otomatis terisi sesuai jenis pembayaran
                    </p>
                    @error('jumlah')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Tanggal Bayar -->
                <div>
                    <label for="tanggal_bayar" class="block text-sm font-semibold text-slate-700 mb-2">
                        Tanggal Bayar <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_bayar" 
                           id="tanggal_bayar" 
                           value="{{ old('tanggal_bayar', date('Y-m-d')) }}" 
                           required
                           class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('tanggal_bayar') border-red-500 @enderror">
                    @error('tanggal_bayar')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <label for="metode_pembayaran" class="block text-sm font-semibold text-slate-700 mb-2">
                        Metode Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <select name="metode_pembayaran" 
                            id="metode_pembayaran" 
                            required
                            class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 bg-white @error('metode_pembayaran') border-red-500 @enderror">
                        <option value="tunai" {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="qris" {{ old('metode_pembayaran') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    </select>
                    @error('metode_pembayaran')
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
                <a href="{{ route('pembayaran.index') }}" 
                   class="px-6 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-slate-200 transition-all duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/40 hover:shadow-xl hover:shadow-blue-500/50 focus:outline-none focus:ring-4 focus:ring-blue-500/50 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Pembayaran</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('jenis_pembayaran_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const jumlah = selected.getAttribute('data-jumlah');
    if (jumlah) {
        document.getElementById('jumlah').value = jumlah;
    }
});
</script>
@endsection
