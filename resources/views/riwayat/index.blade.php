@extends('layouts.app')

@section('title', 'Riwayat Aktivitas - ' . config('app.name'))
@section('page-title', 'Riwayat Aktivitas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Riwayat Aktivitas</h2>
            <p class="mt-1 text-sm text-gray-600">Semua aktivitas dan kegiatan yang telah dilakukan di sistem</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" action="{{ route('riwayat.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Type Filter -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Aktivitas</label>
                    <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua Aktivitas</option>
                        <option value="pembayaran" {{ $type === 'pembayaran' ? 'selected' : '' }}>Pembayaran</option>
                        <option value="siswa" {{ $type === 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="pengeluaran" {{ $type === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                        <option value="kelas" {{ $type === 'kelas' ? 'selected' : '' }}>Kelas</option>
                        <option value="aset" {{ $type === 'aset' ? 'selected' : '' }}>Aset</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                    <input type="text" name="search" id="search" value="{{ $search }}" 
                           placeholder="Cari aktivitas..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
                @if($type !== 'all' || $dateFrom || $dateTo || $search)
                <a href="{{ route('riwayat.index') }}" class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Filter
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Activity List -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        @if($activities->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($activities as $activity)
            <a href="{{ $activity['url'] }}" class="block px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full {{ $activity['icon_bg'] }} flex items-center justify-center">
                            <svg class="w-6 h-6 {{ $activity['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $activity['icon'] }}"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <p class="text-sm font-semibold text-gray-900">{{ $activity['title'] }}</p>
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $activity['icon_bg'] }} {{ $activity['icon_color'] }}">
                                    {{ $activity['type_label'] }}
                                </span>
                            </div>
                            <span class="text-xs text-gray-500">{{ $activity['time']->format('d M Y H:i') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $activity['description'] }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-sm font-semibold text-blue-600">{{ $activity['amount'] }}</span>
                            <div class="flex items-center text-xs text-gray-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $activity['time']->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    <div class="ml-4">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($activities->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $activities->appends(request()->query())->links() }}
        </div>
        @endif
        @else
        <!-- Empty State -->
        <div class="px-6 py-16 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Tidak Ada Aktivitas</h3>
            <p class="text-sm text-gray-500">
                @if($type !== 'all' || $dateFrom || $dateTo || $search)
                    Tidak ada aktivitas yang sesuai dengan filter yang dipilih
                @else
                    Belum ada aktivitas yang tercatat di sistem
                @endif
            </p>
            @if($type !== 'all' || $dateFrom || $dateTo || $search)
            <a href="{{ route('riwayat.index') }}" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Lihat Semua Aktivitas
            </a>
            @endif
        </div>
        @endif
    </div>

    <!-- Statistics -->
    @if($activities->count() > 0)
    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Aktivitas Ditampilkan</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ $activities->total() }}</p>
            </div>
            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
