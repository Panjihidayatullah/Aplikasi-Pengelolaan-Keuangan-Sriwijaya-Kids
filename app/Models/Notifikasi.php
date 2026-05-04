<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notifikasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notifikasi';
    protected $fillable = ['user_id', 'judul', 'isi', 'tipe', 'terkait_dengan', 'terkait_id', 'is_read', 'dibaca_pada'];
    protected $casts = [
        'is_read' => 'boolean',
        'dibaca_pada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'dibaca_pada' => now(),
        ]);
    }
}
