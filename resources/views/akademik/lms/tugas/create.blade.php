@extends('layouts.app')

@section('title', 'Buat Tugas LMS')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Buat Tugas LMS</h1>
    <p class="text-gray-600 mb-6">Guru dapat memberi tugas beserta deadline</p>

    @if(!empty($prefillPertemuanTanggal))
    <div class="mb-6 rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-900">
        <p class="font-semibold">Mode Pertemuan Aktif</p>
        <p>Tugas ini akan dihubungkan ke tanggal pertemuan: <span class="font-semibold">{{ \Carbon\Carbon::parse($prefillPertemuanTanggal)->format('d M Y') }}</span></p>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow p-8 md:p-10 max-w-6xl w-full mx-auto">
        <form method="POST" action="{{ route('akademik.lms.tugas.store') }}" enctype="multipart/form-data">
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Tugas</label>
                    <input type="text" name="judul" value="{{ old('judul') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Instruksi</label>
                    <textarea name="instruksi" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('instruksi') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deadline</label>
                    <input type="datetime-local" name="tanggal_deadline" value="{{ old('tanggal_deadline') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nilai Maksimal</label>
                    <input type="number" step="0.01" min="1" max="100" name="max_nilai" value="{{ old('max_nilai', 100) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Lampiran Tugas</label>
                    <input type="file" name="lampiran" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-</option>
                        @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}" @selected((string) old('kelas_id', $prefillKelasId ?? null) === (string) $kelas->id)>{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-</option>
                        @foreach($mataPelajarans as $mp)
                        <option value="{{ $mp->id }}" @selected((string) old('mata_pelajaran_id') === (string) $mp->id)>{{ $mp->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Semester</label>
                    <select name="semester_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-</option>
                        @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected((string) old('semester_id', $prefillSemesterId) === (string) $semester->id)>{{ $semester->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-</option>
                        @foreach($tahunAjarans as $tahun)
                        <option value="{{ $tahun->id }}" @selected((string) old('tahun_ajaran_id', $prefillTahunAjaranId) === (string) $tahun->id)>{{ $tahun->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pertemuan</label>
                    <input type="date" name="tanggal_pertemuan" value="{{ old('tanggal_pertemuan', $prefillPertemuanTanggal) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">Simpan Tugas</button>
                <a href="{{ route('akademik.lms.tugas.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold">Batal</a>
                @if(!empty($prefillPertemuanTanggal))
                <a href="{{ route('akademik.lms.pertemuan', array_filter(['tanggal' => $prefillPertemuanTanggal, 'semester_id' => old('semester_id', $prefillSemesterId), 'kelas_id' => old('kelas_id', $prefillKelasId ?? null)], fn($value) => $value !== null && $value !== '')) }}" class="px-6 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg font-semibold">Kembali ke Pertemuan</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
