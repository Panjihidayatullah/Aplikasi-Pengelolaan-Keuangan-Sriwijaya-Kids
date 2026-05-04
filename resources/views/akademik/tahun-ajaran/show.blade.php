@extends('layouts.app')

@section('title', $tahunAjaran->nama)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Link -->
    <a href="{{ route('akademik.tahun-ajaran.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold mb-6 inline-block">← Kembali ke Tahun Ajaran</a>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $tahunAjaran->nama }}</h1>
                <p class="text-gray-600 mt-2">Kurikulum: <span class="font-semibold">{{ $tahunAjaran->kurikulum->nama ?? '-' }}</span></p>
            </div>
            
            <!-- Status Badge -->
            <span class="inline-block px-4 py-2 
                @if($tahunAjaran->is_active) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif
                rounded-lg text-sm font-bold">
                @if($tahunAjaran->is_active) ✓ Aktif @else Tidak Aktif @endif
            </span>
        </div>
    </div>

    <!-- Grid Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Info Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Informasi Tahun Ajaran</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Nama Tahun Ajaran</p>
                    <p class="text-gray-800 font-bold text-lg">{{ $tahunAjaran->nama }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Tahun</p>
                    <p class="text-gray-800 font-semibold">{{ $tahunAjaran->tahun }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Kurikulum</p>
                    <p class="text-gray-800 font-semibold">{{ $tahunAjaran->kurikulum->nama ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Deskripsi Kurikulum</p>
                    <p class="text-gray-700">{{ $tahunAjaran->kurikulum->deskripsi ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Tanggal Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Periode Tahun Ajaran</h2>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Tanggal Mulai</p>
                    <p class="text-gray-800 font-bold">{{ $tahunAjaran->tanggal_mulai->format('d M Y') }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Tanggal Selesai</p>
                    <p class="text-gray-800 font-bold">{{ $tahunAjaran->tanggal_selesai->format('d M Y') }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Durasi</p>
                    @php
                        $duration = $tahunAjaran->tanggal_mulai->diffInDays($tahunAjaran->tanggal_selesai);
                    @endphp
                    <p class="text-gray-800 font-semibold">{{ $duration }} hari ({{ ceil($duration / 30) }} bulan)</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-semibold">Status</p>
                    @if($tahunAjaran->is_active)
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded text-sm font-bold">Sedang Aktif</span>
                    @else
                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 rounded text-sm font-bold">Tidak Aktif</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Semester List -->
    @if($tahunAjaran->semester->isNotEmpty())
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-4">
            <h2 class="text-lg font-bold">Daftar Semester</h2>
        </div>

        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Nama Semester</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Tanggal Mulai</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Tanggal UTS</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Tanggal UAS</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tahunAjaran->semester as $sem)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-800 font-semibold">{{ $sem->nama }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $sem->tanggal_mulai->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $sem->tanggal_uts ? $sem->tanggal_uts->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $sem->tanggal_uas ? $sem->tanggal_uas->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if(now()->between($sem->tanggal_mulai, $sem->tanggal_selesai ?? now()))
                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-bold">Sedang Berlangsung</span>
                        @else
                            <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-bold">Selesai</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Statistics Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-600 font-semibold">Total Semester</p>
            <p class="text-3xl font-bold text-blue-700">{{ $tahunAjaran->semester->count() }}</p>
        </div>
        
        <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 border border-cyan-200 rounded-lg p-4">
            <p class="text-sm text-cyan-600 font-semibold">Kelas Aktif</p>
            <p class="text-3xl font-bold text-cyan-700">{{ $tahunAjaran->guruWaliKelas->count() }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-4">
            <p class="text-sm text-green-600 font-semibold">Kenaikan Kelas</p>
            <p class="text-3xl font-bold text-green-700">{{ $tahunAjaran->kenaikanKelas->count() }}</p>
        </div>
    </div>

    <!-- Meta Information -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-600"><strong>Dibuat:</strong> {{ $tahunAjaran->created_at->format('d M Y H:i') }}</p>
        <p class="text-sm text-gray-600"><strong>Diperbarui:</strong> {{ $tahunAjaran->updated_at->format('d M Y H:i') }}</p>
    </div>

    <!-- Actions -->
    <div class="flex gap-3">
        @can('edit tahun ajaran')
        <a href="{{ route('akademik.tahun-ajaran.edit', $tahunAjaran->id) }}" class="px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold transition">
            ✏️ Edit
        </a>
        @endcan
        @can('delete tahun ajaran')
        <form method="POST" action="{{ route('akademik.tahun-ajaran.destroy', $tahunAjaran->id) }}" onsubmit="return confirm('Hapus tahun ajaran ini?');" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition">
                🗑️ Hapus
            </button>
        </form>
        @endcan
    </div>
</div>
@endsection
