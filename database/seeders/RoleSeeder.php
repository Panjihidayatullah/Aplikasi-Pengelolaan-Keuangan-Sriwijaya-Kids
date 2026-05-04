<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard
            'view dashboard',
            
            // Siswa
            'view siswa',
            'create siswa',
            'edit siswa',
            'delete siswa',
            'import siswa',
            'export siswa',
            
            // Kelas
            'view kelas',
            'create kelas',
            'edit kelas',
            'delete kelas',
            
            // Pembayaran
            'view pembayaran',
            'create pembayaran',
            'edit pembayaran',
            'delete pembayaran',
            'approve pembayaran',
            'export pembayaran',
            
            // Pengeluaran
            'view pengeluaran',
            'create pengeluaran',
            'edit pengeluaran',
            'delete pengeluaran',
            'approve pengeluaran',
            'export pengeluaran',

            // Gaji Guru
            'view gaji guru',
            'export gaji guru',
            'view gaji saya',
            'export gaji saya',
            
            // Aset
            'view aset',
            'create aset',
            'edit aset',
            'delete aset',
            
            // Laporan
            'view laporan cashflow',
            'view laporan pemasukan',
            'view laporan pengeluaran',
            'export laporan',
            
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role & Permission
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Riwayat
            'view riwayat',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // 1. Admin - Full Access (42 permissions)
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 2. Bendahara - Financial Management (28 permissions)
        $bendaharaRole = Role::firstOrCreate(['name' => 'Bendahara']);
        $bendaharaRole->givePermissionTo([
            'view dashboard',
            'view siswa',
            'create siswa',
            'edit siswa',
            'view pembayaran',
            'create pembayaran',
            'edit pembayaran',
            'export pembayaran',
            'view pengeluaran',
            'create pengeluaran',
            'edit pengeluaran',
            'view gaji guru',
            'export gaji guru',
            'view aset',
            'create aset',
            'edit aset',
            'view laporan cashflow',
            'view laporan pemasukan',
            'view laporan pengeluaran',
            'export laporan',
            'view riwayat',
        ]);

        // 3. Kepala Sekolah - Monitoring & Reporting (18 permissions)
        $kepalaSekolahRole = Role::firstOrCreate(['name' => 'Kepala Sekolah']);
        $kepalaSekolahRole->givePermissionTo([
            'view dashboard',
            'view siswa',
            'view pembayaran',
            'view pengeluaran',
            'view aset',
            'view laporan cashflow',
            'view laporan pemasukan',
            'view laporan pengeluaran',
            'export laporan',
            'view riwayat',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Admin: ' . $adminRole->permissions->count() . ' permissions');
        $this->command->info('Bendahara: ' . $bendaharaRole->permissions->count() . ' permissions');
        $this->command->info('Kepala Sekolah: ' . $kepalaSekolahRole->permissions->count() . ' permissions');
    }
}
