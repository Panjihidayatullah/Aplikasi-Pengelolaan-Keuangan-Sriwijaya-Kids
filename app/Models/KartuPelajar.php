<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KartuPelajar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kartu_pelajar';
    protected $fillable = ['siswa_id', 'nomor_kartu', 'nis_otomatis', 'tanggal_terbit', 'tanggal_berlaku_akhir', 'status', 'catatan'];
    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_berlaku_akhir' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Generate automatic NIS based on pattern: TGL + MONTH + YEAR + SEQUENCE
     * e.g., 310326001 (31/03/26/001)
     */
    public static function generateNIS()
    {
        $date = now();
        $prefix = $date->format('dmy');
        
        $count = self::whereDate('created_at', $date)
            ->count() + 1;
        
        return $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
