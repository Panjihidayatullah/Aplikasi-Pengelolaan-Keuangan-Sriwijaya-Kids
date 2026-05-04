@extends('layouts.app')

@section('title', 'Transkrip Nilai')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Transkrip Nilai</h1>
            <p class="text-gray-600 mt-1">Pilih kelas terlebih dahulu, lalu pilih mata pelajaran.</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <form method="GET" action="{{ route('akademik.transkrip-nilai.index') }}" class="min-w-max w-full flex flex-nowrap items-end gap-3 overflow-x-auto pb-1">
            <div class="min-w-[280px]">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Cari Kelas</label>
                <input type="text" name="kelas_search" value="{{ request('kelas_search') }}" placeholder="Nama kelas..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            @if($selectedKelas)
            <div class="min-w-[280px]">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Cari Mapel</label>
                <input type="text" name="mapel_search" value="{{ request('mapel_search') }}" placeholder="Nama/kode mapel..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <input type="hidden" name="kelas_id" value="{{ $selectedKelas->id }}">
            @endif
            <div class="flex items-center gap-2 min-w-max">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-semibold">Cari</button>
                <a href="{{ route('akademik.transkrip-nilai.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition font-semibold">Reset</a>
            </div>
        </form>
    </div>

    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-3">Pilih Kelas</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @forelse($kelases as $kelas)
                <a
                    href="{{ route('akademik.transkrip-nilai.index', ['kelas_id' => $kelas->id]) }}"
                    class="rounded-xl border p-4 transition {{ $selectedKelas && $selectedKelas->id === $kelas->id ? 'border-blue-400 bg-blue-50 shadow-sm' : 'border-gray-200 bg-white hover:border-blue-300 hover:shadow-sm' }}"
                >
                    <p class="font-bold text-gray-800">{{ $kelas->nama }}</p>
                    <p class="text-xs text-gray-600 mt-2">Klik untuk pilih kelas ini</p>
                </a>
            @empty
                <div class="col-span-full rounded-lg border border-dashed border-gray-300 bg-white px-4 py-8 text-sm text-gray-500 text-center">
                    Tidak ada kelas yang cocok dengan pencarian.
                </div>
            @endforelse
        </div>
    </div>

    @if($selectedKelas)
    <div>
        <div class="mb-3 flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-gray-800">Pilih Mapel Untuk {{ $selectedKelas->nama }}</h2>
            <a href="{{ route('akademik.transkrip-nilai.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">Ganti kelas</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @forelse($mapels as $mapel)
                <a
                    href="{{ route('akademik.transkrip-nilai.kelas-mapel', ['kelas' => $selectedKelas, 'mataPelajaran' => $mapel]) }}"
                    class="rounded-xl border border-gray-200 bg-white hover:border-blue-300 hover:shadow-sm p-4 transition"
                >
                    <p class="font-bold text-gray-800">{{ $mapel->nama }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $mapel->kode_mapel ?: '-' }}</p>
                    <p class="text-xs text-blue-700 mt-3 font-semibold">{{ $transkripCounts[$mapel->id] ?? 0 }} data nilai</p>
                </a>
            @empty
                <div class="col-span-full rounded-lg border border-dashed border-gray-300 bg-white px-4 py-8 text-sm text-gray-500 text-center">
                    Belum ada mapel yang cocok untuk kelas ini.
                </div>
            @endforelse
        </div>
    </div>
    @endif
</div>
@endsection
