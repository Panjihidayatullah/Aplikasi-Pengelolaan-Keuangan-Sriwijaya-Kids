<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ujian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ujian';
    protected $fillable = ['mata_pelajaran_id', 'kelas_id', 'semester_id', 'jenis_ujian', 'tanggal_ujian', 'jam_mulai', 'jam_selesai', 'ruang', 'catatan'];
    protected $casts = [
        'tanggal_ujian' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function pesertaUjian()
    {
        return $this->belongsToMany(Siswa::class, 'ujian_siswa')
            ->withPivot('hadir', 'nilai')
            ->withTimestamps();
    }
}
