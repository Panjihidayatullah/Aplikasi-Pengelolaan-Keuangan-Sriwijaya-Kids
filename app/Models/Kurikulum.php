<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kurikulum extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kurikulum';
    protected $fillable = ['nama', 'deskripsi', 'tahun_berlaku', 'is_active'];

    public function tahunAjaran()
    {
        return $this->hasMany(TahunAjaran::class);
    }
}
