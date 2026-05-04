<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('finance.school.name', 'Sriwijaya Kids') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gray-100 flex items-center justify-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">
                        {{ config('finance.school.name', 'Sistem Keuangan Sekolah') }}
                    </h1>
                    <p class="text-lg text-gray-600 mb-8">
                        Sistem Manajemen Keuangan dan Administrasi Sekolah
                    </p>
                    
                    <div class="flex items-center justify-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700">
                                Login
                            </a>
                        @endauth
                    </div>

                    <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-indigo-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Manajemen Siswa</h3>
                            <p class="mt-2 text-sm text-gray-600">Kelola data siswa dengan mudah</p>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-green-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Pembayaran</h3>
                            <p class="mt-2 text-sm text-gray-600">Catat dan monitor pembayaran siswa</p>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-red-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Laporan</h3>
                            <p class="mt-2 text-sm text-gray-600">Generate laporan keuangan lengkap</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} {{ config('finance.school.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>