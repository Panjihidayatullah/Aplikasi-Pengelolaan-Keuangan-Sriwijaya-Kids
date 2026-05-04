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
        @if(is_admin() || auth()->user()->hasRole('Guru'))
        <a href="{{ route('akademik.lms.materi.create', ['pertemuan_tanggal' => request('pertemuan_tanggal'), 'semester_id' => request('semester_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id'), 'kelas_id' => request('kelas_id')]) }}" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">Upload Materi</a>
        @endif
    </div>


    @if(request()->filled('pertemuan_tanggal'))
    <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
        Menampilkan data untuk pertemuan tanggal <span class="font-semibold">{{ \Carbon\Carbon::parse(request('pertemuan_tanggal'))->format('d M Y') }}</span>.
        @if(!empty($selectedKelasId))
        Kelas: <span class="font-semibold">{{ optional($kelases->firstWhere('id', (int) $selectedKelasId))->nama ?? '-' }}</span>.
        @endif
    </div>
    @endif

    <form method="GET" class="bg-white rounded-lg shadow p-4 mb-6 space-y-3">
        @if(request()->filled('semester_id'))
        <input type="hidden" name="semester_id" value="{{ request('semester_id') }}">
        @endif
        @if(request()->filled('tahun_ajaran_id'))
        <input type="hidden" name="tahun_ajaran_id" value="{{ request('tahun_ajaran_id') }}">
        @endif

        @if(!$isSiswaScope)
        <div class="grid grid-cols-1 lg:grid-cols-[2fr_auto] gap-3 items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Kelas Terlebih Dahulu</label>
                <select name="kelas_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" onchange="this.form.submit()">
                    <option value="">Pilih Kelas</option>
                    @foreach($kelases as $kelas)
                    <option value="{{ $kelas->id }}" @selected((string) request('kelas_id') === (string) $kelas->id)>{{ $kelas->nama }}</option>
                    @endforeach
                </select>
            </div>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">Terapkan Kelas</button>
        </div>
        @else
            @if(!empty($selectedKelasId))
            <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
            <div class="rounded-lg border border-green-100 bg-green-50 px-3 py-2 text-sm text-green-900">
                Kelas materi otomatis disesuaikan dengan kelas Anda: <span class="font-semibold">{{ optional($kelases->firstWhere('id', (int) $selectedKelasId))->nama ?? '-' }}</span>.
            </div>
            @endif
        @endif

        @if(!empty($selectedKelasId))
        <div class="rounded-lg border border-blue-100 bg-blue-50 px-3 py-2 text-sm text-blue-900">
            Filter lanjutan aktif untuk kelas <span class="font-semibold">{{ optional($kelases->firstWhere('id', (int) $selectedKelasId))->nama ?? '-' }}</span>.
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-3 items-end">
            <div class="lg:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Cari Materi</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul materi..." class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Mata Pelajaran</label>
                <select name="mata_pelajaran_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Mapel</option>
                    @foreach($mataPelajarans as $mp)
                    <option value="{{ $mp->id }}" @selected((string) request('mata_pelajaran_id') === (string) $mp->id)>{{ $mp->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pertemuan</label>
                <input type="date" name="pertemuan_tanggal" value="{{ request('pertemuan_tanggal') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div class="flex items-center gap-2">
                <button class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg">Filter Lanjutan</button>
                <a href="{{ route('akademik.lms.materi.index', array_filter(['kelas_id' => $isSiswaScope ? $selectedKelasId : request('kelas_id'), 'semester_id' => request('semester_id'), 'tahun_ajaran_id' => request('tahun_ajaran_id')], fn ($value) => $value !== null && $value !== '')) }}" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-semibold whitespace-nowrap">Reset</a>
            </div>
        </div>
        @else
        <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-800">
            {{ $isSiswaScope ? 'Kelas siswa Anda belum terhubung. Silakan hubungi admin.' : 'Pilih kelas terlebih dahulu untuk menampilkan filter lanjutan dan daftar materi.' }}
        </div>
        @endif
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
