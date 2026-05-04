<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the pembayaran for the siswa.
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'siswa_id');
    }

    /**
     * Get kartu pelajar for the siswa.
     */
    public function kartuPelajar()
    {
        return $this->hasMany(KartuPelajar::class, 'siswa_id');
    }

    /**
     * Get transkrip nilai for the siswa.
     */
    public function transkripsNilai()
    {
        return $this->hasMany(TranskripsNilai::class, 'siswa_id');
    }

    /**
     * Get kenaikan kelas for the siswa.
     */
    public function kenaikanKelas()
    {
        return $this->hasMany(KenaikanKelas::class, 'siswa_id');
    }

    /**
     * Get ujian yang diikuti.
     */
    public function ujian()
    {
        return $this->belongsToMany(Ujian::class, 'ujian_siswa')
            ->withPivot('hadir', 'nilai')
            ->withTimestamps();
    }

    public function pengumpulanTugas()
    {
        return $this->hasMany(PengumpulanTugas::class, 'siswa_id');
    }
}
