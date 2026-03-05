<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        // Get all siswa IDs and jenis pembayaran IDs
        $siswaIds = DB::table('siswa')->pluck('id')->toArray();
        $jenisPembayaran = DB::table('jenis_pembayaran')->get();
        $userIds = DB::table('users')->pluck('id')->toArray();
        
        if (empty($userIds)) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }
        
        $pembayaran = [];
        
        // Find SPP Bulanan
        $sppBulanan = $jenisPembayaran->where('nama', 'SPP Bulanan')->first();
        
        // Generate SPP payments for last 6 months
        if ($sppBulanan) {
            foreach ($siswaIds as $siswaId) {
                // Create 4-6 months of SPP payments
                $monthsBack = rand(4, 6);
                
                for ($i = 0; $i < $monthsBack; $i++) {
                    $tanggalBayar = $now->copy()->subMonths($i)->day(rand(1, 28));
                    
                    $pembayaran[] = [
                        'kode_transaksi' => 'SPP-' . $tanggalBayar->format('Ym') . '-' . str_pad($siswaId, 4, '0', STR_PAD_LEFT),
                        'siswa_id' => $siswaId,
                        'jenis_pembayaran_id' => $sppBulanan->id,
                        'user_id' => $userIds[array_rand($userIds)],
                        'tanggal_bayar' => $tanggalBayar,
                        'bulan' => (int) $tanggalBayar->format('m'),
                        'tahun' => (int) $tanggalBayar->format('Y'),
                        'jumlah' => $sppBulanan->nominal_default,
                        'metode_bayar' => ['Tunai', 'Transfer', 'QRIS'][rand(0, 2)],
                        'status' => 'Lunas',
                        'keterangan' => 'Pembayaran SPP bulan ' . $tanggalBayar->format('F Y'),
                        'created_at' => $tanggalBayar,
                        'updated_at' => $tanggalBayar,
                    ];
                }
            }
        }
        
        // Generate other payments (randomly)
        $otherJenis = $jenisPembayaran->where('nama', '!=', 'SPP Bulanan');
        
        foreach ($siswaIds as $siswaId) {
            // Each siswa has 1-3 other payments
            $otherPaymentsCount = rand(1, 3);
            
            foreach ($otherJenis->random(min($otherPaymentsCount, $otherJenis->count())) as $jenis) {
                $tanggalBayar = $now->copy()->subDays(rand(1, 180));
                
                $pembayaran[] = [
                    'kode_transaksi' => strtoupper(substr($jenis->nama, 0, 3)) . '-' . $tanggalBayar->format('Ymd') . '-' . rand(1000, 9999),
                    'siswa_id' => $siswaId,
                    'jenis_pembayaran_id' => $jenis->id,
                    'user_id' => $userIds[array_rand($userIds)],
                    'tanggal_bayar' => $tanggalBayar,
                    'bulan' => null,
                    'tahun' => null,
                    'jumlah' => $jenis->nominal_default,
                    'metode_bayar' => ['Tunai', 'Transfer', 'QRIS'][rand(0, 2)],
                    'status' => 'Lunas',
                    'keterangan' => 'Pembayaran ' . $jenis->nama,
                    'created_at' => $tanggalBayar,
                    'updated_at' => $tanggalBayar,
                ];
            }
        }
        
        // Insert in batches
        foreach (array_chunk($pembayaran, 100) as $batch) {
            DB::table('pembayaran')->insert($batch);
        }
        
        $this->command->info('Created ' . count($pembayaran) . ' pembayaran records');
    }
}
