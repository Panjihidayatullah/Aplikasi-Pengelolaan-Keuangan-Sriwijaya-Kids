<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AcademicPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds for academic module permissions.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Academic Module Permissions
        $academicPermissions = [
            // Kurikulum
            'view kurikulum',
            'create kurikulum',
            'edit kurikulum',
            'delete kurikulum',
            
            // Tahun Ajaran
            'view tahun-ajaran',
            'create tahun-ajaran',
            'edit tahun-ajaran',
            'delete tahun-ajaran',
            'manage tahun-ajaran',
            
            // Semester
            'view semester',
            'create semester',
            'edit semester',
            'delete semester',
            
            // Guru Wali Kelas
            'view guru-wali-kelas',
            'create guru-wali-kelas',
            'edit guru-wali-kelas',
            'delete guru-wali-kelas',
            
            // Kartu Pelajar & NIS
            'view kartu-pelajar',
            'create kartu-pelajar',
            'print kartu-pelajar',
            'bulk-generate kartu-pelajar',
            
            // Transkrip Nilai
            'view transkrip-nilai',
            'create transkrip-nilai',
            'edit transkrip-nilai',
            'export transkrip-nilai',
            
            // Pengumuman
            'view pengumuman',
            'create pengumuman',
            'edit pengumuman',
            'delete pengumuman',
            'publish pengumuman',
            
            // Kalender Akademik
            'view kalender-akademik',
            'create kalender-akademik',
            'edit kalender-akademik',
            'delete kalender-akademik',
            
            // Ujian
            'view ujian',
            'create ujian',
            'edit ujian',
            'delete ujian',
            'manage ujian-peserta',
            'input ujian-nilai',
            
            // Kenaikan Kelas & Kelulusan
            'view kenaikan-kelas',
            'manage kenaikan-kelas',
            'process kenaikan-kelas',
            'approve kenaikan-kelas',
            
            // Notifikasi
            'view notifikasi',
            'manage notifikasi',
            
            // Import & Export
            'import siswa-excel',
            'import guru-excel',
            'export siswa-excel',
            'export guru-excel',
            'export kenaikan-kelas',
            
            // Rekap Nilai
            'view rekap-nilai',
            'export rekap-nilai',
            
            // Academic Dashboard
            'view akademik-dashboard',

            // Absensi Harian
            'view absensi',
            'create absensi',
            'edit absensi',
            'delete absensi',

            // LMS Materi
            'view lms-materi',
            'create lms-materi',
            'delete lms-materi',

            // LMS Tugas
            'view lms-tugas',
            'create lms-tugas',
            'delete lms-tugas',
            'submit lms-tugas',
            'grade lms-tugas',

            // LMS Monitoring
            'view lms-monitoring',
            'sync lms-nilai',

            // Keuangan - Gaji Guru
            'view gaji guru',
            'export gaji guru',
            'view gaji saya',
            'export gaji saya',
        ];

        // Create all academic permissions
        foreach ($academicPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Get/create roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $bendaharaRole = Role::firstOrCreate(['name' => 'Bendahara']);
        $kepalaSekolahRole = Role::firstOrCreate(['name' => 'Kepala Sekolah']);

        // Admin gets all academic permissions
        $adminRole->givePermissionTo($academicPermissions);

        // Bendahara gets academic permissions (limited)
        $bendaharaAcademicPerms = [
            'view akademik-dashboard',
            'view kurikulum',
            'view tahun-ajaran',
            'view semester',
            'view guru-wali-kelas',
            'view kartu-pelajar',
            'create kartu-pelajar',
            'print kartu-pelajar',
            'view transkrip-nilai',
            'view pengumuman',
            'view ujian',
            'view notifikasi',
            'export siswa-excel',
            'export guru-excel',
            'view rekap-nilai',
            'export rekap-nilai',
            'view absensi',
            'view lms-materi',
            'view lms-tugas',
            'view lms-monitoring',
            'view gaji guru',
            'export gaji guru',
        ];
        $bendaharaRole->givePermissionTo($bendaharaAcademicPerms);

        // Kepala Sekolah gets academic read-only permissions
        $kepalaSekolahAcademicPerms = [
            'view akademik-dashboard',
            'view kurikulum',
            'view tahun-ajaran',
            'view semester',
            'view guru-wali-kelas',
            'view kartu-pelajar',
            'view transkrip-nilai',
            'view pengumuman',
            'view ujian',
            'view notifikasi',
            'view rekap-nilai',
            'export rekap-nilai',
            'view absensi',
            'view lms-materi',
            'view lms-tugas',
            'view lms-monitoring',
        ];
        $kepalaSekolahRole->givePermissionTo($kepalaSekolahAcademicPerms);

        // Create Guru role for teachers (new role)
        $guruRole = Role::firstOrCreate(['name' => 'Guru']);
        $guruAcademicPerms = [
            'view akademik-dashboard',
            'view siswa',
            'view transkrip-nilai',
            'create transkrip-nilai',
            'edit transkrip-nilai',
            'view pengumuman',
            'view ujian',
            'manage ujian-peserta',
            'input ujian-nilai',
            'view absensi',
            'create absensi',
            'edit absensi',
            'view notifikasi',
            'create pengumuman',
            'view rekap-nilai',
            'view lms-materi',
            'create lms-materi',
            'delete lms-materi',
            'view lms-tugas',
            'create lms-tugas',
            'delete lms-tugas',
            'submit lms-tugas',
            'grade lms-tugas',
            'view lms-monitoring',
            'sync lms-nilai',
            'view gaji saya',
            'export gaji saya',
        ];
        $guruRole->givePermissionTo($guruAcademicPerms);

        // Optional Siswa role for LMS access
        $siswaRole = Role::firstOrCreate(['name' => 'Siswa']);
        $siswaAcademicPerms = [
            'view akademik-dashboard',
            'view lms-materi',
            'view lms-tugas',
            'submit lms-tugas',
            'view pengumuman',
            'view notifikasi',
        ];
        $siswaRole->givePermissionTo($siswaAcademicPerms);

        $this->command->info('✓ Academic permissions created successfully!');
        $this->command->info('✓ Admin role updated with all academic permissions');
        $this->command->info('✓ Bendahara role updated with academic permissions');
        $this->command->info('✓ Kepala Sekolah role updated with academic permissions');
        $this->command->info('✓ Guru role created with classroom management permissions');
        $this->command->info('✓ Siswa role updated with LMS access permissions');
    }
}
