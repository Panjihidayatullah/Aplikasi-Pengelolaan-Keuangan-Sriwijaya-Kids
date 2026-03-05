<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JenisPembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('jenis_pembayaran')->insert([
            [
                'nama' => 'SPP Bulanan',
                'keterangan' => 'Sumbangan Pembinaan Pendidikan per bulan',
                'nominal_default' => 500000,
                'tipe' => 'Bulanan',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Uang Gedung',
                'keterangan' => 'Uang gedung untuk siswa baru',
                'nominal_default' => 5000000,
                'tipe' => 'Sekali',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Uang Buku',
                'keterangan' => 'Uang pembelian buku pelajaran',
                'nominal_default' => 750000,
                'tipe' => 'Tahunan',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Uang Seragam',
                'keterangan' => 'Uang pembelian seragam sekolah',
                'nominal_default' => 800000,
                'tipe' => 'Tahunan',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Uang Praktikum',
                'keterangan' => 'Biaya praktikum laboratorium',
                'nominal_default' => 300000,
                'tipe' => 'Bulanan',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Uang Ujian',
                'keterangan' => 'Biaya ujian semester',
                'nominal_default' => 200000,
                'tipe' => 'Tahunan',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Uang Kegiatan',
                'keterangan' => 'Biaya kegiatan ekstrakurikuler',
                'nominal_default' => 150000,
                'tipe' => 'Bulanan',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Uang Daftar Ulang',
                'keterangan' => 'Biaya daftar ulang tahun ajaran baru',
                'nominal_default' => 1000000,
                'tipe' => 'Tahunan',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
