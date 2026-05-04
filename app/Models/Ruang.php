<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruang extends Model
{
    use SoftDeletes;

    protected $table = 'ruang';

    protected $fillable = [
        'kode_ruang',
        'nama_ruang',
        'lokasi',
        'kapasitas',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'kapasitas' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['nama'];

    public function getNamaAttribute(): string
    {
        return (string) $this->nama_ruang;
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'ruang_id');
    }
}
