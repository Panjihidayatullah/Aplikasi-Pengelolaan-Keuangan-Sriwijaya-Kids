@extends('layouts.app')

@section('title', 'Edit Tugas LMS')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit Tugas LMS</h1>
    <p class="text-gray-600 mb-6">Perbarui tugas dan deadline</p>

    <div class="bg-white rounded-lg shadow p-8 md:p-10 max-w-6xl w-full mx-auto">
        <form method="POST" action="{{ route('akademik.lms.tugas.update', $tugas) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Tugas</label>
                    <input type="text" name="judul" value="{{ old('judul', $tugas->judul) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Instruksi</label>
                    <textarea name="instruksi" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('instruksi', $tugas->instruksi) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deadline</label>
                    <input type="datetime-local" name="tanggal_deadline" value="{{ old('tanggal_deadline', $tugas->tanggal_deadline?->format('Y-m-d\\TH:i')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nilai Maksimal</label>
                    <input type="number" step="0.01" min="1" max="100" name="max_nilai" value="{{ old('max_nilai', $tugas->max_nilai) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Lampiran Baru (opsional)</label>
                    <input type="file" name="lampiran" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-</option>
                        @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}" @selected((string) old('kelas_id', $tugas->kelas_id) === (string) $kelas->id)>{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mata Pelajaran</label>
                    <select name="mata_pelajaran_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-</option>
                        @foreach($mataPelajarans as $mp)
                        <option value="{{ $mp->id }}" @selected((string) old('mata_pelajaran_id', $tugas->mata_pelajaran_id) === (string) $mp->id)>{{ $mp->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Semester</label>
                    <select name="semester_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-</option>
                        @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected((string) old('semester_id', $tugas->semester_id) === (string) $semester->id)>{{ $semester->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">-</option>
                        @foreach($tahunAjarans as $tahun)
                        <option value="{{ $tahun->id }}" @selected((string) old('tahun_ajaran_id', $tugas->tahun_ajaran_id) === (string) $tahun->id)>{{ $tahun->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pertemuan</label>
                    <input type="date" name="tanggal_pertemuan" value="{{ old('tanggal_pertemuan', optional($tugas->tanggal_pertemuan)->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">Simpan Perubahan</button>
                <a href="{{ route('akademik.lms.tugas.show', $tugas) }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
