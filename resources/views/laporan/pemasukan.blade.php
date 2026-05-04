@extends('layouts.app')

@section('title', 'Laporan Pemasukan - ' . config('app.name'))
@section('page-title', 'Laporan Pemasukan')

@section('content')
@php
    $selectedJenisPembayaranId = \App\Models\JenisPembayaran::representativeIdFor((int) request('jenis_pembayaran_id'))
        ?? request('jenis_pembayaran_id');
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Laporan Pemasukan</h2>
            <p class="mt-1 text-sm text-gray-600">Rincian pemasukan sekolah</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('laporan.export.pdf', ['type' => 'pemasukan', 'start_date' => request('start_date', now()->startOfMonth()->format('Y-m-d')), 'end_date' => request('end_date', now()->endOfMonth()->format('Y-m-d')), 'jenis_pembayaran_id' => request('jenis_pembayaran_id'), 'metode_bayar' => request('metode_bayar')]) }}" 
               target="_blank"
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Export PDF
            </a>
            <a href="{{ route('laporan.export.excel', ['type' => 'pemasukan', 'start_date' => request('start_date', now()->startOfMonth()->format('Y-m-d')), 'end_date' => request('end_date', now()->endOfMonth()->format('Y-m-d')), 'jenis_pembayaran_id' => request('jenis_pembayaran_id'), 'metode_bayar' => request('metode_bayar')]) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Data</h3>
        <form method="GET" action="{{ route('laporan.pemasukan') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Kode/NIS/Nama Siswa"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Tanggal Akhir -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Jenis Pembayaran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pembayaran</label>
                    <select name="jenis_pembayaran_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Jenis</option>
                        @foreach($jenisPembayaran as $jenis)
                            <option value="{{ $jenis->id }}" {{ (string) $selectedJenisPembayaranId === (string) $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Metode Bayar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Bayar</label>
                    <select name="metode_bayar" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Metode</option>
                        <option value="tunai" {{ request('metode_bayar') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ request('metode_bayar') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="qris" {{ request('metode_bayar') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('laporan.pemasukan') }}" 
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition-colors inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </a>
            </div>

            <!-- Result Counter -->
            <div class="text-sm text-gray-600">
                Menampilkan <span class="font-semibold text-gray-900">{{ number_format($pembayaran->count()) }}</span> data pemasukan
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <p class="text-sm text-gray-600">Total Pemasukan</p>
        <p class="text-3xl font-bold text-green-600">{{ format_rupiah($grandTotal) }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($pembayaran as $item)
                <tr>
                    <td class="px-6 py-4 text-sm">{{ $item->tanggal_bayar->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm">{{ $item->siswa->nama ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $item->jenis ? \App\Models\JenisPembayaran::normalizeNama($item->jenis->nama) : '-' }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-green-600">{{ format_rupiah($item->jumlah) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
