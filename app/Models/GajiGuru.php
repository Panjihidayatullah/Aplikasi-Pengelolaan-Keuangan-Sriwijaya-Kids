<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GajiGuru extends Model
{
    use SoftDeletes;

    protected $table = 'gaji_guru';

    protected $fillable = [
        'pengeluaran_id',
        'guru_id',
        'periode_bulan',
        'periode_tahun',
        'detail',
        'dibayar_oleh_user_id',
    ];

    /**
     * Get the pengeluaran linked to this salary record.
     */
    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'pengeluaran_id');
    }

    /**
     * Get the teacher receiving this salary.
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Get user who recorded the salary payment.
     */
    public function dibayarOleh()
    {
        return $this->belongsTo(User::class, 'dibayar_oleh_user_id');
    }
}
