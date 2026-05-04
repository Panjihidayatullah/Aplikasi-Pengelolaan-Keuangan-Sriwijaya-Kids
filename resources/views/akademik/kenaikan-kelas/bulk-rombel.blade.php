@extends('layouts.app')

@section('title', 'Proses Kenaikan Per Rombel')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Proses Kenaikan Per Rombel</h1>
            <p class="text-gray-600 mt-1">Pilih rombel terlebih dahulu, lalu checklist siswa dan tentukan status kenaikan kelas.</p>
        </div>
        <a href="{{ route('akademik.kenaikan-kelas.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">Pilih Tahun Ajaran</option>
                    @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" @selected((string) $selectedTahunAjaranId === (string) $ta->id)>{{ $ta->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Rombel</label>
                <select name="kelas_id" id="kelas_id_select" placeholder="Pilih Rombel..." required>
                    <option value="">Pilih Rombel</option>
                    @foreach($kelases as $kelas)
                    <option value="{{ $kelas->id }}" @selected((string) $selectedKelasId === (string) $kelas->id)>{{ $kelas->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">Tampilkan</button>
                <a href="{{ route('akademik.kenaikan-kelas.index') }}" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition">Batal</a>
            </div>
        </form>
    </div>

    @if($selectedKelas)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-5 py-4 border-b bg-gray-50 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Daftar Siswa Rombel {{ $selectedKelas->nama }}</h2>
                <p class="text-sm text-gray-600">Nilai rata-rata diambil dari data transkrip nilai pada tahun ajaran terpilih. Status tidak naik akan turun satu tingkat.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2 bg-white px-3 py-1.5 border border-gray-300 rounded-lg shadow-sm">
                    <label for="bulk_status_action" class="text-sm font-semibold text-gray-700 whitespace-nowrap">Set Status Massal:</label>
                    <select id="bulk_status_action" class="text-sm text-gray-800 border-none bg-transparent focus:ring-0 cursor-pointer outline-none">
                        <option value="">-- Pilih Aksi --</option>
                        <option value="naik">Set Naik Kelas</option>
                        <option value="tidak_naik">Set Tidak Naik</option>
                        @if($isLastGrade)
                        <option value="lulus">Set Lulus</option>
                        @endif
                    </select>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700 font-semibold cursor-pointer">
                    <input type="checkbox" id="check_all_rows" class="rounded border-gray-300 w-4 h-4 text-blue-600 focus:ring-blue-500">
                    Pilih Semua
                </label>
            </div>
        </div>

        @if($siswaRows->isEmpty())
        <div class="px-5 py-8 text-center text-gray-500">Belum ada siswa aktif pada rombel ini.</div>
        @else
        <form method="POST" action="{{ route('akademik.kenaikan-kelas.proses-rombel.store') }}" class="p-5">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $selectedKelas->id }}">
            <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTahunAjaranId }}">

            @error('selected')
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>
            @enderror

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full min-w-[1200px]">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-800 w-16">Pilih</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-800">NIS</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-800">Nama Siswa</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-800">Rata-rata Nilai</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-800">Status Kenaikan</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-800">Preview Kelas Tujuan</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-800">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswaRows as $row)
                        @php
                            $siswa = $row['siswa'];
                            $oldStatus = old('rows.' . $siswa->id . '.status', $row['status_default']);
                            $oldCatatan = old('rows.' . $siswa->id . '.catatan', $row['catatan']);
                            $oldChecked = old('selected.' . $siswa->id, '1');
                        @endphp
                        <tr class="border-b align-top">
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <input type="checkbox" name="selected[{{ $siswa->id }}]" value="1" class="row-check rounded border-gray-300" @checked($oldChecked == '1')>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-700">{{ $row['nis'] }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $siswa->nama }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @if($row['rata_rata_nilai'] !== null)
                                <span class="inline-flex items-center px-2.5 py-1 rounded bg-blue-100 text-blue-700 font-semibold">{{ number_format((float) $row['rata_rata_nilai'], 2) }}</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded bg-amber-100 text-amber-700 font-semibold">Belum ada transkrip</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <div class="flex flex-wrap items-center gap-4">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="rows[{{ $siswa->id }}][status]" value="naik" class="rounded-full border-gray-300" @checked($oldStatus === 'naik')>
                                        <span>Naik Kelas</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="rows[{{ $siswa->id }}][status]" value="tidak_naik" class="rounded-full border-gray-300" @checked($oldStatus === 'tidak_naik')>
                                        <span>Tidak Naik Kelas (Menetap)</span>
                                    </label>
                                    @if($isLastGrade)
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="rows[{{ $siswa->id }}][status]" value="lulus" class="rounded-full border-gray-300" @checked($oldStatus === 'lulus')>
                                        <span>Lulus</span>
                                    </label>
                                    @endif
                                </div>
                                @error('rows.' . $siswa->id . '.status')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $row['kelas_tujuan_preview'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <textarea name="rows[{{ $siswa->id }}][catatan]" rows="2" class="w-full min-w-[200px] px-3 py-2 border border-gray-300 rounded-lg" placeholder="Opsional">{{ $oldCatatan }}</textarea>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex items-center gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">Simpan</button>
                <a href="{{ route('akademik.kenaikan-kelas.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition">Batal</a>
            </div>
        </form>
        @endif
    </div>
    @endif
</div>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<style>
    .ts-wrapper.single .ts-control {
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        border-color: #d1d5db;
        min-height: 42px;
        box-shadow: none !important;
        background-color: #ffffff;
        font-size: 1rem;
        line-height: 1.5rem;
    }
    .ts-wrapper.single .ts-control input,
    .ts-wrapper.single .ts-control .item {
        font-size: 1rem;
        line-height: 1.5rem;
    }
    .ts-dropdown {
        border-radius: 0.5rem;
        border-color: #d1d5db;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        font-size: 1rem;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('kelas_id_select')) {
            new TomSelect('#kelas_id_select', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Ketik untuk mencari rombel..."
            });
        }
    });

    (function () {
        const checkAllEl = document.getElementById('check_all_rows');
        if (!checkAllEl) {
            return;
        }

        const rowChecks = Array.from(document.querySelectorAll('.row-check'));
        if (!rowChecks.length) {
            checkAllEl.disabled = true;
            return;
        }

        const syncMaster = function () {
            checkAllEl.checked = rowChecks.every((el) => el.checked);
        };

        checkAllEl.addEventListener('change', function () {
            rowChecks.forEach((el) => {
                el.checked = checkAllEl.checked;
            });
        });

        rowChecks.forEach((el) => {
            el.addEventListener('change', syncMaster);
        });

        syncMaster();

        const bulkStatusSelect = document.getElementById('bulk_status_action');
        if (bulkStatusSelect) {
            bulkStatusSelect.addEventListener('change', function () {
                const status = this.value;
                if (!status) return;

                const checkedRows = rowChecks.filter(el => el.checked);
                if (checkedRows.length === 0) {
                    alert('Silakan centang (pilih) minimal satu siswa pada tabel terlebih dahulu.');
                    this.value = '';
                    return;
                }

                checkedRows.forEach(rowCheck => {
                    const tr = rowCheck.closest('tr');
                    if (tr) {
                        const radio = tr.querySelector(`input[type="radio"][value="${status}"]`);
                        if (radio) {
                            radio.checked = true;
                        }
                    }
                });

                this.value = ''; // Reset dropdown
            });
        }
    })();
</script>
@endsection
