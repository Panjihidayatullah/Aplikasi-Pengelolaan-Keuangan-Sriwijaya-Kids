<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPembayaran extends Model
{
    use SoftDeletes;

    protected $table = 'jenis_pembayaran';

    protected $fillable = [
        'nama',
        'keterangan',
        'nominal_default',
        'tipe',
        'is_active',
    ];

    protected $casts = [
        'nominal_default' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the pembayaran for the jenis.
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'jenis_pembayaran_id');
    }
}
