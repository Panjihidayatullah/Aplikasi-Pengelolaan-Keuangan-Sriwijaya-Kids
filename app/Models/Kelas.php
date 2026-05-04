<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'wali_kelas',
        'is_tingkat_akhir',
    ];

    protected $casts = [
        'tingkat' => 'integer',
        'is_tingkat_akhir' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $kelas) {
            $kelas->nama_kelas = trim((string) $kelas->nama_kelas);

            if ((int) $kelas->tingkat <= 0) {
                $inferred = self::inferTingkatFromNama($kelas->nama_kelas);
                if ($inferred !== null) {
                    $kelas->tingkat = $inferred;
                }
            }

            if ((int) $kelas->tingkat <= 0) {
                $kelas->tingkat = null;
            }
        });
    }

    public function getNamaAttribute()
    {
        return $this->nama_kelas;
    }

    public function scopeOrderByTingkat($query)
    {
        return $query
            ->orderByRaw("CASE WHEN tingkat IS NULL OR TRIM(tingkat) = '' THEN 1 ELSE 0 END")
            ->orderByRaw("LENGTH(COALESCE(tingkat, '')) ASC")
            ->orderBy('tingkat');
    }

    public static function inferTingkatFromNama(?string $namaKelas): ?int
    {
        if (!$namaKelas) {
            return null;
        }

        preg_match_all('/\d+/', $namaKelas, $matches);
        foreach ($matches[0] ?? [] as $chunk) {
            $number = (int) $chunk;
            if ($number >= 1 && $number <= 12) {
                return $number;
            }
        }

        return null;
    }

    /**
     * Get the siswa for the kelas.
     */
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    /**
     * Accessor for siswa count
     */
    public function getSiswaCountAttribute()
    {
        return $this->siswa()->count();
    }

    /**
     * Get guru wali kelas assignment.
     */
    public function guruWaliKelas()
    {
        return $this->hasMany(GuruWaliKelas::class, 'kelas_id');
    }

    /**
     * Get ujian for this class.
     */
    public function ujian()
    {
        return $this->hasMany(Ujian::class, 'kelas_id');
    }

    /**
     * Get kenaikan kelas for this class.
     */
    public function kenaikanKelas()
    {
        return $this->hasMany(KenaikanKelas::class, 'kelas_sekarang_id');
    }

    /**
     * Get current wali kelas (active).
     */
    public function waliKelasAktif()
    {
        return $this->guruWaliKelas()
            ->where('is_active', true)
            ->first();
    }
}
