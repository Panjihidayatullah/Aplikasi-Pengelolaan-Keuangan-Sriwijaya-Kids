@extends('layouts.app')

@section('title', 'Daftar Pengumuman')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Pengumuman Akademik</h1>
            <p class="text-gray-600 mt-1">Kelola pengumuman sekolah</p>
        </div>
        @can('create pengumuman')
        <a href="{{ route('akademik.pengumuman.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition font-semibold">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Pengumuman
        </a>
        @endcan
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Judul</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Kategori</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Tanggal</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Penulis</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengumuman as $item)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-800 font-semibold">{{ Str::limit($item->judul, 30) }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-2 py-1 text-xs rounded 
                            @if($item->kategori == 'ujian') bg-red-100 text-red-800
                            @elseif($item->kategori == 'libur') bg-green-100 text-green-800
                            @elseif($item->kategori == 'kegiatan') bg-cyan-100 text-cyan-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($item->kategori) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">
                        {{ $item->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->user->name }}</td>
                    <td class="px-6 py-4">
                        @if($item->is_published)
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Dipublikasikan</span>
                        @else
                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Draft</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('akademik.pengumuman.show', $item) }}" class="text-blue-500 hover:text-blue-700 font-semibold text-sm">
                                Lihat
                            </a>
                            @can('edit pengumuman')
                            <a href="{{ route('akademik.pengumuman.edit', $item) }}" class="text-yellow-500 hover:text-yellow-700 font-semibold text-sm">
                                Edit
                            </a>
                            @endcan
                            @can('delete pengumuman')
                            <form method="POST" action="{{ route('akademik.pengumuman.destroy', $item) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        Belum ada pengumuman
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($pengumuman->hasPages())
    <div class="mt-6">
        {{ $pengumuman->links() }}
    </div>
    @endif
</div>
@endsection
