@extends('layouts.app')

@section('title', 'Detail Semester')

@section('content')
<div class="container mx-auto px-4 py-6">
    <a href="{{ route('akademik.semester.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-block mb-6"><- Kembali ke Semester</a>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Semester {{ $semester->nomor_semester }}</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $semester->tahunAjaran->nama ?? '-' }}</p>
            </div>
            @if(can_access('edit semester'))
            <a href="{{ route('akademik.semester.edit', $semester) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold transition">Edit</a>
            @endif
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Tahun Ajaran</p>
                <p class="text-lg font-semibold text-gray-800">{{ $semester->tahunAjaran->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p class="text-lg font-semibold text-gray-800">{{ $semester->is_active ? 'Aktif' : 'Nonaktif' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Mulai</p>
                <p class="text-lg font-semibold text-gray-800">{{ optional($semester->tanggal_mulai)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Selesai</p>
                <p class="text-lg font-semibold text-gray-800">{{ optional($semester->tanggal_selesai)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal UTS</p>
                <p class="text-lg font-semibold text-gray-800">{{ optional($semester->tanggal_uts)->format('d M Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal UAS</p>
                <p class="text-lg font-semibold text-gray-800">{{ optional($semester->tanggal_uas)->format('d M Y') ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
