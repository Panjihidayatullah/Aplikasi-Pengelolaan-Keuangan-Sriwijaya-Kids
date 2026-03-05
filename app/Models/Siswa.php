<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $table = 'siswa';

    protected $fillable = [
        'kelas_id',
        'nis',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'telepon',
        'email',
        'nama_ayah',
        'telepon_ayah',
        'nama_ibu',
        'telepon_ibu',
        'foto',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the kelas that owns the siswa.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Get the pembayaran for the siswa.
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'siswa_id');
    }
}
