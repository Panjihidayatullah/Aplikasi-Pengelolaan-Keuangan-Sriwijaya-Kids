<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users first (needed for foreign keys) - only if they don't exist
        if (!User::where('email', 'admin@sriwijayakidss.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@sriwijayakidss.com',
            ]);
        }

        if (!User::where('email', 'budi.santoso@sriwijayakidss.com')->exists()) {
            User::factory()->create([
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@sriwijayakidss.com',
            ]);
        }

        if (!User::where('email', 'siti.nurhaliza@sriwijayakidss.com')->exists()) {
            User::factory()->create([
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@sriwijayakidss.com',
            ]);
        }

        if (!User::where('email', 'ahmad.fauzi@sriwijayakidss.com')->exists()) {
            User::factory()->create([
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@sriwijayakidss.com',
            ]);
        }

        if (!User::where('email', 'dewi.lestari@sriwijayakidss.com')->exists()) {
            User::factory()->create([
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@sriwijayakidss.com',
            ]);
        }

        // Seed reference data first
        $this->call([
            JenisPembayaranSeeder::class,
            JenisPengeluaranSeeder::class,
            KelasSeeder::class,
        ]);

        // Then seed master data
        $this->call([
            SiswaSeeder::class,
            AsetSeeder::class,
        ]);

        // Finally seed transactional data
        $this->call([
            PembayaranSeeder::class,
            PengeluaranSeeder::class,
        ]);

        $this->command->info('✓ Database seeding completed successfully!');
    }
}
