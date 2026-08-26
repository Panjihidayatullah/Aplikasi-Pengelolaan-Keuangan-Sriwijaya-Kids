@extends('layouts.app')

@section('title', 'LMS Materi')

@section('content')
<div class="container mx-auto px-4 py-6">
    @php
        $isSiswaScope = (bool) ($isSiswaScope ?? false);
    @endphp

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            @php
                $backUrl = request('back_url')
                    ? urldecode(request('back_url'))
                    : (request()->filled('pertemuan_tanggal')
                        ? route('akademik.lms.pertemuan', array_filter([
                            'tanggal'     => request('pertemuan_tanggal'),
                            'semester_id' => request('semester_id'),
                            'kelas_id'    => request('kelas_id'),
                          ], fn($v) => $v !== null && $v !== ''))
                        : route('akademik.lms.index', array_filter([
                            'semester_id' => request('semester_id'),
                            'kelas_id'    => request('kelas_id'),
                          ], fn($v) => $v !== null && $v !== '')));
            @endphp
            <a href="{{ $backUrl }}"
               class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">LMS - Manajemen Materi</h1>
                <p class="text-gray-600 mt-1">Upload dan akses materi PDF, video, dan PPT</p>
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
            <a href="{{ route('akademik.lms.materi.create', ['pertemuan_tanggal' => request('pertemuan_tanggal'), 'semester_id' => request('semester_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'kelas_id' => request('kelas_id')]) }}" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">Upload Materi</a>
            @endif
        </div>
    </div>


    @if(request()->filled('pertemuan_tanggal'))
    <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
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
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul materi..." class="w-full px-3 py-2 border border-gray-300 rounded-lg">
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
                @if(request()->filled('pertemuan_tanggal') && request()->filled('back_url'))
                <input type="hidden" name="pertemuan_tanggal" value="{{ request('pertemuan_tanggal') }}">
                <input type="date" disabled value="{{ request('pertemuan_tanggal') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                @else
                <input type="date" name="pertemuan_tanggal" value="{{ request('pertemuan_tanggal') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                @endif
            </div>

            <div class="min-w-[140px] w-full sm:w-auto">
                <button class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">Filter</button>
            </div>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($materi as $item)
        <div class="bg-white rounded-lg shadow p-5 border border-gray-100">
            <div class="flex items-start justify-between gap-3">
                <h2 class="text-lg font-bold text-gray-800">{{ $item->judul }}</h2>
                <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700 uppercase">{{ $item->tipe }}</span>
            </div>
            <p class="text-sm text-gray-600 mt-2">{{ Str::limit($item->deskripsi, 120) }}</p>
            <div class="text-xs text-gray-500 mt-3 space-y-1">
                <p>Kelas: {{ $item->kelas->nama ?? '-' }}</p>
                <p>Mapel: {{ $item->mataPelajaran->nama ?? '-' }}</p>
                <p>Pengajar: {{ $item->guru->nama ?? '-' }}</p>
                <p>Pertemuan: {{ $item->tanggal_pertemuan?->format('d M Y') ?? '-' }}</p>
            </div>
            <div class="mt-4 flex items-center gap-3">
                <a href="{{ route('akademik.lms.materi.show', $item) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Lihat</a>
                @if(is_admin() || auth()->user()->hasRole('Guru'))
                <a href="{{ route('akademik.lms.materi.edit', $item) }}" class="text-yellow-600 hover:text-yellow-800 font-semibold text-sm">Edit</a>
                @endif
                @if($item->file_path)
                <a href="{{ route('akademik.lms.materi.file', $item) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm" target="_blank">Lihat File</a>
                <a href="{{ route('akademik.lms.materi.download', $item) }}" class="text-cyan-600 hover:text-cyan-800 font-semibold text-sm">Unduh</a>
                @endif
                @if((is_admin() || auth()->user()->hasRole('Guru')))
                <form method="POST" action="{{ route('akademik.lms.materi.destroy', $item) }}" onsubmit="return confirm('Hapus materi ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm bg-transparent hover:bg-transparent focus:bg-transparent active:bg-transparent border-0 p-0 appearance-none">Hapus</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-lg shadow p-10 text-center text-gray-500">
            {{ empty($selectedKelasId) ? ($isSiswaScope ? 'Kelas siswa Anda belum terhubung. Silakan hubungi admin.' : 'Pilih kelas terlebih dahulu untuk menampilkan materi.') : 'Belum ada materi' }}
        </div>
        @endforelse
    </div>

    @if($materi->hasPages())
    <div class="mt-6">{{ $materi->links() }}</div>
    @endif
</div>
@endsection
