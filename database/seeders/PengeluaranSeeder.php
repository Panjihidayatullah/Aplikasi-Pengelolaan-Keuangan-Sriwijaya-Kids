<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        // Get jenis pengeluaran and users
        $jenisPengeluaran = DB::table('jenis_pengeluaran')->get();
        $userIds = DB::table('users')->pluck('id')->toArray();
        
        if (empty($userIds)) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }
        
        $pengeluaran = [];
        
        // Generate expenses for last 6 months
        for ($month = 0; $month < 6; $month++) {
            $monthDate = $now->copy()->subMonths($month);
            
            foreach ($jenisPengeluaran as $jenis) {
                // Number of expenses per jenis per month
                $count = match($jenis->nama) {
                    'Gaji Guru' => 1, // Once per month
                    'Gaji Pegawai' => 1, // Once per month
                    'Aset' => rand(1, 3),
                    'Operasional' => rand(4, 8),
                    default => rand(1, 3),
                };
                
                for ($i = 0; $i < $count; $i++) {
                    $tanggal = $monthDate->copy()->day(rand(1, 28));
                    
                    // Determine amount based on type
                    $jumlah = match($jenis->nama) {
                        'Gaji Guru' => rand(3000000, 9000000),
                        'Gaji Pegawai' => rand(80000000, 140000000),
                        'Aset' => rand(2000000, 25000000),
                        'Operasional' => rand(300000, 6000000),
                        default => rand(500000, 2000000),
                    };
                    
                    $pengeluaran[] = [
                        'kode_transaksi' => strtoupper(substr($jenis->nama, 0, 3)) . '-' . $tanggal->format('Ymd') . '-' . rand(1000, 9999),
                        'jenis_pengeluaran_id' => $jenis->id,
                        'user_id' => $userIds[array_rand($userIds)],
                        'tanggal' => $tanggal,
                        'jumlah' => $jumlah,
                        'keterangan' => $this->generateKeterangan($jenis->nama, $tanggal),
                        'bukti_file' => null,
                        'status' => 'Disetujui',
                        'created_at' => $tanggal,
                        'updated_at' => $tanggal,
                    ];
                }
            }
        }
        
        // Insert in batches
        foreach (array_chunk($pengeluaran, 100) as $batch) {
            DB::table('pengeluaran')->insert($batch);
        }
        
        $this->command->info('Created ' . count($pengeluaran) . ' pengeluaran records');
    }
    
    private function generateKeterangan($namaJenis, $tanggal): string
    {
        return match($namaJenis) {
            'Gaji Guru' => 'Pembayaran gaji guru bulan ' . $tanggal->format('F Y'),
            'Gaji Pegawai' => 'Pembayaran gaji pegawai non-guru bulan ' . $tanggal->format('F Y'),
            'Aset' => collect([
                'Pembelian meja kelas baru',
                'Pembelian kursi siswa',
                'Pembelian papan tulis',
                'Pembelian lemari arsip',
                'Pembelian proyektor kelas',
            ])->random(),
            'Operasional' => collect([
                'Pembayaran tagihan listrik bulan ' . $tanggal->format('F Y'),
                'Pembayaran tagihan air bulan ' . $tanggal->format('F Y'),
                'Pembayaran internet bulan ' . $tanggal->format('F Y'),
                'Pembelian ATK operasional',
                'Biaya kebersihan dan perlengkapan habis pakai',
                'Biaya transport operasional sekolah',
            ])->random(),
            default => 'Pengeluaran ' . $namaJenis,
        };
    }
}
