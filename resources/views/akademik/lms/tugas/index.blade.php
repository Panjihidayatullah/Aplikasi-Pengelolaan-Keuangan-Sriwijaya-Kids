@extends('layouts.app')

@section('title', 'LMS Tugas')

@section('content')
<div class="container mx-auto px-4 py-6">
    @php
        $isSiswaScope = (bool) ($isSiswaScope ?? false);
    @endphp

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
                <h1 class="text-3xl font-bold text-gray-800">LMS - Tugas</h1>
                <p class="text-gray-600 mt-1">Pemberian tugas, deadline, dan monitoring pengumpulan</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if(request('back_url'))
            <a href="{{ urldecode(request('back_url')) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke LMS Pertemuan
            </a>
            @endif
            @if(is_admin() || auth()->user()->hasRole('Guru'))
            <a href="{{ route('akademik.lms.tugas.create', ['pertemuan_tanggal' => request('pertemuan_tanggal'), 'semester_id' => request('semester_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'kelas_id' => request('kelas_id')]) }}" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">Buat Tugas</a>
            @endif
        </div>
    </div>

    @if(request()->filled('pertemuan_tanggal'))
    <div class="mb-4 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-900">
        Menampilkan data untuk pertemuan tanggal <span class="font-semibold">{{ \Carbon\Carbon::parse(request('pertemuan_tanggal'))->format('d M Y') }}</span>.
        @if(!empty($selectedKelasId))
        Kelas: <span class="font-semibold">{{ optional($kelases->firstWhere('id', (int) $selectedKelasId))->nama ?? '-' }}</span>.
        @endif
    </div>
    @endif

    <form method="GET" class="bg-white rounded-lg shadow p-4 mb-6">
        @if(request()->filled('semester_id'))
        <input type="hidden" name="semester_id" value="{{ request('semester_id') }}">
        @endif
        @if(request()->filled('tahun_ajaran_id'))
        <input type="hidden" name="tahun_ajaran_id" value="{{ request('tahun_ajaran_id') }}">
        @endif

        <div class="w-full flex flex-wrap xl:flex-nowrap items-end gap-3">
            <div class="flex-1 min-w-[260px]">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul tugas..." class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            @if(!$isSiswaScope)
            <div class="flex-1 min-w-[220px]">
                <select name="kelas_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Kelas</option>
                    @foreach($kelases as $kelas)
                    <option value="{{ $kelas->id }}" @selected((string) request('kelas_id') === (string) $kelas->id)>{{ $kelas->nama }}</option>
                    @endforeach
                </select>
            </div>
            @else
                @if(!empty($selectedKelasId))
                <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                @endif
                <div class="flex-1 min-w-[220px] px-3 py-2 border border-green-200 rounded-lg bg-green-50 text-sm text-green-800">
                    Kelas otomatis: <span class="font-semibold">{{ optional($kelases->firstWhere('id', (int) $selectedKelasId))->nama ?? 'Belum terhubung' }}</span>
                </div>
            @endif

            <div class="flex-1 min-w-[220px]">
                <select name="mata_pelajaran_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Mapel</option>
                    @foreach($mataPelajarans as $mp)
                    <option value="{{ $mp->id }}" @selected((string) request('mata_pelajaran_id') === (string) $mp->id)>{{ $mp->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[220px]">
                <input type="date" name="pertemuan_tanggal" value="{{ request('pertemuan_tanggal') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div class="min-w-[140px] w-full sm:w-auto">
                <button class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">Filter</button>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Judul</th>
                    <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Kelas</th>
                    <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Mapel</th>
                    <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Deadline</th>
                    <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Pertemuan</th>
                    <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Pengumpulan</th>
                    <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tugas as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-5 py-4 text-gray-800 font-semibold">{{ $item->judul }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $item->kelas->nama ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $item->mataPelajaran->nama ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $item->tanggal_deadline?->format('d M Y H:i') }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $item->tanggal_pertemuan?->format('d M Y') ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-700 font-semibold">{{ $item->pengumpulan_tugas_count }}</td>
                    <td class="px-5 py-4">
                        <div class="flex gap-3">
                            <a href="{{ route('akademik.lms.tugas.show', $item) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Detail</a>
                            @if(is_admin() || auth()->user()->hasRole('Guru'))
                            <a href="{{ route('akademik.lms.tugas.edit', $item) }}" class="text-yellow-600 hover:text-yellow-800 font-semibold text-sm">Edit</a>
                            @endif
                            @if(is_admin() || auth()->user()->hasRole('Guru'))
                            <form method="POST" action="{{ route('akademik.lms.tugas.destroy', $item) }}" onsubmit="return confirm('Hapus tugas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm bg-transparent hover:bg-transparent focus:bg-transparent active:bg-transparent border-0 p-0 appearance-none">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-gray-500">{{ empty($selectedKelasId) && $isSiswaScope ? 'Kelas siswa belum terhubung. Silakan hubungi admin.' : 'Belum ada tugas' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tugas->hasPages())
    <div class="mt-6">{{ $tugas->links() }}</div>
    @endif
</div>
@endsection
