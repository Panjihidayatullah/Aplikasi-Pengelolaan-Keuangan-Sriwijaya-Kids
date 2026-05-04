<?php

namespace App\Console\Commands;

use App\Models\GajiGuru;
use App\Models\GajiGuruDefault;
use App\Models\JenisPengeluaran;
use App\Models\Pengeluaran;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BayarGajiOtomatis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bayar-gaji-otomatis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Proses penggajian otomatis berdasarkan tanggal yang ditentukan di Gaji Default';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now();
        $tanggalHariIni = $today->day;
        $bulanSekarang = $today->month;
        $tahunSekarang = $today->year;

        $this->info("Menjalankan penggajian otomatis untuk tanggal: $tanggalHariIni-$bulanSekarang-$tahunSekarang");

        // Ambil gaji default yang aktif, auto_gaji true, dan tanggal_gaji hari ini
        $gajiDefaults = GajiGuruDefault::query()
            ->with('guru')
            ->where('is_active', true)
            ->where('auto_gaji', true)
            ->where('tanggal_gaji', $tanggalHariIni)
            ->get();

        if ($gajiDefaults->isEmpty()) {
            $this->info("Tidak ada jadwal penggajian otomatis untuk hari ini.");
            return;
        }

        // Cari user admin pertama sebagai penanggung jawab transaksi sistem
        $systemUser = User::role('Admin')->first() ?: User::first();
        if (!$systemUser) {
            $this->error("Gagal menjalankan: Tidak ada user ditemukan untuk mencatat transaksi.");
            return;
        }

        // Pastikan jenis pengeluaran Gaji Guru tersedia
        JenisPengeluaran::ensureKategoriInti();
        $jenisPengeluaran = JenisPengeluaran::query()
            ->where('is_active', true)
            ->whereRaw("LOWER(nama) LIKE '%gaji guru%' OR LOWER(nama) LIKE '%guru%'")
            ->orderBy('id')
            ->first();

        $bulanNames = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
        ];
        $bulanStr = $bulanNames[$bulanSekarang];

        $count = 0;
        foreach ($gajiDefaults as $default) {
            $guru = $default->guru;
            if (!$guru) continue;

            // Cek apakah sudah dibayar bulan ini
            $exists = GajiGuru::query()
                ->where('guru_id', $guru->id)
                ->where('periode_bulan', $bulanSekarang)
                ->where('periode_tahun', $tahunSekarang)
                ->exists();

            if ($exists) {
                $this->line("SKIPPED: Gaji {$guru->nama} untuk periode ini sudah ada.");
                continue;
            }

            try {
                DB::transaction(function () use ($default, $guru, $jenisPengeluaran, $systemUser, $bulanSekarang, $tahunSekarang, $bulanStr, $today) {
                    $kode = 'AUTO-GAJI-' . ($guru->nip ?: strtoupper(substr(str_replace(' ', '', $guru->nama), 0, 4)));
                    $kode .= '-' . $tahunSekarang . str_pad($bulanSekarang, 2, '0', STR_PAD_LEFT);

                    $pengeluaran = Pengeluaran::create([
                        'kode_transaksi'      => $kode,
                        'jenis_pengeluaran_id' => $jenisPengeluaran?->id,
                        'user_id'             => $systemUser->id,
                        'tanggal'             => $today->format('Y-m-d'),
                        'jumlah'              => $default->nominal,
                        'keterangan'          => "Gaji Otomatis: {$guru->nama} — {$bulanStr} {$tahunSekarang}",
                        'status'              => 'approved',
                    ]);

                    GajiGuru::create([
                        'pengeluaran_id'      => $pengeluaran->id,
                        'guru_id'             => $guru->id,
                        'periode_bulan'       => $bulanSekarang,
                        'periode_tahun'       => $tahunSekarang,
                        'dibayar_oleh_user_id' => $systemUser->id,
                    ]);
                });

                $count++;
                $this->info("SUCCESS: Gaji {$guru->nama} berhasil diproses.");
                Log::info("Auto Payroll: Gaji {$guru->nama} diproses otomatis.");
            } catch (\Exception $e) {
                $this->error("FAILED: Gaji {$guru->nama} gagal diproses. " . $e->getMessage());
                Log::error("Auto Payroll Error: " . $e->getMessage());
            }
        }

        $this->info("Selesai. $count gaji guru berhasil diproses secara otomatis.");
    }
}
