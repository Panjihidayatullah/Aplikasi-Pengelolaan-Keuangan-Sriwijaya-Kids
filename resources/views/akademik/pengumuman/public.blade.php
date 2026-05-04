@extends('layouts.app')

@section('title', 'Pengumuman')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Pengumuman Akademik</h1>
        <p class="text-gray-600 mt-2">Informasi terbaru dari sekolah</p>
    </div>

    <!-- Pengumuman List -->
    <div class="space-y-6">
        @forelse($pengumuman as $item)
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $item->judul }}</h2>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="inline-block px-3 py-1 text-xs rounded 
                            @if($item->kategori == 'ujian') bg-red-100 text-red-800
                            @elseif($item->kategori == 'libur') bg-green-100 text-green-800
                            @elseif($item->kategori == 'kegiatan') bg-cyan-100 text-cyan-800
                            @else bg-blue-100 text-blue-800 @endif
                            font-semibold">
                            {{ ucfirst($item->kategori) }}
                        </span>
                        <span class="text-sm text-gray-500">{{ $item->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="text-gray-700 leading-relaxed">
                {{ $item->isi }}
            </div>

            @if($item->tanggal_mulai && $item->tanggal_selesai)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    <strong>Periode:</strong> {{ $item->tanggal_mulai->format('d M Y') }} - {{ $item->tanggal_selesai->format('d M Y') }}
                </p>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0018 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-500 text-lg">Belum ada pengumuman</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($pengumuman->hasPages())
    <div class="mt-8">
        {{ $pengumuman->links() }}
    </div>
    @endif
</div>
@endsection
