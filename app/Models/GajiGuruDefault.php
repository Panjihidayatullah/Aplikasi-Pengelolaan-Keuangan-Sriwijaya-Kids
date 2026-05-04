<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GajiGuruDefault extends Model
{
    use SoftDeletes;

    protected $table = 'gaji_guru_default';

    protected $fillable = [
        'guru_id',
        'nominal',
        'keterangan',
        'tanggal_gaji',
        'auto_gaji',
        'is_active',
    ];

    protected $casts = [
        'nominal'     => 'decimal:2',
        'tanggal_gaji'=> 'integer',
        'auto_gaji'   => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}
