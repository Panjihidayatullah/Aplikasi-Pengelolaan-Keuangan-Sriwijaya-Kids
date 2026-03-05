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
                    'Listrik', 'Air', 'Internet' => 1, // Monthly bills
                    'ATK', 'Pemeliharaan', 'Transport', 'Konsumsi' => rand(2, 5), // Multiple times
                    default => rand(1, 3),
                };
                
                for ($i = 0; $i < $count; $i++) {
                    $tanggal = $monthDate->copy()->day(rand(1, 28));
                    
                    // Determine amount based on type
                    $jumlah = match($jenis->nama) {
                        'Gaji Guru' => rand(80000000, 120000000), // 80-120 juta
                        'Listrik' => rand(3000000, 5000000), // 3-5 juta
                        'Air' => rand(500000, 1000000), // 500k-1jt
                        'Internet' => rand(1000000, 2000000), // 1-2 juta
                        'ATK' => rand(200000, 1000000), // 200k-1jt
                        'Pemeliharaan' => rand(1000000, 5000000), // 1-5 juta
                        'Transport' => rand(300000, 1500000), // 300k-1.5jt
                        'Konsumsi' => rand(500000, 2000000), // 500k-2jt
                        'Honor' => rand(1000000, 3000000), // 1-3 juta
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
            'Gaji Guru' => 'Pembayaran gaji guru dan staff bulan ' . $tanggal->format('F Y'),
            'Listrik' => 'Pembayaran tagihan listrik bulan ' . $tanggal->format('F Y'),
            'Air' => 'Pembayaran PDAM bulan ' . $tanggal->format('F Y'),
            'Internet' => 'Pembayaran internet dan telepon bulan ' . $tanggal->format('F Y'),
            'ATK' => 'Pembelian alat tulis kantor',
            'Pemeliharaan' => 'Biaya pemeliharaan dan perbaikan fasilitas',
            'Transport' => 'Biaya transportasi dan BBM',
            'Konsumsi' => 'Biaya konsumsi rapat dan acara',
            'Honor' => 'Honor narasumber',
            default => 'Pengeluaran ' . $namaJenis,
        };
    }
}
