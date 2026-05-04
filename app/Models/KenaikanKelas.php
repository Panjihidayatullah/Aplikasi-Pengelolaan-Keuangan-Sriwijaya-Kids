<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KenaikanKelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kenaikan_kelas';
    protected $fillable = ['siswa_id', 'kelas_sekarang_id', 'kelas_tujuan_id', 'tahun_ajaran_id', 'status', 'rata_rata_nilai', 'catatan', 'tanggal_penetapan', 'is_applied'];
    protected $casts = [
        'rata_rata_nilai' => 'decimal:2',
        'tanggal_penetapan' => 'date',
        'is_applied' => 'boolean',
    ];

    protected static function booted(): void
    {
        // When a record is updated by user (not by the approve process itself),
        // reset is_applied so the button shows again.
        static::updating(function (self $model) {
            // Only reset if is_applied is not being explicitly set in this update
            if (!array_key_exists('is_applied', $model->getDirty())) {
                $model->is_applied = false;
            }
        });
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelasSekarang()
    {
        return $this->belongsTo(Kelas::class, 'kelas_sekarang_id');
    }

    public function kelasTujuan()
    {
        return $this->belongsTo(Kelas::class, 'kelas_tujuan_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /**
     * Determine promotion based on average grade
     * Naik: >= 70
     * Tidak Naik: < 70
     * Lulus: Last grade
     */
    public static function determineStatus($rataRataNilai, $isLastGrade = false)
    {
        if ($isLastGrade) {
            return 'lulus';
        }

        return $rataRataNilai >= 70 ? 'naik' : 'tidak_naik';
    }
}
