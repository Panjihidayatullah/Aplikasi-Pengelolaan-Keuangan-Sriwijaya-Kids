<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-6xl bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <!-- Left Side - Illustration -->
                <div class="lg:w-1/2 bg-gradient-to-br from-blue-600 via-cyan-500 to-blue-700 p-12 flex flex-col justify-between relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
                    </div>
                    
                    <!-- Logo/Brand -->
                    <div class="relative z-10">
                        <div class="inline-flex items-center space-x-3 mb-8">
                            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <h1 class="text-2xl font-bold text-white">{{ config('finance.school.name', config('app.name')) }}</h1>
                        </div>
                        
                        <h2 class="text-4xl font-bold text-white leading-tight mb-4">@yield('welcome-title', 'Selamat Datang')</h2>
                        <p class="text-blue-100 text-lg leading-relaxed">@yield('welcome-description', 'Sistem Manajemen Keuangan Sekolah yang Modern dan Efisien')</p>
                    </div>
                    
                    <!-- Illustration/Image Placeholder -->
                    <div class="relative z-10 hidden lg:block">
                        <div class="flex items-center justify-center space-x-4">
                            <!-- School Building Icon -->
                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 transform hover:scale-105 transition-transform duration-300">
                                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            
                            <!-- Students Icon -->
                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 transform hover:scale-105 transition-transform duration-300">
                                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            
                            <!-- Chart Icon -->
                            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 transform hover:scale-105 transition-transform duration-300">
                                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Features List -->
                    <div class="relative z-10 mt-8 space-y-3">
                        <div class="flex items-center space-x-3 text-white">
                            <svg class="w-5 h-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium">Manajemen Pembayaran SPP</span>
                        </div>
                        <div class="flex items-center space-x-3 text-white">
                            <svg class="w-5 h-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium">Laporan Keuangan Real-time</span>
                        </div>
                        <div class="flex items-center space-x-3 text-white">
                            <svg class="w-5 h-5 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium">Pencatatan Aset Sekolah</span>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side - Form -->
                <div class="lg:w-1/2 p-12 flex items-center">
                    <div class="w-full max-w-md mx-auto">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
