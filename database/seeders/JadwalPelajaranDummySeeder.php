<?php

namespace Database\Seeders;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Ruang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class JadwalPelajaranDummySeeder extends Seeder
{
    public function run(): void
    {
        $kelasList = Kelas::query()->orderBy('id')->get();

        if ($kelasList->isEmpty()) {
            $this->command->warn('Tidak ada data kelas. Seeder jadwal dummy dibatalkan.');
            return;
        }

        $now = Carbon::now();

        $guruSeed = [
            ['nip' => 'G-1001', 'nama' => 'Bayu Wijaya', 'jenis_kelamin' => 'L', 'email' => 'guru.bayu@sriwijayakidss.com'],
            ['nip' => 'G-1002', 'nama' => 'Dewi Anggraini', 'jenis_kelamin' => 'P', 'email' => 'guru.dewi@sriwijayakidss.com'],
            ['nip' => 'G-1003', 'nama' => 'Rudi Hartono', 'jenis_kelamin' => 'L', 'email' => 'guru.rudi@sriwijayakidss.com'],
            ['nip' => 'G-1004', 'nama' => 'Sinta Maharani', 'jenis_kelamin' => 'P', 'email' => 'guru.sinta@sriwijayakidss.com'],
            ['nip' => 'G-1005', 'nama' => 'Andi Pratama', 'jenis_kelamin' => 'L', 'email' => 'guru.andi@sriwijayakidss.com'],
            ['nip' => 'G-1006', 'nama' => 'Lina Marlina', 'jenis_kelamin' => 'P', 'email' => 'guru.lina@sriwijayakidss.com'],
        ];

        foreach ($guruSeed as $index => $guru) {
            DB::table('guru')->updateOrInsert(
                ['nip' => $guru['nip']],
                [
                    'user_id' => null,
                    'nama' => $guru['nama'],
                    'jenis_kelamin' => $guru['jenis_kelamin'],
                    'tanggal_lahir' => Carbon::create(1987 + $index, 1, 10)->toDateString(),
                    'alamat' => 'Alamat dummy guru',
                    'telepon' => '081200000' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'email' => $guru['email'],
                    'pendidikan_terakhir' => 'S1',
                    'foto' => null,
                    'is_active' => true,
                    'deleted_at' => null,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $mapelSeed = [
            ['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika'],
            ['kode_mapel' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia'],
            ['kode_mapel' => 'BIG', 'nama_mapel' => 'Bahasa Inggris'],
            ['kode_mapel' => 'IPA', 'nama_mapel' => 'Ilmu Pengetahuan Alam'],
            ['kode_mapel' => 'IPS', 'nama_mapel' => 'Ilmu Pengetahuan Sosial'],
            ['kode_mapel' => 'PKN', 'nama_mapel' => 'Pendidikan Kewarganegaraan'],
            ['kode_mapel' => 'PAI', 'nama_mapel' => 'Pendidikan Agama'],
            ['kode_mapel' => 'SBK', 'nama_mapel' => 'Seni Budaya'],
            ['kode_mapel' => 'PJOK', 'nama_mapel' => 'PJOK'],
            ['kode_mapel' => 'TIK', 'nama_mapel' => 'Informatika'],
        ];

        foreach ($mapelSeed as $mapel) {
            MataPelajaran::query()->updateOrCreate(
                ['kode_mapel' => $mapel['kode_mapel']],
                [
                    'nama_mapel' => $mapel['nama_mapel'],
                    'deskripsi' => 'Data dummy mapel untuk jadwal pelajaran.',
                    'is_active' => true,
                    'deleted_at' => null,
                ]
            );
        }

        $ruangIds = [];
        foreach ($kelasList as $kelasIndex => $kelas) {
            $ruang = Ruang::query()->updateOrCreate(
                ['kode_ruang' => 'R-' . str_pad((string) ($kelasIndex + 1), 3, '0', STR_PAD_LEFT)],
                [
                    'nama_ruang' => 'Ruang ' . $kelas->nama,
                    'lokasi' => 'Gedung A',
                    'kapasitas' => 30,
                    'keterangan' => 'Ruang dummy untuk jadwal kelas ' . $kelas->nama,
                    'is_active' => true,
                    'deleted_at' => null,
                ]
            );

            $ruangIds[] = $ruang->id;
        }

        $gurus = DB::table('guru')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $mapels = MataPelajaran::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->pluck('id')
            ->values();

        if ($gurus->isEmpty() || $mapels->isEmpty()) {
            $this->command->warn('Data guru/mapel tidak tersedia. Seeder jadwal dummy dibatalkan.');
            return;
        }

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamSlots = [
            ['07:00', '08:00'],
            ['08:00', '09:00'],
            ['09:15', '10:15'],
            ['10:15', '11:15'],
        ];

        foreach ($kelasList as $kelasIndex => $kelas) {
            foreach ($hariList as $hariIndex => $hari) {
                for ($slotIndex = 0; $slotIndex < 2; $slotIndex++) {
                    $jam = $jamSlots[($hariIndex + $slotIndex) % count($jamSlots)];
                    $guruId = $gurus[($kelasIndex + $hariIndex + $slotIndex) % $gurus->count()];
                    $mapelId = $mapels[($kelasIndex + $hariIndex + $slotIndex) % $mapels->count()];
                    $ruangId = $ruangIds[$kelasIndex % count($ruangIds)];
                    $ruangNama = Ruang::query()->find($ruangId)?->nama ?? null;

                    $jadwal = JadwalPelajaran::withTrashed()->firstOrNew([
                        'kelas_id' => $kelas->id,
                        'hari' => $hari,
                        'jam_mulai' => $jam[0],
                        'jam_selesai' => $jam[1],
                    ]);

                    $jadwal->mata_pelajaran_id = $mapelId;
                    $jadwal->guru_id = $guruId;
                    $jadwal->ruang_id = $ruangId;
                    $jadwal->ruang = $ruangNama;
                    $jadwal->keterangan = 'Jadwal dummy otomatis untuk kelas ' . $kelas->nama;
                    $jadwal->is_active = true;
                    $jadwal->deleted_at = null;
                    $jadwal->save();
                }
            }
        }

        $this->command->info('Seeder jadwal dummy per kelas berhasil dijalankan.');
    }
}
