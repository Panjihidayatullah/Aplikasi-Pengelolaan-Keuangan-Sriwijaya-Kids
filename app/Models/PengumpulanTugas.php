<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengumpulanTugas extends Model
{
    use SoftDeletes;

    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'tugas_id',
        'siswa_id',
        'file_jawaban',
        'file_jawaban_path',
        'keterangan_siswa',
        'catatan_siswa',
        'tanggal_kumpul',
        'submitted_at',
        'status',
        'nilai',
        'feedback',
        'dinilai_oleh',
        'graded_by_guru_id',
        'tanggal_dinilai',
        'graded_at',
    ];

    protected $casts = [
        'tanggal_kumpul' => 'datetime',
        'submitted_at' => 'datetime',
        'tanggal_dinilai' => 'datetime',
        'graded_at' => 'datetime',
        'nilai' => 'decimal:2',
    ];

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function dinilaiOleh(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'graded_by_guru_id');
    }
}
