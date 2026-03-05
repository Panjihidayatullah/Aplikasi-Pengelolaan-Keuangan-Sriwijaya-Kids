@extends('layouts.app')

@section('title', 'Detail Aset - ' . config('app.name'))
@section('page-title', 'Detail Aset')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden mb-8">
        <div class="px-8 py-6 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">Detail Aset</h3>
                <p class="mt-2 text-sm text-slate-600">Informasi lengkap aset sekolah</p>
            </div>
            <div class="flex items-center space-x-2">
                @if($aset->kondisi == 'Baik')
                    <span class="px-4 py-2 bg-green-100 text-green-700 rounded-xl text-sm font-semibold inline-flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $aset->kondisi }}
                    </span>
                @elseif($aset->kondisi == 'Rusak Ringan')
                    <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-xl text-sm font-semibold inline-flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $aset->kondisi }}
                    </span>
                @else
                    <span class="px-4 py-2 bg-red-100 text-red-700 rounded-xl text-sm font-semibold inline-flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        {{ $aset->kondisi }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Data Aset -->
            <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <h4 class="text-lg font-bold text-slate-800">Informasi Aset</h4>
                </div>
                <div class="p-8 space-y-6">
                    <div class="flex items-start py-3">
                        <div class="w-2/5">
                            <p class="text-sm font-semibold text-slate-500">Nama Aset</p>
                        </div>
                        <div class="w-3/5">
                            <p class="text-base font-bold text-purple-600">{{ $aset->nama }}</p>
                        </div>
                    </div>

                    <div class="flex items-start py-3 border-t border-slate-100">
                        <div class="w-2/5">
                            <p class="text-sm font-semibold text-slate-500">Kategori</p>
                        </div>
                        <div class="w-3/5">
                            @if($aset->kategori == 'elektronik')
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Elektronik
                                </span>
                            @elseif($aset->kategori == 'furniture')
                                <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-lg text-sm font-semibold inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Furniture
                                </span>
                            @elseif($aset->kategori == 'kendaraan')
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    Kendaraan
                                </span>
                            @elseif($aset->kategori == 'gedung')
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-sm font-semibold inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Gedung
                                </span>
                            @else
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm font-semibold inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Lainnya
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-start py-3 border-t border-slate-100">
                        <div class="w-2/5">
                            <p class="text-sm font-semibold text-slate-500">Harga Perolehan</p>
                        </div>
                        <div class="w-3/5">
                            <p class="text-3xl font-bold text-purple-600">Rp {{ number_format($aset->harga_perolehan, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start py-3 border-t border-slate-100">
                        <div class="w-2/5">
                            <p class="text-sm font-semibold text-slate-500">Tanggal Perolehan</p>
                        </div>
                        <div class="w-3/5">
                            <p class="text-base text-slate-800">{{ $aset->tanggal_perolehan->format('d F Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start py-3 border-t border-slate-100">
                        <div class="w-2/5">
                            <p class="text-sm font-semibold text-slate-500">Kondisi</p>
                        </div>
                        <div class="w-3/5">
                            <p class="text-base font-semibold text-slate-800">{{ $aset->kondisi }}</p>
                        </div>
                    </div>

                    <div class="flex items-start py-3 border-t border-slate-100">
                        <div class="w-2/5">
                            <p class="text-sm font-semibold text-slate-500">Lokasi</p>
                        </div>
                        <div class="w-3/5">
                            <p class="text-base text-slate-800">{{ $aset->lokasi }}</p>
                        </div>
                    </div>

                    @if($aset->keterangan)
                    <div class="flex items-start py-3 border-t border-slate-100">
                        <div class="w-2/5">
                            <p class="text-sm font-semibold text-slate-500">Keterangan</p>
                        </div>
                        <div class="w-3/5">
                            <p class="text-base text-slate-700">{{ $aset->keterangan }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Data Penanggung Jawab -->
            @if($aset->user)
            <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <h4 class="text-lg font-bold text-slate-800">Penanggung Jawab</h4>
                </div>
                <div class="p-8 space-y-6">
                    <div class="flex items-start py-3">
                        <div class="w-2/5">
                            <p class="text-sm font-semibold text-slate-500">Nama</p>
                        </div>
                        <div class="w-3/5">
                            <p class="text-base font-semibold text-slate-800">{{ $aset->user->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-start py-3 border-t border-slate-100">
                        <div class="w-2/5">
                            <p class="text-sm font-semibold text-slate-500">Email</p>
                        </div>
                        <div class="w-3/5">
                            <p class="text-base text-slate-800">{{ $aset->user->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Timeline -->
            <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <h4 class="text-lg font-bold text-slate-800">Timeline</h4>
                </div>
                <div class="p-6 space-y-5">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Dibuat</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $aset->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    @if($aset->updated_at != $aset->created_at)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Terakhir Diupdate</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $aset->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <h4 class="text-lg font-bold text-slate-800">Aksi</h4>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('aset.index') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar
                    </a>

                    <a href="{{ route('aset.edit', $aset->id) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>

                    <form action="{{ route('aset.destroy', $aset->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus aset ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg shadow-purple-200 overflow-hidden text-white">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-bold">Nilai Aset</h4>
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold">Rp {{ number_format($aset->harga_perolehan, 0, ',', '.') }}</p>
                    <p class="text-sm text-white/80 mt-2">{{ $aset->tanggal_perolehan->format('d F Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
