@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .mod-overview-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.375rem 0.75rem;
        border-radius: 0.6rem;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .mod-overview-btn-lms {
        background: #059669 !important;
        color: #ffffff !important;
        border-color: #047857 !important;
    }

    .mod-overview-btn-lms:hover {
        background: #047857 !important;
    }

    .mod-overview-btn-fin {
        background: #f59e0b !important;
        color: #111827 !important;
        border-color: #d97706 !important;
    }

    .mod-overview-btn-fin:hover {
        background: #fbbf24 !important;
    }

    .mod-overview-btn-fin-alt {
        background: #4f46e5 !important;
        color: #ffffff !important;
        border-color: #4338ca !important;
    }

    .mod-overview-btn-fin-alt:hover {
        background: #4338ca !important;
    }
</style>
@endpush

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
            <p class="text-blue-100 text-sm">{{ $dashboardSubtitle ?? (config('finance.school.name', 'Sekolah') . ' - Dashboard Terpadu Akademik, LMS, dan Keuangan') }}</p>
        </div>
        <div class="hidden md:flex items-center space-x-3">
            <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-5 py-3 text-white">
                <p class="text-xs text-blue-100 font-medium">Tahun Ajaran</p>
                <p class="text-lg font-bold">{{ config('finance.defaults.academic_year', '2025/2026') }}</p>
            </div>
        </div>
    </div>
</div>

@if(($dashboardRole ?? 'default') === 'guru')
    @include('dashboard.partials.guru')
@elseif(($dashboardRole ?? 'default') === 'siswa')
    @include('dashboard.partials.siswa')
@else
@php
    $canViewKelas = (bool) ($canViewKelas ?? false);
    $canViewUjian = (bool) ($canViewUjian ?? false);
    $canViewJadwal = (bool) ($canViewJadwal ?? false);
    $canViewLmsIndex = (bool) ($canViewLmsIndex ?? false);
    $canViewLmsMateri = (bool) ($canViewLmsMateri ?? false);
    $canViewLmsTugas = (bool) ($canViewLmsTugas ?? false);
    $canViewLmsMonitoring = (bool) ($canViewLmsMonitoring ?? false);
    $canViewPembayaran = (bool) ($canViewPembayaran ?? false);
    $canViewPengeluaran = (bool) ($canViewPengeluaran ?? false);
    $canViewCashflow = (bool) ($canViewCashflow ?? false);
    $canViewSiswa = (bool) ($canViewSiswa ?? false);
    $canViewRiwayat = (bool) ($canViewRiwayat ?? false);
    $canCreateSiswa = (bool) ($canCreateSiswa ?? false);
    $canCreatePembayaran = (bool) ($canCreatePembayaran ?? false);
    $canCreatePengeluaran = (bool) ($canCreatePengeluaran ?? false);
    $canExportLaporan = (bool) ($canExportLaporan ?? false);
    $normalizedRoleNames = auth()->user()->getRoleNames()->map(function ($role) {
        $normalized = strtolower((string) $role);

        return preg_replace('/[^a-z0-9]/', '', $normalized);
    });
    $isKepalaSekolahRole = $normalizedRoleNames->contains('kepalasekolah');

    $filteredTransactions = collect($recentTransactions ?? [])->filter(function ($trx) use ($canViewPembayaran, $canViewPengeluaran) {
        $tipe = strtolower((string) ($trx['tipe'] ?? ''));

        return ($tipe === 'pemasukan' && $canViewPembayaran)
            || ($tipe === 'pengeluaran' && $canViewPengeluaran);
    })->values();
@endphp

