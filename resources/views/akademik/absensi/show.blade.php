@extends('layouts.app')

@section('title', 'Detail Absensi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
        <div class="flex items-center gap-4">
            <a href="{{ request('back_url') ? urldecode(request('back_url')) : route('akademik.absensi.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Absensi</h1>
                <p class="text-gray-500 text-sm mt-0.5">
                    {{ $absensi->tanggal_absensi?->format('d M Y') ?? '-' }} &mdash;
                    {{ $absensi->kelas?->nama ?? '-' }} &mdash;
                    {{ $absensi->mataPelajaran?->nama ?? 'Umum' }}
                </p>
            </div>
        </div>
        @if(is_admin() || auth()->user()->hasRole('Guru'))
        <a href="{{ route('akademik.absensi.edit', $absensi) }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold text-sm shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Absensi
        </a>
        @endif
    </div>


    {{-- Info Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Tanggal</p>
            <p class="text-sm font-bold text-gray-800 mt-1">{{ $absensi->tanggal_absensi?->format('d M Y') ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Kelas</p>
            <p class="text-sm font-bold text-gray-800 mt-1">{{ $absensi->kelas?->nama ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Mata Pelajaran</p>
            <p class="text-sm font-bold text-gray-800 mt-1">{{ $absensi->mataPelajaran?->nama ?? 'Umum' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase">Guru</p>
            <p class="text-sm font-bold text-gray-800 mt-1">{{ $absensi->guru?->nama ?? '-' }}</p>
        </div>
    </div>

    {{-- Rekap Cepat --}}
    @php
        $hadirCount = $absensi->details->where('status','hadir')->count();
        $izinCount  = $absensi->details->where('status','izin')->count();
        $sakitCount = $absensi->details->where('status','sakit')->count();
        $alpaCount  = $absensi->details->where('status','alpa')->count();
        $total      = $absensi->details->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 text-center">
            <p class="text-xs font-semibold text-emerald-600 uppercase">Hadir</p>
            <p class="text-2xl font-black text-emerald-700 mt-1">{{ $hadirCount }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-center">
            <p class="text-xs font-semibold text-blue-600 uppercase">Izin</p>
            <p class="text-2xl font-black text-blue-700 mt-1">{{ $izinCount }}</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-center">
            <p class="text-xs font-semibold text-amber-600 uppercase">Sakit</p>
            <p class="text-2xl font-black text-amber-700 mt-1">{{ $sakitCount }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-center">
            <p class="text-xs font-semibold text-red-600 uppercase">Alpa</p>
            <p class="text-2xl font-black text-red-700 mt-1">{{ $alpaCount }}</p>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-center">
            <p class="text-xs font-semibold text-gray-500 uppercase">Total Siswa</p>
            <p class="text-2xl font-black text-gray-700 mt-1">{{ $total }}</p>
        </div>
    </div>

    {{-- Detail Per Siswa --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b bg-gray-50">
            <h2 class="text-base font-bold text-gray-800">Detail Status Per Siswa</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-12">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Siswa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NIS</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi->details as $i => $detail)
                    @php
                        $st = (string) ($detail->status ?? '-');
                        $stClass = match($st) {
                            'hadir'   => 'bg-emerald-100 text-emerald-800',
                            'izin'    => 'bg-blue-100 text-blue-800',
                            'sakit'   => 'bg-amber-100 text-amber-800',
                            default   => 'bg-red-100 text-red-800',
                        };
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $detail->siswa?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $detail->siswa?->nis ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $stClass }}">
                                {{ ucfirst($st) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $detail->catatan ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada detail absensi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
