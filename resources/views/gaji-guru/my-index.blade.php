@extends('layouts.app')

@section('title', 'Gaji Saya - ' . config('app.name'))
@section('page-title', 'Gaji Saya')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Riwayat Gaji Saya</h2>
        <p class="mt-1 text-sm text-gray-600">Daftar gaji guru yang sudah tercatat di sistem.</p>
    </div>

    @if(!$guru)
    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-800">
        Akun Anda belum terhubung dengan data guru. Hubungi admin untuk menghubungkan akun guru Anda.
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-8 gap-4 items-end">
            <div class="lg:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="periode_bulan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">Semua Bulan</option>
                    @foreach($bulanOptions as $bulanValue => $bulanLabel)
                        <option value="{{ $bulanValue }}" {{ (string) request('periode_bulan') === (string) $bulanValue ? 'selected' : '' }}>{{ $bulanLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <input type="number"
                       name="periode_tahun"
                       min="2000"
                       max="2100"
                       value="{{ request('periode_tahun') }}"
                       placeholder="2026"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>

            <div class="lg:col-span-3 flex flex-wrap md:flex-nowrap items-center gap-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold">Filter</button>
                <a href="{{ route('gaji-saya.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-semibold">Reset</a>
                <span class="inline-flex items-center gap-1 text-sm text-gray-600 whitespace-nowrap">Total halaman ini: <strong>{{ format_rupiah($totalDibayarkan) }}</strong></span>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($gajiGuru as $item)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $bulanOptions[$item->periode_bulan] ?? $item->periode_bulan }} {{ $item->periode_tahun }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $item->pengeluaran->kode_transaksi ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ optional($item->pengeluaran->tanggal)->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-blue-700">{{ format_rupiah((float) ($item->pengeluaran->jumlah ?? 0)) }}</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="inline-flex items-center gap-3">
                            <a href="{{ route('gaji-saya.show', $item) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Detail</a>
                            <a href="{{ route('gaji-saya.export.pdf', $item) }}" class="text-green-600 hover:text-green-800 font-semibold" target="_blank">PDF</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada riwayat gaji untuk akun Anda.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($gajiGuru->hasPages())
        <div class="p-4 border-t">{{ $gajiGuru->links() }}</div>
        @endif
    </div>
    @endif
</div>
@endsection
