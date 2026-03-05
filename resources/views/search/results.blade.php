@extends('layouts.app')

@section('title', 'Hasil Pencarian - ' . config('app.name'))
@section('page-title', 'Hasil Pencarian')

@section('content')
<div class="space-y-6">
    <!-- Search Info -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Hasil Pencarian</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Menampilkan hasil untuk "<span class="font-semibold text-blue-600">{{ $query }}</span>"
                </p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-blue-600">{{ $totalResults }}</p>
                <p class="text-xs text-slate-500">Total Hasil</p>
            </div>
        </div>
    </div>

    @if($totalResults === 0)
        <!-- No Results -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-20 h-20 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-xl font-semibold text-slate-700 mb-2">Tidak Ada Hasil</h3>
            <p class="text-slate-500">Tidak ditemukan hasil untuk pencarian "{{ $query }}"</p>
            <a href="{{ route('dashboard') }}" class="inline-block mt-6 px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                Kembali ke Dashboard
            </a>
        </div>
    @else
        <!-- Siswa Results -->
        @if($siswa->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Data Siswa
                    </h3>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">{{ $siswa->count() }}</span>
                </div>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($siswa as $s)
                <a href="{{ route('siswa.show', $s->id) }}" class="block px-6 py-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-white font-bold">
                                {{ substr($s->nama, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $s->nama }}</p>
                                <p class="text-sm text-slate-500">NIS: {{ $s->nis }} • {{ $s->kelas->nama_kelas ?? '-' }}</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Kelas Results -->
        @if($kelas->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Data Kelas
                    </h3>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">{{ $kelas->count() }}</span>
                </div>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($kelas as $k)
                <a href="{{ route('kelas.show', $k->id) }}" class="block px-6 py-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $k->nama_kelas }}</p>
                            <p class="text-sm text-slate-500">Wali Kelas: {{ $k->wali_kelas ?? '-' }} • Tingkat {{ $k->tingkat }}</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Pembayaran Results -->
        @if($pembayaran->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-green-50 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pembayaran
                    </h3>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold">{{ $pembayaran->count() }}</span>
                </div>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($pembayaran as $p)
                <a href="{{ route('pembayaran.show', $p->id) }}" class="block px-6 py-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $p->siswa->nama ?? '-' }}</p>
                            <p class="text-sm text-slate-500">{{ $p->jenisPembayaran->nama ?? '-' }} • Rp {{ number_format($p->jumlah, 0, ',', '.') }}</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Pengeluaran Results -->
        @if($pengeluaran->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-rose-50 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        </svg>
                        Pengeluaran
                    </h3>
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">{{ $pengeluaran->count() }}</span>
                </div>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($pengeluaran as $p)
                <a href="{{ route('pengeluaran.show', $p->id) }}" class="block px-6 py-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $p->keterangan }}</p>
                            <p class="text-sm text-slate-500">{{ $p->jenisPengeluaran->nama ?? '-' }} • Rp {{ number_format($p->jumlah, 0, ',', '.') }}</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Aset Results -->
        @if($aset->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Aset Sekolah
                    </h3>
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold">{{ $aset->count() }}</span>
                </div>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($aset as $a)
                <a href="{{ route('aset.show', $a->id) }}" class="block px-6 py-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $a->nama }}</p>
                            <p class="text-sm text-slate-500">{{ $a->kategori }} • {{ $a->lokasi }}</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
