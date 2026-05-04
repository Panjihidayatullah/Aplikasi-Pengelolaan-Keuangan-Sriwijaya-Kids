@extends('layouts.app')

@section('title', 'Detail Ujian')

@section('content')
<div class="container mx-auto px-4 py-6">
    <a href="{{ route('akademik.ujian.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-block mb-6"><- Kembali ke Jadwal Ujian</a>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Jadwal Ujian</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $ujian->jenis_ujian }} - {{ $ujian->mataPelajaran->nama ?? '-' }}</p>
            </div>
            @can('edit ujian')
            <a href="{{ route('akademik.ujian.edit', $ujian) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold transition">Edit</a>
            @endcan
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Mata Pelajaran</p>
                <p class="text-lg font-semibold text-gray-800">{{ $ujian->mataPelajaran->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Kelas</p>
                <p class="text-lg font-semibold text-gray-800">{{ $ujian->kelas->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Semester</p>
                <p class="text-lg font-semibold text-gray-800">{{ $ujian->semester->nama ?? '-' }} ({{ $ujian->semester->tahunAjaran->nama ?? '-' }})</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Ujian</p>
                <p class="text-lg font-semibold text-gray-800">{{ optional($ujian->tanggal_ujian)->format('d M Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Jam</p>
                <p class="text-lg font-semibold text-gray-800">{{ $ujian->jam_mulai }} - {{ $ujian->jam_selesai }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Ruang</p>
                <p class="text-lg font-semibold text-gray-800">{{ $ujian->ruang }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Catatan</p>
                <p class="text-gray-800">{{ $ujian->catatan ?: '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
