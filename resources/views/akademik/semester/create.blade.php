@extends('layouts.app')

@section('title', 'Tambah Semester')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Tambah Semester</h1>
        <p class="text-gray-600 mt-1">Buat periode semester baru</p>
    </div>

    <div class="bg-white rounded-lg shadow p-8 md:p-10 max-w-6xl w-full mx-auto">
        <form method="POST" action="{{ route('akademik.semester.store') }}">
            @csrf

            <div class="mb-6">
                <label for="tahun_ajaran_id" class="block text-sm font-semibold text-gray-700 mb-2">Tahun Ajaran</label>
                <select id="tahun_ajaran_id" name="tahun_ajaran_id" class="w-full px-4 py-2 border @error('tahun_ajaran_id') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    @foreach($tahunAjarans as $tahun)
                    <option value="{{ $tahun->id }}" @selected((string) old('tahun_ajaran_id') === (string) $tahun->id)>{{ $tahun->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label for="nomor_semester" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Semester</label>
                <select id="nomor_semester" name="nomor_semester" class="w-full px-4 py-2 border @error('nomor_semester') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                    <option value="">-- Pilih Semester --</option>
                    <option value="1" @selected(old('nomor_semester') == '1')>Semester 1</option>
                    <option value="2" @selected(old('nomor_semester') == '2')>Semester 2</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-6">
                    <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="w-full px-4 py-2 border @error('tanggal_mulai') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                </div>
                <div class="mb-6">
                    <label for="tanggal_selesai" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="w-full px-4 py-2 border @error('tanggal_selesai') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-6">
                    <label for="tanggal_uts" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal UTS</label>
                    <input type="date" id="tanggal_uts" name="tanggal_uts" value="{{ old('tanggal_uts') }}" class="w-full px-4 py-2 border @error('tanggal_uts') border-red-400 @else border-gray-300 @enderror rounded-lg">
                </div>
                <div class="mb-6">
                    <label for="tanggal_uas" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal UAS</label>
                    <input type="date" id="tanggal_uas" name="tanggal_uas" value="{{ old('tanggal_uas') }}" class="w-full px-4 py-2 border @error('tanggal_uas') border-red-400 @else border-gray-300 @enderror rounded-lg">
                </div>
            </div>

            <div class="mb-6 flex items-center gap-3">
                <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active')) class="w-5 h-5 rounded">
                <label for="is_active" class="text-sm font-semibold text-gray-700">Jadikan semester aktif</label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">Simpan</button>
                <a href="{{ route('akademik.semester.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