<!-- Modul Terpadu Overview -->
<div class="grid grid-cols-1 gap-5 mb-6 {{ ($dashboardRole ?? 'default') === 'bendahara' ? '' : 'lg:grid-cols-12' }}">
    @if(($dashboardRole ?? 'default') !== 'bendahara')
    <div class="rounded-2xl shadow-lg shadow-blue-100/70 p-5 border border-blue-200 bg-gradient-to-br from-blue-50 to-cyan-50 {{ ($dashboardRole ?? 'default') === 'bendahara' ? '' : 'lg:col-span-3' }}">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-blue-900">Akademik</h3>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-200 text-blue-800">Aktif</span>
        </div>
        <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between"><span class="text-blue-700">Total Kelas</span><span class="font-bold text-blue-950">{{ number_format($totalKelas) }}</span></div>
            <div class="flex items-center justify-between"><span class="text-blue-700">Jadwal Aktif</span><span class="font-bold text-blue-950">{{ number_format($totalJadwalAktif) }}</span></div>
            <div class="flex items-center justify-between"><span class="text-blue-700">Ujian Mendatang</span><span class="font-bold text-blue-950">{{ number_format($totalUjianMendatang) }}</span></div>
        </div>
        <div class="mt-4 pt-4 border-t border-blue-200 flex flex-wrap gap-2">
            @if($canViewJadwal)
            <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">Jadwal</a>
            @endif
            @if($canViewUjian)
            <a href="{{ route('akademik.ujian.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">Ujian</a>
            @endif
            @if($canViewKelas)
            <a href="{{ route('kelas.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">Kelas</a>
            @endif
            @if(!$canViewJadwal && !$canViewUjian && !$canViewKelas)
            <span class="text-xs font-semibold text-slate-500">Akses akademik terbatas</span>
            @endif
        </div>
    </div>

    <div class="rounded-2xl shadow-lg shadow-emerald-100/70 p-5 border border-emerald-200 bg-gradient-to-br from-emerald-50 to-teal-50 {{ ($dashboardRole ?? 'default') === 'bendahara' ? '' : 'lg:col-span-4' }}">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-emerald-900">LMS</h3>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-200 text-emerald-800">Online</span>
        </div>
        <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between"><span class="text-emerald-700">Materi</span><span class="font-bold text-emerald-950">{{ number_format($totalMateri) }}</span></div>
            <div class="flex items-center justify-between"><span class="text-emerald-700">Tugas</span><span class="font-bold text-emerald-950">{{ number_format($totalTugas) }}</span></div>
            <div class="flex items-center justify-between"><span class="text-emerald-700">Pengumpulan</span><span class="font-bold text-emerald-950">{{ number_format($totalPengumpulan) }}</span></div>
        </div>
        <div class="mt-4 pt-4 border-t border-emerald-200 flex flex-wrap gap-2">
            @if($canViewLmsIndex)
            <a href="{{ route('akademik.lms.index') }}" class="mod-overview-btn mod-overview-btn-lms">LMS</a>
            @endif
            @if($canViewLmsMateri)
            <a href="{{ route('akademik.lms.materi.index') }}" class="mod-overview-btn mod-overview-btn-lms">Materi</a>
            @endif
            @if($canViewLmsTugas)
            <a href="{{ route('akademik.lms.tugas.index') }}" class="mod-overview-btn mod-overview-btn-lms">Tugas</a>
            @endif
            @if($canViewLmsMonitoring)
            <a href="{{ route('akademik.lms.monitoring.index') }}" class="mod-overview-btn mod-overview-btn-lms">Monitoring</a>
            @endif
            @if(!$canViewLmsIndex && !$canViewLmsMateri && !$canViewLmsTugas && !$canViewLmsMonitoring)
            <span class="text-xs font-semibold text-slate-500">Akses LMS terbatas</span>
            @endif
        </div>
    </div>
    @endif

    <div class="rounded-2xl shadow-lg shadow-amber-100/80 p-5 border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 {{ ($dashboardRole ?? 'default') === 'bendahara' ? 'w-full' : 'lg:col-span-5' }}">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-amber-900">Ringkasan Keuangan</h3>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-200 text-amber-900">Bulan Ini</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="flex flex-col"><span class="text-amber-800 mb-1">Pemasukan</span><span class="text-lg font-bold text-emerald-700">{{ format_rupiah($totalPembayaran) }}</span></div>
            <div class="flex flex-col"><span class="text-amber-800 mb-1">Pengeluaran</span><span class="text-lg font-bold text-rose-700">{{ format_rupiah($totalPengeluaran) }}</span></div>
            <div class="flex flex-col"><span class="text-amber-800 mb-1">Total Transaksi</span><span class="text-lg font-bold text-amber-950">{{ number_format($transaksiKeuanganBulanIni) }}</span></div>
        </div>
        <div class="mt-4 pt-4 border-t border-amber-200 flex flex-wrap gap-2">
            @if($canViewPembayaran && !$isKepalaSekolahRole)
            <a href="{{ route('pembayaran.index') }}" class="mod-overview-btn mod-overview-btn-fin">Daftar Pembayaran</a>
            @endif
            @if($canViewPengeluaran && !$isKepalaSekolahRole)
            <a href="{{ route('pengeluaran.index') }}" class="mod-overview-btn mod-overview-btn-fin">Daftar Pengeluaran</a>
            @endif
            @if($canViewCashflow && !$isKepalaSekolahRole)
            <a href="{{ route('laporan.cashflow') }}" class="mod-overview-btn mod-overview-btn-fin">Laporan Cashflow</a>
            @endif
            @if((!$canViewPembayaran && !$canViewPengeluaran && !$canViewCashflow) || $isKepalaSekolahRole)
            <span class="text-xs font-semibold text-slate-500">Aksi keuangan dibatasi</span>
            @endif
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
            @if($canViewCashflow && !$isKepalaSekolahRole)
            <a href="{{ route('laporan.cashflow') }}" class="flex items-center justify-between text-xs font-semibold text-white hover:text-blue-100 transition-colors group">
                <span>Lihat Detail</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
            @else
            <span class="text-xs font-semibold text-white/80">Aksi keuangan dibatasi</span>
            @endif
        </div>
    </div>

    <!-- Total Siswa / Transaksi Card -->
    <div class="group relative bg-white rounded-2xl shadow-lg shadow-slate-200 overflow-hidden hover:shadow-xl hover:shadow-slate-300 transition-all duration-300 hover:-translate-y-1">
        <div class="p-5">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    @if(($dashboardRole ?? 'default') === 'bendahara')
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Transaksi</p>
                        <p class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-500 bg-clip-text text-transparent mt-2 mb-1.5">{{ number_format($transaksiKeuanganBulanIni) }}</p>
                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-50">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span>
                            <span class="text-xs font-semibold text-emerald-700">Bulan Ini</span>
                        </div>
                    @else
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Siswa</p>
                        <p class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-500 bg-clip-text text-transparent mt-2 mb-1.5">{{ $totalSiswa }}</p>
                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-50">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span>
                            <span class="text-xs font-semibold text-emerald-700">Siswa Aktif</span>
                        </div>
                    @endif
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-400 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/40 group-hover:scale-110 transition-transform duration-300">
                    @if(($dashboardRole ?? 'default') === 'bendahara')
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    @else
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    @endif
                </div>
            </div>
        </div>
        <div class="bg-slate-50 px-5 py-2.5 border-t border-slate-100">
            @if(($dashboardRole ?? 'default') === 'bendahara')
                <a href="{{ route('riwayat.index') }}" class="flex items-center justify-between text-xs font-semibold text-slate-600 hover:text-emerald-600 transition-colors group">
                    <span>Lihat Riwayat</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </a>
            @else
                @if($canViewSiswa)
                <a href="{{ route('siswa.index') }}" class="flex items-center justify-between text-xs font-semibold text-slate-600 hover:text-emerald-600 transition-colors group">
                    <span>Lihat Siswa</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </a>
                @else
                <span class="text-xs font-semibold text-slate-500">Akses siswa dibatasi</span>
                @endif
            @endif
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
            @if($canViewPembayaran && !$isKepalaSekolahRole)
            <a href="{{ route('pembayaran.index') }}" class="flex items-center justify-between text-xs font-semibold text-slate-600 hover:text-blue-600 transition-colors group">
                <span>Lihat Transaksi</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
            @else
            <span class="text-xs font-semibold text-slate-500">Aksi keuangan dibatasi</span>
            @endif
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
            @if($canViewPengeluaran && !$isKepalaSekolahRole)
            <a href="{{ route('pengeluaran.index') }}" class="flex items-center justify-between text-xs font-semibold text-slate-600 hover:text-rose-600 transition-colors group">
                <span>Lihat Detail</span>
                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
            @else
            <span class="text-xs font-semibold text-slate-500">Aksi keuangan dibatasi</span>
            @endif
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 gap-5 mb-6 lg:grid-cols-3">
    <!-- Cashflow Chart -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg shadow-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-5 gap-3">
            <div>
                <h3 class="text-lg font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">Cashflow Overview</h3>
                <p class="text-xs text-slate-500 mt-1">Pemasukan vs Pengeluaran</p>
            </div>
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row gap-2" id="chartFilterForm">
                <input type="date" 
                       name="chart_start_date" 
                       value="{{ request('chart_start_date', now()->subMonths(5)->startOfMonth()->format('Y-m-d')) }}"
                       class="text-xs border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm px-2 py-1.5 bg-slate-50"
                       onchange="this.form.submit()">
                <input type="date" 
                       name="chart_end_date" 
                       value="{{ request('chart_end_date', now()->endOfMonth()->format('Y-m-d')) }}"
                       class="text-xs border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm px-2 py-1.5 bg-slate-50"
                       onchange="this.form.submit()">
            </form>
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
        <div class="relative h-56">
            <canvas id="expenseCompositionChart" class="{{ ($expenseComposition ?? collect())->isEmpty() ? 'opacity-20' : '' }}"></canvas>
            @if(($expenseComposition ?? collect())->isEmpty())
                <div class="absolute inset-0 flex items-center justify-center rounded-xl bg-gradient-to-br from-slate-50 to-slate-100">
                    <div class="text-center text-slate-400 px-4">
                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                        <p class="text-xs font-medium">Belum ada data pengeluaran bulan ini</p>
                    </div>
                </div>
            @endif
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
                @if($canViewRiwayat)
                <a href="{{ route('riwayat.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">Lihat Semua →</a>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" data-slider-per-page="10">
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
                    @forelse($filteredTransactions as $trx)
                        @php
                            $tipe = strtolower((string) ($trx['tipe'] ?? ''));
                            $isPemasukan = $tipe === 'pemasukan';
                            $statusText = (string) ($trx['status'] ?? '-');
                            $statusKey = strtolower($statusText);
                            $typeClass = $isPemasukan
                                ? 'bg-green-100 text-green-800'
                                : 'bg-red-100 text-red-800';
                            $amountClass = $isPemasukan
                                ? 'text-green-600'
                                : 'text-red-600';

                            if (in_array($statusKey, ['lunas', 'disetujui', 'approved'], true)) {
                                $statusClass = 'bg-green-100 text-green-800';
                            } elseif (in_array($statusKey, ['pending'], true)) {
                                $statusClass = 'bg-yellow-100 text-yellow-800';
                            } elseif (in_array($statusKey, ['ditolak', 'dibatalkan', 'cancelled'], true)) {
                                $statusClass = 'bg-red-100 text-red-800';
                            } else {
                                $statusClass = 'bg-slate-100 text-slate-700';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                {{ !empty($trx['tanggal']) ? \Carbon\Carbon::parse($trx['tanggal'])->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-900 max-w-xs">
                                <a href="{{ $trx['url'] ?? ($canViewRiwayat ? route('riwayat.index') : '#') }}" class="hover:text-indigo-600 transition-colors line-clamp-1">
                                    {{ $trx['deskripsi'] ?? '-' }}
                                </a>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $typeClass }}">{{ $trx['tipe'] ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs font-medium {{ $amountClass }}">
                                {{ $isPemasukan ? '+' : '-' }}{{ format_rupiah((float) ($trx['jumlah'] ?? 0)) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                Tidak ada transaksi yang dapat ditampilkan untuk role Anda.
                            </td>
                        </tr>
                    @endforelse
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
                @if(($dashboardRole ?? 'default') === 'bendahara')
                <a href="{{ route('gaji-guru.index') }}" class="flex items-center justify-between p-2.5 bg-blue-50 hover:bg-blue-100 rounded-lg transition group">
                    <div class="flex items-center">
                        <div class="p-1.5 bg-blue-600 rounded-lg">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="ml-2 text-xs font-medium text-gray-900">Kelola Gaji Guru</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endif

                @if($canCreateSiswa && ($dashboardRole ?? 'default') !== 'bendahara')
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
                    </svg>
                </a>
                @endif

                @if($canCreatePembayaran)
                <a href="{{ route('pembayaran.create') }}" class="flex items-center justify-between p-2.5 bg-green-50 hover:bg-green-100 rounded-lg transition group">
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
                @endif

                @if($canCreatePengeluaran)
                <a href="{{ route('pengeluaran.create') }}" class="flex items-center justify-between p-2.5 bg-red-50 hover:bg-red-100 rounded-lg transition group">
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
                @endif

                @if($canViewCashflow)
                <a href="{{ route('laporan.cashflow') }}" class="flex items-center justify-between p-2.5 bg-purple-50 hover:bg-purple-100 rounded-lg transition group">
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
                @endif

                @if(!$canCreateSiswa && !$canCreatePembayaran && !$canCreatePengeluaran && !$canViewCashflow && ($dashboardRole ?? 'default') !== 'bendahara')
                <p class="text-xs text-slate-500">Role Anda berada pada mode monitor. Tidak ada aksi input cepat.</p>
                @endif
            </div>
        </div>

        <!-- System Info -->
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-sm p-5 text-white">
            <h3 class="text-base font-semibold mb-2">Sistem Info</h3>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="opacity-90">Laravel Version:</span>
                    <span class="font-semibold">{{ app()->version() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="opacity-90">Database:</span>
                    <span class="font-semibold">{{ strtoupper((string) config('database.default', '-')) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="opacity-90">Server Time:</span>
                    <span class="font-semibold">{{ now()->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cashflow Chart
    const ctx = document.getElementById('dashboardCashflowChart');
    
    if (ctx) {
        const pemasukanData = @json($pemasukanData ?? []);
        const pengeluaranData = @json($pengeluaranData ?? []);
        const groupBy = @json($groupBy ?? 'month');
        
        // Get all unique periods
        const allPeriods = new Set();
        pemasukanData.forEach(item => allPeriods.add(item.periode));
        pengeluaranData.forEach(item => allPeriods.add(item.periode));
        
        // Sort periods
        const sortedPeriods = Array.from(allPeriods).sort();
        
        // Format labels based on groupBy
        const labels = sortedPeriods.map(periode => {
            const date = new Date(periode);
            if (groupBy === 'day') {
                // Format: DD MMM
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
            } else {
                // Format: MMM YY
                return date.toLocaleDateString('id-ID', { month: 'short', year: '2-digit' });
            }
        });
        
        // Map data to sorted periods
        const pemasukanValues = sortedPeriods.map(periode => {
            const item = pemasukanData.find(p => p.periode === periode);
            return item ? parseFloat(item.total) : 0;
        });
        
        const pengeluaranValues = sortedPeriods.map(periode => {
            const item = pengeluaranData.find(p => p.periode === periode);
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

    // Expense Composition Chart
    const expenseCtx = document.getElementById('expenseCompositionChart');

    if (expenseCtx) {
        const expenseComposition = @json($expenseComposition ?? []);

        if (expenseComposition.length > 0) {
            const labels = expenseComposition.map(item => item.kategori);
            const values = expenseComposition.map(item => parseFloat(item.total));
            const colors = [
                'rgba(59, 130, 246, 0.85)',
                'rgba(16, 185, 129, 0.85)',
                'rgba(249, 115, 22, 0.85)',
                'rgba(239, 68, 68, 0.85)',
                'rgba(99, 102, 241, 0.85)',
                'rgba(20, 184, 166, 0.85)'
            ];

            new Chart(expenseCtx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: values,
                            backgroundColor: colors,
                            borderColor: '#ffffff',
                            borderWidth: 2,
                            hoverOffset: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                padding: 12,
                                font: {
                                    size: 10,
                                    weight: '600'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    return `${context.label}: ${new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        minimumFractionDigits: 0
                                    }).format(value)}`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Guru LMS trend chart
    const guruTrendCtx = document.getElementById('guruLmsTrendChart');
    if (guruTrendCtx) {
        const labels = @json($guruMonthlyLabels ?? []);
        const tugasValues = @json($guruMonthlyTugas ?? []);
        const pengumpulanValues = @json($guruMonthlyPengumpulan ?? []);

        new Chart(guruTrendCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Tugas Dibuat',
                        data: tugasValues,
                        borderColor: 'rgb(79, 70, 229)',
                        backgroundColor: 'rgba(79, 70, 229, 0.15)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                    },
                    {
                        label: 'Pengumpulan Masuk',
                        data: pengumpulanValues,
                        borderColor: 'rgb(5, 150, 105)',
                        backgroundColor: 'rgba(5, 150, 105, 0.15)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                        },
                    },
                },
            },
        });
    }

    // Guru grading status chart
    const guruStatusCtx = document.getElementById('guruPenilaianChart');
    if (guruStatusCtx) {
        const labels = @json($guruStatusLabels ?? []);
        const values = @json($guruStatusValues ?? []);

        new Chart(guruStatusCtx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [
                    {
                        data: values,
                        backgroundColor: [
                            'rgba(5, 150, 105, 0.85)',
                            'rgba(245, 158, 11, 0.85)',
                        ],
                        borderColor: '#fff',
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });
    }

    // Siswa weekly schedule chart
    const siswaJadwalCtx = document.getElementById('siswaJadwalChart');
    if (siswaJadwalCtx) {
        const labels = @json($siswaScheduleLabels ?? []);
        const values = @json($siswaScheduleValues ?? []);

        new Chart(siswaJadwalCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Jumlah Jadwal',
                        data: values,
                        backgroundColor: 'rgba(59, 130, 246, 0.75)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        borderRadius: 8,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                        },
                    },
                },
            },
        });
    }

    // Siswa assignment progress chart
    const siswaProgressCtx = document.getElementById('siswaTugasProgressChart');
    if (siswaProgressCtx) {
        const rawLabels = @json($siswaProgressLabels ?? []);
        const rawValues = @json($siswaProgressValues ?? []);
        const labels = Array.isArray(rawLabels) && rawLabels.length >= 2
            ? rawLabels
            : ['Selesai', 'Belum Dikumpulkan'];
        const values = Array.isArray(rawValues) && rawValues.length >= 2
            ? rawValues.map((value) => Number(value || 0))
            : [0, 0];
        const total = values.reduce((sum, current) => sum + Number(current || 0), 0);
        const percentages = values.map((value) => {
            if (total <= 0) {
                return 0;
            }

            return (Number(value || 0) / total) * 100;
        });

        const formatPercent = (value) => {
            const rounded = Math.round(value * 10) / 10;
            return Number.isInteger(rounded) ? String(rounded) : rounded.toFixed(1);
        };

        const selesaiPctEl = document.getElementById('siswaProgressPctSelesai');
        const belumPctEl = document.getElementById('siswaProgressPctBelum');
        if (selesaiPctEl) {
            selesaiPctEl.textContent = `${formatPercent(percentages[0] || 0)}%`;
        }
        if (belumPctEl) {
            belumPctEl.textContent = `${formatPercent(percentages[1] || 0)}%`;
        }

        new Chart(siswaProgressCtx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [
                    {
                        data: values,
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.85)',
                            'rgba(239, 68, 68, 0.85)',
                        ],
                        borderColor: '#fff',
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        display: false,
                    },
                },
            },
        });
    }
});
</script>
@endpush
