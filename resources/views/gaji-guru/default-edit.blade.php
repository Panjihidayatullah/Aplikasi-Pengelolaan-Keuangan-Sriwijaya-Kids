@extends('layouts.app')

@section('title', 'Edit Gaji Default - ' . config('app.name'))
@section('page-title', 'Edit Gaji Default')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden">
        {{-- Header Dekoratif --}}
        <div class="bg-indigo-600 px-8 py-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">Edit Gaji Default</h2>
                    <p class="text-indigo-100 text-xs mt-1">Perbarui konfigurasi gaji untuk guru pilihan Anda</p>
                </div>
                <a href="{{ route('gaji-guru.default.index') }}" 
                   class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-8">
            <div class="mb-8 flex items-center gap-4 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                    {{ strtoupper(substr($default->guru?->nama ?? 'G', 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">{{ $default->guru?->nama }}</h3>
                    <p class="text-xs text-gray-500 font-medium">{{ $default->guru?->nip ?? 'Tanpa NIP' }}</p>
                </div>
            </div>

            <form action="{{ route('gaji-guru.default.update', $default) }}" method="POST" id="editForm" class="space-y-6">
                @csrf 
                @method('PUT')

                @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                    <ul class="list-disc list-inside text-xs text-red-600 font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Kiri --}}
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nominal Gaji (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
                                <input type="text" name="nominal" id="nominal_input" 
                                       value="{{ number_format($default->nominal, 0, ',', '.') }}" required
                                       class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl text-sm font-bold text-indigo-700 focus:ring-2 focus:ring-indigo-400 focus:outline-none bg-white shadow-sm transition-all hover:border-indigo-300">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan</label>
                            <textarea name="keterangan" rows="4" placeholder="Opsional, misal: Guru Matematika Tetap"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none resize-none bg-gray-50/30 transition-all hover:bg-white">{{ old('keterangan', $default->keterangan) }}</textarea>
                        </div>
                    </div>

                    {{-- Kanan --}}
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jadwal Penggajian Otomatis</label>
                            <div class="relative group">
                                <input type="number" name="tanggal_gaji" id="tanggal_gaji_input" 
                                       value="{{ old('tanggal_gaji', $default->tanggal_gaji) }}" min="1" max="28" list="tanggal_list"
                                       placeholder="-- Mode Manual (Kosongkan) --"
                                       style="padding-left: 120px !important;"
                                       class="peer w-full pr-4 py-3 border border-gray-300 rounded-xl text-sm font-bold text-gray-700 focus:ring-2 focus:ring-indigo-400 focus:outline-none bg-white shadow-sm transition-all hover:border-indigo-300">
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
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative flex items-center">
                                    <input type="checkbox" name="auto_gaji" id="auto_gaji_checkbox" value="1" 
                                           {{ old('auto_gaji', $default->auto_gaji) ? 'checked' : '' }} class="sr-only">
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

                        <div class="flex items-center gap-3 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                            <div class="relative flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', $default->is_active) ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 rounded-lg border-emerald-300 focus:ring-emerald-500">
                            </div>
                            <div>
                                <span class="text-xs font-bold text-emerald-800">Status Aktif</span>
                                <p class="text-[9px] text-emerald-600 font-medium">Jika mati, data ini tidak akan muncul saat bayar gaji.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-6 border-t">
                    <button type="submit"
                            class="flex-1 py-4 text-sm font-black uppercase tracking-widest text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all transform active:scale-95">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('gaji-guru.default.index') }}"
                       class="px-8 py-4 text-sm font-bold text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all text-center">
                        Batal
                    </a>
                </div>
            </form>
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
    
    nominalInput.addEventListener('input', function(e) {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value) {
            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        } else {
            this.value = '';
        }
    });

    document.getElementById('editForm').addEventListener('submit', function() {
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

@endsection
