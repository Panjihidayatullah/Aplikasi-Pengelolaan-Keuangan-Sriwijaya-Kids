@extends('layouts.app')

@section('title', 'Raport Siswa - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- ── HEADER ── --}}
    <div class="bg-white border-b shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-5 flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Raport Siswa</h1>
                <p class="text-sm text-gray-500 mt-0.5">Rekap nilai per kelas & semester sepanjang riwayat belajar siswa</p>
            </div>
            @if($selectedSiswa && $raportData->isNotEmpty())
            <a href="{{ route('akademik.raport.pdf', $selectedSiswa->id) }}"
               data-no-loader target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                Export PDF
            </a>
            @endif
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-6 space-y-6">

        {{-- ── FORM PILIH SISWA ── --}}
        @php
            $siswasJson = $siswas->map(fn($s) => [
                'id'    => $s->id,
                'label' => $s->nama . ($s->nis ? ' (' . $s->nis . ')' : ''),
                'nama'  => $s->nama,
                'nis'   => $s->nis ?? '',
            ])->values();
            $inputValue = $selectedSiswa
                ? $selectedSiswa->nama . ($selectedSiswa->nis ? ' (' . $selectedSiswa->nis . ')' : '')
                : '';
        @endphp

        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm font-semibold text-gray-800 mb-4">Pilih Siswa</p>
            <div class="relative" id="siswa-search-wrapper">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Cari Nama / NIS Siswa</label>
                {{-- Input + tombol × dalam flex container --}}
                <div id="siswa-input-wrapper"
                     class="flex items-center border border-gray-300 rounded-lg overflow-hidden
                            focus-within:ring-2 focus-within:ring-blue-400 focus-within:border-blue-400 transition">
                    <input
                        type="text"
                        id="siswa-search-input"
                        autocomplete="off"
                        placeholder="Ketik nama atau NIS siswa..."
                        value="{{ $inputValue }}"
                        class="flex-1 px-4 py-2.5 text-sm bg-transparent outline-none min-w-0"
                    >
                    <button type="button" id="siswa-search-clear"
                            class="{{ $inputValue !== '' ? 'flex' : 'hidden' }}
                                   items-center justify-center mr-2 w-5 h-5 rounded-full flex-shrink-0
                                   bg-gray-200 hover:bg-gray-300 text-gray-500 transition"
                            title="Hapus pilihan">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>


                {{-- Dropdown --}}
                <div id="siswa-dropdown"
                     class="hidden absolute z-50 w-full mt-1.5 bg-white border border-gray-200
                            rounded-xl shadow-2xl max-h-72 overflow-y-auto divide-y divide-gray-50">
                </div>
                <p id="siswa-no-result" class="hidden mt-2 text-xs text-gray-400 pl-1">
                    Tidak ada siswa yang cocok.
                </p>

                @if($selectedSiswa)
                <p class="mt-2 text-xs text-blue-500">
                    ✓ Menampilkan raport untuk <strong>{{ $selectedSiswa->nama }}</strong>
                </p>
                @else
                <p class="mt-2 text-xs text-gray-400">Mulai ketik untuk mencari siswa...</p>
                @endif
            </div>
        </div>

        <script>
        (function () {
            const siswas   = @json($siswasJson);
            const input    = document.getElementById('siswa-search-input');
            const dropdown = document.getElementById('siswa-dropdown');
            const noResult = document.getElementById('siswa-no-result');
            const clearBtn = document.getElementById('siswa-search-clear');
            const baseUrl  = '{{ route("akademik.raport.index") }}';

            function renderItems(items) {
                dropdown.innerHTML = '';
                if (!items.length) {
                    dropdown.classList.add('hidden');
                    noResult.classList.remove('hidden');
                    return;
                }
                noResult.classList.add('hidden');
                items.forEach(s => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition';
                    div.innerHTML =
                        `<span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center
                                      justify-center text-sm font-bold flex-shrink-0">
                             ${s.nama.charAt(0).toUpperCase()}
                         </span>
                         <span class="min-w-0">
                             <span class="block font-medium text-gray-800 text-sm truncate">${s.nama}</span>
                             ${s.nis ? `<span class="block text-gray-400 text-xs">${s.nis}</span>` : ''}
                         </span>`;
                    div.addEventListener('mousedown', e => { e.preventDefault(); selectSiswa(s); });
                    dropdown.appendChild(div);
                });
                dropdown.classList.remove('hidden');
            }

            function selectSiswa(s) {
                input.value = s.label;
                dropdown.classList.add('hidden');
                noResult.classList.add('hidden');
                clearBtn.classList.remove('hidden');
                window.location.href = baseUrl + '?siswa_id=' + s.id;
            }

            clearBtn.addEventListener('click', () => {
                input.value = '';
                dropdown.classList.add('hidden');
                noResult.classList.add('hidden');
                clearBtn.classList.remove('flex');
                clearBtn.classList.add('hidden');
                input.focus();
                window.location.href = baseUrl;
            });

            input.addEventListener('input', function () {
                const q = this.value.trim().toLowerCase();
                if (q.length > 0) {
                    clearBtn.classList.remove('hidden');
                    clearBtn.classList.add('flex');
                } else {
                    clearBtn.classList.remove('flex');
                    clearBtn.classList.add('hidden');
                }
                if (!q) { dropdown.classList.add('hidden'); noResult.classList.add('hidden'); return; }
                renderItems(siswas.filter(s =>
                    s.nama.toLowerCase().includes(q) || s.nis.toLowerCase().includes(q)
                ).slice(0, 25));
            });

            input.addEventListener('focus', function () {
                const q = this.value.trim().toLowerCase();
                if (q.length >= 1) {
                    renderItems(siswas.filter(s =>
                        s.nama.toLowerCase().includes(q) || s.nis.toLowerCase().includes(q)
                    ).slice(0, 25));
                }
            });

            input.addEventListener('keydown', e => {
                if (e.key === 'Escape') { dropdown.classList.add('hidden'); noResult.classList.add('hidden'); }
            });

            document.addEventListener('click', e => {
                if (!document.getElementById('siswa-search-wrapper').contains(e.target)) {
                    dropdown.classList.add('hidden');
                    noResult.classList.add('hidden');
                }
            });
        })();
        </script>

        {{-- ── INFO SISWA (jika sudah dipilih) ── --}}
        @if($selectedSiswa)
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center gap-4">
                @if($selectedSiswa->foto)
                <img src="{{ asset('storage/' . $selectedSiswa->foto) }}" class="w-14 h-14 rounded-full object-cover border-2 border-indigo-100" alt="Foto">
                @else
                <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xl">
                    {{ mb_substr($selectedSiswa->nama, 0, 1) }}
                </div>
                @endif
                <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs">Nama Siswa</p>
                        <p class="font-bold text-gray-900">{{ $selectedSiswa->nama }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">NIS</p>
                        <p class="font-semibold text-gray-800">{{ $selectedSiswa->nis ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Kelas Saat Ini</p>
                        <p class="font-semibold text-gray-800">{{ $selectedSiswa->kelas?->nama_kelas ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Jenis Kelamin</p>
                        <p class="font-semibold text-gray-800">{{ $selectedSiswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ── RAPORT DATA ── --}}
        @if($selectedSiswa)
            @if($raportData->isEmpty())
            <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="font-semibold text-gray-500">Belum ada data nilai untuk siswa ini.</p>
            </div>
            @else
            {{-- Tampilkan dari kelas terbaru (atas) ke terlama (bawah) --}}
            @foreach($raportData->reverse()->values() as $kelasGroup)
            <div class="bg-white rounded-xl shadow overflow-hidden">
                {{-- Header Kelas --}}
                <div class="px-5 py-4 flex items-center justify-between gap-2"
                     style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-white font-bold text-lg">
                            {{ $kelasGroup['tingkat'] <= 0 ? '?' : $kelasGroup['tingkat'] }}
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">{{ $kelasGroup['nama_kelas'] }}</h2>
                            <p class="text-xs text-indigo-200">Tahun Ajaran: {{ $kelasGroup['tahun_ajaran'] }}</p>
                        </div>
                    </div>
                    @php
                        $statusLabel = match($kelasGroup['status'] ?? '') {
                            'naik'       => ['label' => 'Naik Kelas', 'color' => 'bg-green-400/90 text-white'],
                            'tidak_naik' => ['label' => 'Tidak Naik', 'color' => 'bg-red-400/90 text-white'],
                            'lulus'      => ['label' => 'Lulus', 'color' => 'bg-yellow-300/90 text-gray-900'],
                            'aktif'      => ['label' => 'Aktif', 'color' => 'bg-blue-300/90 text-white'],
                            default      => ['label' => '-', 'color' => 'bg-gray-300/90 text-gray-700'],
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusLabel['color'] }}">
                        {{ $statusLabel['label'] }}
                    </span>
                </div>

                {{-- Semester 1 & 2 side by side --}}
                <div class="grid grid-cols-1 md:grid-cols-2 divide-x divide-gray-100">
                    @foreach([1, 2] as $semesterNomor)
                    @php $semData = $kelasGroup['per_semester'][$semesterNomor]; @endphp
                    <div class="p-0">
                        {{-- Semester Header --}}
                        <div class="px-4 py-2.5 bg-gray-50 border-b flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-700">{{ $semData['semester_nama'] }}</span>
                            @if($semData['avg_nilai'] !== null)
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                {{ $semData['avg_grade'] === 'A' ? 'bg-green-100 text-green-700' :
                                   ($semData['avg_grade'] === 'B' ? 'bg-blue-100 text-blue-700' :
                                   ($semData['avg_grade'] === 'C' ? 'bg-yellow-100 text-yellow-700' :
                                   'bg-red-100 text-red-700')) }}">
                                Rata-rata: {{ number_format($semData['avg_nilai'], 2, ',', '.') }}
                                ({{ $semData['avg_grade'] }})
                            </span>
                            @else
                            <span class="text-xs text-gray-400">Belum ada nilai</span>
                            @endif
                        </div>

                        {{-- Tabel Nilai --}}
                        @if($semData['mapel_rows']->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-xs text-gray-500 border-b">
                                        <th class="px-3 py-2 text-left font-semibold">Mata Pelajaran</th>
                                        <th class="px-2 py-2 text-center font-semibold">Tugas</th>
                                        <th class="px-2 py-2 text-center font-semibold">UTS</th>
                                        <th class="px-2 py-2 text-center font-semibold">UAS</th>
                                        <th class="px-2 py-2 text-center font-semibold">Akhir</th>
                                        <th class="px-2 py-2 text-center font-semibold">Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($semData['mapel_rows'] as $mRow)
                                    <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                                        <td class="px-3 py-2 font-medium text-gray-800 text-xs">{{ $mRow['mapel_nama'] }}</td>
                                        <td class="px-2 py-2 text-center text-gray-700 text-xs">
                                            {{ $mRow['nilai_tugas'] !== null ? number_format((float)$mRow['nilai_tugas'], 1, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-2 py-2 text-center text-gray-700 text-xs">
                                            {{ $mRow['nilai_uts'] !== null ? number_format((float)$mRow['nilai_uts'], 1, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-2 py-2 text-center text-gray-700 text-xs">
                                            {{ $mRow['nilai_uas'] !== null ? number_format((float)$mRow['nilai_uas'], 1, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-2 py-2 text-center font-semibold text-gray-900 text-xs">
                                            {{ $mRow['nilai_akhir'] !== null ? number_format((float)$mRow['nilai_akhir'], 2, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-2 py-2 text-center text-xs">
                                            @if($mRow['grade'])
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full font-bold text-xs
                                                {{ $mRow['grade'] === 'A' ? 'bg-green-100 text-green-700' :
                                                   ($mRow['grade'] === 'B' ? 'bg-blue-100 text-blue-700' :
                                                   ($mRow['grade'] === 'C' ? 'bg-yellow-100 text-yellow-700' :
                                                   'bg-red-100 text-red-700')) }}">
                                                {{ $mRow['grade'] }}
                                            </span>
                                            @else
                                            <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="px-4 py-6 text-center text-gray-400 text-sm">
                            Belum ada nilai pada semester ini
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
            @endif
        @else
        {{-- Belum pilih siswa --}}
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <p class="font-semibold text-gray-400 text-lg">Pilih siswa terlebih dahulu</p>
            <p class="text-sm text-gray-400 mt-1">Gunakan form di atas untuk mencari dan memilih nama siswa</p>
        </div>
        @endif

    </div>
</div>
@endsection
