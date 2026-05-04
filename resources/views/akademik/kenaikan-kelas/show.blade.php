@extends('layouts.app')

@section('title', 'Detail Kenaikan Kelas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <a href="{{ route('akademik.kenaikan-kelas.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-block mb-6"><- Kembali ke Kenaikan Kelas</a>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Detail Kenaikan Kelas</h1>
            @if(can_access('edit kenaikan kelas') || is_admin() || auth()->user()->hasRole('Guru'))
            <a href="{{ route('akademik.kenaikan-kelas.edit', $kenaikanKelas) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold transition">Edit</a>
            @endif
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Nama Siswa</p>
                <p class="text-lg font-semibold text-gray-800">{{ $kenaikanKelas->siswa->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Kelas Saat Ini</p>
                <p class="text-lg font-semibold text-gray-800">{{ $kenaikanKelas->kelasSekarang->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Kelas Tujuan</p>
                <p class="text-lg font-semibold text-gray-800">{{ $kenaikanKelas->kelasTujuan->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tahun Ajaran</p>
                <p class="text-lg font-semibold text-gray-800">{{ $kenaikanKelas->tahunAjaran->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Rata-rata Nilai</p>
                <p class="text-lg font-semibold text-gray-800">{{ number_format($kenaikanKelas->rata_rata_nilai, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p class="text-lg font-semibold text-gray-800">{{ str_replace('_', ' ', ucfirst($kenaikanKelas->status)) }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Catatan</p>
                <p class="text-gray-800">{{ $kenaikanKelas->catatan ?: '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
