@extends('layouts.app')

@section('title', 'Monitoring Progress LMS')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6 gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            @if(request('back_url'))
            <a href="{{ urldecode(request('back_url')) }}"
               class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors"
               title="Kembali ke LMS Pertemuan">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            @endif
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Monitoring Performa Siswa Per Kelas</h1>
                <p class="text-gray-600 mt-1">Pilih kelas terlebih dahulu, lalu lihat ringkasan absensi, tugas, dan nilai siswa.</p>
            </div>
        </div>
        @if(request('back_url'))
        <a href="{{ urldecode(request('back_url')) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke LMS Pertemuan
        </a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="w-full min-w-max flex flex-nowrap items-end gap-3 overflow-x-auto pb-1">
            <div class="flex-1 min-w-[230px]">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas</label>
                <select name="kelas_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">Pilih kelas terlebih dahulu</option>
                    @foreach($kelases as $kelas)
                    <option value="{{ $kelas->id }}" @selected((string) $selectedKelasId === (string) $kelas->id)>{{ $kelas->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[230px]">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Semester</label>
                <select name="semester_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}" @selected((string) $selectedSemesterId === (string) $semester->id)>
                        {{ $semester->nama }} - {{ $semester->tahunAjaran->nama ?? '-' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[230px]">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahunAjarans as $tahun)
                    <option value="{{ $tahun->id }}" @selected((string) $selectedTahunAjaranId === (string) $tahun->id)>{{ $tahun->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-[140px] shrink-0">
                <button class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">Tampilkan</button>
            </div>
            <div class="min-w-[120px] shrink-0">
                <a href="{{ route('akademik.lms.monitoring.index') }}" class="inline-flex w-full items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold">Reset</a>
            </div>
        </form>
    </div>

    @if(!$selectedKelasId)
    <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 px-4 py-4 text-sm text-amber-800">
        Silakan pilih <span class="font-semibold">kelas</span> terlebih dahulu untuk menampilkan ringkasan performa siswa.
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-700 font-semibold">Kelas</p>
            <p class="text-2xl font-bold text-blue-800">{{ $selectedKelas->nama ?? '-' }}</p>
        </div>
        <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-4">
            <p class="text-sm text-cyan-700 font-semibold">Total Absensi Kelas</p>
            <p class="text-2xl font-bold text-cyan-800">{{ $totalAbsensiClass }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm text-green-700 font-semibold">Total Tugas Kelas</p>
            <p class="text-2xl font-bold text-green-800">{{ $totalTugasClass }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Ringkasan Performa Siswa</h2>
                <p class="text-sm text-gray-600 mt-1">Data terhubung dengan absensi, pengumpulan tugas, dan transkrip nilai.</p>
            </div>
            <div class="text-xs text-gray-500">
                Semester: {{ $selectedSemester->nama ?? '-' }} | Tahun Ajaran: {{ $selectedTahunAjaran->nama ?? '-' }}
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1220px]">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-14">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">NISN</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nama</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Absensi</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tugas</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nilai</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">UTS</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">UAS</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Nilai</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaSummary as $row)
                    <tr class="border-b">
                        <td class="px-4 py-3 text-sm text-gray-700">{{ ($siswaSummary->firstItem() ?? 1) + $loop->index }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['nisn'] }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $row['nama'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['absensi_hadir'] }}/{{ $row['absensi_total'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['tugas_submit'] }}/{{ $row['tugas_total'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['nilai_isi'] }}/{{ $row['nilai_total'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['uts'] !== null ? number_format((float) $row['uts'], 2, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['uas'] !== null ? number_format((float) $row['uas'], 2, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $row['total_nilai'] !== null ? number_format((float) $row['total_nilai'], 2, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="inline-flex items-center gap-3 whitespace-nowrap">
                                @if($canEditTranskrip)
                                    @if(!empty($row['latest_transkrip_id']))
                                    <a href="{{ route('akademik.transkrip-nilai.edit', $row['latest_transkrip_id']) }}" class="text-yellow-600 hover:text-yellow-800 font-semibold">Edit</a>
                                    @else
                                    <a href="{{ route('akademik.transkrip-nilai.create', ['kelas_id' => $selectedKelasId, 'semester_id' => $selectedSemesterId, 'tahun_ajaran_id' => $selectedTahunAjaranId, 'siswa_id' => $row['id']]) }}" class="text-yellow-600 hover:text-yellow-800 font-semibold">Edit</a>
                                    @endif
                                @endif

                                <a href="{{ route('akademik.lms.monitoring.show', ['siswa' => $row['id'], 'semester_id' => $selectedSemesterId, 'tahun_ajaran_id' => $selectedTahunAjaranId]) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Detail</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">Belum ada data siswa pada kelas ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswaSummary->hasPages())
        <div class="p-4 border-t">{{ $siswaSummary->links() }}</div>
        @endif
    </div>
    @endif
</div>
@endsection
