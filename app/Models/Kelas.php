<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'wali_kelas',
    ];

    protected $casts = [
        'tingkat' => 'integer',
    ];

    /**
     * Get the siswa for the kelas.
     */
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    /**
     * Accessor for siswa count
     */
    public function getSiswaCountAttribute()
    {
        return $this->siswa()->count();
    }
}
