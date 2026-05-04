@extends('layouts.app')

@section('title', 'Transkrip Nilai Siswa')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Back Link -->
    <a href="{{ route('akademik.transkrip-nilai.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold mb-6 inline-block">← Kembali ke Transkrip Nilai</a>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Transkrip Nilai Siswa</h1>
        <p class="text-gray-600 mt-1">Detail akademik {{ $siswa->nama }}</p>
    </div>

    <!-- Informasi Siswa -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 font-semibold">Nama Siswa</p>
                <p class="text-gray-800 font-bold text-lg">{{ $siswa->nama }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-semibold">NIS</p>
                <p class="text-gray-800 font-bold text-lg">{{ $siswa->nis ?? optional($siswa->kartuPelajar->first())->nis_otomatis ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-semibold">Kelas</p>
                <p class="text-gray-800 font-bold text-lg">{{ $siswa->kelas->nama ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Transkrip Nilai per Semester -->
    @forelse($transkrip as $semester => $nilais)
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <!-- Semester Header -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 py-4">
            <h2 class="text-lg font-bold">{{ $semester }}</h2>
        </div>

        <!-- Nilai Table -->
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Mata Pelajaran</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">UTS</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">UAS</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Tugas</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Nilai Akhir</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Grade</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nilais as $nilai)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-800 font-semibold">{{ $nilai->mataPelajaran->nama ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center font-semibold">{{ $nilai->nilai_uts ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center font-semibold">{{ $nilai->nilai_uas ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600 text-center font-semibold">{{ $nilai->nilai_harian ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-3 py-1 
                            @if($nilai->nilai_akhir >= 85) bg-green-100 text-green-800
                            @elseif($nilai->nilai_akhir >= 70) bg-blue-100 text-blue-800
                            @elseif($nilai->nilai_akhir >= 60) bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif
                            rounded text-sm font-bold">
                            {{ number_format($nilai->nilai_akhir, 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-3 py-1 
                            @if($nilai->grade == 'A') bg-green-100 text-green-800
                            @elseif($nilai->grade == 'B') bg-blue-100 text-blue-800
                            @elseif($nilai->grade == 'C') bg-yellow-100 text-yellow-800
                            @elseif($nilai->grade == 'D') bg-orange-100 text-orange-800
                            @else bg-red-100 text-red-800 @endif
                            rounded text-sm font-bold">
                            {{ $nilai->grade ?? '-' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Belum ada nilai untuk semester ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Semester Summary -->
        @if($nilais->isNotEmpty())
        <div class="bg-gray-50 px-6 py-4 border-t">
            <div class="flex justify-between items-center">
                <p class="font-semibold text-gray-700">Rata-rata Nilai Semester:</p>
                @php
                    $avg = $nilais->avg('nilai_akhir');
                @endphp
                <span class="inline-block px-4 py-2 
                    @if($avg >= 85) bg-green-100 text-green-800
                    @elseif($avg >= 70) bg-blue-100 text-blue-800
                    @elseif($avg >= 60) bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif
                    rounded text-lg font-bold">
                    {{ number_format($avg, 2) }}
                </span>
            </div>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
        <p>Belum ada data transkrip nilai untuk siswa ini</p>
    </div>
    @endforelse

    <!-- Ringkasan Keseluruhan -->
    @if($transkrip->flatten()->isNotEmpty())
    <div class="bg-gradient-to-r from-cyan-50 to-blue-50 rounded-lg shadow p-6 border border-cyan-200">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Ringkasan Keseluruhan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 font-semibold">Rata-rata Total</p>
                @php
                    $totalAvg = $transkrip->flatten()->avg('nilai_akhir');
                @endphp
                <p class="text-3xl font-bold text-blue-600">{{ number_format($totalAvg, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-semibold">Nilai Tertinggi</p>
                @php
                    $maxNilai = $transkrip->flatten()->max('nilai_akhir');
                @endphp
                <p class="text-3xl font-bold text-green-600">{{ $maxNilai }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-semibold">Nilai Terendah</p>
                @php
                    $minNilai = $transkrip->flatten()->min('nilai_akhir');
                @endphp
                <p class="text-3xl font-bold text-red-600">{{ $minNilai }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Print Button -->
    <div class="mt-8 flex gap-3">
        <a href="{{ route('akademik.transkrip-nilai.print', $transkripNilai) }}"
           target="_blank"
           class="inline-flex items-center px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2"
           style="background-color: #dc2626 !important; color: #ffffff !important;">
            Cetak Transkrip
        </a>
    </div>
</div>
@endsection
