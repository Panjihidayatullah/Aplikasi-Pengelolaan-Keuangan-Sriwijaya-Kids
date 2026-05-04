@extends('layouts.app')

@section('title', 'LMS')
@section('page-title', 'LMS')

@section('content')
<div class="space-y-6" data-table-slider-ignore>
    @php
        $isSiswaScope = (bool) ($isSiswaScope ?? false);
        $canManage = !$isSiswaScope && (is_admin() || auth()->user()->hasRole('Guru'));
    @endphp

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">LMS</h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ $isSiswaScope ? 'Lihat pertemuan kelas Anda.' : 'Kelola pertemuan kelas — klik pertemuan untuk membuka Materi, Tugas, Absensi, dan Monitoring.' }}
            </p>
        </div>
        @if($canManage && $selectedSemester && $selectedKelasId)
        <button onclick="document.getElementById('modal-tambah-pertemuan').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm shadow-sm shadow-indigo-500/30 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pertemuan
        </button>
        @endif
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        @if($selectedSemester)
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Semester</label>
                <select name="semester_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                    @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}" @selected((int) $selectedSemester->id === (int) $semester->id)>
                        {{ $semester->nama }} - {{ $semester->tahunAjaran->nama ?? '-' }}
                    </option>
                    @endforeach
                </select>
            </div>

            @if(!$isSiswaScope)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas</label>
                <select name="kelas_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
                    <option value="">Semua / Pilih Kelas</option>
                    @forelse($kelasOptions as $kelas)
                    <option value="{{ $kelas->id }}" @selected((int) ($selectedKelasId ?? 0) === (int) $kelas->id)>{{ $kelas->nama }}</option>
                    @empty
                    <option value="" disabled>Tidak ada kelas tersedia</option>
                    @endforelse
                </select>
            </div>
            @else
            <div>
                @if(!empty($selectedKelasId))
                <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                @endif
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas</label>
                <div class="w-full px-3 py-2 border border-green-200 rounded-lg bg-green-50 text-sm text-green-900">
                    {{ $selectedKelas?->nama ?? 'Belum terhubung' }}
                </div>
            </div>
            @endif

            <div class="grid grid-cols-3 gap-2 items-end">
                <div class="col-span-2 rounded-lg bg-gray-50 border border-gray-200 px-3 py-2 text-xs text-gray-600">
                    <span class="font-semibold text-gray-700">{{ $selectedSemester->nama }}</span><br>
                    {{ optional($selectedSemester->tanggal_mulai)->format('d M Y') }} — {{ optional($selectedSemester->tanggal_selesai)->format('d M Y') }}
                </div>
                <a href="{{ route('akademik.lms.index', ['semester_id' => $selectedSemester->id]) }}"
                    class="px-3 py-2 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition">Reset</a>
            </div>
        </form>
        @else
        <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-sm text-gray-600">
            Data semester belum tersedia. Tambahkan semester terlebih dahulu.
        </div>
        @endif
    </div>

    {{-- Pertemuan Grid --}}
    @if($selectedSemester)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between gap-3 mb-5">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Daftar Pertemuan</h3>
                <p class="text-sm text-gray-500 mt-0.5">
                    @if($selectedKelasId)
                        Kelas: <span class="font-semibold text-indigo-700">{{ $selectedKelas?->nama }}</span> — 
                        <span class="font-semibold">{{ $selectedMeetingCount }}</span> pertemuan tercatat
                    @else
                        Pilih kelas untuk menampilkan pertemuan.
                    @endif
                </p>
            </div>
        </div>

        @if(!$selectedKelasId)
        <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 px-4 py-8 text-center text-sm text-amber-800">
            <svg class="w-8 h-8 mx-auto mb-2 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z"/></svg>
            {{ $isSiswaScope ? 'Kelas siswa belum terhubung. Hubungi admin.' : 'Pilih kelas terlebih dahulu agar pertemuan bisa ditampilkan.' }}
        </div>
        @elseif(empty($meetingList))
        <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Belum ada pertemuan. 
            @if($canManage) Klik <strong>Tambah Pertemuan</strong> untuk memulai. @endif
        </div>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($meetingList as $meeting)
            @php
                $tanggal = \Carbon\Carbon::parse($meeting->tanggal);
                $pertemuanUrl = route('akademik.lms.pertemuan', array_filter([
                    'tanggal' => $meeting->tanggal,
                    'semester_id' => $selectedSemester->id,
                    'kelas_id' => $selectedKelasId,
                ], fn($v) => $v !== null && $v !== ''));
            @endphp
            <div class="group relative flex flex-col rounded-xl border border-gray-200 bg-gradient-to-br from-white to-indigo-50/40 hover:border-indigo-400 hover:shadow-md hover:shadow-indigo-100 transition-all duration-200 overflow-hidden">
                {{-- Top accent bar --}}
                <div class="h-1.5 bg-gradient-to-r from-indigo-500 to-indigo-400 w-full"></div>

                <a href="{{ $pertemuanUrl }}" class="flex-1 flex flex-col items-center justify-center p-4 text-center">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-1">Pertemuan</span>
                    <span class="text-3xl font-black text-indigo-700 leading-none">{{ $meeting->nomor }}</span>
                    <span class="text-xs text-gray-500 mt-2 font-medium">{{ $tanggal->translatedFormat('d M Y') }}</span>
                    <span class="text-[10px] text-gray-400 mt-0.5">{{ $tanggal->translatedFormat('l') }}</span>
                </a>

                @if($canManage)
                <div class="px-2 pb-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="openEditPertemuan({{ $meeting->id }}, '{{ $meeting->tanggal }}')"
                        class="flex-1 px-1 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-[10px] font-semibold transition">
                        Edit
                    </button>
                    <form method="POST" action="{{ route('akademik.lms.pertemuan.delete', $meeting->id) }}"
                        onsubmit="return confirm('Hapus Pertemuan ke-{{ $meeting->nomor }}? Data terkait tidak akan ikut terhapus.')" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-1 py-1 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-[10px] font-semibold transition">
                            Hapus
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    {{-- Modal Tambah Pertemuan --}}
    @if($canManage && $selectedSemester && $selectedKelasId)
    <div id="modal-tambah-pertemuan" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-200">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Tambah Pertemuan Baru</h3>
                <button onclick="document.getElementById('modal-tambah-pertemuan').classList.add('hidden')"
                    class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('akademik.lms.pertemuan.store') }}" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="semester_id" value="{{ $selectedSemester->id }}">
                <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pertemuan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="tanggal-tambah"
                        value="{{ now()->toDateString() }}"
                        min="{{ optional($selectedSemester->tanggal_mulai)->toDateString() }}"
                        max="{{ optional($selectedSemester->tanggal_selesai)->toDateString() }}"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-sm">
                    <p class="mt-1 text-xs text-gray-500">
                        Periode semester: {{ optional($selectedSemester->tanggal_mulai)->format('d M Y') }} — {{ optional($selectedSemester->tanggal_selesai)->format('d M Y') }}
                    </p>
                </div>

                {{-- Preview 4 modul --}}
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-2">Setelah disimpan, Anda bisa mengisi:</p>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-center gap-2 bg-indigo-50 border border-indigo-100 rounded-lg px-3 py-2 text-xs font-semibold text-indigo-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            LMS Materi
                        </div>
                        <div class="flex items-center gap-2 bg-cyan-50 border border-cyan-100 rounded-lg px-3 py-2 text-xs font-semibold text-cyan-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            LMS Tugas
                        </div>
                        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2 text-xs font-semibold text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Absensi
                        </div>
                        <div class="flex items-center gap-2 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 text-xs font-semibold text-amber-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Monitoring
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modal-tambah-pertemuan').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm shadow-sm transition">
                        Simpan Pertemuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Pertemuan --}}
    <div id="modal-edit-pertemuan" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm border border-gray-200">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Edit Tanggal Pertemuan</h3>
                <button onclick="document.getElementById('modal-edit-pertemuan').classList.add('hidden')"
                    class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="form-edit-pertemuan" method="POST" action="" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pertemuan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="tanggal-edit" required
                        min="{{ optional($selectedSemester->tanggal_mulai)->toDateString() }}"
                        max="{{ optional($selectedSemester->tanggal_selesai)->toDateString() }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition text-sm">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('modal-edit-pertemuan').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold text-sm transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Error / Success Messages --}}
    @if(session('success'))
    <div class="fixed bottom-4 right-4 z-50 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-semibold animate-fade-in" id="flash-msg">
        {{ session('success') }}
    </div>
    <script>setTimeout(() => { const el = document.getElementById('flash-msg'); if(el) el.remove(); }, 3500);</script>
    @endif
    @if(session('error'))
    <div class="fixed bottom-4 right-4 z-50 bg-red-600 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-semibold" id="flash-err">
        {{ session('error') }}
    </div>
    <script>setTimeout(() => { const el = document.getElementById('flash-err'); if(el) el.remove(); }, 4000);</script>
    @endif
</div>

@if(session('error_detail'))
<div class="fixed bottom-4 right-4 z-50 bg-red-600 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-semibold">
    {{ session('error_detail') }}
</div>
@endif

<script>
function openEditPertemuan(id, tanggal) {
    const form = document.getElementById('form-edit-pertemuan');
    form.action = '/akademik/lms/pertemuan/' + id;
    document.getElementById('tanggal-edit').value = tanggal;
    document.getElementById('modal-edit-pertemuan').classList.remove('hidden');
}
// Close modal on backdrop click
['modal-tambah-pertemuan','modal-edit-pertemuan'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', function(e){ if(e.target === this) this.classList.add('hidden'); });
});
</script>
@endsection
