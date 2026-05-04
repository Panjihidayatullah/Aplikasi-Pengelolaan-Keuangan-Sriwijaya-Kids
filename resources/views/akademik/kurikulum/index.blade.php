@extends('layouts.app')

@section('title', 'Daftar Kurikulum')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Kurikulum</h1>
            <p class="text-gray-600 mt-1">Kelola kurikulum sekolah</p>
        </div>
        @can('create kurikulum')
        <a href="{{ route('akademik.kurikulum.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition font-semibold">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kurikulum
        </a>
        @endcan
    </div>

    <!-- Search & Filter -->
    <div class="mb-6">
        <form method="GET" action="{{ route('akademik.kurikulum.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kurikulum..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Nama</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Tahun Berlaku</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kurikulum as $item)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-800 font-semibold">{{ $item->nama }}</td>
                    <td class="px-6 py-4 text-gray-600 text-sm">{{ Str::limit($item->deskripsi, 50) }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->tahun_berlaku }}</td>
                    <td class="px-6 py-4">
                        @if($item->is_active)
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Aktif</span>
                        @else
                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <div class="inline-flex items-center gap-3 whitespace-nowrap">
                            <a href="{{ route('akademik.kurikulum.show', $item) }}" class="text-blue-500 hover:text-blue-700 font-semibold text-sm">
                                Lihat
                            </a>
                            @can('edit kurikulum')
                            <a href="{{ route('akademik.kurikulum.edit', $item) }}" class="text-yellow-500 hover:text-yellow-700 font-semibold text-sm">
                                Edit
                            </a>
                            @endcan
                            @can('delete kurikulum')
                            <form method="POST" action="{{ route('akademik.kurikulum.destroy', $item) }}" class="inline-flex" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-sm">
                                    Hapus
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        Belum ada kurikulum
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($kurikulum->hasPages())
    <div class="mt-6">
        {{ $kurikulum->links() }}
    </div>
    @endif
</div>
@endsection
