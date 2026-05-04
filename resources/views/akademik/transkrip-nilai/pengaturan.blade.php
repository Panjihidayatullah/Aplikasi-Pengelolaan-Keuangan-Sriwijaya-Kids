@extends('layouts.app')

@section('title', 'Pengaturan Bobot & Grade')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ $backUrl }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">← Kembali</a>
        <div class="mt-2">
            <h1 class="text-3xl font-bold text-gray-800">Pengaturan Bobot & Grade Nilai</h1>
            <p class="text-gray-600 mt-1">Atur bobot nilai akhir dan rentang grade (A sampai E) secara terpusat.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('akademik.transkrip-nilai.pengaturan.update') }}" class="space-y-6">
        @csrf

        <input type="hidden" name="kelas_id" value="{{ $returnKelasId }}">
        <input type="hidden" name="mapel_id" value="{{ $returnMapelId }}">
        <input type="hidden" name="semester_id" value="{{ $returnSemesterId }}">
        <input type="hidden" name="tahun_ajaran_id" value="{{ $returnTahunAjaranId }}">
        <input type="hidden" name="search" value="{{ $returnSearch }}">

        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Bobot Nilai</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bobot Nilai Tugas (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="bobot_tugas" value="{{ old('bobot_tugas', $pengaturan->bobot_tugas) }}" class="w-full px-4 py-2 border @error('bobot_tugas') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bobot UTS (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="bobot_uts" value="{{ old('bobot_uts', $pengaturan->bobot_uts) }}" class="w-full px-4 py-2 border @error('bobot_uts') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bobot UAS (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="bobot_uas" value="{{ old('bobot_uas', $pengaturan->bobot_uas) }}" class="w-full px-4 py-2 border @error('bobot_uas') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Total bobot harus tepat 100%.</p>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Rentang Grade</h2>
            <p class="text-sm text-gray-600 mb-4">Isi batas minimum dan maksimum untuk setiap grade. Rentang tidak boleh bertumpang tindih.</p>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px]">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Grade</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nilai Minimum</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nilai Maksimum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['a', 'b', 'c', 'd', 'e'] as $grade)
                        <tr class="border-b">
                            <td class="px-4 py-3 text-sm font-bold text-gray-800">{{ strtoupper($grade) }}</td>
                            <td class="px-4 py-3">
                                <input type="number" step="0.01" min="0" max="100" name="grade_{{ $grade }}_min" value="{{ old('grade_' . $grade . '_min', data_get($pengaturan, 'grade_' . $grade . '_min')) }}" class="w-full px-3 py-2 border @error('grade_' . $grade . '_min') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" step="0.01" min="0" max="100" name="grade_{{ $grade }}_max" value="{{ old('grade_' . $grade . '_max', data_get($pengaturan, 'grade_' . $grade . '_max')) }}" class="w-full px-3 py-2 border @error('grade_' . $grade . '_max') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($errors->any())
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition">Simpan Pengaturan</button>
            <a href="{{ $backUrl }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition">Batal</a>
        </div>
    </form>
</div>
@endsection
