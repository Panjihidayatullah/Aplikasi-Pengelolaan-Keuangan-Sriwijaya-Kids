<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPengeluaran extends Model
{
    use SoftDeletes;

    public const KATEGORI_ASET = 'Aset';
    public const KATEGORI_OPERASIONAL = 'Operasional';
    public const KATEGORI_GAJI_PEGAWAI = 'Gaji Pegawai';
    public const KATEGORI_GAJI_GURU = 'Gaji Guru';

    protected $table = 'jenis_pengeluaran';

    protected $fillable = [
        'nama',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the pengeluaran for the jenis.
     */
    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'jenis_pengeluaran_id');
    }

    public static function kategoriInti(): array
    {
        return [
            self::KATEGORI_ASET => 'Pengeluaran pembelian aset tahan lama seperti meja, kursi, papan tulis, komputer, dan sejenisnya.',
            self::KATEGORI_OPERASIONAL => 'Pengeluaran operasional habis pakai seperti listrik, air, internet, ATK, konsumsi, transport, dan pemeliharaan.',
            self::KATEGORI_GAJI_PEGAWAI => 'Pembayaran gaji pegawai non-guru sekolah seperti satpam, kebersihan, staf administrasi, dan karyawan lainnya.',
            self::KATEGORI_GAJI_GURU => 'Pembayaran gaji khusus guru berdasarkan periode bulan dan tahun penggajian.',
        ];
    }

    public static function normalizeNama(?string $nama): string
    {
        $normalized = mb_strtolower(trim((string) $nama));

        if ($normalized === '') {
            return self::KATEGORI_OPERASIONAL;
        }

        if (self::containsAny($normalized, [
            'guru',
            'pengajar',
            'tenaga pendidik',
        ])) {
            return self::KATEGORI_GAJI_GURU;
        }

        if (self::containsAny($normalized, [
            'gaji',
            'honor',
            'satpam',
            'kebersihan',
            'pegawai',
            'karyawan',
            'staff',
            'staf',
        ])) {
            return self::KATEGORI_GAJI_PEGAWAI;
        }

        if (self::containsAny($normalized, [
            'aset',
            'inventaris',
            'meja',
            'kursi',
            'papan',
            'furnitur',
            'komputer',
            'laptop',
            'printer',
            'proyektor',
            'perangkat',
            'peralatan',
            'sarana',
        ])) {
            return self::KATEGORI_ASET;
        }

        return self::KATEGORI_OPERASIONAL;
    }

    public static function ensureKategoriInti(): void
    {
        foreach (self::kategoriInti() as $nama => $keterangan) {
            static::query()->updateOrCreate(
                ['nama' => $nama],
                [
                    'keterangan' => $keterangan,
                    'is_active' => true,
                    'deleted_at' => null,
                ]
            );
        }
    }

    public static function dropdownOptions(): Collection
    {
        self::ensureKategoriInti();

        $all = static::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'nama']);

        return collect(array_keys(self::kategoriInti()))
            ->map(function (string $kategori) use ($all) {
                $row = $all->first(function ($item) use ($kategori) {
                    return static::normalizeNama($item->nama) === $kategori;
                });

                if (!$row) {
                    return null;
                }

                return (object) [
                    'id' => (int) $row->id,
                    'nama' => $kategori,
                ];
            })
            ->filter()
            ->values();
    }

    public static function equivalentIds(?int $jenisPengeluaranId): array
    {
        if (!$jenisPengeluaranId) {
            return [];
        }

        $selected = static::query()->withTrashed()->find($jenisPengeluaranId);
        if (!$selected) {
            return [];
        }

        $targetKategori = static::normalizeNama((string) $selected->nama);

        return static::query()
            ->withTrashed()
            ->get(['id', 'nama'])
            ->filter(fn ($item) => static::normalizeNama((string) $item->nama) === $targetKategori)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    public static function representativeIdFor(?int $jenisPengeluaranId): ?int
    {
        if (!$jenisPengeluaranId) {
            return null;
        }

        $selected = static::query()->withTrashed()->find($jenisPengeluaranId);
        if (!$selected) {
            return null;
        }

        $targetKategori = static::normalizeNama((string) $selected->nama);
        $option = static::dropdownOptions()->firstWhere('nama', $targetKategori);

        if ($option && isset($option->id)) {
            return (int) $option->id;
        }

        return (int) $jenisPengeluaranId;
    }

    public function getNamaKategoriAttribute(): string
    {
        return static::normalizeNama((string) $this->nama);
    }

    private static function containsAny(string $value, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($value, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
