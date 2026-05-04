<?php

use App\Http\Controllers\AsetController;
use App\Http\Controllers\Auth\DirectPasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GajiGuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\KenaikanKelasController;
use App\Http\Controllers\TranskripsNilaiController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\RaportController;

use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\RuangController;
use App\Http\Controllers\LmsController;
use App\Http\Controllers\LmsMateriController;
use App\Http\Controllers\LmsTugasController;
use App\Http\Controllers\LmsPengumpulanController;
use App\Http\Controllers\LmsMonitoringController;
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
    
    // Master Data - Siswa (Only Admin for full management, others view through context)
    Route::resource('siswa', SiswaController::class)->middleware('role:Admin');
    
    // Master Data - Guru (Only Admin)
    Route::resource('guru', GuruController::class)->middleware('role:Admin');
    
    // Master Data - Kelas (Only Admin)
    Route::middleware('role:Admin')->group(function () {
        Route::resource('kelas', KelasController::class);
        Route::post('/kelas/{kelas}/siswa', [KelasController::class, 'storeSiswa'])->name('kelas.siswa.store');
        Route::put('/kelas/{kelas}/siswa/{siswa}', [KelasController::class, 'updateSiswa'])->name('kelas.siswa.update');
        Route::post('/kelas/{kelas}/siswa/{siswa}/transfer', [KelasController::class, 'transferSiswa'])->name('kelas.siswa.transfer');
        Route::delete('/kelas/{kelas}/siswa/{siswa}', [KelasController::class, 'destroySiswa'])->name('kelas.siswa.destroy');
    });
    
    // Keuangan - Pembayaran
    Route::get('/pembayaran/{pembayaran}/export/pdf', [PembayaranController::class, 'exportPdf'])->name('pembayaran.export.pdf');
    Route::resource('pembayaran', PembayaranController::class);
    
    // Keuangan - Pengeluaran
    Route::get('/pengeluaran/{pengeluaran}/export/pdf', [PengeluaranController::class, 'exportPdf'])->name('pengeluaran.export.pdf');
    Route::resource('pengeluaran', PengeluaranController::class);

    // Keuangan - Gaji Guru
    Route::get('/gaji-guru',                               [GajiGuruController::class, 'index'])->name('gaji-guru.index');
    Route::post('/gaji-guru',                              [GajiGuruController::class, 'store'])->name('gaji-guru.store');
    Route::delete('/gaji-guru/{gajiGuru}',                 [GajiGuruController::class, 'destroy'])->name('gaji-guru.destroy');
    Route::get('/gaji-guru/{gajiGuru}',                    [GajiGuruController::class, 'show'])->name('gaji-guru.show');
    Route::get('/gaji-guru/{gajiGuru}/export/pdf',         [GajiGuruController::class, 'exportPdf'])->name('gaji-guru.export.pdf');

    // Gaji Default per Guru (CRUD)
    Route::get('/gaji-guru-default',                       [GajiGuruController::class, 'defaultIndex'])->name('gaji-guru.default.index');
    Route::post('/gaji-guru-default',                      [GajiGuruController::class, 'defaultStore'])->name('gaji-guru.default.store');
    Route::get('/gaji-guru-default/{default}/edit',        [GajiGuruController::class, 'defaultEdit'])->name('gaji-guru.default.edit');
    Route::put('/gaji-guru-default/{default}',             [GajiGuruController::class, 'defaultUpdate'])->name('gaji-guru.default.update');
    Route::delete('/gaji-guru-default/{default}',          [GajiGuruController::class, 'defaultDestroy'])->name('gaji-guru.default.destroy');
    Route::get('/api/gaji-default',                        [GajiGuruController::class, 'getGajiDefault'])->name('gaji-guru.api.default');

    // Keuangan - Gaji Saya (Guru)
    Route::get('/gaji-saya', [GajiGuruController::class, 'myIndex'])->name('gaji-saya.index');
    Route::get('/gaji-saya/{gajiGuru}', [GajiGuruController::class, 'myShow'])->name('gaji-saya.show');
    Route::get('/gaji-saya/{gajiGuru}/export/pdf', [GajiGuruController::class, 'myExportPdf'])->name('gaji-saya.export.pdf');
    
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
    Route::resource('users', UserController::class)->middleware('role:Admin');
    
    // Pengaturan - Role & Permission
    Route::resource('roles', RoleController::class)->middleware('role:Admin');

    // ========== MODUL AKADEMIK (Excluded for Bendahara) ==========
    Route::prefix('akademik')->name('akademik.')->middleware('role:Admin|Guru|Kepala Sekolah|Siswa')->group(function () {
        
        // Kurikulum
        Route::resource('kurikulum', KurikulumController::class);
        
        // Tahun Ajaran
        Route::resource('tahun-ajaran', TahunAjaranController::class);
        Route::post('/tahun-ajaran/{tahunAjaran}/set-active', [TahunAjaranController::class, 'setActive'])->name('tahun-ajaran.set-active');
        Route::post('/tahun-ajaran/{tahunAjaran}/set-inactive', [TahunAjaranController::class, 'setInactive'])->name('tahun-ajaran.set-inactive');

        // Semester
        Route::resource('semester', SemesterController::class);
        
        // Pengumuman
        Route::resource('pengumuman', PengumumanController::class);
        
        // Ujian
        Route::resource('ujian', UjianController::class);

        // Jadwal Pelajaran
        Route::get('/jadwal-pelajaran/export/pdf', [JadwalPelajaranController::class, 'exportPdf'])->name('jadwal-pelajaran.export.pdf');
        Route::resource('jadwal-pelajaran', JadwalPelajaranController::class)
            ->parameters(['jadwal-pelajaran' => 'jadwalPelajaran'])
            ->except('show');

        // Manajemen Ruang
        Route::resource('ruang', RuangController::class)
            ->parameters(['ruang' => 'ruang'])
            ->except('show');
        
        // Kenaikan Kelas
        Route::get('/kenaikan-kelas/proses-rombel', [KenaikanKelasController::class, 'prosesRombel'])->name('kenaikan-kelas.proses-rombel');
        Route::post('/kenaikan-kelas/proses-rombel', [KenaikanKelasController::class, 'simpanProsesRombel'])->name('kenaikan-kelas.proses-rombel.store');
        Route::post('/kenaikan-kelas/bulk-approve', [KenaikanKelasController::class, 'bulkApprove'])->name('kenaikan-kelas.bulk-approve');
        Route::resource('kenaikan-kelas', KenaikanKelasController::class)
            ->parameters(['kenaikan-kelas' => 'kenaikanKelas']);
        Route::post('/kenaikan-kelas/{kenaikanKelas}/approve', [KenaikanKelasController::class, 'approve'])->name('kenaikan-kelas.approve');
        
        // Transkrips Nilai
        Route::get('/transkrip-nilai/saya', [TranskripsNilaiController::class, 'siswaIndex'])->name('transkrip-nilai.saya');
        Route::get('/transkrip-nilai/saya/download', [TranskripsNilaiController::class, 'siswaDownloadPdf'])->name('transkrip-nilai.saya.download');
        Route::get('/transkrip-nilai/pengaturan', [TranskripsNilaiController::class, 'pengaturan'])->name('transkrip-nilai.pengaturan');
        Route::post('/transkrip-nilai/pengaturan', [TranskripsNilaiController::class, 'updatePengaturan'])->name('transkrip-nilai.pengaturan.update');
        Route::get('/transkrip-nilai/kelas/{kelas}/mapel/{mataPelajaran}', [TranskripsNilaiController::class, 'byMapel'])->name('transkrip-nilai.kelas-mapel');
        Route::get('/transkrip-nilai/kelas/{kelas}/mapel/{mataPelajaran}/export', [TranskripsNilaiController::class, 'exportByMapel'])->name('transkrip-nilai.kelas-mapel.export');
        Route::get('/transkrip-nilai/kelas/{kelas}/mapel/{mataPelajaran}/excel/export', [TranskripsNilaiController::class, 'exportExcelByMapel'])->name('transkrip-nilai.kelas-mapel.export-excel');
        Route::post('/transkrip-nilai/kelas/{kelas}/mapel/{mataPelajaran}/excel/import', [TranskripsNilaiController::class, 'importExcelByMapel'])->name('transkrip-nilai.kelas-mapel.import-excel');
        Route::get('/transkrip-nilai/kelas/{kelas}/mapel/{mataPelajaran}/excel/template', [TranskripsNilaiController::class, 'downloadTemplate'])->name('transkrip-nilai.kelas-mapel.template');
        Route::post('/transkrip-nilai/kelas/{kelas}/mapel/{mataPelajaran}/nilai', [TranskripsNilaiController::class, 'saveNilaiMapel'])->name('transkrip-nilai.save-nilai');
        Route::get('/transkrip-nilai/{transkripNilai}/print', [TranskripsNilaiController::class, 'print'])->name('transkrip-nilai.print');
        Route::resource('transkrip-nilai', TranskripsNilaiController::class);

        // Raport Siswa
        Route::get('/raport', [RaportController::class, 'index'])->name('raport.index');
        Route::get('/raport/{siswa}/pdf', [RaportController::class, 'pdf'])->name('raport.pdf');


        // Absensi Harian
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::get('/absensi/{absensi}', [AbsensiController::class, 'show'])->name('absensi.show');
        Route::get('/absensi/{absensi}/edit', [AbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('/absensi/{absensi}', [AbsensiController::class, 'update'])->name('absensi.update');
        Route::delete('/absensi/{absensi}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
        Route::get('/absensi/saya/export/pdf', [AbsensiController::class, 'exportSiswaPdf'])->name('absensi.saya.export.pdf');
        Route::get('/absensi/export/pdf', [AbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');
        
        // Notifikasi
        Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
            Route::get('/', [NotifikasiController::class, 'index'])->name('index');
            Route::get('/unread', [NotifikasiController::class, 'getUnread'])->name('unread');
            Route::post('/{notifikasi}/read', [NotifikasiController::class, 'markAsRead'])->name('read');
            Route::post('/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{notifikasi}', [NotifikasiController::class, 'destroy'])->name('destroy');
        });

        // LMS
        Route::prefix('lms')->name('lms.')->group(function () {
            Route::get('/', [LmsController::class, 'index'])->name('index');
            Route::get('/pertemuan/{tanggal}', [LmsController::class, 'pertemuan'])->name('pertemuan');
            Route::post('/pertemuan', [LmsController::class, 'storePertemuan'])->name('pertemuan.store');
            Route::put('/pertemuan/{lmsPertemuan}', [LmsController::class, 'updatePertemuan'])->name('pertemuan.update');
            Route::delete('/pertemuan/{lmsPertemuan}', [LmsController::class, 'deletePertemuan'])->name('pertemuan.delete');

            Route::get('/materi', [LmsMateriController::class, 'index'])->name('materi.index');
            Route::get('/materi/create', [LmsMateriController::class, 'create'])->name('materi.create');
            Route::post('/materi', [LmsMateriController::class, 'store'])->name('materi.store');
            Route::get('/materi/{materi}/file', [LmsMateriController::class, 'viewFile'])->name('materi.file');
            Route::get('/materi/{materi}/download', [LmsMateriController::class, 'downloadFile'])->name('materi.download');
            Route::get('/materi/{materi}', [LmsMateriController::class, 'show'])->name('materi.show');
            Route::get('/materi/{materi}/edit', [LmsMateriController::class, 'edit'])->name('materi.edit');
            Route::put('/materi/{materi}', [LmsMateriController::class, 'update'])->name('materi.update');
            Route::delete('/materi/{materi}', [LmsMateriController::class, 'destroy'])->name('materi.destroy');

            Route::get('/tugas', [LmsTugasController::class, 'index'])->name('tugas.index');
            Route::get('/tugas/create', [LmsTugasController::class, 'create'])->name('tugas.create');
            Route::post('/tugas', [LmsTugasController::class, 'store'])->name('tugas.store');
            Route::get('/tugas/{tugas}', [LmsTugasController::class, 'show'])->name('tugas.show');
            Route::get('/tugas/{tugas}/edit', [LmsTugasController::class, 'edit'])->name('tugas.edit');
            Route::put('/tugas/{tugas}', [LmsTugasController::class, 'update'])->name('tugas.update');
            Route::delete('/tugas/{tugas}', [LmsTugasController::class, 'destroy'])->name('tugas.destroy');

            Route::post('/tugas/{tugas}/pengumpulan', [LmsPengumpulanController::class, 'store'])->name('pengumpulan.store');
            Route::put('/tugas/{tugas}/pengumpulan/{pengumpulan}', [LmsPengumpulanController::class, 'update'])->name('pengumpulan.update');
            Route::delete('/tugas/{tugas}/pengumpulan/{pengumpulan}', [LmsPengumpulanController::class, 'destroy'])->name('pengumpulan.destroy');
            Route::post('/pengumpulan/{pengumpulan}/grade', [LmsPengumpulanController::class, 'grade'])->name('pengumpulan.grade');
            Route::delete('/pengumpulan/{pengumpulan}/grade', [LmsPengumpulanController::class, 'ungrade'])->name('pengumpulan.ungrade');

            Route::get('/monitoring', [LmsMonitoringController::class, 'index'])->name('monitoring.index');
            Route::get('/monitoring/siswa/{siswa}', [LmsMonitoringController::class, 'show'])->name('monitoring.show');
            Route::get('/monitoring/siswa/{siswa}/pdf', [LmsMonitoringController::class, 'exportPdf'])->name('monitoring.pdf');
        });
    });
    
    // Public announcements (no auth required for viewing basic info)
    Route::get('/pengumuman', [PengumumanController::class, 'public'])->name('pengumuman.public');
});

require __DIR__.'/settings.php';
