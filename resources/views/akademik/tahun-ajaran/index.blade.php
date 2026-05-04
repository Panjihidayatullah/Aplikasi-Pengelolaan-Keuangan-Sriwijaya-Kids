@extends('layouts.app')

@section('title', 'Daftar Tahun Ajaran')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Tahun Ajaran</h1>
            <p class="text-gray-600 mt-1">Kelola tahun akademik sekolah</p>
        </div>
        @can('manage tahun-ajaran')
        <a href="{{ route('akademik.tahun-ajaran.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition font-semibold">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Tahun Ajaran
        </a>
        @endcan
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Nama</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Kurikulum</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Periode</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tahunAjaran as $item)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-800 font-semibold">{{ $item->nama }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->kurikulum->nama }}</td>
                    <td class="px-6 py-4 text-gray-600">
                        <small>
                            {{ $item->tanggal_mulai->format('d M Y') }} - {{ $item->tanggal_selesai->format('d M Y') }}
                        </small>
                    </td>
                    <td class="px-6 py-4">
                        @if($item->is_active)
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Aktif</span>
                        @else
                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 align-middle">
                        <div class="inline-flex items-center gap-3 whitespace-nowrap">
                            <a href="{{ route('akademik.tahun-ajaran.show', $item) }}" class="text-blue-500 hover:text-blue-700 font-semibold text-sm">
                                Lihat
                            </a>
                            @can('manage tahun-ajaran')
                            <a href="{{ route('akademik.tahun-ajaran.edit', $item) }}" class="text-yellow-500 hover:text-yellow-700 font-semibold text-sm">
                                Edit
                            </a>
                            @if($item->is_active)
                            <form method="POST" action="{{ route('akademik.tahun-ajaran.set-inactive', $item) }}" class="inline-flex" onsubmit="return confirm('Nonaktifkan tahun ajaran ini?')">
                                @csrf
                                <button type="submit" class="bg-transparent border-0 p-0 appearance-none shadow-none text-gray-500 hover:text-gray-700 font-semibold text-sm">
                                    Nonaktifkan
                                </button>
                            </form>
                            @endif
                            @if(!$item->is_active)
                            <form method="POST" action="{{ route('akademik.tahun-ajaran.set-active', $item) }}" class="inline-flex" onsubmit="return confirm('Jadikan tahun ajaran aktif?')">
                                @csrf
                                <button type="submit" class="bg-transparent border-0 p-0 appearance-none shadow-none text-green-500 hover:text-green-700 font-semibold text-sm">
                                    Aktifkan
                                </button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('akademik.tahun-ajaran.destroy', $item) }}" class="inline-flex" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-transparent border-0 p-0 appearance-none shadow-none text-red-500 hover:text-red-700 font-semibold text-sm">
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
                        Belum ada tahun ajaran
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($tahunAjaran->hasPages())
    <div class="mt-6">
        {{ $tahunAjaran->links() }}
    </div>
    @endif
</div>
@endsection
