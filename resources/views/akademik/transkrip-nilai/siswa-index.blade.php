@extends('layouts.app')

@section('title', 'Transkrip Nilai Saya')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Transkrip Nilai Saya</h1>
            <p class="text-gray-600 mt-1">Lihat ringkasan nilai per semester dan unduh PDF.</p>
        </div>
        <a href="{{ route('akademik.transkrip-nilai.saya.download') }}" class="inline-flex items-center px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition">
            Download PDF
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500">Nama Siswa</p>
                <p class="text-lg font-bold text-gray-800">{{ $siswa->nama }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">NIS</p>
                <p class="text-lg font-bold text-gray-800">{{ $siswa->nis ?? optional($siswa->kartuPelajar->first())->nis_otomatis ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Kelas</p>
                <p class="text-lg font-bold text-gray-800">{{ $siswa->kelas->nama ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-3">Status Kenaikan Kelas</h2>
        @if($kenaikanTerbaru)
        @php
            $status = (string) $kenaikanTerbaru->status;
            $statusLabel = $status === 'naik' ? 'Naik Kelas' : ($status === 'tidak_naik' ? 'Tidak Naik Kelas' : 'Lulus');
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Tahun Ajaran</p>
                <p class="font-semibold text-gray-800">{{ $kenaikanTerbaru->tahunAjaran->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Status</p>
                <span class="inline-flex items-center px-3 py-1 rounded text-sm font-bold
                    @if($status === 'naik') bg-green-100 text-green-800
                    @elseif($status === 'tidak_naik') bg-red-100 text-red-800
                    @else bg-cyan-100 text-cyan-800 @endif">
                    {{ $statusLabel }}
                </span>
            </div>
            <div>
                <p class="text-gray-500">Kelas Sebelumnya</p>
                <p class="font-semibold text-gray-800">{{ $kenaikanTerbaru->kelasSekarang->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Kelas Tujuan</p>
                <p class="font-semibold text-gray-800">{{ $kenaikanTerbaru->kelasTujuan->nama ?? '-' }}</p>
            </div>
        </div>
        @if($kenaikanTerbaru->rata_rata_nilai !== null)
        <p class="mt-3 text-sm text-gray-600">Rata-rata nilai acuan kenaikan: <span class="font-semibold text-gray-800">{{ number_format((float) $kenaikanTerbaru->rata_rata_nilai, 2) }}</span></p>
        @endif
        @else
        <p class="text-sm text-gray-500">Status kenaikan kelas belum tersedia.</p>
        @endif
    </div>

    @forelse($transkrip as $semester => $nilais)
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 py-4">
            <h2 class="text-lg font-bold">{{ $semester }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px]">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Tugas</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">UTS</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">UAS</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Nilai Akhir</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nilais as $nilai)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $nilai->mataPelajaran->nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ number_format((float) $nilai->nilai_harian, 2) }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ number_format((float) $nilai->nilai_uts, 2) }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ number_format((float) $nilai->nilai_uas, 2) }}</td>
                        <td class="px-6 py-4 font-bold text-blue-700">{{ number_format((float) $nilai->nilai_akhir, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1 rounded text-sm font-bold
                                @if($nilai->grade === 'A') bg-green-100 text-green-800
                                @elseif($nilai->grade === 'B') bg-blue-100 text-blue-800
                                @elseif($nilai->grade === 'C') bg-yellow-100 text-yellow-800
                                @elseif($nilai->grade === 'D') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $nilai->grade ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-gray-500">Belum ada nilai untuk semester ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($nilais->isNotEmpty())
        @php
            $avg = $nilais->avg('nilai_akhir');
        @endphp
        <div class="bg-gray-50 px-6 py-3 border-t flex items-center justify-between">
            <p class="text-sm font-semibold text-gray-700">Rata-rata Semester</p>
            <p class="text-lg font-bold text-blue-700">{{ number_format((float) $avg, 2) }}</p>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
        Belum ada data nilai.
    </div>
    @endforelse
</div>
@endsection
