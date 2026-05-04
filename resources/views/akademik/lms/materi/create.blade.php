@extends('layouts.app')

@section('title', 'Upload Materi LMS')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Upload Materi LMS</h1>
    <p class="text-gray-600 mb-6">Tambahkan materi pembelajaran untuk kelas</p>

    @if(!empty($prefillPertemuanTanggal))
    <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
        <p class="font-semibold">Mode Pertemuan Aktif</p>
        <p>Materi ini akan dihubungkan ke tanggal pertemuan: <span class="font-semibold">{{ \Carbon\Carbon::parse($prefillPertemuanTanggal)->format('d M Y') }}</span></p>
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-semibold mb-2">Gagal menyimpan materi. Periksa data berikut:</p>
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow p-8 md:p-10 max-w-6xl w-full mx-auto">
        <form method="POST" action="{{ route('akademik.lms.materi.store') }}" enctype="multipart/form-data">
            @csrf
            @if(!empty($prefillPertemuanTanggal))
            <input type="hidden" name="pertemuan_tanggal_context" value="{{ $prefillPertemuanTanggal }}">
            @endif
            @if(!empty($prefillSemesterId))
            <input type="hidden" name="semester_id_context" value="{{ $prefillSemesterId }}">
            @endif
            @if(!empty($prefillTahunAjaranId))
            <input type="hidden" name="tahun_ajaran_id_context" value="{{ $prefillTahunAjaranId }}">
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Judul</label>
                    <input type="text" name="judul" value="{{ old('judul') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base">{{ old('deskripsi') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe Materi</label>
                    <select name="tipe" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base" required>
                        <option value="pdf" @selected(old('tipe') === 'pdf')>PDF</option>
                        <option value="video" @selected(old('tipe') === 'video')>Video</option>
                        <option value="ppt" @selected(old('tipe') === 'ppt')>PPT</option>
                        <option value="link" @selected(old('tipe') === 'link')>Link</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">File</label>
                    <input type="file" name="file" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Video/Link URL</label>
                    <input type="url" name="video_url" value="{{ old('video_url') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base" placeholder="https://...">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base">
                        <option value="">-</option>
                        @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}" @selected((string) old('kelas_id', $prefillKelasId ?? null) === (string) $kelas->id)>{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base">
                        <option value="">-</option>
                        @foreach($mataPelajarans as $mp)
                        <option value="{{ $mp->id }}" @selected((string) old('mata_pelajaran_id') === (string) $mp->id)>{{ $mp->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Semester</label>
                    <select name="semester_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base">
                        <option value="">-</option>
                        @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected((string) old('semester_id', $prefillSemesterId) === (string) $semester->id)>{{ $semester->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base">
                        <option value="">-</option>
                        @foreach($tahunAjarans as $tahun)
                        <option value="{{ $tahun->id }}" @selected((string) old('tahun_ajaran_id', $prefillTahunAjaranId) === (string) $tahun->id)>{{ $tahun->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pertemuan</label>
                    <input type="date" name="tanggal_pertemuan" value="{{ old('tanggal_pertemuan', $prefillPertemuanTanggal) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base">
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">Simpan Materi</button>
                <a href="{{ route('akademik.lms.materi.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold">Batal</a>
                @if(!empty($prefillPertemuanTanggal))
                <a href="{{ route('akademik.lms.pertemuan', array_filter(['tanggal' => $prefillPertemuanTanggal, 'semester_id' => old('semester_id', $prefillSemesterId), 'kelas_id' => old('kelas_id', $prefillKelasId ?? null)], fn($value) => $value !== null && $value !== '')) }}" class="px-6 py-3 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg font-semibold">Kembali ke Pertemuan</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
