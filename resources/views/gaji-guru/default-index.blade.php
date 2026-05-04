@extends('layouts.app')

@section('title', 'Gaji Default Guru - ' . config('app.name'))
@section('page-title', 'Gaji Default Guru')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Pengaturan Gaji Default</h2>
            <p class="mt-1 text-sm text-gray-500">Nominal gaji default akan otomatis terisi saat bayar gaji guru.</p>
        </div>
        <a href="{{ route('gaji-guru.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
            ← Kembali
        </a>
    </div>


    <div class="space-y-6">
        {{-- Form tambah gaji default --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tambah / Perbarui Gaji Default
            </h3>
            <form action="{{ route('gaji-guru.default.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @csrf
                
                @if($errors->any())
                <div class="col-span-full p-4 bg-red-50 border border-red-200 rounded-xl">
                    <ul class="list-disc list-inside text-xs text-red-600 font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Guru <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <input type="text" id="guru_search" list="guru_list" required autocomplete="off"
                                   placeholder="-- Cari atau Ketik Nama Guru --"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-bold text-gray-700 focus:ring-2 focus:ring-indigo-400 focus:outline-none bg-white shadow-sm transition-all hover:border-indigo-300">
                            <input type="hidden" name="guru_id" id="guru_id_hidden">
                            <datalist id="guru_list">
                                @foreach($guruOptions as $guru)
                                <option value="{{ $guru->nama }}{{ $guru->nip ? ' — '.$guru->nip : '' }}" data-id="{{ $guru->id }}">
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nominal Gaji (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
                            <input type="text" name="nominal" id="nominal_input" required placeholder="contoh: 2.500.000"
                                   class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm font-bold text-indigo-700 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" rows="3" placeholder="Opsional, misal: Guru Matematika Tetap"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none resize-none bg-gray-50/30"></textarea>
                    </div>

                    <div class="pt-8 flex gap-3">
                        <button type="submit" @if($guruOptions->isEmpty()) disabled @endif
                                class="flex-1 py-4 text-sm font-black uppercase tracking-widest text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Bayar & Simpan Gaji
                        </button>
                        <button type="reset" onclick="window.location.reload()"
                                class="px-8 py-4 text-sm font-bold text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">
                            Reset
                        </button>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jadwal Penggajian Otomatis</label>
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-inner space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Ketik Tanggal (1-28)</label>
                                <div class="relative group">
                                    <input type="number" name="tanggal_gaji" id="tanggal_gaji_input" min="1" max="28" list="tanggal_list"
                                           placeholder="-- Mode Manual (Kosongkan) --"
                                           style="padding-left: 120px !important;"
                                           class="peer w-full pr-4 py-3 border border-gray-300 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-indigo-400 focus:outline-none bg-white shadow-sm transition-all hover:border-indigo-300 focus:placeholder-transparent">
                                    <div class="absolute top-1/2 -translate-y-1/2 text-indigo-500 transition-all peer-focus:scale-0 peer-focus:opacity-0 pointer-events-none"
                                         style="left: 45px;">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <datalist id="tanggal_list">
                                        @for($i = 1; $i <= 28; $i++)
                                        <option value="{{ $i }}">Tanggal {{ $i }}</option>
                                        @endfor
                                    </datalist>
                                </div>
                                <p class="mt-2 text-[10px] text-gray-400 font-medium italic">*Bisa diketik atau pilih dari list yang muncul</p>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative flex items-center">
                                        <input type="checkbox" name="auto_gaji" id="auto_gaji_checkbox" value="1" class="sr-only">
                                        <div id="toggle_bg" class="w-11 h-6 bg-gray-200 rounded-full transition-all duration-300 shadow-inner relative">
                                            <div id="toggle_knob" class="absolute top-1 w-4 h-4 bg-white rounded-full transition-all duration-300 shadow-md" style="left: 4px;"></div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-700 group-hover:text-indigo-600 transition-colors">Aktifkan Auto-Payroll</span>
                                        <span id="auto-status-text" class="text-[9px] text-gray-400 font-medium">Nonaktif (Mode Manual)</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="mt-4 p-5 bg-indigo-50 rounded-xl border border-indigo-100 flex gap-4">
                            <div class="text-indigo-500 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-indigo-900">Catatan Penggajian Otomatis:</p>
                                <p class="text-xs text-indigo-700 leading-relaxed font-medium">
                                    Jika Anda mengisi tanggal (1-28), sistem akan secara otomatis mencatat pengeluaran gaji setiap bulan pada tanggal tersebut. Kosongkan untuk mode pembayaran manual.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Daftar gaji default --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Daftar Gaji Default Guru</h3>
                <span class="px-3 py-1 bg-white border rounded-full text-[10px] font-bold text-gray-500 shadow-sm">Total: {{ count($defaults) }} Guru</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/30">
                        <tr>
                            <th class="px-8 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Guru</th>
                            <th class="px-8 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-widest">Nominal</th>
                            <th class="px-8 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">Jadwal Auto</th>
                            <th class="px-8 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($defaults as $d)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-8 py-5 text-sm font-medium text-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($d->guru?->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $d->guru?->nama ?? '—' }}</div>
                                        @if($d->guru?->nip)
                                        <div class="text-[10px] font-bold text-gray-400 mt-0.5">{{ $d->guru->nip }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm font-bold text-indigo-600 text-right">
                                {{ format_rupiah((float)$d->nominal) }}
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($d->auto_gaji && $d->tanggal_gaji)
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 rounded-full border border-indigo-100">
                                    <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-[10px] font-black text-indigo-700 uppercase">Tgl {{ $d->tanggal_gaji }}</span>
                                </div>
                                @else
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Manual</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($d->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-700 border border-emerald-200">Aktif</span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase bg-gray-100 text-gray-500 border border-gray-200">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-center">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('gaji-guru.default.edit', $d) }}"
                                       class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-7m-5-5l7 7m-7-7l-7 7"/></svg>
                                    </a>
                                    <form action="{{ route('gaji-guru.default.destroy', $d) }}" method="POST"
                                          onsubmit="return confirm('Hapus gaji default {{ $d->guru?->nama }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="p-4 bg-gray-50 rounded-full">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <span class="text-sm font-bold text-gray-400">Belum ada gaji default guru yang terdaftar.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const nominalInput = document.getElementById('nominal_input');
    const tanggalInput = document.getElementById('tanggal_gaji_input');
    const autoGajiCheckbox = document.getElementById('auto_gaji_checkbox');
    const autoStatusText = document.getElementById('auto-status-text');
    const toggleBg = document.getElementById('toggle_bg');
    const toggleKnob = document.getElementById('toggle_knob');
    
    // Guru Search Logic
    const guruSearch = document.getElementById('guru_search');
    const guruIdHidden = document.getElementById('guru_id_hidden');
    const guruList = document.getElementById('guru_list');

    function syncGuruId() {
        const val = guruSearch.value.trim().toLowerCase();
        const options = guruList.options;
        let foundId = '';
        for (let i = 0; i < options.length; i++) {
            if (options[i].value.trim().toLowerCase() === val) {
                foundId = options[i].getAttribute('data-id');
                break;
            }
        }
        guruIdHidden.value = foundId;
        return foundId;
    }

    guruSearch.addEventListener('input', syncGuruId);
    guruSearch.addEventListener('change', syncGuruId);
    
    nominalInput.addEventListener('input', function(e) {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value) {
            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        } else {
            this.value = '';
        }
    });

    nominalInput.closest('form').addEventListener('submit', function(e) {
        if (!syncGuruId()) {
            e.preventDefault();
            alert('Guru tidak valid. Silakan pilih guru dari daftar yang tersedia.');
            guruSearch.focus();
            return;
        }
        nominalInput.value = nominalInput.value.replace(/\./g, '');
    });

    function updateAutoStatus() {
        const val = parseInt(tanggalInput.value);
        if (!isNaN(val) && val >= 1 && val <= 28) {
            if (autoGajiCheckbox.checked) {
                autoStatusText.textContent = 'Aktif (Otomatis Tanggal ' + val + ')';
                autoStatusText.classList.remove('text-gray-400');
                autoStatusText.classList.add('text-indigo-600');
                
                // Visual ON
                toggleBg.classList.remove('bg-gray-200');
                toggleBg.classList.add('bg-indigo-600');
                toggleKnob.style.left = '26px';
            } else {
                autoStatusText.textContent = 'Nonaktif (Manual - Tanggal Terisi)';
                autoStatusText.classList.remove('text-indigo-600');
                autoStatusText.classList.add('text-gray-400');
                
                // Visual OFF
                toggleBg.classList.remove('bg-indigo-600');
                toggleBg.classList.add('bg-gray-200');
                toggleKnob.style.left = '4px';
            }
        } else {
            autoGajiCheckbox.checked = false;
            autoStatusText.textContent = 'Nonaktif (Mode Manual)';
            autoStatusText.classList.remove('text-indigo-600');
            autoStatusText.classList.add('text-gray-400');
            
            // Visual OFF
            toggleBg.classList.remove('bg-indigo-600');
            toggleBg.classList.add('bg-gray-200');
            toggleKnob.style.left = '4px';
        }
    }

    tanggalInput.addEventListener('input', function() {
        const val = parseInt(this.value);
        if (!isNaN(val) && val >= 1 && val <= 28) {
            autoGajiCheckbox.checked = true;
        }
        updateAutoStatus();
    });

    autoGajiCheckbox.addEventListener('change', function() {
        const val = parseInt(tanggalInput.value);
        if (this.checked && (isNaN(val) || val < 1 || val > 28)) {
            alert('Silakan isi tanggal penggajian (1-28) terlebih dahulu untuk mengaktifkan Auto-Payroll.');
            this.checked = false;
        }
        updateAutoStatus();
    });

    // Jalankan saat halaman dimuat untuk sinkronisasi awal
    updateAutoStatus();
</script>

</div>
@endsection
