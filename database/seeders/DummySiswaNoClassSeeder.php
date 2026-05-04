<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class DummySiswaNoClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dummyKelas = Kelas::withTrashed()->firstOrCreate(
            ['nama_kelas' => '_DUMMY_NOCLASS_'],
            ['tingkat' => 99, 'wali_kelas' => 'System Seeder']
        );

        if (!$dummyKelas->trashed()) {
            $dummyKelas->delete();
        }

        for ($i = 1; $i <= 10; $i++) {
            $nis = 'DUMMYNC' . str_pad((string) $i, 4, '0', STR_PAD_LEFT);

            Siswa::query()->updateOrCreate(
                ['nis' => $nis],
                [
                    'kelas_id' => $dummyKelas->id,
                    'nama' => 'Dummy NoClass ' . $i,
                    'jenis_kelamin' => $i % 2 === 0 ? 'P' : 'L',
                    'is_active' => true,
                ]
            );
        }
    }
}
