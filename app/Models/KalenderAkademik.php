<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KalenderAkademik extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kalender_akademik';
    protected $fillable = ['tahun_ajaran_id', 'nama_kegiatan', 'deskripsi', 'tipe', 'tanggal_mulai', 'tanggal_selesai', 'warna'];
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
