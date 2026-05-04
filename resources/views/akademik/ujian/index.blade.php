@extends('layouts.app')

@section('title', 'Jadwal Ujian')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Jadwal Ujian</h1>
        <p class="text-gray-600 mt-1">Kelola ujian dan jadwal</p>
        <div class="mt-4">
            @can('create ujian')
            <a href="{{ route('akademik.ujian.create') }}" class="inline-flex items-center px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                Tambah Jadwal
            </a>
            @endcan
        </div>
    </div>

    <!-- Calendar/List View -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Mata Pelajaran</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Kelas</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Jenis Ujian</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Tanggal & Jam</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Ruang</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ujian as $item)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-800 font-semibold">{{ $item->mataPelajaran->nama ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->kelas->nama ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-3 py-1 
                            @if($item->jenis_ujian == 'UTS') bg-blue-100 text-blue-800
                            @elseif($item->jenis_ujian == 'UAS') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif
                            rounded text-xs font-semibold">
                            {{ $item->jenis_ujian }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <div class="text-sm">
                            <p class="font-semibold">{{ $item->tanggal_ujian->format('d M Y') }}</p>
                            <p class="text-xs">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->ruang ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('akademik.ujian.show', $item) }}" class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Detail</a>
                            @can('edit ujian')
                            <a href="{{ route('akademik.ujian.edit', $item) }}" class="text-yellow-500 hover:text-yellow-700 font-semibold text-sm">Edit</a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        Belum ada jadwal ujian
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($ujian->hasPages())
    <div class="mt-6">
        {{ $ujian->links() }}
    </div>
    @endif
</div>
@endsection
