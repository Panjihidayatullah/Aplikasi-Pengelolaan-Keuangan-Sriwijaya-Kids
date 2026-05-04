<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuruWaliKelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'guru_wali_kelas';
    protected $fillable = ['guru_id', 'kelas_id', 'tahun_ajaran_id', 'tanggal_mulai', 'tanggal_selesai', 'is_active'];
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
