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

        $types = [
            [
                'nama' => 'Aset',
                'keterangan' => 'Pengeluaran pembelian aset tahan lama seperti meja, kursi, papan tulis, komputer, dan sejenisnya.',
            ],
            [
                'nama' => 'Operasional',
                'keterangan' => 'Pengeluaran operasional habis pakai seperti listrik, air, internet, ATK, transport, konsumsi, dan pemeliharaan.',
            ],
            [
                'nama' => 'Gaji Pegawai',
                'keterangan' => 'Pembayaran gaji pegawai non-guru sekolah seperti satpam, kebersihan, staf administrasi, dan karyawan lainnya.',
            ],
            [
                'nama' => 'Gaji Guru',
                'keterangan' => 'Pembayaran gaji khusus guru berdasarkan periode bulan dan tahun penggajian.',
            ],
        ];

        foreach ($types as $type) {
            DB::table('jenis_pengeluaran')->updateOrInsert(
                ['nama' => $type['nama']],
                [
                    'keterangan' => $type['keterangan'],
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
