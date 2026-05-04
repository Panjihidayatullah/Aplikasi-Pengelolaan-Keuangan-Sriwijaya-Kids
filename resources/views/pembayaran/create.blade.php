@extends('layouts.app')

@section('title', 'Input Pembayaran - ' . config('app.name'))
@section('page-title', 'Input Pembayaran')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-wrapper.js-jenis-pembayaran,
    .ts-wrapper.js-siswa-pembayaran {
        width: 100%;
    }

    .ts-wrapper.js-jenis-pembayaran .ts-control,
    .ts-wrapper.js-siswa-pembayaran .ts-control {
        min-height: 52px;
        border-radius: 0.75rem;
        border: 2px solid #e2e8f0;
        box-shadow: none;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .ts-wrapper.js-jenis-pembayaran.focus .ts-control,
    .ts-wrapper.js-siswa-pembayaran.focus .ts-control {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .ts-wrapper.js-jenis-pembayaran .ts-control .item,
    .ts-wrapper.js-siswa-pembayaran .ts-control .item {
        margin: 0;
        padding: 0;
        color: #0f172a;
        line-height: 1.5;
        font-size: 1rem;
    }

    .ts-wrapper.js-jenis-pembayaran .ts-control input,
    .ts-wrapper.js-siswa-pembayaran .ts-control input {
        margin: 0;
        padding: 0;
        border: 0 !important;
        box-shadow: none !important;
        background: transparent !important;
        line-height: 1.5;
        font-size: 1rem;
    }

    .ts-wrapper.js-jenis-pembayaran .ts-control .placeholder,
    .ts-wrapper.js-siswa-pembayaran .ts-control .placeholder {
        font-size: 1rem;
        color: #64748b;
    }

    .ts-wrapper.js-jenis-pembayaran .ts-dropdown,
    .ts-wrapper.js-siswa-pembayaran .ts-dropdown {
        border: 1px solid #cbd5e1;
        border-radius: 0.75rem;
        margin-top: 0.35rem;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    }

    .ts-wrapper.js-jenis-pembayaran .ts-dropdown .option,
    .ts-wrapper.js-siswa-pembayaran .ts-dropdown .option {
        padding: 0.65rem 1rem;
        font-size: 1rem;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="px-8 py-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
            <h3 class="text-2xl font-bold text-slate-800">Form Input Pembayaran</h3>
            <p class="mt-1 text-sm text-slate-500">Catat pembayaran siswa dengan lengkap</p>
        </div>

        <form action="{{ route('pembayaran.store') }}" method="POST" class="p-8">
            @csrf

            <div class="space-y-6">
                <!-- Siswa -->
                <div>
                    <label for="siswa_id" class="block text-sm font-semibold text-slate-700 mb-2">
                        Siswa <span class="text-red-500">*</span>
                    </label>
                    <select name="siswa_id" 
                            id="siswa_id" 
                            required
                            class="js-siswa-pembayaran w-full @error('siswa_id') is-invalid @enderror">
                        <option value="">Pilih Siswa</option>
                        @foreach($siswa as $siswa)
                            <option value="{{ $siswa->id }}" {{ old('siswa_id') == $siswa->id ? 'selected' : '' }}>
                                {{ $siswa->nis }} - {{ $siswa->nama }} ({{ $siswa->kelas->nama_kelas ?? 'Tanpa Kelas' }})
                            </option>
                        @endforeach
                    </select>
                    @error('siswa_id')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Jenis Pembayaran -->
                <div>
                    <label for="jenis_pembayaran_id" class="block text-sm font-semibold text-slate-700 mb-2">
                        Jenis Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_pembayaran_id" 
                            id="jenis_pembayaran_id" 
                            required
                            class="js-jenis-pembayaran w-full @error('jenis_pembayaran_id') is-invalid @enderror">
                        <option value="">Pilih Jenis Pembayaran</option>
                        @foreach($jenisPembayaran as $jenis)
                            <option value="{{ $jenis->id }}" {{ old('jenis_pembayaran_id') == $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_pembayaran_id')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="keterangan" class="block text-sm font-semibold text-slate-700 mb-2">
                        Deskripsi Pembayaran
                    </label>
                    <textarea name="keterangan"
                              id="keterangan"
                              rows="4"
                              placeholder="Tulis deskripsi pembayaran secara lengkap (opsional)"
                              class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 resize-none @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                    <p class="mt-2 text-xs text-slate-500">Admin/user dapat mengisi catatan detail pembayaran sesuai kebutuhan.</p>
                    @error('keterangan')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Jumlah -->
                <div>
                    <label for="jumlah_display" class="block text-sm font-semibold text-slate-700 mb-2">
                        Jumlah Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">Rp</span>
                        <input type="hidden"
                               name="jumlah"
                               id="jumlah"
                               value="{{ old('jumlah') }}" 
                               required>
                        <input type="text"
                               id="jumlah_display"
                               value=""
                               inputmode="numeric"
                               placeholder="0"
                               class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('jumlah') border-red-500 @enderror">
                    </div>
                    <p class="mt-2 text-xs text-slate-500">Format otomatis: 1.000, 10.000, 100.000, dst.</p>
                    @error('jumlah')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Tanggal Bayar -->
                <div>
                    <label for="tanggal_bayar" class="block text-sm font-semibold text-slate-700 mb-2">
                        Tanggal Bayar <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_bayar" 
                           id="tanggal_bayar" 
                           value="{{ old('tanggal_bayar', date('Y-m-d')) }}" 
                           required
                           class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('tanggal_bayar') border-red-500 @enderror">
                    @error('tanggal_bayar')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <label for="metode_pembayaran" class="block text-sm font-semibold text-slate-700 mb-2">
                        Metode Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <select name="metode_pembayaran" 
                            id="metode_pembayaran" 
                            required
                            class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 bg-white @error('metode_pembayaran') border-red-500 @enderror">
                        <option value="tunai" {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="qris" {{ old('metode_pembayaran') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    </select>
                    @error('metode_pembayaran')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-8 mt-8 border-t border-slate-100">
                <a href="{{ route('pembayaran.index') }}" 
                   class="px-6 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-slate-200 transition-all duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/40 hover:shadow-xl hover:shadow-blue-500/50 focus:outline-none focus:ring-4 focus:ring-blue-500/50 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Pembayaran</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof TomSelect !== 'undefined') {
            function initSearchable(selectId, placeholder) {
                const select = document.getElementById(selectId);
                if (!select || select.tomselect) return;

                const emptyOption = select.querySelector('option[value=""]');
                if (emptyOption) {
                    emptyOption.textContent = '';
                }

                new TomSelect(select, {
                    create: false,
                    allowEmptyOption: true,
                    maxOptions: 300,
                    searchField: ['text'],
                    placeholder,
                    render: {
                        no_results: function () {
                            return '<div class="no-results">Tidak ada hasil</div>';
                        },
                    },
                    onInitialize: function () {
                        if (this.control_input && !this.getValue()) {
                            this.control_input.placeholder = placeholder;
                        }
                    },
                    onFocus: function () {
                        if (this.control_input) {
                            this.control_input.placeholder = '';
                        }
                    },
                    onDropdownOpen: function () {
                        if (this.control_input) {
                            this.control_input.placeholder = '';
                        }
                    },
                    onBlur: function () {
                        if (this.control_input && !this.getValue()) {
                            this.control_input.placeholder = placeholder;
                        }
                    },
                });
            }

            initSearchable('siswa_id', 'Pilih Siswa');
            initSearchable('jenis_pembayaran_id', 'Pilih Jenis Pembayaran');
        }

        const jumlahInput = document.getElementById('jumlah');
        const jumlahDisplayInput = document.getElementById('jumlah_display');

        if (jumlahInput && jumlahDisplayInput) {
            const formatRibuan = (digits) => digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            const initialRaw = String(jumlahInput.value ?? '').trim();
            const initialNumeric = Number(initialRaw);
            const initialDigits = initialRaw === ''
                ? ''
                : (Number.isFinite(initialNumeric)
                    ? String(Math.max(0, Math.floor(initialNumeric)))
                    : initialRaw.replace(/\D/g, ''));

            jumlahInput.value = initialDigits;
            jumlahDisplayInput.value = initialDigits ? formatRibuan(initialDigits) : '';

            const syncJumlah = () => {
                const digits = jumlahDisplayInput.value.replace(/\D/g, '');
                jumlahInput.value = digits;
                jumlahDisplayInput.value = digits ? formatRibuan(digits) : '';
            };

            jumlahDisplayInput.addEventListener('input', syncJumlah);
            jumlahDisplayInput.addEventListener('blur', syncJumlah);
            jumlahDisplayInput.addEventListener('focus', function () {
                if (jumlahDisplayInput.value === '0') {
                    jumlahDisplayInput.value = '';
                    jumlahInput.value = '';
                }
            });

            if (jumlahDisplayInput.form) {
                jumlahDisplayInput.form.addEventListener('submit', function () {
                    jumlahInput.value = jumlahDisplayInput.value.replace(/\D/g, '');
                });
            }
        }
    });
</script>
@endpush
@endsection
