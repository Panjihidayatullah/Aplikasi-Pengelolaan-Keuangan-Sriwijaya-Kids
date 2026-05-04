@extends('layouts.app')

@section('title', 'Edit Absensi')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    @php $backTarget = request('back_url') ? urldecode(request('back_url')) : route('akademik.absensi.index'); @endphp
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ $backTarget }}"
           class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Absensi</h1>
            <p class="text-gray-500 text-sm mt-0.5">
                {{ $absensi->tanggal_absensi?->translatedFormat('l, d M Y') ?? '-' }}
                &mdash; {{ $absensi->kelas?->nama ?? '-' }}
                &mdash; {{ $absensi->mataPelajaran?->nama ?? 'Umum' }}
            </p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm">
        <p class="font-semibold mb-1">Periksa input:</p>
        <ul class="list-disc pl-5 space-y-0.5">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Info Card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase">Tanggal</p>
            <p class="text-sm font-bold text-gray-800 mt-1">{{ $absensi->tanggal_absensi?->format('d M Y') ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase">Kelas</p>
            <p class="text-sm font-bold text-gray-800 mt-1">{{ $absensi->kelas?->nama ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase">Mata Pelajaran</p>
            <p class="text-sm font-bold text-gray-800 mt-1">{{ $absensi->mataPelajaran?->nama ?? 'Umum' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase">Guru</p>
            <p class="text-sm font-bold text-gray-800 mt-1">{{ $absensi->guru?->nama ?? '-' }}</p>
        </div>
    </div>

    {{-- Form Edit --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Form header & legend --}}
        <div class="px-6 py-4 border-b bg-gradient-to-r from-amber-50 to-white flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-gray-800">Ubah Status & Catatan Siswa</h2>
                <p class="text-xs text-gray-500 mt-0.5">Klik tombol status untuk mengubah. Isi catatan jika ada.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-300 font-semibold">H = Hadir</span>
                <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 border border-blue-300 font-semibold">I = Izin</span>
                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-300 font-semibold">S = Sakit</span>
                <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 border border-red-300 font-semibold">A = Alpa</span>
            </div>
        </div>

        <form method="POST" action="{{ route('akademik.absensi.update', $absensi) }}" id="form-edit-absensi">
            @csrf
            @method('PUT')

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-12">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-24">NIS</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-28">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($siswas as $idx => $siswa)
                        @php
                            $curStatus  = old('status.' . $siswa->id, $detailMap[(int)$siswa->id]['status']  ?? 'hadir');
                            $curCatatan = old('catatan.' . $siswa->id, $detailMap[(int)$siswa->id]['catatan'] ?? '');
                            $inputId    = 'inp-edit-' . $siswa->id;
                        @endphp
                        <tr class="hover:bg-amber-50/30 transition-colors">
                            <td class="px-4 py-4 text-sm text-gray-500 text-center">{{ $idx + 1 }}</td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-gray-800">{{ $siswa->nama }}</p>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $siswa->nis ?? '-' }}</td>
                            <td class="px-4 py-4 text-center">
                                <input type="hidden"
                                    id="{{ $inputId }}"
                                    name="status[{{ $siswa->id }}]"
                                    value="{{ $curStatus }}">
                                <button type="button"
                                    class="attendance-cell w-12 h-12 rounded-xl border-2 text-sm font-black transition-all duration-150 shadow-sm"
                                    data-target="{{ $inputId }}"
                                    data-status="{{ $curStatus }}"
                                    aria-label="Status {{ $siswa->nama }}">
                                    {{ strtoupper(substr($curStatus, 0, 1)) }}
                                </button>
                            </td>
                            <td class="px-4 py-4">
                                <input type="text"
                                    name="catatan[{{ $siswa->id }}]"
                                    value="{{ $curCatatan }}"
                                    placeholder="Catatan untuk {{ $siswa->nama }}..."
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm placeholder-gray-300 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                Belum ada siswa aktif pada kelas ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer actions --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="setAllEditStatus('hadir')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-700 transition">
                        Semua Hadir
                    </button>
                    <button type="button" onclick="setAllEditStatus('alpa')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-100 hover:bg-red-200 text-red-700 transition">
                        Semua Alpa
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ $backTarget }}"
                        class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-semibold text-sm hover:bg-gray-100 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold text-sm shadow-sm transition">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 z-50 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-semibold" id="flash-ok">
    {{ session('success') }}
</div>
<script>setTimeout(() => { const el = document.getElementById('flash-ok'); if(el) el.remove(); }, 3500);</script>
@endif
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
    const allClasses = [
        'bg-emerald-100','text-emerald-700','border-emerald-400',
        'bg-blue-100',   'text-blue-700',   'border-blue-400',
        'bg-amber-100',  'text-amber-700',  'border-amber-400',
        'bg-red-100',    'text-red-700',    'border-red-400',
    ];

    function applyStatus(btn, input, status) {
        const s = order.includes(status) ? status : 'hadir';
        btn.dataset.status = s;
        input.value = s;
        btn.textContent = labels[s];
        btn.classList.remove(...allClasses);
        btn.classList.add(...classMap[s]);
    }

    document.querySelectorAll('.attendance-cell').forEach(btn => {
        const input = document.getElementById(btn.dataset.target);
        if (!input) return;
        applyStatus(btn, input, input.value || btn.dataset.status || 'hadir');
        btn.addEventListener('click', () => {
            const next = order[(order.indexOf(btn.dataset.status) + 1) % order.length];
            applyStatus(btn, input, next);
        });
    });
});

function setAllEditStatus(status) {
    const order = ['hadir', 'izin', 'sakit', 'alpa'];
    const labels = { hadir: 'H', izin: 'I', sakit: 'S', alpa: 'A' };
    const classMap = {
        hadir: ['bg-emerald-100','text-emerald-700','border-emerald-400'],
        izin:  ['bg-blue-100',  'text-blue-700',  'border-blue-400'],
        sakit: ['bg-amber-100', 'text-amber-700', 'border-amber-400'],
        alpa:  ['bg-red-100',   'text-red-700',   'border-red-400'],
    };
    const allClasses = [
        'bg-emerald-100','text-emerald-700','border-emerald-400',
        'bg-blue-100',   'text-blue-700',   'border-blue-400',
        'bg-amber-100',  'text-amber-700',  'border-amber-400',
        'bg-red-100',    'text-red-700',    'border-red-400',
    ];
    document.querySelectorAll('#form-edit-absensi .attendance-cell').forEach(btn => {
        const input = document.getElementById(btn.dataset.target);
        if (!input) return;
        btn.dataset.status = status;
        input.value = status;
        btn.textContent = labels[status];
        btn.classList.remove(...allClasses);
        btn.classList.add(...classMap[status]);
    });
}
</script>
@endpush
