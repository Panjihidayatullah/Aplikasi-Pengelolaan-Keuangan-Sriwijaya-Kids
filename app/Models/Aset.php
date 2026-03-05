<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aset extends Model
{
    use SoftDeletes;

    protected $table = 'aset';

    protected $fillable = [
        'nama',
        'kategori',
        'tanggal_perolehan',
        'harga_perolehan',
        'kondisi',
        'lokasi',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'harga_perolehan' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
