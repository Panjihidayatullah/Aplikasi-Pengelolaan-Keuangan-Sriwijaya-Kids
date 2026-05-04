@extends('layouts.app')

@section('title', 'Absensi Siswa')

@section('content')
<div class="container mx-auto px-4 py-6">
    @php
        $isSiswaScope = (bool) ($isSiswaScope ?? false);
    @endphp

    <div class="flex items-center justify-between mb-6 gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            @if(request('back_url'))
            <a href="{{ urldecode(request('back_url')) }}"
               class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors"
               title="Kembali ke LMS Pertemuan">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            @endif
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Absensi Siswa</h1>
                <p class="text-gray-600 mt-1">
                    @if($isKepalaSekolahReadOnly ?? false)
                        Kepala Sekolah hanya dapat melihat rekap absensi kelas.
                    @elseif($isSiswaScope)
                        Lihat riwayat absensi pribadi Anda per semester dan bulan.
                    @else
                        Absensi diisi per kelas, per semester, dan per bulan dengan klik langsung pada tabel.
                    @endif
                </p>
            </div>
        </div>
        @if(request('back_url'))
        <a href="{{ urldecode(request('back_url')) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke LMS Pertemuan
        </a>
        @endif
    </div>


    @if($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
        <p class="font-semibold mb-2">Periksa input absensi:</p>
        <ul class="list-disc pl-5 space-y-1 text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <form method="GET" action="{{ route('akademik.absensi.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @if(!empty($pertemuanTanggalFocus))
            <input type="hidden" name="pertemuan_tanggal" value="{{ $pertemuanTanggalFocus->toDateString() }}">
            @endif
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Semester</label>
                <select name="semester_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">Pilih semester</option>
                    @foreach($semesters as $semesterOption)
                    <option value="{{ $semesterOption->id }}" @selected((string) $selectedSemesterId === (string) $semesterOption->id)>
                        Semester {{ $semesterOption->nomor_semester }} - {{ $semesterOption->tahunAjaran->nama ?? '-' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan</label>
                <select name="bulan" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    @foreach(($availableBulanOptions ?? []) as $bulanKey => $bulanLabel)
                    <option value="{{ $bulanKey }}" @selected((int) $selectedBulan === (int) $bulanKey)>{{ $bulanLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas</label>
                @if(!$isSiswaScope)
                <select name="kelas_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">Pilih kelas</option>
                    @foreach($kelasOptions as $kelas)
                    <option value="{{ $kelas->id }}" @selected((string) $selectedKelasId === (string) $kelas->id)>
                        {{ $kelas->nama ?? '-' }}
                    </option>
                    @endforeach
                </select>
                @else
                    @if(!empty($selectedKelasId))
                    <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                    @endif
                    <div class="w-full px-3 py-2 border border-green-200 rounded-lg bg-green-50 text-sm text-green-800">
                        Kelas otomatis: <span class="font-semibold">{{ $selectedKelas->nama ?? 'Belum terhubung' }}</span>
                    </div>
                @endif
            </div>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">Tampilkan Daftar</button>
        </form>
    </div>

    @if($monthAdjusted ?? false)
    <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800">
        Bulan yang dipilih tidak termasuk periode semester, sehingga sistem otomatis menyesuaikan ke bulan yang valid.
    </div>
    @endif

    @if(!empty($pertemuanTanggalFocus))
    <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-blue-800">
        Halaman ini dibuka dari LMS pertemuan tanggal <span class="font-semibold">{{ $pertemuanTanggalFocus->format('d M Y') }}</span>.
        {{ $isSiswaScope ? 'Kelas Anda dipilih otomatis oleh sistem.' : 'Pilih kelas, lalu isi absensi pada kolom tanggal tersebut.' }}
    </div>
    @endif

    @if($isSiswaScope && empty($selectedKelasId))
    <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800">
        Kelas siswa belum terhubung ke akun ini. Silakan hubungi admin.
    </div>
    @endif

    @if($isSiswaScope)
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-5 py-4 border-b bg-gray-50">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Absensi Saya</h2>
                    <p class="text-sm text-gray-600 mt-1">Tabel ini hanya menampilkan absensi untuk akun Anda sendiri.</p>
                </div>
                @if(!empty($selectedSemesterId) && !empty($selectedKelasId))
                <div class="flex items-center gap-2">
                    <a
                        href="{{ route('akademik.absensi.saya.export.pdf', ['semester_id' => $selectedSemesterId, 'bulan' => $selectedBulan, 'mode' => 'preview']) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm whitespace-nowrap"
                    >
                        Preview PDF Saya
                    </a>
                    <a
                        href="{{ route('akademik.absensi.saya.export.pdf', ['semester_id' => $selectedSemesterId, 'bulan' => $selectedBulan]) }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm whitespace-nowrap"
                    >
                        Unduh PDF Saya
                    </a>
                </div>
                @endif
            </div>

            <div class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
                <div class="rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-2">
                    <p class="text-xs text-emerald-700 font-semibold">Hadir</p>
                    <p class="text-lg font-bold text-emerald-800">{{ $siswaAbsensiSummary['hadir'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg border border-blue-100 bg-blue-50 px-3 py-2">
                    <p class="text-xs text-blue-700 font-semibold">Izin</p>
                    <p class="text-lg font-bold text-blue-800">{{ $siswaAbsensiSummary['izin'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg border border-amber-100 bg-amber-50 px-3 py-2">
                    <p class="text-xs text-amber-700 font-semibold">Sakit</p>
                    <p class="text-lg font-bold text-amber-800">{{ $siswaAbsensiSummary['sakit'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg border border-red-100 bg-red-50 px-3 py-2">
                    <p class="text-xs text-red-700 font-semibold">Alpa</p>
                    <p class="text-lg font-bold text-red-800">{{ $siswaAbsensiSummary['alpa'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                    <p class="text-xs text-gray-700 font-semibold">Total</p>
                    <p class="text-lg font-bold text-gray-800">{{ $siswaAbsensiSummary['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-14">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Pertemuan</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Mapel</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status Saya</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Catatan</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Guru</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($siswaAbsensiRows?->items() ?? []) as $row)
                    @php
                        $status = (string) ($row->status ?? '-');
                        $statusClass = $status === 'hadir'
                            ? 'bg-emerald-100 text-emerald-800'
                            : ($status === 'izin'
                                ? 'bg-blue-100 text-blue-800'
                                : ($status === 'sakit' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800'));
                    @endphp
                    <tr class="border-b">
                        <td class="px-4 py-3 text-sm text-gray-700">{{ (($siswaAbsensiRows->firstItem() ?? 1) + $loop->index) }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-indigo-700">{{ $row->pertemuan_ke ? 'P' . $row->pertemuan_ke : '' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($row->absensi?->tanggal_absensi)->format('d M Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row->absensi?->mataPelajaran?->nama ?? 'Umum' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row->catatan ?: ($row->absensi?->keterangan ?: '-') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row->absensi?->guru?->nama ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada data absensi pribadi pada filter ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($siswaAbsensiRows && $siswaAbsensiRows->hasPages())
        <div class="p-4 border-t">{{ $siswaAbsensiRows->links() }}</div>
        @endif
    </div>
    @endif

    @if(!$isSiswaScope)

    @if($selectedKelas)
    <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 mb-6 text-sm text-blue-900">
        <p><span class="font-semibold">Kelas:</span> {{ $selectedKelas->nama ?? '-' }}</p>
        @if($selectedJadwal)
        <p><span class="font-semibold">Acuan Jadwal:</span> {{ $selectedJadwal->mataPelajaran->nama ?? '-' }} | {{ $selectedJadwal->hari }} {{ substr((string) $selectedJadwal->jam_mulai, 0, 5) }}-{{ substr((string) $selectedJadwal->jam_selesai, 0, 5) }}</p>
        @endif
        @if($selectedSemester)
        <p><span class="font-semibold">Semester:</span> {{ $selectedSemester->nama }} - {{ $selectedSemester->tahunAjaran->nama ?? '-' }}</p>
        @endif
    </div>
    @endif

    @if($selectedKelasId && !$selectedJadwal)
    <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800">
        Belum ada jadwal aktif untuk kelas terpilih, sehingga input absensi belum dapat dibuka.
    </div>
    @endif

    @if($selectedJadwal && $selectedSemester && $tanggalKolom->isEmpty())
    <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800">
        Tidak ada tanggal pada bulan ini yang sesuai hari jadwal dan periode semester.
    </div>
    @endif

    @if($selectedJadwal && $selectedSemester && $tanggalKolom->isNotEmpty() && ($canManageAbsensi ?? false))
    @php
        // Tentukan tanggal aktif (fokus): dari LMS pertemuan atau hari ini
        $focusDateStr = !empty($pertemuanTanggalFocus)
            ? $pertemuanTanggalFocus->toDateString()
            : now()->toDateString();

        // Cari tanggal fokus di dalam kolom yang tersedia
        $activeTanggal = $tanggalKolom->first(fn($t) => $t->toDateString() === $focusDateStr)
            ?? $tanggalKolom->last(); // fallback ke tanggal terakhir jika tidak ketemu

        $activeDateKey = $activeTanggal ? $activeTanggal->toDateString() : null;

        // Ambil status & catatan yang sudah ada di DB untuk tanggal aktif
        $existingAbsensiForDate = $activeDateKey
            ? \App\Models\Absensi::query()
                ->with('details')
                ->where('kelas_id', $selectedJadwal->kelas_id)
                ->whereDate('tanggal_absensi', $activeDateKey)
                ->first()
            : null;

        // Map siswa_id => [status, catatan]
        $existingDetailMap = [];
        if ($existingAbsensiForDate) {
            foreach ($existingAbsensiForDate->details as $det) {
                $existingDetailMap[(int) $det->siswa_id] = [
                    'status'  => $det->status,
                    'catatan' => $det->catatan,
                ];
            }
        }

        $hariNama = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        {{-- Header --}}
        <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-white flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Input Absensi Pertemuan</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    @if($activeTanggal)
                    Tanggal: <span class="font-semibold text-indigo-700">{{ $activeTanggal->translatedFormat('l, d M Y') }}</span>
                    &mdash; Kelas: <span class="font-semibold text-indigo-700">{{ $selectedKelas?->nama ?? '-' }}</span>
                    @else
                    Pilih tanggal pertemuan untuk mulai input absensi.
                    @endif
                </p>
            </div>

            {{-- Pilih Tanggal Pertemuan --}}
            <div>
                <form method="GET" action="{{ route('akademik.absensi.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="semester_id" value="{{ $selectedSemesterId }}">
                    <input type="hidden" name="bulan" value="{{ $selectedBulan }}">
                    <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                    <select name="pertemuan_tanggal" onchange="this.form.submit()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-indigo-400">
                        @foreach($tanggalKolom as $tgl)
                        <option value="{{ $tgl->toDateString() }}" @selected($tgl->toDateString() === $activeDateKey)>
                            {{ $tgl->translatedFormat('d M Y') }} ({{ $hariNama[$tgl->dayOfWeekIso] ?? '-' }})
                        </option>
                        @endforeach
                    </select>
                    <noscript><button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm">Pilih</button></noscript>
                </form>
            </div>
        </div>

        {{-- Legend --}}
        <div class="px-6 py-3 border-b bg-gray-50 flex flex-wrap items-center gap-2 text-xs">
            <span class="font-semibold text-gray-600">Status:</span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold">H = Hadir</span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-semibold">I = Izin</span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-semibold">S = Sakit</span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-700 border border-red-200 font-semibold">A = Alpa</span>
            <span class="text-gray-400 ml-2">Klik tombol status untuk mengubah. Isi catatan jika perlu.</span>
        </div>

        @if($siswas->isEmpty())
        <div class="px-6 py-10 text-center text-gray-500">Belum ada siswa aktif pada kelas ini.</div>
        @elseif(!$activeDateKey)
        <div class="px-6 py-10 text-center text-gray-500">Tidak ada tanggal pertemuan yang tersedia pada bulan ini.</div>
        @else
        <form method="POST" action="{{ route('akademik.absensi.store') }}" id="form-absensi-pertemuan">
            @csrf
            <input type="hidden" name="jadwal_pelajaran_id" value="{{ $selectedJadwalId }}">
            <input type="hidden" name="semester_id" value="{{ $selectedSemesterId }}">
            <input type="hidden" name="bulan" value="{{ $selectedBulan }}">
            <input type="hidden" name="pertemuan_tanggal_input" value="{{ $activeDateKey }}">

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-12">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Siswa</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-28">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Catatan (opsional)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($siswas as $idx => $siswa)
                        @php
                            $existingStatus  = data_get(old('status', []), $siswa->id . '.' . $activeDateKey)
                                ?? ($existingDetailMap[(int) $siswa->id]['status'] ?? 'hadir');
                            $existingCatatan = old('catatan.' . $siswa->id)
                                ?? ($existingDetailMap[(int) $siswa->id]['catatan'] ?? '');
                            $btnId = 'btn-status-' . $siswa->id;
                            $inputId = 'inp-status-' . $siswa->id;
                        @endphp
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="px-4 py-4 text-sm text-gray-500 text-center">{{ $idx + 1 }}</td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-gray-800">{{ $siswa->nama }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">NIS: {{ $siswa->nis }}</p>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <input type="hidden"
                                    id="{{ $inputId }}"
                                    name="status[{{ $siswa->id }}][{{ $activeDateKey }}]"
                                    value="{{ $existingStatus }}">
                                <button type="button"
                                    id="{{ $btnId }}"
                                    class="attendance-cell w-12 h-12 rounded-xl border-2 text-sm font-black transition-all duration-150 shadow-sm"
                                    data-target="{{ $inputId }}"
                                    data-status="{{ $existingStatus }}"
                                    aria-label="Status {{ $siswa->nama }}">
                                    {{ strtoupper(substr($existingStatus, 0, 1)) }}
                                </button>
                            </td>
                            <td class="px-4 py-4">
                                <input type="text"
                                    name="catatan[{{ $siswa->id }}]"
                                    value="{{ $existingCatatan }}"
                                    placeholder="Tambahkan catatan untuk {{ $siswa->nama }}..."
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 placeholder-gray-300 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="setAllStatus('hadir')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-700 transition">
                        Semua Hadir
                    </button>
                    <button type="button" onclick="setAllStatus('alpa')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition">
                        Semua Alpa
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500">
                        Menyimpan absensi untuk tanggal <strong>{{ $activeTanggal?->format('d M Y') }}</strong>
                    </span>
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm shadow-sm transition">
                        Simpan Absensi
                    </button>
                </div>
            </div>
        </form>
        @endif
    </div>
    @endif

    @if($selectedKelasId && !($canManageAbsensi ?? false))
    <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800">
        Mode lihat saja: Anda dapat meninjau riwayat absensi, namun tidak dapat menginput atau menyimpan absensi.
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-5 py-4 border-b bg-gray-50">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Riwayat Absensi Bulan Terpilih</h2>
                    <p class="text-sm text-gray-600 mt-1">Riwayat otomatis mengikuti filter semester, bulan, dan kelas di atas.</p>
                </div>
                @if($selectedKelasId && $selectedSemester)
                <div class="flex items-center gap-2">
                    <a
                        href="{{ route('akademik.absensi.export.pdf', ['kelas_id' => $selectedKelasId, 'semester_id' => $selectedSemester->id, 'mode' => 'preview']) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm whitespace-nowrap"
                    >
                        Preview PDF
                    </a>
                    <a
                        href="{{ route('akademik.absensi.export.pdf', ['kelas_id' => $selectedKelasId, 'semester_id' => $selectedSemester->id]) }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm whitespace-nowrap"
                    >
                        Unduh PDF
                    </a>
                </div>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-14">Urutan</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Kelas</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Mapel</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Rekap Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Guru</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatAbsensi as $item)
                    @php
                        $hadirCount = $item->details->where('status', 'hadir')->count();
                        $izinCount  = $item->details->where('status', 'izin')->count();
                        $sakitCount = $item->details->where('status', 'sakit')->count();
                        $alpaCount  = $item->details->where('status', 'alpa')->count();
                        $canDelete  = (is_admin() || auth()->user()->hasRole('Guru')) && !($isKepalaSekolahReadOnly ?? false);
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-700">{{ ($riwayatAbsensi->firstItem() ?? 1) + $loop->index }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $item->tanggal_absensi?->format('d M Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $item->kelas->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $item->mataPelajaran->nama ?? 'Umum' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <span class="text-green-700 font-semibold">H: {{ $hadirCount }}</span>
                            <span class="mx-1 text-gray-400">|</span>
                            <span class="text-blue-700 font-semibold">I: {{ $izinCount }}</span>
                            <span class="mx-1 text-gray-400">|</span>
                            <span class="text-amber-700 font-semibold">S: {{ $sakitCount }}</span>
                            <span class="mx-1 text-gray-400">|</span>
                            <span class="text-red-700 font-semibold">A: {{ $alpaCount }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $item->guru->nama ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php $backUrl = urlencode(request()->fullUrl()); @endphp
                            <div class="flex items-center gap-2">
                                <a href="{{ route('akademik.absensi.show', $item) }}?back_url={{ $backUrl }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Lihat</a>
                                @if($canDelete)
                                <a href="{{ route('akademik.absensi.edit', $item) }}?back_url={{ $backUrl }}" class="text-amber-600 hover:text-amber-800 font-semibold text-sm">Edit</a>
                                <form method="POST" action="{{ route('akademik.absensi.destroy', $item) }}" onsubmit="return confirm('Hapus data absensi tanggal {{ $item->tanggal_absensi?->format('d M Y') }}? Semua detail akan ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm bg-transparent border-0 p-0">Hapus</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada data absensi pada filter ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayatAbsensi->hasPages())
        <div class="p-4 border-t">{{ $riwayatAbsensi->links() }}</div>
        @endif
    </div>
        @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const order = ['hadir', 'izin', 'sakit', 'alpa'];
        const labels = { hadir: 'H', izin: 'I', sakit: 'S', alpa: 'A' };
        const classMap = {
            hadir: ['bg-emerald-100', 'text-emerald-700', 'border-emerald-400'],
            izin:  ['bg-blue-100',    'text-blue-700',    'border-blue-400'],
            sakit: ['bg-amber-100',   'text-amber-700',   'border-amber-400'],
            alpa:  ['bg-red-100',     'text-red-700',     'border-red-400'],
        };
        const allStateClasses = [
            'bg-emerald-100','text-emerald-700','border-emerald-400',
            'bg-blue-100',   'text-blue-700',   'border-blue-400',
            'bg-amber-100',  'text-amber-700',  'border-amber-400',
            'bg-red-100',    'text-red-700',    'border-red-400',
        ];

        const applyStatus = (button, input, status) => {
            const resolved = order.includes(status) ? status : 'hadir';
            button.dataset.status = resolved;
            input.value = resolved;
            button.textContent = labels[resolved];
            button.classList.remove(...allStateClasses);
            button.classList.add(...classMap[resolved]);
        };

        document.querySelectorAll('.attendance-cell').forEach((button) => {
            const targetId = button.dataset.target;
            const input = document.getElementById(targetId);
            if (!input) return;
            applyStatus(button, input, input.value || button.dataset.status || 'hadir');
            button.addEventListener('click', function () {
                const current = button.dataset.status || 'hadir';
                const next = order[(order.indexOf(current) + 1) % order.length];
                applyStatus(button, input, next);
            });
        });
    });

    // Tombol "Semua Hadir / Semua Alpa"
    function setAllStatus(status) {
        document.querySelectorAll('.attendance-cell').forEach((button) => {
            const input = document.getElementById(button.dataset.target);
            if (!input) return;
            const order    = ['hadir', 'izin', 'sakit', 'alpa'];
            const labels   = { hadir: 'H', izin: 'I', sakit: 'S', alpa: 'A' };
            const classMap = {
                hadir: ['bg-emerald-100', 'text-emerald-700', 'border-emerald-400'],
                izin:  ['bg-blue-100',    'text-blue-700',    'border-blue-400'],
                sakit: ['bg-amber-100',   'text-amber-700',   'border-amber-400'],
                alpa:  ['bg-red-100',     'text-red-700',     'border-red-400'],
            };
            const allClasses = [
                'bg-emerald-100','text-emerald-700','border-emerald-400',
                'bg-blue-100',   'text-blue-700',   'border-blue-400',
                'bg-amber-100',  'text-amber-700',  'border-amber-400',
                'bg-red-100',    'text-red-700',    'border-red-400',
            ];
            button.dataset.status = status;
            input.value = status;
            button.textContent = labels[status];
            button.classList.remove(...allClasses);
            button.classList.add(...classMap[status]);
        });
    }
</script>
@endpush

