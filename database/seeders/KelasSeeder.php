<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('kelas')->insert([
            [
                'nama_kelas' => '7A',
                'tingkat' => 7,
                'wali_kelas' => 'Budi Santoso, S.Pd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kelas' => '7B',
                'tingkat' => 7,
                'wali_kelas' => 'Siti Aminah, S.Pd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kelas' => '7C',
                'tingkat' => 7,
                'wali_kelas' => 'Dewi Lestari, S.Pd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kelas' => '8A',
                'tingkat' => 8,
                'wali_kelas' => 'Ahmad Ridwan, S.Pd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kelas' => '8B',
                'tingkat' => 8,
                'wali_kelas' => 'Rina Kusuma, S.Pd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kelas' => '8C',
                'tingkat' => 8,
                'wali_kelas' => 'Hendra Wijaya, S.Pd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kelas' => '9A',
                'tingkat' => 9,
                'wali_kelas' => 'Lina Marlina, S.Pd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kelas' => '9B',
                'tingkat' => 9,
                'wali_kelas' => 'Bambang Suryanto, S.Pd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kelas' => '9C',
                'tingkat' => 9,
                'wali_kelas' => 'Yuli Handayani, S.Pd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
