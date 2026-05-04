@extends('layouts.app')

@section('title', 'Detail Monitoring Siswa')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-start justify-between gap-3 flex-wrap">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detail Monitoring Siswa</h1>
            <p class="text-gray-600 mt-1">Ringkasan per mata pelajaran untuk {{ $siswa->nama }}</p>
        </div>
        <div class="inline-flex items-center gap-2">
            <a
                href="{{ route('akademik.lms.monitoring.pdf', ['siswa' => $siswa->id, 'semester_id' => $selectedSemesterId, 'tahun_ajaran_id' => $selectedTahunAjaranId]) }}"
                target="_blank"
                rel="noopener"
                data-no-loader
                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm"
            >
                Export PDF
            </a>
            <a
                href="{{ route('akademik.lms.monitoring.index', ['kelas_id' => $selectedKelas?->id, 'semester_id' => $selectedSemesterId, 'tahun_ajaran_id' => $selectedTahunAjaranId]) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold text-sm"
            >
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Nama Siswa</p>
                <p class="font-semibold text-gray-800">{{ $siswa->nama }}</p>
            </div>
            <div>
                <p class="text-gray-500">NISN</p>
                <p class="font-semibold text-gray-800">{{ $studentNisn }}</p>
            </div>
            <div>
                <p class="text-gray-500">Kelas</p>
                <p class="font-semibold text-gray-800">{{ $selectedKelas->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Periode</p>
                <p class="font-semibold text-gray-800">{{ $selectedSemester->nama ?? '-' }} / {{ $selectedTahunAjaran->nama ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-5 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800">Detail Performa Per Mata Pelajaran</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1320px]">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-14">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Mapel</th>
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
                    @forelse($detailRows as $row)
                    <tr class="border-b">
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $row['mapel_nama'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['absensi_hadir'] }}/{{ $row['absensi_total'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['tugas_submit'] }}/{{ $row['tugas_total'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['nilai_isi'] }}/{{ $row['nilai_total'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['uts'] !== null ? number_format((float) $row['uts'], 2, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row['uas'] !== null ? number_format((float) $row['uas'], 2, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $row['total_nilai'] !== null ? number_format((float) $row['total_nilai'], 2, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($canEditTranskrip)
                                @if(!empty($row['transkrip_id']))
                                <a href="{{ route('akademik.transkrip-nilai.edit', $row['transkrip_id']) }}" class="text-yellow-600 hover:text-yellow-800 font-semibold">Edit Nilai</a>
                                @else
                                <a
                                    href="{{ route('akademik.transkrip-nilai.create', ['kelas_id' => $selectedKelas?->id, 'mapel_id' => $row['mapel_id'], 'semester_id' => $selectedSemesterId, 'tahun_ajaran_id' => $selectedTahunAjaranId, 'siswa_id' => $siswa->id]) }}"
                                    class="text-yellow-600 hover:text-yellow-800 font-semibold"
                                >
                                    Input Nilai
                                </a>
                                @endif
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">Belum ada data detail untuk siswa ini pada periode terpilih.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($detailRows->isNotEmpty())
                <tfoot class="bg-gray-50 border-t">
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-sm font-bold text-gray-800">TOTAL / RATA-RATA</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $detailTotals['absensi_hadir'] }}/{{ $detailTotals['absensi_total'] }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $detailTotals['tugas_submit'] }}/{{ $detailTotals['tugas_total'] }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $detailTotals['nilai_isi'] }}/{{ $detailTotals['nilai_total'] }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $detailTotals['avg_uts'] !== null ? number_format((float) $detailTotals['avg_uts'], 2, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $detailTotals['avg_uas'] !== null ? number_format((float) $detailTotals['avg_uas'], 2, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ $detailTotals['avg_total_nilai'] !== null ? number_format((float) $detailTotals['avg_total_nilai'], 2, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">-</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
