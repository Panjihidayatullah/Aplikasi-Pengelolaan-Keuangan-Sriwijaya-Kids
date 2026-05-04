@extends('layouts.app')

@section('title', 'Edit Jadwal Pelajaran')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Jadwal Pelajaran</h1>
        <p class="text-gray-600 mt-1">Perbarui jadwal kelas, mapel, guru, hari, dan jam.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-8 md:p-10 max-w-6xl w-full mx-auto">
        <form method="POST" action="{{ route('akademik.jadwal-pelajaran.update', $jadwalPelajaran) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">
                <label class="inline-flex items-center gap-2 text-sm font-semibold text-amber-900">
                    <input id="is_istirahat" type="checkbox" name="is_istirahat" value="1" @checked(old('is_istirahat', $jadwalPelajaran->is_istirahat))>
                    Jadwal Ishoma / Istirahat
                </label>
                <p class="mt-1 text-xs text-amber-800">Jika dipilih, Mata Pelajaran, Guru, dan Ruang menjadi opsional.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kelas <span class="text-red-500">*</span></label>
                    <select name="kelas_id" class="w-full px-4 py-2 border @error('kelas_id') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                        @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}" @selected(old('kelas_id', $jadwalPelajaran->kelas_id) == $kelas->id)>{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                    @error('kelas_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mata Pelajaran <span data-required-mark="mapel" class="text-red-500">*</span></label>
                    <select id="mata_pelajaran_id" name="mata_pelajaran_id" class="w-full px-4 py-2 border @error('mata_pelajaran_id') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($mataPelajarans as $mapel)
                        <option value="{{ $mapel->id }}" @selected(old('mata_pelajaran_id', $jadwalPelajaran->mata_pelajaran_id) == $mapel->id)>{{ $mapel->nama }}</option>
                        @endforeach
                    </select>
                    @error('mata_pelajaran_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Guru Pengampu <span data-required-mark="guru" class="text-red-500">*</span></label>
                    <select id="guru_id" name="guru_id" class="w-full px-4 py-2 border @error('guru_id') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}" @selected(old('guru_id', $jadwalPelajaran->guru_id) == $guru->id)>{{ $guru->nama }}</option>
                        @endforeach
                    </select>
                    @error('guru_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hari <span class="text-red-500">*</span></label>
                    <select name="hari" class="w-full px-4 py-2 border @error('hari') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                        @foreach($hariOptions as $hari)
                        <option value="{{ $hari }}" @selected(old('hari', $jadwalPelajaran->hari) == $hari)>{{ $hari }}</option>
                        @endforeach
                    </select>
                    @error('hari')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai', substr((string) $jadwalPelajaran->jam_mulai, 0, 5)) }}" class="w-full px-4 py-2 border @error('jam_mulai') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                    @error('jam_mulai')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai', substr((string) $jadwalPelajaran->jam_selesai, 0, 5)) }}" class="w-full px-4 py-2 border @error('jam_selesai') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                    @error('jam_selesai')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ruang <span data-required-mark="ruang" class="text-red-500">*</span></label>
                    <select id="ruang_id" name="ruang_id" class="w-full px-4 py-2 border @error('ruang_id') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                        <option value="">-- Pilih Ruang --</option>
                        @foreach($ruangs as $ruang)
                        <option value="{{ $ruang->id }}" @selected(old('ruang_id', $jadwalPelajaran->ruang_id) == $ruang->id)>{{ $ruang->kode_ruang }} - {{ $ruang->nama }}</option>
                        @endforeach
                    </select>
                    @error('ruang_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end pb-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 font-semibold">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $jadwalPelajaran->is_active))>
                        Jadwal aktif
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border @error('keterangan') border-red-400 @else border-gray-300 @enderror rounded-lg">{{ old('keterangan', $jadwalPelajaran->keterangan) }}</textarea>
                @error('keterangan')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">Simpan Perubahan</button>
                <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const breakToggle = document.getElementById('is_istirahat');
        if (!breakToggle) return;

        const mapel = document.getElementById('mata_pelajaran_id');
        const guru = document.getElementById('guru_id');
        const ruang = document.getElementById('ruang_id');
        const marks = {
            mapel: document.querySelector('[data-required-mark="mapel"]'),
            guru: document.querySelector('[data-required-mark="guru"]'),
            ruang: document.querySelector('[data-required-mark="ruang"]'),
        };

        const updateBreakMode = () => {
            const isBreak = breakToggle.checked;
            [mapel, guru, ruang].forEach((field) => {
                if (!field) return;
                field.required = !isBreak;
                field.disabled = isBreak;
            });

            Object.values(marks).forEach((mark) => {
                if (!mark) return;
                mark.classList.toggle('hidden', isBreak);
            });
        };

        breakToggle.addEventListener('change', updateBreakMode);
        updateBreakMode();
    });
</script>
@endpush
