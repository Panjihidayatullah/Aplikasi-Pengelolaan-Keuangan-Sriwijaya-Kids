<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = Carbon::now();

        $canonical = [
            'Aset' => 'Pengeluaran pembelian aset tahan lama seperti meja, kursi, papan tulis, komputer, dan sejenisnya.',
            'Operasional' => 'Pengeluaran operasional habis pakai seperti listrik, air, internet, ATK, transport, konsumsi, dan pemeliharaan.',
            'Gaji Pegawai' => 'Pembayaran gaji pegawai non-guru sekolah seperti satpam, kebersihan, staf administrasi, dan karyawan lainnya.',
            'Gaji Guru' => 'Pembayaran gaji khusus guru berdasarkan periode bulan dan tahun penggajian.',
        ];

        $canonicalIds = [];

        foreach ($canonical as $nama => $keterangan) {
            $existing = DB::table('jenis_pengeluaran')
                ->whereRaw('LOWER(TRIM(nama)) = ?', [mb_strtolower($nama)])
                ->orderBy('id')
                ->first();

            if ($existing) {
                DB::table('jenis_pengeluaran')
                    ->where('id', $existing->id)
                    ->update([
                        'nama' => $nama,
                        'keterangan' => $keterangan,
                        'is_active' => true,
                        'deleted_at' => null,
                        'updated_at' => $now,
                    ]);

                $canonicalIds[$nama] = (int) $existing->id;
            } else {
                $id = DB::table('jenis_pengeluaran')->insertGetId([
                    'nama' => $nama,
                    'keterangan' => $keterangan,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $canonicalIds[$nama] = (int) $id;
            }
        }

        $allJenis = DB::table('jenis_pengeluaran')->get(['id', 'nama']);

        $bucketIds = [
            'Aset' => [],
            'Operasional' => [],
            'Gaji Pegawai' => [],
            'Gaji Guru' => [],
        ];

        foreach ($allJenis as $jenis) {
            $kategori = $this->normalizeKategori((string) $jenis->nama);
            $bucketIds[$kategori][] = (int) $jenis->id;
        }

        foreach ($bucketIds as $kategori => $oldIds) {
            $targetId = $canonicalIds[$kategori] ?? null;
            if (!$targetId) {
                continue;
            }

            $idsToMove = array_values(array_filter($oldIds, fn ($id) => $id !== $targetId));

            if (!empty($idsToMove)) {
                DB::table('pengeluaran')
                    ->whereIn('jenis_pengeluaran_id', $idsToMove)
                    ->update([
                        'jenis_pengeluaran_id' => $targetId,
                        'updated_at' => $now,
                    ]);

                DB::table('jenis_pengeluaran')
                    ->whereIn('id', $idsToMove)
                    ->update([
                        'is_active' => false,
                        'deleted_at' => $now,
                        'updated_at' => $now,
                    ]);
            }
        }
    }

    public function down(): void
    {
        // Irreversible data normalization.
    }

    private function normalizeKategori(string $nama): string
    {
        $value = mb_strtolower(trim($nama));

        if ($value === '') {
            return 'Operasional';
        }

        if ($this->containsAny($value, [
            'guru',
            'pengajar',
            'tenaga pendidik',
        ])) {
            return 'Gaji Guru';
        }

        if ($this->containsAny($value, [
            'gaji',
            'honor',
            'satpam',
            'kebersihan',
            'pegawai',
            'karyawan',
            'staff',
            'staf',
        ])) {
            return 'Gaji Pegawai';
        }

        if ($this->containsAny($value, [
            'aset',
            'inventaris',
            'meja',
            'kursi',
            'komputer',
            'laptop',
            'proyektor',
            'printer',
            'lemari',
            'papan',
        ])) {
            return 'Aset';
        }

        return 'Operasional';
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
};
