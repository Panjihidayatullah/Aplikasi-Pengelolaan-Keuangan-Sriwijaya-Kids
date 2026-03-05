<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPengeluaran extends Model
{
    use SoftDeletes;

    protected $table = 'jenis_pengeluaran';

    protected $fillable = [
        'nama',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the pengeluaran for the jenis.
     */
    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'jenis_pengeluaran_id');
    }
}
