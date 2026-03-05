<?php

use App\Http\Controllers\AsetController;
use App\Http\Controllers\Auth\DirectPasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Direct Password Reset (without email)
Route::post('/password/reset-direct', [DirectPasswordResetController::class, 'reset'])
    ->middleware('guest')
    ->name('password.reset.direct');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    
    // Riwayat Aktivitas
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    
    // Master Data - Siswa
    Route::resource('siswa', SiswaController::class);
    
    // Master Data - Kelas
    Route::resource('kelas', KelasController::class);
    
    // Keuangan - Pembayaran
    Route::resource('pembayaran', PembayaranController::class);
    
    // Keuangan - Pengeluaran
    Route::resource('pengeluaran', PengeluaranController::class);
    
    // Keuangan - Aset Sekolah
    Route::resource('aset', AsetController::class);
    
    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/cashflow', [LaporanController::class, 'cashflow'])->name('cashflow');
        Route::get('/pemasukan', [LaporanController::class, 'pemasukan'])->name('pemasukan');
        Route::get('/pengeluaran', [LaporanController::class, 'pengeluaran'])->name('pengeluaran');
        Route::get('/export/{type}/pdf', [LaporanController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/{type}/excel', [LaporanController::class, 'exportExcel'])->name('export.excel');
    });
    
    // Pengaturan - Manajemen User
    Route::resource('users', UserController::class);
    
    // Pengaturan - Role & Permission
    Route::resource('roles', RoleController::class);
});

require __DIR__.'/settings.php';
