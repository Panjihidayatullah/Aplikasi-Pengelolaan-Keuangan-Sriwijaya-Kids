@extends('layouts.app')

@section('title', 'Edit Kenaikan Kelas')

@section('content')
<div class="container mx-auto px-4 py-6">
    @php
        $formatKelasLabel = function ($kelas) {
            $nama = $kelas->nama ?? $kelas->nama_kelas ?? '-';
            $tingkat = (int) ($kelas->tingkat ?: \App\Models\Kelas::inferTingkatFromNama((string) ($kelas->nama_kelas ?? $nama)));
            $rombel = '';
            if (preg_match('/([A-Za-z]+)$/', trim((string) ($kelas->nama_kelas ?? $nama)), $matches)) {
                $rombel = strtoupper((string) ($matches[1] ?? ''));
            }

            return trim($nama . ' (Tingkat ' . ($tingkat > 0 ? $tingkat : '-') . ($rombel !== '' ? ' - Rombel ' . $rombel : '') . ')');
        };
    @endphp

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Kenaikan Kelas</h1>
            <p class="text-gray-600 mt-1">Perbarui hasil proses kenaikan kelas</p>
        </div>
        <a href="{{ route('akademik.kenaikan-kelas.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8 md:p-10 max-w-6xl w-full mx-auto">
        <form method="POST" action="{{ route('akademik.kenaikan-kelas.update', $kenaikanKelas) }}">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="siswa_id" class="block text-sm font-semibold text-gray-700 mb-2">Siswa</label>
                <select id="siswa_id" name="siswa_id" class="w-full px-4 py-2 border @error('siswa_id') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                    @foreach($siswas as $siswa)
                    @php
                        $kelasSiswa = $siswa->kelas;
                        $namaKelas = $kelasSiswa->nama_kelas ?? ($kelasSiswa->nama ?? '-');
                        $tingkat = (int) ($kelasSiswa->tingkat ?? \App\Models\Kelas::inferTingkatFromNama((string) $namaKelas));
                        $rombel = '';
                        if (preg_match('/([A-Za-z]+)$/', trim((string) $namaKelas), $matches)) {
                            $rombel = strtoupper((string) ($matches[1] ?? ''));
                        }
                    @endphp
                    <option
                        value="{{ $siswa->id }}"
                        data-kelas-id="{{ $kelasSiswa->id ?? '' }}"
                        data-kelas-tingkat="{{ $tingkat > 0 ? $tingkat : '' }}"
                        data-kelas-rombel="{{ $rombel }}"
                        @selected(old('siswa_id', $kenaikanKelas->siswa_id) == $siswa->id)
                    >
                        {{ $siswa->nama }} ({{ $namaKelas }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-6">
                    <label for="kelas_sekarang_id" class="block text-sm font-semibold text-gray-700 mb-2">Kelas Saat Ini</label>
                    <select id="kelas_sekarang_id" name="kelas_sekarang_id" class="w-full px-4 py-2 border @error('kelas_sekarang_id') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                        @foreach($kelases as $kelas)
                        @php
                            $namaKelas = $kelas->nama_kelas ?? ($kelas->nama ?? '-');
                            $tingkat = (int) ($kelas->tingkat ?: \App\Models\Kelas::inferTingkatFromNama((string) $namaKelas));
                            $rombel = '';
                            if (preg_match('/([A-Za-z]+)$/', trim((string) $namaKelas), $matches)) {
                                $rombel = strtoupper((string) ($matches[1] ?? ''));
                            }
                        @endphp
                        <option value="{{ $kelas->id }}" data-tingkat="{{ $tingkat > 0 ? $tingkat : '' }}" data-rombel="{{ $rombel }}" data-is-lulus="{{ $kelas->is_tingkat_akhir ? '1' : '0' }}" @selected(old('kelas_sekarang_id', $kenaikanKelas->kelas_sekarang_id) == $kelas->id)>{{ $formatKelasLabel($kelas) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-6">
                    <label for="kelas_tujuan_id" class="block text-sm font-semibold text-gray-700 mb-2">Kelas Tujuan</label>
                    <select id="kelas_tujuan_id" name="kelas_tujuan_id" class="w-full px-4 py-2 border @error('kelas_tujuan_id') border-red-400 @else border-gray-300 @enderror rounded-lg">
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        @foreach($kelases as $kelas)
                        @php
                            $namaKelas = $kelas->nama_kelas ?? ($kelas->nama ?? '-');
                            $tingkat = (int) ($kelas->tingkat ?: \App\Models\Kelas::inferTingkatFromNama((string) $namaKelas));
                            $rombel = '';
                            if (preg_match('/([A-Za-z]+)$/', trim((string) $namaKelas), $matches)) {
                                $rombel = strtoupper((string) ($matches[1] ?? ''));
                            }
                        @endphp
                        <option value="{{ $kelas->id }}" data-tingkat="{{ $tingkat > 0 ? $tingkat : '' }}" data-rombel="{{ $rombel }}" data-is-lulus="{{ $kelas->is_tingkat_akhir ? '1' : '0' }}" @selected(old('kelas_tujuan_id', $kenaikanKelas->kelas_tujuan_id) == $kelas->id)>{{ $formatKelasLabel($kelas) }}</option>
                        @endforeach
                    </select>
                    <p id="kelas_tujuan_hint" class="text-xs text-gray-500 mt-2">Pilihan kelas tujuan menyesuaikan status dan akan memprioritaskan rombel yang sama saat naik kelas.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="mb-6">
                    <label for="tahun_ajaran_id" class="block text-sm font-semibold text-gray-700 mb-2">Tahun Ajaran</label>
                    <select id="tahun_ajaran_id" name="tahun_ajaran_id" class="w-full px-4 py-2 border @error('tahun_ajaran_id') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                        @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" @selected(old('tahun_ajaran_id', $kenaikanKelas->tahun_ajaran_id) == $ta->id)>{{ $ta->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-6">
                    <label for="rata_rata_nilai" class="block text-sm font-semibold text-gray-700 mb-2">Rata-rata Nilai</label>
                    <input type="number" step="0.01" min="0" max="100" id="rata_rata_nilai" name="rata_rata_nilai" value="{{ old('rata_rata_nilai', $kenaikanKelas->rata_rata_nilai) }}" class="w-full px-4 py-2 border @error('rata_rata_nilai') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                </div>
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select id="status" name="status" class="w-full px-4 py-2 border @error('status') border-red-400 @else border-gray-300 @enderror rounded-lg" required>
                    <option value="naik" @selected(old('status', $kenaikanKelas->status) == 'naik')>Naik Kelas</option>
                    <option value="tidak_naik" @selected(old('status', $kenaikanKelas->status) == 'tidak_naik')>Tidak Naik (Menetap)</option>
                    <option value="lulus" @selected(old('status', $kenaikanKelas->status) == 'lulus')>Lulus</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                <textarea id="catatan" name="catatan" rows="3" class="w-full px-4 py-2 border @error('catatan') border-red-400 @else border-gray-300 @enderror rounded-lg">{{ old('catatan', $kenaikanKelas->catatan) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">Simpan Perubahan</button>
                <a href="{{ route('akademik.kenaikan-kelas.show', $kenaikanKelas) }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const siswaSelect = document.getElementById('siswa_id');
        const kelasSekarangSelect = document.getElementById('kelas_sekarang_id');
        const kelasTujuanSelect = document.getElementById('kelas_tujuan_id');
        const statusSelect = document.getElementById('status');
        const hintEl = document.getElementById('kelas_tujuan_hint');

        if (!siswaSelect || !kelasSekarangSelect || !kelasTujuanSelect || !statusSelect) {
            return;
        }

        const allTujuanOptions = Array.from(kelasTujuanSelect.querySelectorAll('option'))
            .filter((opt) => opt.value !== '');

        function parseIntSafe(value) {
            const parsed = parseInt(value, 10);
            return Number.isNaN(parsed) ? 0 : parsed;
        }

        function getSelectedOption(selectEl) {
            return selectEl.options[selectEl.selectedIndex] || null;
        }

        function syncKelasSekarangWithSiswa() {
            const selectedSiswa = getSelectedOption(siswaSelect);
            if (!selectedSiswa) {
                return;
            }

            const kelasId = selectedSiswa.dataset.kelasId || '';
            if (kelasId) {
                kelasSekarangSelect.value = kelasId;
            }
        }

        function rebuildTujuanOptions() {
            const status = statusSelect.value;
            const kelasSekarangOption = getSelectedOption(kelasSekarangSelect);
            const currentTingkat = parseIntSafe(kelasSekarangOption?.dataset?.tingkat || '0');
            const currentRombel = (kelasSekarangOption?.dataset?.rombel || '').toUpperCase();
            const previousSelection = kelasTujuanSelect.value;

            kelasTujuanSelect.innerHTML = '';

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = '-- Pilih Kelas Tujuan --';
            kelasTujuanSelect.appendChild(placeholder);

            const isLulusClass = kelasSekarangOption?.dataset?.isLulus === '1';
            const lulusOption = statusSelect.querySelector('option[value="lulus"]');
            
            if (lulusOption) {
                if (isLulusClass) {
                    lulusOption.style.display = '';
                    lulusOption.disabled = false;
                } else {
                    lulusOption.style.display = 'none';
                    lulusOption.disabled = true;
                    if (status === 'lulus') {
                        statusSelect.value = '';
                        // Re-trigger since status changed
                        kelasTujuanSelect.innerHTML = '';
                        return;
                    }
                }
            }

            if (statusSelect.value === 'lulus') {
                if (!kelasSekarangOption || !isLulusClass) {
                    // Fallback in case they somehow selected it
                    statusSelect.value = '';
                    kelasTujuanSelect.disabled = true;
                    return;
                }
                kelasTujuanSelect.value = '';
                kelasTujuanSelect.setAttribute('disabled', 'disabled');
                hintEl.textContent = 'Status lulus tidak memerlukan kelas tujuan.';
                return;
            }

            kelasTujuanSelect.removeAttribute('disabled');

            if (!currentTingkat) {
                hintEl.textContent = 'Pilih kelas saat ini terlebih dahulu untuk mendapatkan rekomendasi kelas tujuan.';
                return;
            }

            const targetTingkat = status === 'naik' ? currentTingkat + 1 : currentTingkat;
            const candidates = allTujuanOptions.filter((option) => {
                return parseIntSafe(option.dataset.tingkat || '0') === targetTingkat;
            });

            candidates.forEach((option) => {
                kelasTujuanSelect.appendChild(option.cloneNode(true));
            });

            if (!candidates.length) {
                hintEl.textContent = status === 'naik'
                    ? 'Kelas tingkat berikutnya belum tersedia. Tambahkan kelas tujuan terlebih dahulu.'
                    : 'Kelas tingkat sebelumnya belum tersedia. Tambahkan kelas tujuan terlebih dahulu.';
                return;
            }

            const renderedOptions = Array.from(kelasTujuanSelect.querySelectorAll('option'))
                .filter((opt) => opt.value !== '');

            const previousStillExists = renderedOptions.some((opt) => opt.value === previousSelection);
            if (previousStillExists) {
                kelasTujuanSelect.value = previousSelection;
            } else if (status === 'naik') {
                const sameRombel = renderedOptions.find((opt) => {
                    return (opt.dataset.rombel || '').toUpperCase() === currentRombel && currentRombel !== '';
                });
                kelasTujuanSelect.value = sameRombel ? sameRombel.value : renderedOptions[0].value;
            } else {
                const sameRombel = renderedOptions.find((opt) => {
                    return (opt.dataset.rombel || '').toUpperCase() === currentRombel && currentRombel !== '';
                });
                kelasTujuanSelect.value = sameRombel ? sameRombel.value : renderedOptions[0].value;
            }

            hintEl.textContent = status === 'naik'
                ? 'Rekomendasi diarahkan ke tingkat berikutnya, rombel sama diprioritaskan jika tersedia.'
                : 'Untuk status tidak naik, kelas tujuan diarahkan ke kelas yang sama saat ini.';
        }

        siswaSelect.addEventListener('change', function () {
            syncKelasSekarangWithSiswa();
            rebuildTujuanOptions();
        });

        kelasSekarangSelect.addEventListener('change', rebuildTujuanOptions);
        statusSelect.addEventListener('change', rebuildTujuanOptions);

        rebuildTujuanOptions();
    })();
</script>
@endsection
