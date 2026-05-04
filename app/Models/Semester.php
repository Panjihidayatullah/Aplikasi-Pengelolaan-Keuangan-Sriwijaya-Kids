<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Semester extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'semester';
    protected $fillable = ['tahun_ajaran_id', 'nomor_semester', 'tanggal_mulai', 'tanggal_selesai', 'tanggal_uts', 'tanggal_uas', 'is_active'];
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_uts' => 'date',
        'tanggal_uas' => 'date',
        'is_active' => 'boolean',
    ];

    public function getNamaAttribute()
    {
        return 'Semester ' . $this->nomor_semester;
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function transkripsNilai()
    {
        return $this->hasMany(TranskripsNilai::class);
    }

    public function ujian()
    {
        return $this->hasMany(Ujian::class);
    }
}
