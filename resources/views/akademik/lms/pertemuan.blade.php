@extends('layouts.app')

@section('title', 'LMS Pertemuan')
@section('page-title', 'LMS Pertemuan')

@section('content')
@php
    $isSiswaScope = (bool) ($isSiswaScope ?? false);
@endphp
<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                @if(!empty($meetingNumber))
                Pertemuan ke-{{ $meetingNumber }} ({{ $date->format('d M Y') }})
                @else
                Pertemuan {{ $date->format('d M Y') }}
                @endif
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ $semester ? ($semester->nama . ' - ' . ($semester->tahunAjaran->nama ?? '-')) : 'Semester belum terdeteksi' }}
                @if($isSiswaScope)
                    | Ringkasan hasil pertemuan kelas Anda.
                @endif
            </p>
            @if(!empty($selectedKelas))
            <p class="mt-1 text-xs font-semibold text-emerald-700">Kelas terpilih: {{ $selectedKelas->nama }}</p>
            @endif
            @if(!empty($meetingNumber))
            <p class="mt-1 text-xs text-indigo-600 font-semibold">Tanggal ini sudah tercatat sebagai pertemuan ke-{{ $meetingNumber }}.</p>
            @elseif(!empty($canSelectPertemuan))
            <p class="mt-1 text-xs text-gray-500">Tanggal ini baru akan diberi nomor pertemuan setelah dipilih oleh guru/admin.</p>
            @else
            <p class="mt-1 text-xs text-gray-500">Nomor pertemuan akan muncul setelah guru/admin memilih tanggal ini.</p>
            @endif
        </div>
        <a href="{{ route('akademik.lms.index', array_filter(['semester_id' => $semesterId, 'month_key' => $date->format('Y-m'), 'kelas_id' => $selectedKelasId], fn($value) => $value !== null && $value !== '')) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-200 transition">
            Kembali ke Kalender LMS
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
        @if(!$isSiswaScope)
        <form method="GET" action="{{ route('akademik.lms.pertemuan', ['tanggal' => $date->toDateString()]) }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <input type="hidden" name="semester_id" value="{{ $semesterId }}">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Filter Kelas</label>
                <select name="kelas_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" onchange="this.form.submit()">
                    <option value="">Semua / Pilih Kelas</option>
                    @forelse($kelasOptions as $kelas)
                    <option value="{{ $kelas->id }}" @selected((int) ($selectedKelasId ?? 0) === (int) $kelas->id)>{{ $kelas->nama }}</option>
                    @empty
                    <option value="" disabled>Tidak ada kelas yang tersedia</option>
                    @endforelse
                </select>
            </div>
            <div class="md:col-span-2 text-xs text-gray-500">
                Kelas yang dipilih akan otomatis dibawa ke input Materi, Tugas, Absensi, dan Monitoring.
            </div>
        </form>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-center">
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900">
                Kelas otomatis: <span class="font-semibold">{{ $selectedKelas->nama ?? 'Belum terhubung' }}</span>
            </div>
            <div class="text-xs text-gray-500">
                Hasil pertemuan ditampilkan otomatis berdasarkan kelas Anda.
            </div>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <a href="{{ $links['materi'] . (str_contains($links['materi'], '?') ? '&' : '?') . 'back_url=' . urlencode(request()->fullUrl()) }}" class="group bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-indigo-300 transition">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold tracking-wide uppercase text-indigo-600">LMS Materi</p>
                    <h3 class="mt-1 text-lg font-bold text-gray-900">{{ $isSiswaScope ? 'Materi Pertemuan' : 'Kelola Materi Pertemuan' }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ $isSiswaScope ? 'Lihat materi yang tersedia pada tanggal pertemuan ini.' : 'Masuk ke input/daftar materi untuk tanggal pertemuan ini.' }}</p>
                    <p class="mt-2 text-xs font-semibold text-indigo-700">Realtime: {{ $materiCount }} materi tercatat pada tanggal ini.</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">{{ $materiCount }} data</span>
            </div>
        </a>

        <a href="{{ $links['tugas'] . (str_contains($links['tugas'], '?') ? '&' : '?') . 'back_url=' . urlencode(request()->fullUrl()) }}" class="group bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-cyan-300 transition">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold tracking-wide uppercase text-cyan-600">LMS Tugas</p>
                    <h3 class="mt-1 text-lg font-bold text-gray-900">{{ $isSiswaScope ? 'Tugas Pertemuan' : 'Kelola Tugas Pertemuan' }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ $isSiswaScope ? 'Lihat tugas aktif untuk tanggal pertemuan ini.' : 'Masuk ke input/daftar tugas untuk tanggal pertemuan ini.' }}</p>
                    <p class="mt-2 text-xs font-semibold text-cyan-700">Realtime: {{ $tugasCount }} tugas aktif pada tanggal ini.</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-700">{{ $tugasCount }} data</span>
            </div>
        </a>

        @php
            $absensiLinkWithBack = $links['absensi'] . (str_contains($links['absensi'], '?') ? '&' : '?') . 'back_url=' . urlencode(request()->fullUrl());
        @endphp
        <a href="{{ $absensiLinkWithBack }}" class="group bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold tracking-wide uppercase text-emerald-600">Absensi</p>
                    <h3 class="mt-1 text-lg font-bold text-gray-900">{{ $isSiswaScope ? 'Rekap Absensi Pertemuan' : 'Input Absensi Pertemuan' }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ $isSiswaScope ? 'Lihat ringkasan absensi untuk pertemuan ini.' : 'Isi absensi siswa pada tanggal pertemuan ini.' }}</p>
                    <p class="mt-2 text-xs font-semibold text-emerald-700">
                        Realtime:
                        @if(($absensiScope ?? 'tanggal') === 'bulan')
                            {{ $absensiCount }} sesi dan {{ $absensiDetailCount }} input status siswa pada bulan ini.
                        @else
                            {{ $absensiCount }} sesi dan {{ $absensiDetailCount }} input status siswa pada tanggal ini.
                        @endif
                    </p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">{{ $absensiCount }} sesi</span>
            </div>
        </a>

        <a href="{{ $links['monitoring'] . (str_contains($links['monitoring'], '?') ? '&' : '?') . 'back_url=' . urlencode(request()->fullUrl()) }}" class="group bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-amber-300 transition">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold tracking-wide uppercase text-amber-600">LMS Monitoring</p>
                    <h3 class="mt-1 text-lg font-bold text-gray-900">{{ $isSiswaScope ? 'Hasil Aktivitas Pertemuan' : 'Monitoring Aktivitas Pertemuan' }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ $isSiswaScope ? 'Lihat progres aktivitas LMS pada tanggal pertemuan ini.' : 'Pantau progres aktivitas LMS sesuai tanggal pertemuan ini.' }}</p>
                    <p class="mt-2 text-xs font-semibold text-amber-700">
                        Realtime:
                        @if(($monitoringScope ?? 'tanggal') === 'periode')
                            {{ $monitoringCount }} aktivitas, {{ $monitoringDinilaiCount }} sudah dinilai pada periode kelas ini.
                        @else
                            {{ $monitoringCount }} aktivitas, {{ $monitoringDinilaiCount }} sudah dinilai pada tanggal ini.
                        @endif
                    </p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">{{ $monitoringCount }} aktivitas</span>
            </div>
        </a>
    </div>
</div>
@endsection
