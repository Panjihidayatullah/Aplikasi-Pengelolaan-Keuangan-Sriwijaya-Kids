@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-cyan-500 to-blue-700 rounded-2xl shadow-2xl shadow-blue-500/30 p-6 mb-6">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>
    
    <div class="relative flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-blue-100 text-sm">{{ config('finance.school.name', 'Sekolah') }} - Sistem Keuangan</p>
        </div>
        <div class="hidden md:flex items-center space-x-3">
            <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-5 py-3 text-white">
                <p class="text-xs text-blue-100 font-medium">Tahun Ajaran</p>
                <p class="text-lg font-bold">{{ config('finance.defaults.academic_year', '2025/2026') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="grid grid-cols-1 gap-5 mb-6 lg:grid-cols-4">
    <!-- Saldo Card -->
    <div class="group relative bg-gradient-to-br from-blue-500 to-cyan-400 rounded-2xl shadow-xl shadow-blue-500/40 overflow-hidden hover:shadow-2xl hover:shadow-blue-500/50 transition-all duration-300 hover:-translate-y-1">
        <!-- Animated Background -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 30px 30px;"></div>
        </div>
        
        <div class="relative p-5">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-blue-100 uppercase tracking-wide">Saldo Saat Ini</p>
                    <p class="text-2xl font-bold text-white mt-2 mb-1.5">{{ format_rupiah($saldo) }}</p>
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-white/20 backdrop-blur-sm">
                        <svg class="w-3.5 h-3.5 mr-1 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-bold text-white">Sehat</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="relative bg-white/10 backdrop-blur-sm px-5 py-2.5 border-t border-white/20">
            <a href="#" class="flex items-center justify-between text-xs font-semibold text-white hover:text-blue-100 transition-colors group">
                <span>Lihat Detail</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Total Siswa Card -->
    <div class="group relative bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden hover:shadow-xl hover:shadow-slate-300 transition-all duration-300 hover:-translate-y-1">
        <div class="p-5">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Siswa</p>
                    <p class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-500 bg-clip-text text-transparent mt-2 mb-1.5">{{ $totalSiswa }}</p>
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-50">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span>
                        <span class="text-xs font-semibold text-emerald-700">Siswa Aktif</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-400 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/40 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-slate-50 px-5 py-2.5 border-t border-slate-100">
            <a href="{{ route('siswa.index') }}" class="flex items-center justify-between text-xs font-semibold text-slate-600 hover:text-emerald-600 transition-colors group">
                <span>Lihat Siswa</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Total Pembayaran Card -->
    <div class="group relative bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden hover:shadow-xl hover:shadow-slate-300 transition-all duration-300 hover:-translate-y-1">
        <div class="p-5">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pemasukan Bulan Ini</p>
                    <p class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent mt-2 mb-1.5">{{ format_rupiah($totalPembayaran) }}</p>
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-green-50">
                        <svg class="w-3.5 h-3.5 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-semibold text-green-700">+12.5%</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/40 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-slate-50 px-5 py-2.5 border-t border-slate-100">
            <a href="{{ route('pembayaran.index') }}" class="flex items-center justify-between text-xs font-semibold text-slate-600 hover:text-blue-600 transition-colors group">
                <span>Lihat Transaksi</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Total Pengeluaran Card -->
    <div class="group relative bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden hover:shadow-xl hover:shadow-slate-300 transition-all duration-300 hover:-translate-y-1">
        <div class="p-5">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pengeluaran Bulan Ini</p>
                    <p class="text-2xl font-bold bg-gradient-to-r from-rose-600 to-red-500 bg-clip-text text-transparent mt-2 mb-1.5">{{ format_rupiah($totalPengeluaran) }}</p>
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-red-50">
                        <svg class="w-3.5 h-3.5 mr-1 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-semibold text-red-700">+8.3%</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-red-400 rounded-2xl flex items-center justify-center shadow-lg shadow-rose-500/40 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-slate-50 px-5 py-2.5 border-t border-slate-100">
            <a href="{{ route('pengeluaran.index') }}" class="flex items-center justify-between text-xs font-semibold text-slate-600 hover:text-rose-600 transition-colors group">
                <span>Lihat Detail</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 gap-5 mb-6 lg:grid-cols-3">
    <!-- Cashflow Chart -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg shadow-slate-200 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">Cashflow Overview</h3>
                <p class="text-xs text-slate-500 mt-1">Pemasukan vs Pengeluaran 6 Bulan Terakhir</p>
            </div>
            <select class="text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm px-3 py-1.5 bg-slate-50">
                <option>6 Bulan</option>
                <option>3 Bulan</option>
                <option>1 Tahun</option>
            </select>
        </div>
        <div class="h-56">
            <canvas id="dashboardCashflowChart"></canvas>
        </div>
    </div>

    <!-- Expense Composition Chart -->
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200 p-6">
        <div class="mb-5">
            <h3 class="text-lg font-bold bg-gradient-to-r from-rose-600 to-red-500 bg-clip-text text-transparent">Komposisi Pengeluaran</h3>
            <p class="text-xs text-slate-500 mt-1">Bulan Ini</p>
        </div>
        <div class="h-56 flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl">
            <div class="text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                <p class="text-xs font-medium">Pie Chart Integration</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions & Quick Stats -->
<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
    <!-- Recent Transactions -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Transaksi Terakhir</h3>
                <a href="#" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">Lihat Semua →</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">27 Feb 2026</td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">Pembayaran SPP - Ahmad Rizki</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Pemasukan</span></td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-green-600">+Rp 500.000</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Lunas</span></td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">26 Feb 2026</td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">Pembelian ATK</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Pengeluaran</span></td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-red-600">-Rp 250.000</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Approved</span></td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">25 Feb 2026</td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">Pembayaran Uang Gedung - Siti Nurhaliza</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Pemasukan</span></td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-green-600">+Rp 2.000.000</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Lunas</span></td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">24 Feb 2026</td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">Pembayaran Listrik</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Pengeluaran</span></td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-red-600">-Rp 850.000</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span></td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">23 Feb 2026</td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">Pembayaran SPP - Budi Santoso</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Pemasukan</span></td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-green-600">+Rp 500.000</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Lunas</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Stats & Actions -->
    <div class="space-y-5">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('siswa.create') }}" class="flex items-center justify-between p-2.5 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition group">
                    <div class="flex items-center">
                        <div class="p-1.5 bg-indigo-600 rounded-lg">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <span class="ml-2 text-xs font-medium text-gray-900">Tambah Siswa</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="#" class="flex items-center justify-between p-2.5 bg-green-50 hover:bg-green-100 rounded-lg transition group">
                    <div class="flex items-center">
                        <div class="p-1.5 bg-green-600 rounded-lg">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <span class="ml-2 text-xs font-medium text-gray-900">Input Pembayaran</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="#" class="flex items-center justify-between p-2.5 bg-red-50 hover:bg-red-100 rounded-lg transition group">
                    <div class="flex items-center">
                        <div class="p-1.5 bg-red-600 rounded-lg">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <span class="ml-2 text-xs font-medium text-gray-900">Input Pengeluaran</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <a href="#" class="flex items-center justify-between p-2.5 bg-purple-50 hover:bg-purple-100 rounded-lg transition group">
                    <div class="flex items-center">
                        <div class="p-1.5 bg-purple-600 rounded-lg">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="ml-2 text-xs font-medium text-gray-900">Generate Laporan</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- System Info -->
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-sm p-5 text-white">
            <h3 class="text-base font-semibold mb-2">Sistem Info</h3>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="opacity-90">Laravel Version:</span>
                    <span class="font-semibold">12.53.0</span>
                </div>
                <div class="flex justify-between">
                    <span class="opacity-90">Database:</span>
                    <span class="font-semibold">PostgreSQL</span>
                </div>
                <div class="flex justify-between">
                    <span class="opacity-90">Last Backup:</span>
                    <span class="font-semibold">Today</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cashflow Chart
    const ctx = document.getElementById('dashboardCashflowChart');
    
    if (ctx) {
        const pemasukanData = @json($pemasukanBulanan ?? []);
        const pengeluaranData = @json($pengeluaranBulanan ?? []);
        
        // Get all unique months
        const allMonths = new Set();
        pemasukanData.forEach(item => allMonths.add(item.bulan));
        pengeluaranData.forEach(item => allMonths.add(item.bulan));
        
        // If no data, create last 6 months
        let sortedMonths = Array.from(allMonths).sort();
        if (sortedMonths.length === 0) {
            const now = new Date();
            for (let i = 5; i >= 0; i--) {
                const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
                sortedMonths.push(date.toISOString().slice(0, 10));
            }
        }
        
        // Format labels
        const labels = sortedMonths.map(month => {
            const date = new Date(month);
            return date.toLocaleDateString('id-ID', { month: 'short', year: '2-digit' });
        });
        
        // Map data to sorted months
        const pemasukanValues = sortedMonths.map(month => {
            const item = pemasukanData.find(p => p.bulan === month);
            return item ? parseFloat(item.total) : 0;
        });
        
        const pengeluaranValues = sortedMonths.map(month => {
            const item = pengeluaranData.find(p => p.bulan === month);
            return item ? parseFloat(item.total) : 0;
        });
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: pemasukanValues,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: 'rgb(34, 197, 94)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Pengeluaran',
                        data: pengeluaranValues,
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: 'rgb(239, 68, 68)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 10,
                        titleFont: {
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 11
                        },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0
                                }).format(context.parsed.y);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                }
                                return 'Rp ' + value;
                            },
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
