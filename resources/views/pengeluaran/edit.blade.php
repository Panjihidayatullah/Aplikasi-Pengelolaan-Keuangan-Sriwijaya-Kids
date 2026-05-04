@extends('layouts.app')

@section('title', 'Edit Pengeluaran - ' . config('app.name'))
@section('page-title', 'Edit Pengeluaran')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="px-8 py-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
            <h3 class="text-2xl font-bold text-slate-800">Edit Pengeluaran</h3>
            <p class="mt-1 text-sm text-slate-500">Perbarui data pengeluaran sekolah</p>
        </div>

        <form action="{{ route('pengeluaran.update', $pengeluaran->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Jenis Pengeluaran -->
                <div>
                    <label for="jenis_pengeluaran_id" class="block text-sm font-semibold text-slate-700 mb-2">
                        Jenis Pengeluaran <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_pengeluaran_id" 
                            id="jenis_pengeluaran_id" 
                            required
                            class="js-jenis-pengeluaran w-full @error('jenis_pengeluaran_id') is-invalid @enderror">
                        <option value="">Pilih Jenis Pengeluaran</option>
                        @foreach($jenisPengeluaran as $jenis)
                            <option value="{{ $jenis->id }}" data-kategori="{{ $jenis->nama }}" {{ old('jenis_pengeluaran_id', $selectedJenisPengeluaranId ?? $pengeluaran->jenis_pengeluaran_id) == $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_pengeluaran_id')
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
                    <label for="deskripsi" class="block text-sm font-semibold text-slate-700 mb-2">
                        Deskripsi Pengeluaran <span class="text-red-500">*</span>
                    </label>
                    <textarea name="deskripsi" 
                              id="deskripsi" 
                              rows="4" 
                              required
                              placeholder="Jelaskan detail pengeluaran..."
                              class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 resize-none @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $pengeluaran->keterangan) }}</textarea>
                    <p class="mt-2 text-xs text-slate-500 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Deskripsikan tujuan dan detail pengeluaran dengan jelas
                    </p>
                    @error('deskripsi')
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
                        Jumlah Pengeluaran <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">Rp</span>
                        <input type="hidden"
                               name="jumlah"
                               id="jumlah"
                               value="{{ old('jumlah', $pengeluaran->jumlah) }}" 
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

                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-sm font-semibold text-slate-700 mb-2">
                        Tanggal Pengeluaran <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal" 
                           id="tanggal" 
                           value="{{ old('tanggal', $pengeluaran->tanggal->format('Y-m-d')) }}" 
                           required
                           class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 @error('tanggal') border-red-500 @enderror">
                    @error('tanggal')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Bukti File -->
                <div>
                    <label for="bukti_file" class="block text-sm font-semibold text-slate-700 mb-2">
                        Bukti/File Pendukung
                    </label>
                    
                    @if($pengeluaran->bukti_file)
                    <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-blue-700 font-medium">File saat ini: {{ basename($pengeluaran->bukti_file) }}</span>
                            </div>
                            <a href="{{ Storage::url($pengeluaran->bukti_file) }}" target="_blank" 
                               class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                Lihat File
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="relative">
                        <input type="file" 
                               name="bukti_file" 
                               id="bukti_file" 
                               accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('bukti_file') border-red-500 @enderror">
                    </div>
                    <p class="mt-2 text-xs text-slate-500 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Format: JPG, PNG, PDF (Maks. 2MB) - Kosongkan jika tidak ingin mengubah file
                    </p>
                    @error('bukti_file')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" 
                            id="status" 
                            required
                            class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 bg-white @error('status') border-red-500 @enderror">
                        <option value="Disetujui" {{ old('status', $pengeluaran->status) == 'Disetujui' ? 'selected' : '' }}>Approved</option>
                        <option value="Pending" {{ old('status', $pengeluaran->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Ditolak" {{ old('status', $pengeluaran->status) == 'Ditolak' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status')
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
                <a href="{{ route('pengeluaran.index') }}" 
                   class="px-6 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-slate-200 transition-all duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/40 hover:shadow-xl hover:shadow-blue-500/50 focus:outline-none focus:ring-4 focus:ring-blue-500/50 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Update Pengeluaran</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.css" rel="stylesheet">
<style>
    .ts-wrapper.js-jenis-pengeluaran {
        width: 100%;
        border: 0 !important;
        padding: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        outline: 0 !important;
    }

    .ts-wrapper.js-jenis-pengeluaran .ts-control {
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        min-height: 54px;
        padding: 0.75rem 1rem;
        background: #fff;
        box-shadow: none;
    }

    .ts-wrapper.js-jenis-pengeluaran .ts-control.focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .ts-wrapper.js-jenis-pengeluaran.is-invalid .ts-control {
        border-color: #ef4444;
    }

    .ts-wrapper.js-jenis-pengeluaran .ts-control .item {
        margin: 0;
        padding: 0;
        color: #0f172a;
        line-height: 1.5;
        font-size: 1rem;
    }

    .ts-wrapper.js-jenis-pengeluaran .ts-control input {
        margin: 0;
        padding: 0;
        border: 0 !important;
        box-shadow: none !important;
        background: transparent !important;
        line-height: 1.5;
        font-size: 1rem;
    }

    .ts-wrapper.js-jenis-pengeluaran .ts-control .placeholder {
        font-size: 1rem;
        color: #64748b;
    }

    .ts-wrapper.js-jenis-pengeluaran .ts-dropdown {
        border: 1px solid #cbd5e1;
        border-radius: 0.75rem;
        margin-top: 0.35rem;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    }

    .ts-wrapper.js-jenis-pengeluaran .ts-dropdown .option {
        padding: 0.65rem 1rem;
        font-size: 1rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('jenis_pengeluaran_id');

        if (select && typeof TomSelect !== 'undefined' && !select.tomselect) {
            const emptyOption = select.querySelector('option[value=""]');
            if (emptyOption) {
                emptyOption.textContent = '';
            }

            new TomSelect(select, {
                create: false,
                allowEmptyOption: true,
                maxOptions: 300,
                searchField: ['text'],
                placeholder: 'Pilih Jenis Pengeluaran',
                render: {
                    no_results: function () {
                        return '<div class="no-results">Tidak ada hasil</div>';
                    },
                },
                onInitialize: function () {
                    if (this.control_input && !this.getValue()) {
                        this.control_input.placeholder = 'Pilih Jenis Pengeluaran';
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
                        this.control_input.placeholder = 'Pilih Jenis Pengeluaran';
                    }
                },
            });
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
