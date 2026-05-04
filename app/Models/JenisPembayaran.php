<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPembayaran extends Model
{
    use SoftDeletes;

    public const KATEGORI_WAJIB = 'Pembayaran Wajib';
    public const KATEGORI_KEGIATAN = 'Pembayaran Kegiatan';
    public const KATEGORI_LAYANAN_FASILITAS = 'Pembayaran Layanan dan Fasilitas';
    public const KATEGORI_DENDA = 'Pembayaran Denda';
    public const KATEGORI_OPSIONAL = 'Pembayaran Opsional';

    protected $table = 'jenis_pembayaran';

    protected $fillable = [
        'nama',
        'keterangan',
        'nominal_default',
        'tipe',
        'is_active',
    ];

    protected $casts = [
        'nominal_default' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the pembayaran for the jenis.
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'jenis_pembayaran_id');
    }

    public static function kategoriInti(): array
    {
        return [
            self::KATEGORI_WAJIB => 'Pembayaran utama yang wajib dibayarkan secara rutin atau periodik.',
            self::KATEGORI_KEGIATAN => 'Pembayaran untuk kegiatan sekolah seperti ekstrakurikuler, acara, atau kunjungan edukasi.',
            self::KATEGORI_LAYANAN_FASILITAS => 'Pembayaran untuk layanan pendukung dan fasilitas sekolah.',
            self::KATEGORI_DENDA => 'Pembayaran terkait denda keterlambatan atau pelanggaran ketentuan.',
            self::KATEGORI_OPSIONAL => 'Pembayaran bersifat pilihan atau tambahan sesuai kebutuhan.',
        ];
    }

    public static function normalizeNama(?string $nama): string
    {
        $normalized = mb_strtolower(trim((string) $nama));

        if ($normalized === '') {
            return self::KATEGORI_OPSIONAL;
        }

        if (self::containsAny($normalized, [
            'denda',
            'telat',
            'terlambat',
            'sanksi',
            'penalty',
        ])) {
            return self::KATEGORI_DENDA;
        }

        if (self::containsAny($normalized, [
            'kegiatan',
            'ekstra',
            'ekstrakurikuler',
            'outing',
            'studi',
            'study',
            'acara',
            'event',
        ])) {
            return self::KATEGORI_KEGIATAN;
        }

        if (self::containsAny($normalized, [
            'layanan',
            'fasilitas',
            'buku',
            'seragam',
            'praktikum',
            'lab',
            'laboratorium',
            'ujian',
            'administrasi',
        ])) {
            return self::KATEGORI_LAYANAN_FASILITAS;
        }

        if (self::containsAny($normalized, [
            'wajib',
            'spp',
            'pangkal',
            'gedung',
            'daftar ulang',
            'bulanan',
            'tahunan',
        ])) {
            return self::KATEGORI_WAJIB;
        }

        return self::KATEGORI_OPSIONAL;
    }

    public static function ensureKategoriInti(): void
    {
        foreach (self::kategoriInti() as $nama => $keterangan) {
            static::query()->updateOrCreate(
                ['nama' => $nama],
                [
                    'keterangan' => $keterangan,
                    'nominal_default' => 0,
                    'tipe' => 'Sekali',
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
            ->get(['id', 'nama', 'keterangan']);

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
                    'keterangan' => (string) ($row->keterangan ?? ''),
                ];
            })
            ->filter()
            ->values();
    }

    public static function equivalentIds(?int $jenisPembayaranId): array
    {
        if (!$jenisPembayaranId) {
            return [];
        }

        $selected = static::query()->withTrashed()->find($jenisPembayaranId);
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

    public static function representativeIdFor(?int $jenisPembayaranId): ?int
    {
        if (!$jenisPembayaranId) {
            return null;
        }

        $selected = static::query()->withTrashed()->find($jenisPembayaranId);
        if (!$selected) {
            return null;
        }

        $targetKategori = static::normalizeNama((string) $selected->nama);
        $option = static::dropdownOptions()->firstWhere('nama', $targetKategori);

        if ($option && isset($option->id)) {
            return (int) $option->id;
        }

        return (int) $jenisPembayaranId;
    }

    public function getNamaKategoriAttribute(): string
    {
        return static::normalizeNama((string) $this->nama);
    }

    private static function containsAny(string $value, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if ($keyword !== '' && str_contains($value, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
