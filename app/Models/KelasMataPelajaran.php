<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelasMataPelajaran extends Model
{
    protected $table = 'kelas_mata_pelajaran';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'tahun_ajaran',
        'semester',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
