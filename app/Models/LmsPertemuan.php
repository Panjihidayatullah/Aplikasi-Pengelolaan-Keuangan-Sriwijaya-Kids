<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsPertemuan extends Model
{
    protected $table = 'lms_pertemuan';

    protected $fillable = [
        'semester_id',
        'kelas_id',
        'tanggal',
        'selected_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function selectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'selected_by');
    }
}
