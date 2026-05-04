@extends('layouts.app')

@section('title', 'Transkrip Nilai Per Mapel')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('akademik.transkrip-nilai.index', ['kelas_id' => $kelas->id]) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">← Kembali pilih kelas/mapel</a>
        <div class="mt-2 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Nilai Mata Pelajaran</h1>
                <p class="text-gray-600 mt-1">Kelas {{ $kelas->nama }} • {{ $mataPelajaran->nama }} ({{ $mataPelajaran->kode_mapel ?: '-' }})</p>
            </div>
            <div class="inline-flex items-center gap-2">
                <a href="{{ route('akademik.transkrip-nilai.pengaturan', ['kelas_id' => $kelas->id, 'mapel_id' => $mataPelajaran->id, 'semester_id' => $selectedSemesterId, 'tahun_ajaran_id' => $selectedTahunAjaranId, 'search' => request('search')]) }}" class="inline-flex items-center px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition">
                    Pengaturan Bobot & Grade
                </a>
                @can('create transkrip nilai')
                <a href="{{ route('akademik.transkrip-nilai.create', ['kelas_id' => $kelas->id, 'mapel_id' => $mataPelajaran->id, 'semester_id' => $selectedSemesterId, 'tahun_ajaran_id' => $selectedTahunAjaranId]) }}" class="inline-flex items-center px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    Tambah Nilai
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <form method="GET" action="{{ route('akademik.transkrip-nilai.kelas-mapel', ['kelas' => $kelas, 'mataPelajaran' => $mataPelajaran]) }}" class="w-full flex flex-wrap md:flex-nowrap items-end gap-3">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama siswa, NIS, kelas..." class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Semester</label>
                <select name="semester_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}" @selected((string) $selectedSemesterId === (string) $semester->id)>{{ $semester->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[190px]">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" @selected((string) $selectedTahunAjaranId === (string) $ta->id)>{{ $ta->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-auto md:flex-none flex items-center gap-2">
                <button type="submit" class="flex-1 md:flex-none px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">Filter</button>
                <a href="{{ route('akademik.transkrip-nilai.kelas-mapel', ['kelas' => $kelas, 'mataPelajaran' => $mataPelajaran]) }}" class="flex-1 md:flex-none text-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
            <div>
                <p class="text-sm font-bold text-gray-700">Export, Import & Template Excel</p>
                <p class="text-xs text-gray-500 mt-0.5">Unduh template terlebih dahulu, isi nilai, lalu import kembali.</p>
            </div>
            {{-- Tombol Unduh Template (orange) --}}
            <a href="{{ route('akademik.transkrip-nilai.kelas-mapel.template', ['kelas' => $kelas, 'mataPelajaran' => $mataPelajaran]) }}"
               data-no-loader
               style="background-color:#f97316;color:#fff;"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold text-sm transition shadow-sm hover:opacity-90"
               title="Unduh template Excel kosong yang sudah berisi daftar siswa. Isi kolom nilai_tugas, nilai_uts, nilai_uas lalu import.">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Unduh Template Excel
            </a>
        </div>

        {{-- Panduan singkat --}}
        <div class="rounded-lg border border-orange-100 bg-orange-50 px-4 py-3 text-xs text-orange-800 mb-4 space-y-1">
            <p class="font-semibold">📋 Cara Import Excel:</p>
            <ol class="list-decimal list-inside space-y-0.5 pl-1">
                <li>Klik <strong>Unduh Template Excel</strong> — file sudah berisi daftar nama &amp; NIS siswa kelas ini.</li>
                <li>Isi kolom <strong>nilai_tugas</strong>, <strong>nilai_uts</strong>, dan <strong>nilai_uas</strong> (angka 0–100).</li>
                <li>Jangan ubah kolom <strong>siswa_id</strong>, <strong>nis</strong>, dan <strong>nama_siswa</strong>.</li>
                <li>Simpan file lalu gunakan form <strong>Import Excel</strong> di bawah untuk mengunggah.</li>
            </ol>
        </div>

        <div class="w-full flex flex-wrap md:flex-nowrap items-end gap-3">
            {{-- Export --}}
            <form method="GET" action="{{ route('akademik.transkrip-nilai.kelas-mapel.export', ['kelas' => $kelas, 'mataPelajaran' => $mataPelajaran]) }}" data-no-loader class="w-full md:w-2/5 flex items-end gap-2">
                <input type="hidden" name="semester_id" value="{{ $selectedSemesterId }}">
                <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTahunAjaranId }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Format Export Data Nilai</label>
                    <select name="format" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="excel">Excel (.xlsx) — berisi nilai yang sudah tersimpan</option>
                        <option value="pdf">PDF (.pdf) — cetak rekap nilai</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold whitespace-nowrap text-sm">Export</button>
            </form>

            {{-- Import --}}
            <form method="POST" action="{{ route('akademik.transkrip-nilai.kelas-mapel.import-excel', ['kelas' => $kelas, 'mataPelajaran' => $mataPelajaran]) }}" enctype="multipart/form-data" class="w-full md:w-3/5 flex items-end gap-2">
                @csrf
                <input type="hidden" name="semester_id" value="{{ $selectedSemesterId }}">
                <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTahunAjaranId }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Import File Excel (.xlsx/.xls/.csv)</label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2" required>
                </div>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold whitespace-nowrap text-sm">Import Excel</button>
            </form>
        </div>

        @if($errors->has('excel_file'))
        <div class="mt-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('excel_file') }}
        </div>
        @endif

        @if(session('warning'))
        <div class="mt-3 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            {{ session('warning') }}
        </div>
        @endif

        @if(session('import_errors'))
        <div class="mt-3 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
            <p class="font-semibold">Detail error import:</p>
            <ul class="list-disc list-inside mt-1 space-y-1">
                @foreach(session('import_errors') as $item)
                <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>


    <form id="bulk-nilai-form" method="POST" action="{{ route('akademik.transkrip-nilai.save-nilai', ['kelas' => $kelas, 'mataPelajaran' => $mataPelajaran]) }}">
        @csrf
        <input type="hidden" name="semester_id" value="{{ $selectedSemesterId }}">
        <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTahunAjaranId }}">
        <input type="hidden" name="search" value="{{ request('search') }}">
    </form>

    <div class="mb-4 flex items-center justify-between gap-3">
        <p class="text-sm text-gray-600">Input nilai tugas, UTS, dan UAS langsung pada tabel lalu klik simpan.</p>
        <button type="submit" form="bulk-nilai-form" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition">Simpan Semua Nilai</button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1080px]">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Nama Siswa</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Kelas</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Nilai Tugas</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">UTS</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">UAS</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Nilai Total</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Grade</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    @php
                        $record = $transkripBySiswa->get($student->id);
                        $nilaiTugas = old("nilai.{$student->id}.nilai_harian", $record->nilai_harian ?? '');
                        $nilaiUts = old("nilai.{$student->id}.nilai_uts", $record->nilai_uts ?? '');
                        $nilaiUas = old("nilai.{$student->id}.nilai_uas", $record->nilai_uas ?? '');

                        $hasNilai = $nilaiTugas !== '' || $nilaiUts !== '' || $nilaiUas !== '';
                        $preview = $hasNilai
                            ? \App\Models\TranskripsNilai::hitungNilaiDanGrade((float) ($nilaiTugas ?: 0), (float) ($nilaiUts ?: 0), (float) ($nilaiUas ?: 0))
                            : null;
                    @endphp
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-800 font-semibold">{{ $student->nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $student->kelas->nama ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $mataPelajaran->nama ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <input type="number" step="0.01" min="0" max="100" form="bulk-nilai-form" name="nilai[{{ $student->id }}][nilai_harian]" value="{{ $nilaiTugas }}" class="w-24 px-2 py-1 border border-gray-300 rounded @error("nilai.{$student->id}.nilai_harian") border-red-400 @enderror" placeholder="0-100">
                        </td>
                        <td class="px-6 py-4">
                            <input type="number" step="0.01" min="0" max="100" form="bulk-nilai-form" name="nilai[{{ $student->id }}][nilai_uts]" value="{{ $nilaiUts }}" class="w-24 px-2 py-1 border border-gray-300 rounded @error("nilai.{$student->id}.nilai_uts") border-red-400 @enderror" placeholder="0-100">
                        </td>
                        <td class="px-6 py-4">
                            <input type="number" step="0.01" min="0" max="100" form="bulk-nilai-form" name="nilai[{{ $student->id }}][nilai_uas]" value="{{ $nilaiUas }}" class="w-24 px-2 py-1 border border-gray-300 rounded @error("nilai.{$student->id}.nilai_uas") border-red-400 @enderror" placeholder="0-100">
                        </td>
                        <td class="px-6 py-4 font-bold text-blue-700">{{ $preview ? number_format((float) $preview['nilai_akhir'], 2) : '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1 rounded text-sm font-bold
                                @if(($preview['grade'] ?? '-') === 'A') bg-green-100 text-green-800
                                @elseif(($preview['grade'] ?? '-') === 'B') bg-blue-100 text-blue-800
                                @elseif(($preview['grade'] ?? '-') === 'C') bg-yellow-100 text-yellow-800
                                @elseif(($preview['grade'] ?? '-') === 'D') bg-orange-100 text-orange-800
                                @elseif(($preview['grade'] ?? '-') === 'E') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ $preview['grade'] ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($record)
                            <div class="inline-flex items-center gap-3 whitespace-nowrap">
                                <button type="submit" form="bulk-nilai-form" name="single_siswa_id" value="{{ $student->id }}" class="bg-transparent border-0 p-0 text-blue-600 hover:text-blue-800 font-semibold text-sm">Simpan</button>
                                <a href="{{ route('akademik.transkrip-nilai.edit', $record) }}" class="text-yellow-500 hover:text-yellow-700 font-semibold text-sm">Edit</a>
                                <form method="POST" action="{{ route('akademik.transkrip-nilai.destroy', $record) }}" class="inline-flex" onsubmit="return confirm('Yakin ingin menghapus data nilai ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-sm">Hapus</button>
                                </form>
                            </div>
                            @else
                            <div class="inline-flex items-center gap-3 whitespace-nowrap">
                                <button type="submit" form="bulk-nilai-form" name="single_siswa_id" value="{{ $student->id }}" class="bg-transparent border-0 p-0 text-blue-600 hover:text-blue-800 font-semibold text-sm">Simpan</button>
                                <span class="text-xs text-gray-400">Belum tersimpan</span>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">Tidak ada siswa aktif pada kelas ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
