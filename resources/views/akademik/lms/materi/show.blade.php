@extends('layouts.app')

@section('title', 'Detail Materi')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl">
    <a href="{{ route('akademik.lms.materi.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-block mb-4"><- Kembali ke daftar materi</a>

    <div class="bg-white rounded-lg shadow p-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold text-gray-800">{{ $materi->judul }}</h1>
            <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700 uppercase">{{ $materi->tipe }}</span>
        </div>

        <p class="text-gray-700 mb-6">{{ $materi->deskripsi ?: '-' }}</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 mb-6">
            <p><span class="font-semibold text-gray-700">Kelas:</span> {{ $materi->kelas->nama ?? '-' }}</p>
            <p><span class="font-semibold text-gray-700">Mapel:</span> {{ $materi->mataPelajaran->nama ?? '-' }}</p>
            <p><span class="font-semibold text-gray-700">Semester:</span> {{ $materi->semester->nama ?? '-' }}</p>
            <p><span class="font-semibold text-gray-700">Tahun Ajaran:</span> {{ $materi->tahunAjaran->nama ?? '-' }}</p>
            <p><span class="font-semibold text-gray-700">Pengajar:</span> {{ $materi->guru->nama ?? '-' }}</p>
            <p><span class="font-semibold text-gray-700">Publikasi:</span> {{ $materi->published_at?->format('d M Y H:i') ?? '-' }}</p>
        </div>

          <div class="flex flex-wrap gap-3">
            @if(is_admin() || auth()->user()->hasRole('Guru'))
                <a href="{{ route('akademik.lms.materi.edit', $materi) }}"
                    class="inline-flex items-center px-5 py-2.5 bg-yellow-500 hover:bg-yellow-600 rounded-lg font-semibold !text-white no-underline shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:ring-offset-2"
                    style="color: #fff !important; text-decoration: none !important;">Edit Materi</a>
            @endif
            @if($materi->file_path)
                <a href="{{ route('akademik.lms.materi.file', $materi) }}"
                    target="_blank"
                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold !text-white no-underline shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2"
                    style="color: #fff !important; text-decoration: none !important;">Lihat File</a>
                <a href="{{ route('akademik.lms.materi.download', $materi) }}"
                    class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg font-semibold !text-white no-underline shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2"
                    style="background-color: #dc2626 !important; border: 1px solid #b91c1c !important; color: #fff !important; text-decoration: none !important;">Unduh File</a>
            @endif
            @if($materi->video_url)
                <a href="{{ $materi->video_url }}"
                    target="_blank"
                    class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-semibold !text-white no-underline shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:ring-offset-2"
                    style="color: #fff !important; text-decoration: none !important;">Buka Link/Video</a>
            @endif
        </div>

        @if($materi->file_path && ($materi->tipe === 'pdf' || \Illuminate\Support\Str::endsWith(strtolower($materi->file_path), '.pdf')))
        <div class="mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Preview PDF</h2>
            <iframe
                src="{{ route('akademik.lms.materi.file', $materi) }}#zoom=125"
                class="w-full border border-gray-200 rounded-lg"
                height="1200"
                loading="lazy"
            ></iframe>
        </div>
        @endif
    </div>
</div>
@endsection
