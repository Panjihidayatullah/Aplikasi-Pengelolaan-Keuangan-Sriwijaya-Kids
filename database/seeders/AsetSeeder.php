<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userIds = DB::table('users')->pluck('id')->toArray();
        
        if (empty($userIds)) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }
        
        $defaultUserId = $userIds[0];
        
        $aset = [
            // Elektronik
            [
                'nama' => 'Proyektor LCD Epson EB-X41',
                'kategori' => 'Elektronik',
                'tanggal_perolehan' => '2023-07-15',
                'harga_perolehan' => 8500000,
                'kondisi' => 'Baik',
                'lokasi' => 'Ruang Kelas 7A',
                'keterangan' => 'Proyektor untuk pembelajaran',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Laptop Lenovo ThinkPad',
                'kategori' => 'Elektronik',
                'tanggal_perolehan' => '2023-08-20',
                'harga_perolehan' => 12000000,
                'kondisi' => 'Baik',
                'lokasi' => 'Ruang Guru',
                'keterangan' => 'Laptop untuk administrasi',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Printer HP LaserJet Pro',
                'kategori' => 'Elektronik',
                'tanggal_perolehan' => '2023-06-10',
                'harga_perolehan' => 4500000,
                'kondisi' => 'Baik',
                'lokasi' => 'Ruang TU',
                'keterangan' => 'Printer untuk cetak dokumen',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'AC Split 2 PK Daikin',
                'kategori' => 'Elektronik',
                'tanggal_perolehan' => '2022-05-15',
                'harga_perolehan' => 7500000,
                'kondisi' => 'Baik',
                'lokasi' => 'Ruang Kepala Sekolah',
                'keterangan' => 'AC untuk ruang kepala sekolah',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Komputer Desktop HP',
                'kategori' => 'Elektronik',
                'tanggal_perolehan' => '2023-09-01',
                'harga_perolehan' => 9000000,
                'kondisi' => 'Baik',
                'lokasi' => 'Lab Komputer',
                'keterangan' => 'Komputer untuk lab (10 unit)',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Furniture
            [
                'nama' => 'Meja Guru Kayu Jati',
                'kategori' => 'Furniture',
                'tanggal_perolehan' => '2022-07-20',
                'harga_perolehan' => 3500000,
                'kondisi' => 'Baik',
                'lokasi' => 'Ruang Guru',
                'keterangan' => 'Meja untuk guru (15 unit)',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Kursi Siswa',
                'kategori' => 'Furniture',
                'tanggal_perolehan' => '2023-06-15',
                'harga_perolehan' => 12000000,
                'kondisi' => 'Baik',
                'lokasi' => 'Berbagai Ruang Kelas',
                'keterangan' => 'Kursi untuk siswa (200 unit)',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Lemari Arsip Besi',
                'kategori' => 'Furniture',
                'tanggal_perolehan' => '2022-08-10',
                'harga_perolehan' => 5000000,
                'kondisi' => 'Baik',
                'lokasi' => 'Ruang TU',
                'keterangan' => 'Lemari untuk arsip dokumen (5 unit)',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Papan Tulis Whiteboard',
                'kategori' => 'Furniture',
                'tanggal_perolehan' => '2023-07-01',
                'harga_perolehan' => 4500000,
                'kondisi' => 'Baik',
                'lokasi' => 'Berbagai Ruang Kelas',
                'keterangan' => 'Whiteboard untuk kelas (15 unit)',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Rak Buku Perpustakaan',
                'kategori' => 'Furniture',
                'tanggal_perolehan' => '2022-09-15',
                'harga_perolehan' => 8000000,
                'kondisi' => 'Baik',
                'lokasi' => 'Perpustakaan',
                'keterangan' => 'Rak untuk buku perpustakaan (10 unit)',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Kendaraan
            [
                'nama' => 'Mobil Toyota Avanza',
                'kategori' => 'Kendaraan',
                'tanggal_perolehan' => '2021-03-20',
                'harga_perolehan' => 220000000,
                'kondisi' => 'Baik',
                'lokasi' => 'Garasi Sekolah',
                'keterangan' => 'Mobil operasional sekolah',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Bus Sekolah Mercedes-Benz',
                'kategori' => 'Kendaraan',
                'tanggal_perolehan' => '2020-08-15',
                'harga_perolehan' => 850000000,
                'kondisi' => 'Baik',
                'lokasi' => 'Garasi Sekolah',
                'keterangan' => 'Bus untuk antar jemput siswa',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Bangunan
            [
                'nama' => 'Gedung Utama 3 Lantai',
                'kategori' => 'Bangunan',
                'tanggal_perolehan' => '2019-01-10',
                'harga_perolehan' => 5000000000,
                'kondisi' => 'Baik',
                'lokasi' => 'Kampus Sekolah',
                'keterangan' => 'Gedung utama untuk pembelajaran',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Gedung Laboratorium',
                'kategori' => 'Bangunan',
                'tanggal_perolehan' => '2020-06-20',
                'harga_perolehan' => 1500000000,
                'kondisi' => 'Baik',
                'lokasi' => 'Kampus Sekolah',
                'keterangan' => 'Gedung untuk lab IPA dan komputer',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Lainnya
            [
                'nama' => 'Sound System TOA',
                'kategori' => 'Lainnya',
                'tanggal_perolehan' => '2023-04-15',
                'harga_perolehan' => 15000000,
                'kondisi' => 'Baik',
                'lokasi' => 'Aula',
                'keterangan' => 'Sound system untuk acara',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Generator Listrik 50 KVA',
                'kategori' => 'Lainnya',
                'tanggal_perolehan' => '2022-10-20',
                'harga_perolehan' => 45000000,
                'kondisi' => 'Baik',
                'lokasi' => 'Ruang Genset',
                'keterangan' => 'Generator cadangan',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Kamera CCTV Hikvision',
                'kategori' => 'Lainnya',
                'tanggal_perolehan' => '2023-02-10',
                'harga_perolehan' => 12000000,
                'kondisi' => 'Baik',
                'lokasi' => 'Berbagai Lokasi',
                'keterangan' => 'CCTV untuk keamanan (20 unit)',
                'user_id' => $defaultUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        
        DB::table('aset')->insert($aset);
        
        $this->command->info('Created ' . count($aset) . ' aset records');
    }
}
