<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guru extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'guru';

    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'telepon',
        'email',
        'pendidikan_terakhir',
        'foto',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user associated with the guru.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get mata pelajaran that guru teaches.
     */
    public function mataPelajaran()
    {
        return $this->hasMany(MataPelajaran::class, 'guru_id');
    }

    /**
     * Get wali kelas assignment.
     */
    public function guruWaliKelas()
    {
        return $this->hasMany(GuruWaliKelas::class, 'guru_id');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'guru_id');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'guru_id');
    }

    /**
     * Get salary history for this teacher.
     */
    public function gajiGuru()
    {
        return $this->hasMany(GajiGuru::class, 'guru_id');
    }
}

