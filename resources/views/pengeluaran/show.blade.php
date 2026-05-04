@extends('layouts.app')

@section('title', 'Detail Pengeluaran - ' . config('app.name'))
@section('page-title', 'Detail Pengeluaran')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden mb-8 no-print">
        <div class="px-8 py-6 bg-gradient-to-r from-red-50 to-orange-50 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">Detail Transaksi Pengeluaran</h3>
                <p class="mt-2 text-sm text-slate-600">Informasi lengkap pengeluaran sekolah</p>
            </div>
            <div class="flex items-center space-x-2">
                @if($pengeluaran->status == 'Disetujui')
                    <span class="px-4 py-2 bg-green-100 text-green-700 rounded-xl text-sm font-semibold inline-flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $pengeluaran->status }}
                    </span>
                @elseif($pengeluaran->status == 'Pending')
                    <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-xl text-sm font-semibold inline-flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        {{ $pengeluaran->status }}
                    </span>
                @else
                    <span class="px-4 py-2 bg-red-100 text-red-700 rounded-xl text-sm font-semibold inline-flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        {{ $pengeluaran->status }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-8">
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
            <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                <h4 class="text-lg font-bold text-slate-800">Informasi Pengeluaran dan Penanggung Jawab</h4>
            </div>
            <div class="p-8">
                <div class="overflow-x-auto" data-table-slider-ignore>
                    <table class="min-w-full text-sm table-slider-ignore">
                        <tbody class="divide-y divide-slate-100">
                            <tr class="bg-slate-50/70">
                                <th colspan="2" class="py-3 px-4 text-left text-sm font-bold text-slate-700">Informasi Pengeluaran</th>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Kode Transaksi</th>
                                <td class="py-3 text-base font-bold text-red-600">{{ $pengeluaran->kode_transaksi }}</td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Jenis Pengeluaran</th>
                                <td class="py-3 text-base font-semibold text-slate-800">{{ $pengeluaran->jenis ? \App\Models\JenisPengeluaran::normalizeNama($pengeluaran->jenis->nama) : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Jumlah Pengeluaran</th>
                                <td class="py-3 text-3xl font-bold text-red-600">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Tanggal</th>
                                <td class="py-3 text-base text-slate-800">{{ $pengeluaran->tanggal->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Keterangan</th>
                                <td class="py-3 text-base text-slate-700">{{ $pengeluaran->keterangan ?: '-' }}</td>
                            </tr>
                            @if($pengeluaran->gajiGuru)
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Detail Gaji Guru</th>
                                <td class="py-3 text-base text-slate-700 space-y-1">
                                    <p><span class="font-semibold">Guru:</span> {{ $pengeluaran->gajiGuru->guru->nama ?? '-' }}</p>
                                    <p><span class="font-semibold">Periode:</span> {{ $pengeluaran->gajiGuru->periode_bulan }}/{{ $pengeluaran->gajiGuru->periode_tahun }}</p>
                                    <p><span class="font-semibold">Catatan:</span> {{ $pengeluaran->gajiGuru->detail ?: '-' }}</p>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Bukti File</th>
                                <td class="py-3">
                                    @if($pengeluaran->bukti_file)
                                        <a href="{{ Storage::url($pengeluaran->bukti_file) }}"
                                           target="_blank"
                                           class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-sm font-medium transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"/>
                                            </svg>
                                            Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-base text-slate-700">-</span>
                                    @endif
                                </td>
                            </tr>

                            <tr class="bg-slate-50/70">
                                <th colspan="2" class="py-3 px-4 text-left text-sm font-bold text-slate-700">Penanggung Jawab</th>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Nama</th>
                                <td class="py-3 text-base font-semibold text-slate-800">{{ $pengeluaran->user->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Email</th>
                                <td class="py-3 text-base text-slate-800">{{ $pengeluaran->user->email ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden p-6 no-print">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('pengeluaran.index') }}"
                   class="w-full px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>

                <a href="{{ route('pengeluaran.edit', $pengeluaran->id) }}"
                   class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Edit</span>
                </a>

                <a href="{{ route('pengeluaran.export.pdf', $pengeluaran->id) }}"
                   class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2"
                   target="_blank">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Export PDF</span>
                </a>

                @if($pengeluaran->gajiGuru && (is_admin() || is_bendahara() || (auth()->user()->hasRole('Guru') && (int) optional($pengeluaran->gajiGuru->guru)->user_id === (int) auth()->id())))
                @php
                    $gajiGuruDetailRoute = (auth()->user()->hasRole('Guru') && !is_admin() && !is_bendahara())
                        ? route('gaji-saya.show', $pengeluaran->gajiGuru)
                        : route('gaji-guru.show', $pengeluaran->gajiGuru);
                @endphp
                <a href="{{ $gajiGuruDetailRoute }}"
                   class="w-full px-4 py-3 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8L10 18l-5-5"/>
                    </svg>
                    <span>Detail Gaji Guru</span>
                </a>
                @endif

                <form action="{{ route('pengeluaran.destroy', $pengeluaran->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?')" class="w-full">
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
                        <p class="text-xs text-slate-500 mt-1">{{ $pengeluaran->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                @if($pengeluaran->updated_at != $pengeluaran->created_at)
                <div class="flex items-start space-x-3 pt-2">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Diperbarui</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $pengeluaran->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
