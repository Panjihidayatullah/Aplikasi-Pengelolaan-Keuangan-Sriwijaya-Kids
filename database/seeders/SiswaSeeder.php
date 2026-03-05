<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $now = Carbon::now();
        
        // Get all kelas IDs
        $kelasIds = DB::table('kelas')->pluck('id')->toArray();
        
        $siswa = [];
        $usedNIS = [];
        
        // Generate 90 siswa (10 per kelas)
        foreach ($kelasIds as $kelasId) {
            for ($i = 0; $i < 10; $i++) {
                // Generate unique NIS
                do {
                    $nis = '2024' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                } while (in_array($nis, $usedNIS));
                $usedNIS[] = $nis;
                
                $gender = $faker->randomElement(['L', 'P']);
                $namaDepan = $gender === 'L' ? $faker->firstNameMale : $faker->firstNameFemale;
                
                $siswa[] = [
                    'nis' => $nis,
                    'nama' => $namaDepan . ' ' . $faker->lastName,
                    'kelas_id' => $kelasId,
                    'jenis_kelamin' => $gender,
                    'tanggal_lahir' => $faker->dateTimeBetween('-16 years', '-13 years'),
                    'alamat' => $faker->address,
                    'telepon' => '08' . rand(1000000000, 9999999999),
                    'email' => null,
                    'nama_ayah' => $faker->name('male'),
                    'telepon_ayah' => '08' . rand(1000000000, 9999999999),
                    'nama_ibu' => $faker->name('female'),
                    'telepon_ibu' => '08' . rand(1000000000, 9999999999),
                    'foto' => null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        
        // Insert in batches
        foreach (array_chunk($siswa, 50) as $batch) {
            DB::table('siswa')->insert($batch);
        }
        
        $this->command->info('Created ' . count($siswa) . ' siswa records');
    }
}
