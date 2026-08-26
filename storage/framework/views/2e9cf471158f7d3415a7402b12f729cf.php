<!-- Mobile Sidebar Backdrop -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"></div>

<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl transform lg:transform-none transition-transform duration-300 ease-in-out flex flex-col"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
    
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between px-4 border-b border-slate-700/50 flex-shrink-0 overflow-hidden"
         style="height: 5rem; min-height: 5rem; max-height: 5rem;">
        <div class="flex items-center gap-2.5 min-w-0 flex-1 overflow-hidden">
            <!-- Logo — constrained strictly within header height -->
            <div class="flex-shrink-0 flex items-center justify-center"
                 style="width: 2.75rem; height: 2.75rem; overflow: hidden;">
                <img src="<?php echo e(asset(config('finance.school.logo', 'images/educlass-logo.svg'))); ?>"
                     alt="<?php echo e(config('finance.school.name', config('app.name'))); ?>"
                     style="width: 100%; height: 100%; object-fit: contain; display: block;">
            </div>
            <!-- School Name -->
            <div class="min-w-0 overflow-hidden">
                <p class="text-xs text-slate-400 leading-none mb-0.5 truncate">Welcome</p>
                <p class="text-sm font-bold text-white leading-none truncate">Educlass</p>
            </div>
        </div>
        <!-- Close button (mobile only) -->
        <button @click="sidebarOpen = false"
                class="lg:hidden flex-shrink-0 ml-1 p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
        <!-- Dashboard -->
        <a href="<?php echo e(route('dashboard')); ?>" 
           class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
            <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="font-semibold">Dashboard</span>
        </a>

        <!-- Riwayat Aktivitas -->
        <a href="<?php echo e(route('riwayat.index')); ?>" 
           class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('riwayat.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
            <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <span class="font-semibold">Riwayat Aktivitas</span>
        </a>

        <!-- Master Data Section -->
        <?php if(is_admin()): ?>
        <div class="pt-6">
            <div class="px-4 mb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Master Data
            </div>
            
            <!-- Siswa -->
            <a href="<?php echo e(route('siswa.index')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('siswa.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Siswa
            </a>

            <!-- Guru -->
            <a href="<?php echo e(route('guru.index')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('guru.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Guru
            </a>

            <!-- Kelas -->
            <a href="<?php echo e(route('kelas.index')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('kelas.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Kelas
            </a>
        </div>
        <?php endif; ?>

        <!-- Akademik Section -->
        <?php if(is_admin() || (auth()->user()->hasRole('Guru')) || (auth()->user()->hasRole('Kepala Sekolah')) || is_siswa()): ?>
        <div class="pt-6">
            <div class="px-4 mb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Akademik
            </div>

            <!-- Kurikulum & Tahun Ajaran - Admin Only -->
            <?php if(is_admin()): ?>
            <a href="<?php echo e(route('akademik.kurikulum.index')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('akademik.kurikulum.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4a2 2 0 100-4m0 4a2 2 0 110-4"/>
                </svg>
                Kurikulum
            </a>

            <a href="<?php echo e(route('akademik.tahun-ajaran.index')); ?>" 
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('akademik.tahun-ajaran.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Tahun Ajaran
            </a>

            <?php if(can_access('view semester')): ?>
            <a href="<?php echo e(route('akademik.semester.index')); ?>" 
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('akademik.semester.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 4h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Semester
            </a>
            <?php endif; ?>

            <a href="<?php echo e(route('akademik.ruang.index')); ?>"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('akademik.ruang.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4zm0 5l9 4 9-4M3 17l9 4 9-4"/>
                </svg>
                Manajemen Ruang
            </a>
            <?php endif; ?>

            <?php if(can_access('view lms-materi') || can_access('view lms-tugas') || can_access('view lms-monitoring') || can_access('view absensi') || is_admin() || auth()->user()->hasRole('Guru') || auth()->user()->hasRole('Kepala Sekolah') || is_siswa()): ?>
            <?php
                $isLmsMenuActive = request()->routeIs('akademik.lms.*') || request()->routeIs('akademik.absensi.*');
                $normalizedRoleNames = auth()->user()->getRoleNames()->map(function ($role) {
                    $normalized = strtolower((string) $role);

                    return preg_replace('/[^a-z0-9]/', '', $normalized);
                });
                $isKepalaSekolahRole = $normalizedRoleNames->contains('kepalasekolah');
            ?>
            <?php if($isKepalaSekolahRole): ?>
            <a href="<?php echo e(route('akademik.lms.monitoring.index')); ?>"
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('akademik.lms.monitoring.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h10"/>
                </svg>
                LMS
            </a>
            <?php else: ?>
            <div x-data="{ lmsOpen: <?php echo e($isLmsMenuActive ? 'true' : 'false'); ?> }" class="space-y-1">
                <button
                    type="button"
                    @click="lmsOpen = !lmsOpen"
                    class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e($isLmsMenuActive ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>"
                >
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h10"/>
                    </svg>
                    <span class="flex-1 text-left">LMS</span>
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-white/20 flex-shrink-0">
                        <svg class="w-4 h-4 transition-transform duration-200" :class="lmsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </span>
                </button>

                <div x-show="lmsOpen" x-transition class="ml-6 mt-1 space-y-1">
                <?php if(!$isKepalaSekolahRole): ?>
                <a href="<?php echo e(route('akademik.lms.index')); ?>"
                        class="flex items-center px-3 py-2 text-sm rounded-lg transition <?php echo e(request()->routeIs('akademik.lms.index') || request()->routeIs('akademik.lms.pertemuan') ? 'text-cyan-200 bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800'); ?>">
                    Halaman LMS
                </a>
                <?php endif; ?>

                <?php if(can_access('view lms-materi') && !$isKepalaSekolahRole): ?>
                <a href="<?php echo e(route('akademik.lms.materi.index')); ?>"
                        class="flex items-center px-3 py-2 text-sm rounded-lg transition <?php echo e(request()->routeIs('akademik.lms.materi.*') ? 'text-cyan-200 bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800'); ?>">
                    Materi
                </a>
                <?php endif; ?>

                <?php if(can_access('view lms-tugas') && !$isKepalaSekolahRole): ?>
                <a href="<?php echo e(route('akademik.lms.tugas.index')); ?>"
                        class="flex items-center px-3 py-2 text-sm rounded-lg transition <?php echo e(request()->routeIs('akademik.lms.tugas.*') ? 'text-cyan-200 bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800'); ?>">
                    Tugas
                </a>
                <?php endif; ?>

                <?php if((can_access('view absensi') || is_admin() || auth()->user()->hasRole('Guru')) && !$isKepalaSekolahRole): ?>
                <a href="<?php echo e(route('akademik.absensi.index')); ?>"
                        class="flex items-center px-3 py-2 text-sm rounded-lg transition <?php echo e(request()->routeIs('akademik.absensi.*') ? 'text-cyan-200 bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800'); ?>">
                    Absensi
                </a>
                <?php endif; ?>

                <?php if(can_access('view lms-monitoring')): ?>
                <a href="<?php echo e(route('akademik.lms.monitoring.index')); ?>"
                        class="flex items-center px-3 py-2 text-sm rounded-lg transition <?php echo e(request()->routeIs('akademik.lms.monitoring.*') ? 'text-cyan-200 bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800'); ?>">
                    Monitoring
                </a>
                <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Nilai & Transkrip - Guru & Admin -->
            <?php if(is_admin() || auth()->user()->hasRole('Guru')): ?>
            <a href="<?php echo e(route('akademik.transkrip-nilai.index')); ?>" 
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('akademik.transkrip-nilai.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Transkrip Nilai
            </a>
            <?php endif; ?>

            <?php if(is_siswa()): ?>
            <a href="<?php echo e(route('akademik.transkrip-nilai.saya')); ?>"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('akademik.transkrip-nilai.saya*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Transkrip Nilai
            </a>
            <?php endif; ?>

            <?php if(!is_siswa()): ?>
            <!-- Raport Siswa - Admin & Guru -->
            <?php if(is_admin() || auth()->user()->hasRole('Guru')): ?>
            <a href="<?php echo e(route('akademik.raport.index')); ?>"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('akademik.raport.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Raport Siswa
            </a>
            <?php endif; ?>
            <?php endif; ?>


            <?php if(can_access('view ujian') || is_admin() || auth()->user()->hasRole('Guru') || is_siswa()): ?>
            <a href="<?php echo e(route('akademik.ujian.index')); ?>" 
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('akademik.ujian.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3"/>
                </svg>
                Jadwal Ujian
            </a>
            <?php endif; ?>

            <!-- Jadwal Pelajaran - Admin, Guru, Siswa, Kepala Sekolah -->
            <?php if(is_admin() || auth()->user()->hasRole('Guru') || is_siswa() || auth()->user()->hasRole('Kepala Sekolah')): ?>
            <a href="<?php echo e(route('akademik.jadwal-pelajaran.index')); ?>"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('akademik.jadwal-pelajaran.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Jadwal Pelajaran
            </a>
            <?php endif; ?>

            <!-- Pengumuman - All Akademik Users -->
            <?php if(can_access('view pengumuman')): ?>
            <a href="<?php echo e(route('akademik.pengumuman.index')); ?>" 
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('akademik.pengumuman.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Pengumuman
            </a>
            <?php endif; ?>

            <!-- Kenaikan Kelas - Admin & Kepala Sekolah & Guru -->
            <?php if(is_admin() || auth()->user()->hasRole('Kepala Sekolah') || auth()->user()->hasRole('Guru')): ?>
            <a href="<?php echo e(route('akademik.kenaikan-kelas.index')); ?>" 
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('akademik.kenaikan-kelas.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Kenaikan Kelas
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Keuangan Section -->
        <?php if(is_admin() || is_bendahara()): ?>
        <div class="pt-6">
            <div class="px-4 mb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Keuangan
            </div>
            
            <!-- Pembayaran -->
            <a href="<?php echo e(route('pembayaran.index')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('pembayaran.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Pembayaran
            </a>

            <!-- Pengeluaran -->
            <a href="<?php echo e(route('pengeluaran.index')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('pengeluaran.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Pengeluaran
            </a>

            <!-- Gaji Guru -->
            <a href="<?php echo e(route('gaji-guru.index')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('gaji-guru.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Gaji Guru
            </a>

            <!-- Aset -->
            <a href="<?php echo e(route('aset.index')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('aset.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Aset Sekolah
            </a>
        </div>
        <?php endif; ?>

        <!-- Keuangan Guru Section -->
        <?php if(auth()->user()->hasRole('Guru')): ?>
        <div class="pt-6">
            <div class="px-4 mb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Keuangan
            </div>

            <a href="<?php echo e(route('gaji-saya.index')); ?>"
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('gaji-saya.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Gaji Saya
            </a>
        </div>
        <?php endif; ?>

        <!-- Laporan Section -->
        <?php if(can_access('view laporan cashflow') || can_access('view laporan pemasukan') || can_access('view laporan pengeluaran')): ?>
        <div class="pt-6">
            <div class="px-4 mb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Laporan
            </div>
            
            <!-- Laporan Cashflow -->
            <?php if(can_access('view laporan cashflow')): ?>
            <a href="<?php echo e(route('laporan.cashflow')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('laporan.cashflow') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                Cashflow
            </a>
            <?php endif; ?>

            <!-- Laporan Pemasukan -->
            <?php if(can_access('view laporan pemasukan')): ?>
            <a href="<?php echo e(route('laporan.pemasukan')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('laporan.pemasukan') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Pemasukan
            </a>
            <?php endif; ?>

            <!-- Laporan Pengeluaran -->
            <?php if(can_access('view laporan pengeluaran')): ?>
            <a href="<?php echo e(route('laporan.pengeluaran')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('laporan.pengeluaran') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
                Pengeluaran
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Pengaturan Section -->
        <?php if(is_admin()): ?>
        <div class="pt-6 pb-4">
            <div class="px-4 mb-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Pengaturan
            </div>
            
            <!-- User Management -->
            <a href="<?php echo e(route('users.index')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('users.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Manajemen User
            </a>

            <!-- User Approvals -->
            <a href="<?php echo e(route('admin.approvals.index')); ?>" 
               class="flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('admin.approvals.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Persetujuan Akun
                </div>
                <?php
                    $pendingCount = \Illuminate\Support\Facades\Cache::remember('pending_users_count', now()->addMinutes(5), function () {
                        return \App\Models\User::where('is_approved', false)->count();
                    });
                ?>
                <?php if($pendingCount > 0): ?>
                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                        <?php echo e($pendingCount); ?>

                    </span>
                <?php endif; ?>
            </a>

            <!-- Role & Permission -->
            <a href="<?php echo e(route('roles.index')); ?>" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group <?php echo e(request()->routeIs('roles.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-400 text-white shadow-lg shadow-blue-500/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white'); ?>">
                <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Role & Permission
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <!-- User Profile Section -->
    <div class="flex-shrink-0 p-4 border-t border-slate-700/50 bg-slate-900/50 backdrop-blur-sm" x-data="{ profileOpen: false }">
        <div class="relative">
            <!-- Profile Button -->
            <button @click="profileOpen = !profileOpen" class="w-full flex items-center p-3 rounded-xl hover:bg-white/15 transition-all duration-200 group">
                <div class="relative flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                        <?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?>

                    </div>
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-slate-900 rounded-full"></div>
                </div>
                <div class="ml-3 flex-1 min-w-0 text-left">
                    <p class="text-sm font-semibold text-white truncate"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-xs text-slate-400 truncate"><?php echo e(auth()->user()->email); ?></p>
                </div>
                <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition-all transform" :class="profileOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="profileOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 @click.away="profileOpen = false"
                 class="absolute bottom-full left-4 right-4 mb-2 bg-slate-800 rounded-xl shadow-2xl border border-slate-700 overflow-hidden z-50">
                
                <!-- User Info Header -->
                <div class="px-4 py-3 bg-gradient-to-r from-slate-700/50 to-slate-800/50 border-b border-slate-700">
                    <p class="text-sm font-semibold text-white truncate"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-xs text-slate-400 truncate"><?php echo e(auth()->user()->email); ?></p>
                </div>

                <!-- Menu Items -->
                <div class="py-2">
                    <a href="<?php echo e(route('profile.edit')); ?>" class="flex items-center px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition-colors group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil Saya
                    </a>

                    <a href="<?php echo e(route('settings.preferences')); ?>" class="flex items-center px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition-colors group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Pengaturan
                    </a>

                    <div class="border-t border-slate-700 my-2"></div>

                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors group">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</aside>

<style>
    aside nav a.hover\:bg-slate-800:hover,
    aside nav a.hover\:bg-slate-700:hover,
    aside nav a.bg-slate-800,
    aside nav a.bg-slate-700,
    aside nav button.hover\:bg-slate-800:hover,
    aside nav button.hover\:bg-slate-700:hover {
        background-color: rgba(255, 255, 255, 0.14) !important;
    }
</style>
<?php /**PATH C:\laragon\www\sriwijaya_kidss\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>