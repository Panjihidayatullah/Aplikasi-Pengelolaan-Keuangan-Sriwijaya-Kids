@extends('layouts.app')

@section('title', 'Gaji Guru - ' . config('app.name'))
@section('page-title', 'Gaji Guru')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Gaji Guru</h2>
            <p class="mt-1 text-sm text-gray-500">Kelola pembayaran gaji guru. Setiap pembayaran otomatis tercatat di riwayat pengeluaran.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('gaji-guru.default.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-bold shadow-lg shadow-indigo-100 transition-all active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Bayar Gaji
            </a>
        </div>
    </div>


    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
            <div class="lg:col-span-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama guru, NIP, kode transaksi…"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            </div>
            <div class="lg:col-span-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">Guru</label>
                <select name="guru_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                    <option value="">Semua Guru</option>
                    @foreach($guruOptions as $guru)
                    <option value="{{ $guru->id }}" {{ (string)request('guru_id') === (string)$guru->id ? 'selected' : '' }}>
                        {{ $guru->nama }}{{ $guru->nip ? ' — '.$guru->nip : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                <select name="periode_bulan" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                    <option value="">Semua</option>
                    @foreach($bulanOptions as $v => $l)
                    <option value="{{ $v }}" {{ (string)request('periode_bulan') === (string)$v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                <input type="number" name="periode_tahun" value="{{ request('periode_tahun') }}"
                       min="2000" max="2100" placeholder="{{ date('Y') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            </div>
            <div class="lg:col-span-1 flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-bold shadow-sm transition-all active:scale-95">Filter</button>
            </div>
            <div class="lg:col-span-1 flex gap-2">
                <a href="{{ route('gaji-guru.index') }}" 
                   class="flex-1 px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-bold text-center shadow-sm transition-all active:scale-95">Reset</a>
            </div>
            <div class="lg:col-span-12 flex items-center mt-2">
                <span class="text-sm text-gray-500 font-medium">
                    Total Halaman Ini: <strong class="text-indigo-600 ml-1">{{ format_rupiah($totalDibayarkan) }}</strong>
                </span>
            </div>
        </form>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Periode</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Guru</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kode Transaksi</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($gajiGuru as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 text-sm font-medium text-gray-800">
                        {{ $bulanOptions[$item->periode_bulan] ?? $item->periode_bulan }} {{ $item->periode_tahun }}
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-700">
                        {{ $item->guru->nama ?? '-' }}
                        @if($item->guru?->nip)
                        <span class="block text-xs text-gray-400">{{ $item->guru->nip }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-500 font-mono">{{ $item->pengeluaran?->kode_transaksi ?? '-' }}</td>
                    <td class="px-5 py-3 text-sm text-gray-600">
                        {{ optional($item->pengeluaran?->tanggal)->format('d/m/Y') ?? '-' }}
                    </td>
                    <td class="px-5 py-3 text-sm font-bold text-emerald-700 text-right">
                        {{ format_rupiah((float)($item->pengeluaran?->jumlah ?? 0)) }}
                    </td>
                    <td class="px-5 py-3 text-sm text-center">
                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('gaji-guru.show', $item) }}"
                               class="px-3 py-1 text-xs font-semibold text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition">Detail</a>
                            <a href="{{ route('gaji-guru.export.pdf', $item) }}"
                               class="px-3 py-1 text-xs font-semibold text-green-600 border border-green-200 rounded-lg hover:bg-green-50 transition" target="_blank">PDF</a>
                            <form action="{{ route('gaji-guru.destroy', $item) }}" method="POST"
                                  onsubmit="return confirm('Hapus data gaji ini? Pengeluaran terkait juga akan dihapus.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 text-xs font-semibold text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400">
                        Belum ada riwayat gaji guru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($gajiGuru->hasPages())
        <div class="p-4 border-t">{{ $gajiGuru->links() }}</div>
        @endif
    </div>
</div>

{{-- ── MODAL BAYAR GAJI ── --}}
<div id="modal-bayar" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-base font-bold text-gray-900">Bayar Gaji Guru</h3>
            <button onclick="document.getElementById('modal-bayar').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('gaji-guru.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Guru <span class="text-red-500">*</span></label>
                <select name="guru_id" id="guru-select" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($guruOptions as $guru)
                    <option value="{{ $guru->id }}" data-default="{{ $gajiDefaultMap[$guru->id] ?? '' }}">
                        {{ $guru->nama }}{{ $guru->nip ? ' — '.$guru->nip : '' }}
                    </option>
                    @endforeach
                </select>
                <p id="guru-default-hint" class="mt-1 text-xs text-emerald-600 hidden"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Bulan <span class="text-red-500">*</span></label>
                    <select name="periode_bulan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                        <option value="">-- Pilih Bulan --</option>
                        @foreach($bulanOptions as $v => $l)
                        <option value="{{ $v }}" {{ (int)date('n') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" name="periode_tahun" value="{{ date('Y') }}" min="2000" max="2100" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah Gaji (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah" id="jumlah-input" min="1" step="1000" required
                       placeholder="Nominal akan terisi otomatis dari gaji default"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Bayar <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2" placeholder="Opsional — akan diisi otomatis jika kosong"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-bayar').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Batal</button>
                <button type="submit"
                        class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('guru-select').addEventListener('change', function () {
    const opt      = this.options[this.selectedIndex];
    const nominal  = opt.dataset.default;
    const hint     = document.getElementById('guru-default-hint');
    const jumlah   = document.getElementById('jumlah-input');

    if (nominal && parseFloat(nominal) > 0) {
        jumlah.value = parseFloat(nominal);
        const formatted = new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(nominal);
        hint.textContent = '✓ Terisi dari gaji default: ' + formatted;
        hint.classList.remove('hidden');
    } else {
        jumlah.value = '';
        hint.classList.add('hidden');
    }
});
</script>
@endsection
