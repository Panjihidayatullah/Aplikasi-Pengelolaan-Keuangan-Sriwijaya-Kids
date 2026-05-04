@extends('layouts.app')

@section('title', 'Data Aset Sekolah - ' . config('app.name'))
@section('page-title', 'Data Aset Sekolah')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Data Aset Sekolah</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola aset dan inventaris sekolah</p>
        </div>
        <a href="{{ route('aset.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-purple-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Aset
        </a>
    </div>

    <!-- Filter Section -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form method="GET" action="{{ route('aset.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari nama aset, lokasi, atau keterangan..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                    </div>

                    <!-- Kategori Filter -->
                    <div>
                        <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="kategori" 
                                id="kategori" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                            <option value="">Semua Kategori</option>
                            <option value="Elektronik" {{ request('kategori') == 'Elektronik' ? 'selected' : '' }}>Elektronik</option>
                            <option value="Furniture" {{ request('kategori') == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                            <option value="Kendaraan" {{ request('kategori') == 'Kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                            <option value="Bangunan" {{ request('kategori') == 'Bangunan' ? 'selected' : '' }}>Gedung</option>
                            <option value="Lainnya" {{ request('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <!-- Kondisi Filter -->
                    <div>
                        <label for="kondisi" class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
                        <select name="kondisi" 
                                id="kondisi" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                            <option value="">Semua Kondisi</option>
                            <option value="Baik" {{ request('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak Ringan" {{ request('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ request('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>

                    <!-- Tanggal Perolehan -->
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" 
                               name="tanggal_mulai" 
                               id="tanggal_mulai" 
                               value="{{ request('tanggal_mulai') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filter Data
                    </button>
                    <a href="{{ route('aset.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset Filter
                    </a>
                    @if(request()->hasAny(['search', 'kategori', 'kondisi', 'tanggal_mulai', 'tanggal_akhir']))
                        <span class="text-sm text-gray-600">
                            <strong>{{ $aset->total() }}</strong> hasil ditemukan
                        </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <p class="text-sm text-gray-600">Total Aset</p>
            <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <p class="text-sm text-gray-600">Kondisi Baik</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['baik'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <p class="text-sm text-gray-600">Rusak Ringan</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['rusak_ringan'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <p class="text-sm text-gray-600">Rusak Berat</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['rusak_berat'] }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($aset as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium">{{ $item->nama ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->kategori ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ format_rupiah($item->harga_perolehan ?? 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(strtolower($item->kondisi) == 'baik')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Baik</span>
                            @elseif(strtolower($item->kondisi) == 'rusak ringan')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Rusak Ringan</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Rusak Berat</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $item->lokasi ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                            <a href="{{ route('aset.show', $item->id ?? 1) }}" class="text-gray-600 hover:text-gray-900 font-medium">Detail</a>
                            <a href="{{ route('aset.edit', $item->id ?? 1) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                            <form action="{{ route('aset.destroy', $item->id ?? 1) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aset ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data aset</h3>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($aset->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $aset->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
