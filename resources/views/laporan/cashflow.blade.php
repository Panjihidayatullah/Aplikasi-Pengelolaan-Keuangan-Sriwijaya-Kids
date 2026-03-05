@extends('layouts.app')

@section('title', 'Laporan Cashflow - ' . config('app.name'))
@section('page-title', 'Laporan Cashflow')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Laporan Cashflow</h2>
            <p class="mt-1 text-sm text-gray-600">Arus kas pemasukan dan pengeluaran</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('laporan.export.pdf', ['type' => 'cashflow', 'start_date' => request('start_date', now()->startOfMonth()->format('Y-m-d')), 'end_date' => request('end_date', now()->endOfMonth()->format('Y-m-d'))]) }}" 
               target="_blank"
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Export PDF
            </a>
            <a href="{{ route('laporan.export.excel', ['type' => 'cashflow', 'start_date' => request('start_date', now()->startOfMonth()->format('Y-m-d')), 'end_date' => request('end_date', now()->endOfMonth()->format('Y-m-d'))]) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Excel
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border p-4">
        <form method="GET" action="{{ route('laporan.cashflow') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" 
                       name="start_date" 
                       value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" 
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <input type="date" 
                       name="end_date" 
                       value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" 
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>
            </div>
            <div class="flex items-end">
                <a href="{{ route('laporan.cashflow') }}" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition-colors text-center inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <p class="text-sm text-gray-600">Total Pemasukan</p>
            <p class="text-2xl font-bold text-green-600">{{ format_rupiah($pemasukan) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <p class="text-sm text-gray-600">Total Pengeluaran</p>
            <p class="text-2xl font-bold text-red-600">{{ format_rupiah($pengeluaran) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <p class="text-sm text-gray-600">Saldo Akhir</p>
            <p class="text-2xl font-bold text-blue-600">{{ format_rupiah($saldo) }}</p>
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Cashflow Overview</h3>
            <p class="text-sm text-gray-500">
                Pemasukan vs Pengeluaran 
                @if($groupBy === 'day')
                    (Per Hari)
                @else
                    (Per Bulan)
                @endif
            </p>
        </div>
        
        <div class="h-80">
            <canvas id="cashflowChart"></canvas>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Detail Transaksi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemasukan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pengeluaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saldo</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $trans)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($trans['tanggal'])->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $trans['keterangan'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $trans['pemasukan'] > 0 ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
                            {{ $trans['pemasukan'] > 0 ? format_rupiah($trans['pemasukan']) : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $trans['pengeluaran'] > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                            {{ $trans['pengeluaran'] > 0 ? format_rupiah($trans['pengeluaran']) : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-semibold">
                            {{ format_rupiah($trans['saldo']) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data untuk periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('cashflowChart');
    
    if (!ctx) {
        console.error('Canvas element not found');
        return;
    }
    
    // Prepare data from Laravel
    const pemasukanData = @json($pemasukanData);
    const pengeluaranData = @json($pengeluaranData);
    const groupBy = @json($groupBy);
    
    console.log('Pemasukan Data:', pemasukanData);
    console.log('Pengeluaran Data:', pengeluaranData);
    console.log('Group By:', groupBy);
    
    // Get all unique periods
    const allPeriods = new Set();
    pemasukanData.forEach(item => allPeriods.add(item.periode));
    pengeluaranData.forEach(item => allPeriods.add(item.periode));
    
    // Sort periods
    const sortedPeriods = Array.from(allPeriods).sort();
    
    console.log('Sorted Periods:', sortedPeriods);
    
    // Format labels based on groupBy
    const labels = sortedPeriods.map(periode => {
        const date = new Date(periode);
        if (groupBy === 'day') {
            // Format: DD MMM
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
        } else {
            // Format: MMM YYYY
            return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
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
    
    console.log('Labels:', labels);
    console.log('Pemasukan Values:', pemasukanValues);
    console.log('Pengeluaran Values:', pengeluaranValues);
    
    // Create chart
    try {
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
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Pengeluaran',
                    data: pengeluaranValues,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
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
                        padding: 20,
                        font: {
                            size: 13,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
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
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                        },
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
    console.log('Chart created successfully');
    } catch (error) {
        console.error('Error creating chart:', error);
    }
});
</script>
@endpush
