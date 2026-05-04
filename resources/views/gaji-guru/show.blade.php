@extends('layouts.app')

@section('title', 'Detail Gaji Guru - ' . config('app.name'))
@section('page-title', 'Detail Gaji Guru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
        <div class="px-8 py-6 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-slate-100">
            <h3 class="text-2xl font-bold text-slate-800">Detail Gaji Guru</h3>
            <p class="mt-1 text-sm text-slate-600">Informasi lengkap pembayaran gaji guru.</p>
        </div>

        <div class="p-8">
            <div class="overflow-x-auto" data-table-slider-ignore>
                <table class="min-w-full text-sm table-slider-ignore">
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Nama Guru</th>
                            <td class="py-3 text-base font-semibold text-slate-800">{{ $gajiGuru->guru->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">NIP</th>
                            <td class="py-3 text-base text-slate-700">{{ $gajiGuru->guru->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Periode</th>
                            <td class="py-3 text-base text-slate-700">{{ $gajiGuru->periode_bulan }}/{{ $gajiGuru->periode_tahun }}</td>
                        </tr>
                        <tr>
                            <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Kode Transaksi</th>
                            <td class="py-3 text-base text-slate-700">{{ $gajiGuru->pengeluaran->kode_transaksi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Tanggal Bayar</th>
                            <td class="py-3 text-base text-slate-700">{{ optional($gajiGuru->pengeluaran->tanggal)->format('d F Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Jumlah Dibayarkan</th>
                            <td class="py-3 text-3xl font-bold text-blue-700">{{ format_rupiah((float) ($gajiGuru->pengeluaran->jumlah ?? 0)) }}</td>
                        </tr>
                        <tr>
                            <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Keterangan</th>
                            <td class="py-3 text-base text-slate-700">{{ $gajiGuru->pengeluaran->keterangan ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Detail Gaji</th>
                            <td class="py-3 text-base text-slate-700">{{ $gajiGuru->detail ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="w-2/5 py-3 pr-4 text-left font-semibold text-slate-500">Dicatat Oleh</th>
                            <td class="py-3 text-base text-slate-700">{{ $gajiGuru->dibayarOleh->name ?? $gajiGuru->pengeluaran->user->name ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="{{ $isSelfView ? route('gaji-saya.index') : route('gaji-guru.index') }}"
               class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center">
                Kembali
            </a>

            <a href="{{ $isSelfView ? route('gaji-saya.export.pdf', $gajiGuru) : route('gaji-guru.export.pdf', $gajiGuru) }}"
               target="_blank"
               class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center">
                Export PDF
            </a>
        </div>
    </div>
</div>
@endsection
