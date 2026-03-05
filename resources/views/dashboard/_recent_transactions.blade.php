<!-- This is a temporary file to review the new design -->
<!-- Recent Transactions Table with Midone styling -->
<div class="lg:col-span-2 bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden">
    <div class="px-8 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-slate-800">Transaksi Terakhir</h3>
                <p class="text-sm text-slate-500 mt-0.5">5 transaksi terbaru</p>
            </div>
            <a href="{{ route('pembayaran.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                <span>Lihat Semua</span>
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-8 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Jumlah</th>
                    <th class="px-8 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-8 py-4 whitespace-nowrap text-sm font-medium text-slate-700">27 Feb 2026</td>
                    <td class="px-6 py-4 text-sm text-slate-700">Pembayaran SPP - Ahmad Rizki</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-lg bg-gradient-to-r from-emerald-500 to-green-400 text-white shadow-sm shadow-emerald-500/30">
                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                            </svg>
                            Pemasukan
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">+Rp 500.000</td>
                    <td class="px-8 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-lg bg-green-100 text-green-700">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></span>
                            Lunas
                        </span>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-8 py-4 whitespace-nowrap text-sm font-medium text-slate-700">26 Feb 2026</td>
                    <td class="px-6 py-4 text-sm text-slate-700">Pembelian ATK</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-lg bg-gradient-to-r from-rose-500 to-red-400 text-white shadow-sm shadow-rose-500/30">
                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                            </svg>
                            Pengeluaran
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-rose-600">-Rp 250.000</td>
                    <td class="px-8 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-lg bg-blue-100 text-blue-700">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2"></span>
                            Disetujui
                        </span>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-8 py-4 whitespace-nowrap text-sm font-medium text-slate-700">25 Feb 2026</td>
                    <td class="px-6 py-4 text-sm text-slate-700">Pembayaran Uang Gedung - Siti</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-lg bg-gradient-to-r from-emerald-500 to-green-400 text-white shadow-sm shadow-emerald-500/30">
                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                            </svg>
                            Pemasukan
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">+Rp 2.000.000</td>
                    <td class="px-8 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-lg bg-green-100 text-green-700">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></span>
                            Lunas
                        </span>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-8 py-4 whitespace-nowrap text-sm font-medium text-slate-700">24 Feb 2026</td>
                    <td class="px-6 py-4 text-sm text-slate-700">Pembayaran Listrik</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-lg bg-gradient-to-r from-rose-500 to-red-400 text-white shadow-sm shadow-rose-500/30">
                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                            </svg>
                            Pengeluaran
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-rose-600">-Rp 850.000</td>
                    <td class="px-8 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-lg bg-amber-100 text-amber-700">
                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-2 animate-pulse"></span>
                            Pending
                        </span>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-8 py-4 whitespace-nowrap text-sm font-medium text-slate-700">23 Feb 2026</td>
                    <td class="px-6 py-4 text-sm text-slate-700">Pembayaran SPP - Budi Santoso</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-lg bg-gradient-to-r from-emerald-500 to-green-400 text-white shadow-sm shadow-emerald-500/30">
                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                            </svg>
                            Pemasukan
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">+Rp 500.000</td>
                    <td class="px-8 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-lg bg-green-100 text-green-700">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></span>
                            Lunas
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
