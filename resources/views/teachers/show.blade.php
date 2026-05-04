@extends('layouts.app')

@section('title', 'Detail Guru - ' . config('app.name'))
@section('page-title', 'Detail Guru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Breadcrumb and Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('guru.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-xl transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">Detail Profil Guru</h2>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('guru.edit', $guru->id) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit
            </a>
            <form action="{{ route('guru.destroy', $guru->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data guru ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left Column: Profile Card -->
        <div class="flex flex-col space-y-6">
            <!-- Main Profile Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="h-32 bg-gradient-to-r from-blue-500 to-cyan-500"></div>
                <div class="px-6 pb-6 relative">
                    <div class="-mt-12 mb-4 flex justify-center">
                        <div class="relative">
                            @if($guru->foto)
                                <img src="{{ Storage::url($guru->foto) }}" alt="{{ $guru->nama }}" class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-lg bg-white">
                            @else
                                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-blue-600 font-bold text-3xl border-4 border-white shadow-lg">
                                    {{ strtoupper(substr($guru->nama, 0, 2)) }}
                                </div>
                            @endif
                            
                            @if($guru->is_active)
                                <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-green-500 border-2 border-white rounded-full" title="Status: Aktif"></div>
                            @else
                                <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-red-500 border-2 border-white rounded-full" title="Status: Nonaktif"></div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-extrabold text-gray-900">{{ $guru->nama }}</h3>
                        <p class="text-sm text-gray-500 font-medium mb-3">NIP: {{ $guru->nip }}</p>
                        @if($guru->pendidikan_terakhir)
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                {{ $guru->pendidikan_terakhir }}
                            </div>
                        @else
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-500 border border-gray-200">
                                Pendidikan belum diatur
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-3">
                        <div class="flex items-center text-sm">
                            <div class="w-8 flex-shrink-0 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <span class="text-gray-600 truncate" title="{{ $guru->email ?: '-' }}">{{ $guru->email ?: '-' }}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <div class="w-8 flex-shrink-0 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <span class="text-gray-600">{{ $guru->telepon ?: '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Login Account Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex-1 flex flex-col">
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Informasi Akun Login
                </h4>
                
                @if($guru->user)
                    <div class="space-y-3">
                        <div>
                            <span class="block text-xs font-medium text-gray-500 uppercase">Status Akun</span>
                            <div class="mt-1 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                <span class="text-sm font-medium text-gray-900">Aktif & Terhubung</span>
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Email Login</span>
                            <span class="block mt-1 text-sm text-gray-900 font-bold break-all bg-gray-50 p-2 rounded-lg border border-gray-100">{{ $guru->user->email }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Role</span>
                            <span class="inline-flex mt-1 items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                Guru
                            </span>
                        </div>
                    </div>
                    <div class="mt-auto"></div>
                @else
                    <div class="flex flex-col items-center justify-center py-4 text-center">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Belum Punya Akun</p>
                        <p class="text-xs text-gray-500 mt-1">Guru ini belum bisa login ke sistem.</p>
                        <a href="{{ route('guru.edit', $guru->id) }}" class="mt-4 inline-flex items-center px-3 py-1.5 border border-blue-600 text-xs font-medium rounded-lg text-blue-600 hover:bg-blue-50 transition-colors">
                            Buat Akun Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Details & Stats -->
        <div class="md:col-span-2 flex flex-col space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80 flex items-center gap-3">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Informasi Pribadi</h3>
                </div>
                <div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-0">
                        <div class="p-5 sm:p-6 border-b sm:border-r border-gray-100">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Jenis Kelamin</dt>
                            <dd class="text-sm text-gray-900 font-bold flex items-center gap-2">
                                @if($guru->jenis_kelamin === 'L')
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Laki-laki
                                @else
                                    <span class="w-2 h-2 rounded-full bg-pink-500"></span> Perempuan
                                @endif
                            </dd>
                        </div>
                        <div class="p-5 sm:p-6 border-b border-gray-100">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Tanggal Lahir</dt>
                            <dd class="text-sm text-gray-900 font-bold">
                                {{ $guru->tanggal_lahir ? $guru->tanggal_lahir->format('d F Y') : '-' }}
                                @if($guru->tanggal_lahir)
                                    <span class="text-gray-500 font-normal text-xs ml-1 bg-gray-100 px-2 py-0.5 rounded-full">{{ \Carbon\Carbon::parse($guru->tanggal_lahir)->age }} tahun</span>
                                @endif
                            </dd>
                        </div>
                        <div class="p-5 sm:p-6 sm:col-span-2 bg-gray-50/30">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Alamat Lengkap</dt>
                            <dd class="text-sm text-gray-800 leading-relaxed bg-white p-4 rounded-xl border border-gray-100 shadow-sm">{{ $guru->alamat ?: 'Alamat belum diatur' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Optional: Gaji Summary -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex-1 flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 text-green-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Ringkasan Gaji</h3>
                    </div>
                    <a href="{{ route('gaji-guru.index', ['guru_id' => $guru->id]) }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold flex items-center bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                        Lihat Riwayat
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-center">
                    @php
                        $latestGaji = App\Models\GajiGuru::with('pengeluaran')
                            ->where('guru_id', $guru->id)
                            ->orderByDesc('periode_tahun')
                            ->orderByDesc('periode_bulan')
                            ->first();
                    @endphp
                    
                    @if($latestGaji)
                        <div class="flex items-center justify-between p-4 bg-green-50 border border-green-100 rounded-xl">
                            <div class="flex-1">
                                <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-white/60 text-green-800 text-xs font-bold uppercase tracking-wider mb-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Pembayaran Terakhir
                                </div>
                                <p class="text-sm font-extrabold text-gray-900">Periode {{ str_pad($latestGaji->periode_bulan, 2, '0', STR_PAD_LEFT) }}/{{ $latestGaji->periode_tahun }}</p>
                                <p class="text-xs font-medium text-gray-600 mt-1 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ optional($latestGaji->pengeluaran->tanggal)->format('d M Y') ?? '-' }}
                                </p>
                            </div>
                            <div class="text-right flex flex-col justify-center">
                                <p class="text-xl font-black text-green-700 tracking-tight">{{ format_rupiah((float) ($latestGaji->pengeluaran->jumlah ?? 0)) }}</p>
                                <a href="{{ route('gaji-guru.show', $latestGaji->id) }}" class="text-xs font-bold text-green-700 hover:text-green-900 hover:underline mt-2 inline-flex items-center justify-end gap-1">
                                    <span>Lihat Slip</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-sm font-medium text-gray-900">Belum ada riwayat gaji</p>
                            <p class="text-xs text-gray-500 mt-1">Belum ada catatan pembayaran gaji untuk guru ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
