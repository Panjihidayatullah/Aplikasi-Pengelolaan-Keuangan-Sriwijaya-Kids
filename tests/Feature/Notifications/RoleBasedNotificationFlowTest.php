<?php

namespace Tests\Feature\Notifications;

use App\Http\Controllers\LmsPengumpulanController;
use App\Http\Controllers\LmsTugasController;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleBasedNotificationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['Admin', 'Kepala Sekolah', 'Bendahara', 'Guru', 'Siswa'] as $roleName) {
            Role::findOrCreate($roleName);
        }
    }

    private function invokePrivateMethod(object $instance, string $method, array $arguments = []): mixed
    {
        $reflection = new ReflectionMethod($instance, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($instance, $arguments);
    }

    public function test_notifikasi_tugas_baru_hanya_untuk_siswa_kelas_terkait_admin_kepsek(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $kepsek = User::factory()->create();
        $kepsek->assignRole('Kepala Sekolah');

        $guru = User::factory()->create();
        $guru->assignRole('Guru');

        $siswaTargetUser = User::factory()->create();
        $siswaTargetUser->assignRole('Siswa');

        $siswaLuarKelasUser = User::factory()->create();
        $siswaLuarKelasUser->assignRole('Siswa');

        $kelasTarget = Kelas::query()->create([
            'nama_kelas' => 'Kelas Target',
            'tingkat' => '1',
        ]);

        $kelasLain = Kelas::query()->create([
            'nama_kelas' => 'Kelas Lain',
            'tingkat' => '1',
        ]);

        Siswa::query()->create([
            'user_id' => $siswaTargetUser->id,
            'kelas_id' => $kelasTarget->id,
            'nis' => 'NIS-TARGET',
            'nama' => 'Siswa Target',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        Siswa::query()->create([
            'user_id' => $siswaLuarKelasUser->id,
            'kelas_id' => $kelasLain->id,
            'nis' => 'NIS-LUAR',
            'nama' => 'Siswa Luar Kelas',
            'jenis_kelamin' => 'P',
            'is_active' => true,
        ]);

        $tugas = new Tugas([
            'judul' => 'Tugas Pecahan',
            'kelas_id' => $kelasTarget->id,
        ]);
        $tugas->setAttribute('id', 101);

        $this->invokePrivateMethod(new LmsTugasController(), 'notifyTugasBaru', [$tugas]);

        $recipientIds = Notifikasi::query()
            ->where('tipe', 'tugas')
            ->pluck('user_id')
            ->all();

        $expected = [$admin->id, $kepsek->id, $siswaTargetUser->id];
        sort($expected);
        $actual = $recipientIds;
        sort($actual);

        if ($actual !== $expected) {
            throw new \RuntimeException('Recipient notifikasi tugas tidak sesuai ekspektasi.');
        }

        if (in_array($guru->id, $recipientIds, true)) {
            throw new \RuntimeException('Guru tidak boleh menerima notifikasi tugas baru kelas tertentu.');
        }

        if (in_array($siswaLuarKelasUser->id, $recipientIds, true)) {
            throw new \RuntimeException('Siswa luar kelas tidak boleh menerima notifikasi tugas kelas target.');
        }
    }

    public function test_notifikasi_nilai_hanya_untuk_pemilik_guru_admin_kepsek(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $kepsek = User::factory()->create();
        $kepsek->assignRole('Kepala Sekolah');

        $guruUser = User::factory()->create();
        $guruUser->assignRole('Guru');

        $siswaPemilikUser = User::factory()->create();
        $siswaPemilikUser->assignRole('Siswa');

        $siswaLainUser = User::factory()->create();
        $siswaLainUser->assignRole('Siswa');

        $kelas = Kelas::query()->create([
            'nama_kelas' => 'Kelas A',
            'tingkat' => '2',
        ]);

        $siswaPemilik = Siswa::query()->create([
            'user_id' => $siswaPemilikUser->id,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS-PEMILIK',
            'nama' => 'Siswa Pemilik',
            'jenis_kelamin' => 'L',
            'is_active' => true,
        ]);

        Siswa::query()->create([
            'user_id' => $siswaLainUser->id,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS-LAIN',
            'nama' => 'Siswa Lain',
            'jenis_kelamin' => 'P',
            'is_active' => true,
        ]);

        $guru = new Guru([
            'user_id' => $guruUser->id,
        ]);
        $guru->setAttribute('id', 11);

        $tugas = new Tugas([
            'judul' => 'Tugas IPA',
        ]);
        $tugas->setAttribute('id', 202);
        $tugas->setRelation('guru', $guru);

        $pengumpulan = new PengumpulanTugas([
            'siswa_id' => $siswaPemilik->id,
        ]);
        $pengumpulan->setAttribute('id', 303);
        $pengumpulan->setRelation('siswa', $siswaPemilik);
        $pengumpulan->setRelation('tugas', $tugas);

        $this->invokePrivateMethod(new LmsPengumpulanController(), 'notifyNilaiDirilis', [$pengumpulan]);

        $recipientIds = Notifikasi::query()
            ->where('tipe', 'nilai')
            ->pluck('user_id')
            ->all();

        $expected = [$admin->id, $kepsek->id, $guruUser->id, $siswaPemilikUser->id];
        sort($expected);
        $actual = $recipientIds;
        sort($actual);

        if ($actual !== $expected) {
            throw new \RuntimeException('Recipient notifikasi nilai tidak sesuai ekspektasi.');
        }

        if (in_array($siswaLainUser->id, $recipientIds, true)) {
            throw new \RuntimeException('Siswa lain tidak boleh menerima notifikasi nilai siswa pemilik.');
        }
    }

    /**
     * @dataProvider roleUnreadLimitsProvider
     */
    public function test_unread_notification_limit_sesuai_role(?string $roleName, int $expectedLimit): void
    {
        $user = User::factory()->create();

        if ($roleName) {
            $user->assignRole($roleName);
        }

        for ($i = 1; $i <= 20; $i++) {
            Notifikasi::query()->create([
                'user_id' => $user->id,
                'judul' => 'Notif ' . $i,
                'isi' => 'Isi notifikasi ' . $i,
                'tipe' => 'pengumuman',
                'is_read' => false,
            ]);
        }

        $response = $this->actingAs($user)
            ->getJson(route('akademik.notifikasi.unread'));

        if ($response->status() !== 200) {
            throw new \RuntimeException('Endpoint unread notifikasi tidak mengembalikan status 200.');
        }

        if (count($response->json()) !== $expectedLimit) {
            throw new \RuntimeException('Limit unread notifikasi tidak sesuai role.');
        }
    }

    public static function roleUnreadLimitsProvider(): array
    {
        return [
            'admin' => ['Admin', 12],
            'kepala-sekolah' => ['Kepala Sekolah', 10],
            'bendahara' => ['Bendahara', 8],
            'guru' => ['Guru', 7],
            'siswa' => ['Siswa', 5],
            'tanpa-role' => [null, 6],
        ];
    }
}
