<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembayaran extends Model
{
    use SoftDeletes;

    protected $table = 'pembayaran';

    protected $fillable = [
        'kode_transaksi',
        'siswa_id',
        'jenis_pembayaran_id',
        'user_id',
        'tanggal_bayar',
        'bulan',
        'tahun',
        'jumlah',
        'metode_bayar',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah' => 'decimal:2',
    ];

    /**
     * Get the siswa that owns the pembayaran.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Get the jenis pembayaran that owns the pembayaran.
     */
    public function jenis()
    {
        return $this->belongsTo(JenisPembayaran::class, 'jenis_pembayaran_id');
    }

    /**
     * Get the user that created the pembayaran.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
