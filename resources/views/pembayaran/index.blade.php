@extends('layouts.app')

@section('title', 'Data Pembayaran - ' . config('app.name'))
@section('page-title', 'Data Pembayaran')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Data Pembayaran</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola pembayaran siswa (SPP, Uang Gedung, dll)</p>
        </div>
        <a href="{{ route('pembayaran.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Input Pembayaran
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('pembayaran.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}"
                           placeholder="Kode transaksi, NIS, atau nama siswa..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" 
                           name="tanggal_mulai" 
                           id="tanggal_mulai" 
                           value="{{ request('tanggal_mulai') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                </div>

                <!-- Tanggal Akhir -->
                <div>
                    <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                    <input type="date" 
                           name="tanggal_akhir" 
                           id="tanggal_akhir" 
                           value="{{ request('tanggal_akhir') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                </div>

                <!-- Jenis Pembayaran -->
                <div>
                    <label for="jenis_pembayaran_id" class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                    <select name="jenis_pembayaran_id" 
                            id="jenis_pembayaran_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                        <option value="">Semua Jenis</option>
                        @foreach($jenisPembayaran as $jenis)
                            <option value="{{ $jenis->id }}" {{ request('jenis_pembayaran_id') == $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Metode Bayar -->
                <div>
                    <label for="metode_bayar" class="block text-sm font-medium text-gray-700 mb-1">Metode</label>
                    <select name="metode_bayar" 
                            id="metode_bayar" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                        <option value="">Semua Metode</option>
                        <option value="Tunai" {{ request('metode_bayar') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="Transfer" {{ request('metode_bayar') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="QRIS" {{ request('metode_bayar') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter Data
                </button>
                <a href="{{ route('pembayaran.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset Filter
                </a>
                @if(request()->hasAny(['search', 'tanggal_mulai', 'tanggal_akhir', 'jenis_pembayaran_id', 'metode_bayar', 'status']))
                    <span class="text-sm text-gray-600">
                        <strong>{{ $pembayaran->total() }}</strong> hasil ditemukan
                    </span>
                @endif
            </div>
        </form>
    </div>

    <!-- Stats by Metode Bayar -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Semua -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-600 mb-1">Total Pembayaran</p>
                    <p class="text-lg font-bold text-gray-900 break-words">{{ format_rupiah($totalSemua) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg flex-shrink-0 ml-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Tunai -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Tunai</p>
                    <p class="text-lg font-bold text-blue-600 mt-1">{{ number_format($totalTunai, 0, ',', '.') }} transaksi</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Transfer -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Transfer</p>
                    <p class="text-lg font-bold text-indigo-600 mt-1">{{ number_format($totalTransfer, 0, ',', '.') }} transaksi</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- QRIS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">QRIS</p>
                    <p class="text-lg font-bold text-purple-600 mt-1">{{ number_format($totalQRIS, 0, ',', '.') }} transaksi</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pembayaran as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->tanggal_bayar ? $item->tanggal_bayar->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $item->siswa->nama ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->jenis->nama ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">{{ format_rupiah($item->jumlah ?? 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ $item->metode_bayar ?? '-' }}</span></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('pembayaran.show', $item->id ?? 1) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data pembayaran</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan pembayaran baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($pembayaran->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pembayaran->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
