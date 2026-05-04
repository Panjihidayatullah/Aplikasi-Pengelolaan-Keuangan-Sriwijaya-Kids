<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunAjaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tahun_ajaran';
    protected $fillable = ['kurikulum_id', 'nama', 'tahun_mulai', 'tahun_selesai', 'tanggal_mulai', 'tanggal_selesai', 'is_active'];
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function semester()
    {
        return $this->hasMany(Semester::class);
    }

    public function kenaikanKelas()
    {
        return $this->hasMany(KenaikanKelas::class);
    }

    public function guruWaliKelas()
    {
        return $this->hasMany(GuruWaliKelas::class);
    }

    public function kalenderAkademik()
    {
        return $this->hasMany(KalenderAkademik::class);
    }
}
