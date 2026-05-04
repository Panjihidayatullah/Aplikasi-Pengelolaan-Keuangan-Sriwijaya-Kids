@extends('layouts.app')

@section('title', 'Detail Kurikulum')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $kurikulum->nama }}</h1>
            <p class="text-gray-600 mt-1">Detail kurikulum</p>
        </div>
        <div class="flex gap-3">
            @can('edit kurikulum')
            <a href="{{ route('akademik.kurikulum.edit', $kurikulum) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition">
                Edit
            </a>
            @endcan
            @can('delete kurikulum')
            <form method="POST" action="{{ route('akademik.kurikulum.destroy', $kurikulum) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                    Hapus
                </button>
            </form>
            @endcan
        </div>
    </div>

    <!-- Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Informasi Kurikulum</h2>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">Nama</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $kurikulum->nama }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Deskripsi</p>
                        <p class="text-gray-800">{{ $kurikulum->deskripsi ?? '-' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Tahun Berlaku</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $kurikulum->tahun_berlaku }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            @if($kurikulum->is_active)
                            <p class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Aktif</p>
                            @else
                            <p class="inline-block px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">Nonaktif</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Dibuat</p>
                            <p class="text-gray-800">{{ $kurikulum->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Diperbarui</p>
                            <p class="text-gray-800">{{ $kurikulum->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Tahun Ajaran -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Tahun Ajaran</h3>
                
                @if($kurikulum->tahunAjaran->count() > 0)
                <ul class="space-y-2">
                    @foreach($kurikulum->tahunAjaran as $tahun)
                    <li class="p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                        <a href="{{ route('akademik.tahun-ajaran.show', $tahun) }}" class="text-blue-500 hover:text-blue-700 font-semibold">
                            {{ $tahun->nama }}
                        </a>
                        <p class="text-xs text-gray-600">{{ $tahun->tanggal_mulai->format('d M Y') }} - {{ $tahun->tanggal_selesai->format('d M Y') }}</p>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-center text-gray-500 py-4">Belum ada tahun ajaran</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('akademik.kurikulum.index') }}" class="inline-flex items-center text-blue-500 hover:text-blue-700 font-semibold">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Kurikulum
        </a>
    </div>
</div>
@endsection
