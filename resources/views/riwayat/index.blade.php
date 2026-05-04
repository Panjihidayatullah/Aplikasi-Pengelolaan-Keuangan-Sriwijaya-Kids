@extends('layouts.app')

@section('title', 'Riwayat Aktivitas - ' . config('app.name'))
@section('page-title', 'Riwayat Aktivitas')

@push('styles')
<style>
    .riwayat-pagination nav a.inline-flex,
    .riwayat-pagination nav span.inline-flex,
    .riwayat-pagination nav [aria-current="page"] span.inline-flex,
    .riwayat-pagination nav [aria-disabled="true"] span.inline-flex {
        background: #ffffff !important;
        background-color: #ffffff !important;
        border-color: #bfdbfe !important;
        color: #2563eb !important;
        box-shadow: none !important;
    }

    .riwayat-pagination nav a.inline-flex:hover,
    .riwayat-pagination nav a.inline-flex:focus,
    .riwayat-pagination nav a.inline-flex:active,
    .riwayat-pagination nav span.inline-flex:hover,
    .riwayat-pagination nav span.inline-flex:focus,
    .riwayat-pagination nav span.inline-flex:active {
        background: #ffffff !important;
        background-color: #ffffff !important;
        color: #2563eb !important;
        border-color: #bfdbfe !important;
    }

    .riwayat-pagination nav [aria-current="page"] span.inline-flex {
        box-shadow: inset 0 0 0 1px #93c5fd;
        font-weight: 700;
    }

    .riwayat-pagination nav [aria-disabled="true"] span.inline-flex {
        color: #94a3b8 !important;
        border-color: #e2e8f0 !important;
    }

    .riwayat-pagination button,
    .riwayat-pagination button:hover,
    .riwayat-pagination button:focus,
    .riwayat-pagination button:active {
        background: #ffffff !important;
        background-color: #ffffff !important;
        color: #2563eb !important;
        border-color: #bfdbfe !important;
        box-shadow: none !important;
    }

    .riwayat-pagination button[aria-current="page"] {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
    }

    .riwayat-pagination button:disabled {
        background: #f8fafc !important;
        color: #94a3b8 !important;
        border-color: #e2e8f0 !important;
        opacity: 1 !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Riwayat Aktivitas</h2>
            <p class="mt-1 text-sm text-gray-600">Riwayat aktivitas yang berasal dari notifikasi akun Anda</p>
        </div>
        @if(($unreadCount ?? 0) > 0)
        <form method="POST" action="{{ route('akademik.notifikasi.mark-all-read') }}">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-semibold">
                Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" action="{{ route('riwayat.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Type Filter -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Aktivitas</label>
                    <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @foreach(($allowedTypes ?? ['all' => 'Semua Aktivitas']) as $typeValue => $typeLabel)
                        <option value="{{ $typeValue }}" {{ $type === $typeValue ? 'selected' : '' }}>{{ $typeLabel }}</option>
                        @endforeach
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
                <a href="{{ route('riwayat.index') }}" class="inline-flex items-center px-6 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
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
            @php
                $canOpen = !empty($activity['url']);
                $isRead = (bool) ($activity['is_read'] ?? false);
            @endphp
            <div class="block px-6 py-4 {{ $isRead ? 'bg-white' : 'bg-blue-50/30' }}">
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
                    <div class="ml-4 flex-shrink-0 flex flex-col items-end gap-2">
                        @if(!$isRead && !empty($activity['id']))
                        <form method="POST" action="{{ route('akademik.notifikasi.read', $activity['id']) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex appearance-none items-center px-2.5 py-1 bg-white border border-blue-200 rounded-md text-xs font-semibold text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors"
                                    style="background: #ffffff !important; background-color: #ffffff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important;">
                                Tandai Dibaca
                            </button>
                        </form>
                        @else
                        <span class="text-xs text-gray-500">Sudah Dibaca</span>
                        @endif

                        @if($canOpen)
                        <a href="{{ $activity['url'] }}" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800">
                            Buka
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($activities->hasPages())
        <div class="riwayat-pagination px-6 py-4 border-t border-gray-200">
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
                    Belum ada notifikasi aktivitas untuk akun Anda
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

</div>
@endsection
