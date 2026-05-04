<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengeluaran extends Model
{
    use SoftDeletes;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'kode_transaksi',
        'jenis_pengeluaran_id',
        'user_id',
        'tanggal',
        'jumlah',
        'keterangan',
        'bukti_file',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    /**
     * Get the jenis pengeluaran that owns the pengeluaran.
     */
    public function jenis()
    {
        return $this->belongsTo(JenisPengeluaran::class, 'jenis_pengeluaran_id');
    }

    /**
     * Get the user that created the pengeluaran.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get salary record when this expense is teacher payroll.
     */
    public function gajiGuru()
    {
        return $this->hasOne(GajiGuru::class, 'pengeluaran_id');
    }
}
