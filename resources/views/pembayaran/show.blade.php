@extends('layouts.app')

@section('title', 'Detail Pembayaran - ' . config('app.name'))
@section('page-title', 'Detail Pembayaran')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden mb-8 no-print">
        <div class="px-8 py-6 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">Detail Transaksi Pembayaran</h3>
                <p class="mt-2 text-sm text-slate-600">Informasi lengkap pembayaran siswa</p>
            </div>
            <div class="flex items-center space-x-2">
                @if($pembayaran->status == 'Lunas')
                    <span class="px-4 py-2 bg-green-100 text-green-700 rounded-xl text-sm font-semibold">
                        ✓ {{ $pembayaran->status }}
                    </span>
                @elseif($pembayaran->status == 'Pending')
                    <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-xl text-sm font-semibold">
                        ⏳ {{ $pembayaran->status }}
                    </span>
                @else
                    <span class="px-4 py-2 bg-red-100 text-red-700 rounded-xl text-sm font-semibold">
                        ✗ {{ $pembayaran->status }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Print Receipt Header (Hidden on screen, shown on print) -->
    <div class="print-only" style="display: none;">
        <div style="text-align: center; border-bottom: 2px dashed #000; padding-bottom: 16px; margin-bottom: 16px;">
            <h1 style="font-size: 20px; font-weight: bold; margin: 0;">{{ config('finance.school.name', 'Sriwijaya Kids') }}</h1>
            <p style="font-size: 12px; margin: 4px 0;">SMA SRIWIJAYA</p>
            <p style="font-size: 11px; margin: 2px 0;">Jl. Pendidikan No. 123, Palembang</p>
            <p style="font-size: 11px; margin: 2px 0;">Telp: (0711) 123456</p>
        </div>
        <div style="text-align: center; margin-bottom: 16px;">
            <h2 style="font-size: 16px; font-weight: bold; margin: 0;">BUKTI PEMBAYARAN</h2>
            <p style="font-size: 11px; margin: 4px 0;">{{ $pembayaran->kode_transaksi }}</p>
        </div>
    </div>

    <div class="space-y-8">
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
            <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                <h4 class="text-lg font-bold text-slate-800">Informasi Pembayaran dan Data Siswa</h4>
            </div>
            <div class="p-8">
                <div class="overflow-x-auto" data-table-slider-ignore>
                    <table class="min-w-full text-sm table-slider-ignore">
                        <tbody class="divide-y divide-slate-100">
                            <tr class="bg-slate-50/70">
                                <th colspan="2" class="py-3 px-4 text-left text-sm font-bold text-slate-700">Informasi Pembayaran</th>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Kode Transaksi</th>
                                <td class="py-3 text-base font-bold text-blue-600">{{ $pembayaran->kode_transaksi }}</td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Jenis Pembayaran</th>
                                <td class="py-3 text-base font-semibold text-slate-800">{{ $pembayaran->jenis ? \App\Models\JenisPembayaran::normalizeNama($pembayaran->jenis->nama) : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Jumlah Bayar</th>
                                <td class="py-3 text-3xl font-bold text-green-600">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Tanggal Bayar</th>
                                <td class="py-3 text-base text-slate-800">{{ $pembayaran->tanggal_bayar->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Metode Pembayaran</th>
                                <td class="py-3">
                                    @if($pembayaran->metode_bayar == 'Tunai')
                                        <span class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-semibold inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Tunai
                                        </span>
                                    @elseif($pembayaran->metode_bayar == 'Transfer')
                                        <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            Transfer
                                        </span>
                                    @else
                                        <span class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg text-sm font-semibold inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                            </svg>
                                            QRIS
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Keterangan</th>
                                <td class="py-3 text-base text-slate-700">{{ $pembayaran->keterangan ?: '-' }}</td>
                            </tr>

                            <tr class="bg-slate-50/70">
                                <th colspan="2" class="py-3 px-4 text-left text-sm font-bold text-slate-700">Data Siswa</th>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">NIS</th>
                                <td class="py-3 text-base font-semibold text-slate-800">{{ $pembayaran->siswa->nis ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Nama Lengkap</th>
                                <td class="py-3 text-base font-semibold text-slate-800">{{ $pembayaran->siswa->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Kelas</th>
                                <td class="py-3 text-base text-slate-800">{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Telepon</th>
                                <td class="py-3 text-base text-slate-800">{{ $pembayaran->siswa->telepon ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden p-6 no-print">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('pembayaran.index') }}"
                   class="w-full px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali</span>
                </a>

                <a href="{{ route('pembayaran.edit', $pembayaran->id) }}"
                   class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Edit Data</span>
                </a>

                <a href="{{ route('pembayaran.export.pdf', $pembayaran->id) }}"
                   class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2"
                   target="_blank">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Export PDF</span>
                </a>

                <form action="{{ route('pembayaran.destroy', $pembayaran->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-3 bg-red-100 hover:bg-red-200 text-red-700 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden no-print">
            <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                <h4 class="text-lg font-bold text-slate-800">Timeline</h4>
            </div>
            <div class="p-6 space-y-5">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Dibuat</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $pembayaran->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                @if($pembayaran->updated_at != $pembayaran->created_at)
                <div class="flex items-start space-x-3 pt-2">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Diperbarui</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $pembayaran->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @endif

                <div class="flex items-start space-x-3 pt-2">
                    <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Petugas</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $pembayaran->user->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Print Receipt Footer -->
    <div class="print-only" style="display: none;">
        <div style="border-top: 2px dashed #000; margin-top: 24px; padding-top: 16px;">
            <table style="width: 100%; font-size: 12px; line-height: 1.8;">
                <tr>
                    <td style="width: 40%; font-weight: 600;">Tanggal Cetak:</td>
                    <td style="width: 60%;">{{ now()->format('d F Y, H:i') }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Dicetak oleh:</td>
                    <td>{{ Auth::user()->name ?? 'System' }}</td>
                </tr>
            </table>
        </div>
        
        <div style="margin-top: 32px; text-align: center;">
            <div style="display: inline-block; text-align: center; margin: 0 auto;">
                <p style="font-size: 11px; margin: 0;">Bendahara</p>
                <div style="height: 60px;"></div>
                <p style="font-size: 11px; margin: 0; border-top: 1px solid #000; padding-top: 4px; display: inline-block; min-width: 200px;">{{ $pembayaran->user->name ?? '_________________' }}</p>
            </div>
        </div>
        
        <div style="margin-top: 24px; text-align: center; font-size: 10px; color: #666;">
            <p style="margin: 4px 0;">*** Terima kasih atas pembayaran Anda ***</p>
            <p style="margin: 4px 0;">Bukti pembayaran ini sah dan diproses oleh sistem</p>
            <p style="margin: 4px 0;">{{ config('finance.school.name', 'Sriwijaya Kids') }} - {{ now()->format('Y') }}</p>
        </div>
    </div>
</div>

<style>
/* Print Styles - Receipt/Struk Format */
@media print {
    /* Hide web elements */
    aside, nav, header, footer, button, .no-print {
        display: none !important;
    }
    
    /* Show receipt elements */
    .print-only {
        display: block !important;
    }
    
    /* Override body and reset margins */
    body {
        background: white !important;
        margin: 0;
        padding: 20px;
    }
    
    /* Main container for receipt */
    .max-w-5xl {
        max-width: 80mm !important;
        margin: 0 auto;
        font-family: 'Courier New', monospace;
    }
    
    /* Hide cards styling and show as receipt */
    .bg-white, .rounded-2xl, .shadow-lg {
        background: white !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        border: none !important;
    }
    
    /* Hide gradient headers */
    .bg-gradient-to-r {
        display: none !important;
    }
    
    /* Simplify grid for print */
    .grid {
        display: block !important;
    }
    
    .lg\:col-span-2, .lg\:col-span-1 {
        width: 100% !important;
    }
    
    /* Receipt specific styling */
    .p-8, .p-6 {
        padding: 0 !important;
    }
    
    .space-y-6 > * + *, .space-y-8 > * + * {
        margin-top: 12px !important;
    }
    
    /* Print receipt content styles */
    @page {
        size: 80mm auto;
        margin: 5mm;
    }
    
    /* Receipt table formatting */
    .flex.items-start {
        display: table-row !important;
    }
    
    .flex.items-start > div {
        display: table-cell !important;
        padding: 6px 0;
        vertical-align: top;
    }
    
    /* Make text smaller and monospace for receipt feel */
    .text-base, .text-sm {
        font-size: 12px !important;
        line-height: 1.6 !important;
    }
    
    .text-2xl, .text-3xl {
        font-size: 16px !important;
        font-weight: bold !important;
    }
    
    /* Receipt border styling */
    .border-t {
        border-top: 1px dashed #000 !important;
    }
    
    /* Color adjustments for print */
    .text-blue-600, .text-green-600, .text-slate-800 {
        color: #000 !important;
    }
    
    .text-slate-500, .text-slate-600 {
        color: #333 !important;
    }
    
    /* Hide badge backgrounds */
    .bg-green-100, .bg-blue-100, .bg-purple-100, 
    .bg-yellow-100, .bg-red-100 {
        background: transparent !important;
        border: 1px solid #000 !important;
        padding: 2px 6px !important;
        border-radius: 0 !important;
    }
    
    /* Signature area */
    [style*="height: 60px"] {
        border-bottom: 1px solid #000;
        margin: 10px auto;
        width: 200px;
    }
}
</style>
@endsection
