<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JenisPengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('jenis_pengeluaran')->insert([
            [
                'nama' => 'Gaji Guru',
                'keterangan' => 'Pembayaran gaji guru dan staff',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Listrik',
                'keterangan' => 'Biaya listrik bulanan',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Air',
                'keterangan' => 'Biaya air PDAM',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'ATK',
                'keterangan' => 'Alat Tulis Kantor',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Pemeliharaan',
                'keterangan' => 'Biaya pemeliharaan gedung dan fasilitas',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Transport',
                'keterangan' => 'Biaya transportasi dan BBM',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Konsumsi',
                'keterangan' => 'Biaya konsumsi rapat dan acara',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Honor',
                'keterangan' => 'Honor narasumber dan tamu',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Internet',
                'keterangan' => 'Biaya internet dan telepon',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Lain-lain',
                'keterangan' => 'Pengeluaran lain-lain',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
