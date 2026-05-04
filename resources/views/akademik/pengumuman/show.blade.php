@extends('layouts.app')

@section('title', $pengumuman->judul)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Link -->
    <a href="{{ route('akademik.pengumuman.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold mb-6 inline-block">← Kembali ke Pengumuman</a>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $pengumuman->judul }}</h1>
                <div class="flex items-center gap-4 mt-3">
                    <span class="inline-block px-3 py-1 
                        @switch($pengumuman->kategori)
                            @case('ujian') bg-blue-100 text-blue-800 @break
                            @case('libur') bg-cyan-100 text-cyan-800 @break
                            @case('kegiatan') bg-green-100 text-green-800 @break
                            @default bg-orange-100 text-orange-800
                        @endswitch
                        rounded text-sm font-semibold">
                        {{ ucfirst($pengumuman->kategori) }}
                    </span>
                    <span class="text-gray-600 text-sm">
                        Oleh: <strong>{{ $pengumuman->user->name ?? 'Admin' }}</strong>
                    </span>
                </div>
            </div>
            
            <!-- Actions -->
            @can('edit pengumuman')
            <div class="flex gap-2">
                <a href="{{ route('akademik.pengumuman.edit', $pengumuman->id) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold transition">
                    Edit
                </a>
                <form method="POST" action="{{ route('akademik.pengumuman.destroy', $pengumuman->id) }}" onsubmit="return confirm('Hapus pengumuman ini?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition">
                        Hapus
                    </button>
                </form>
            </div>
            @endcan
        </div>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-lg shadow p-8 mb-6">
        <!-- Tanggal Info -->
        <div class="mb-6 pb-6 border-b">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Tanggal Mulai</p>
                    <p class="text-gray-800 font-semibold">{{ $pengumuman->tanggal_mulai->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Tanggal Selesai</p>
                    <p class="text-gray-800 font-semibold">{{ $pengumuman->tanggal_selesai->format('d M Y H:i') }}</p>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm text-gray-600 font-semibold">Status Publikasi</p>
                <span class="inline-block px-3 py-1 
                    @if($pengumuman->is_published) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif
                    rounded text-sm font-semibold">
                    @if($pengumuman->is_published) Dipublikasikan @else Draft @endif
                </span>
            </div>
        </div>

        <!-- Isi Pengumuman -->
        <div>
            <h2 class="text-lg font-bold text-gray-800 mb-4">Isi Pengumuman</h2>
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                {!! nl2br(e($pengumuman->isi)) !!}
            </div>
        </div>
    </div>

    <!-- Meta Info -->
    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
        <p class="mb-1"><strong>Dibuat:</strong> {{ $pengumuman->created_at->format('d M Y H:i') }}</p>
        <p><strong>Diperbarui:</strong> {{ $pengumuman->updated_at->format('d M Y H:i') }}</p>
    </div>
</div>
@endsection
