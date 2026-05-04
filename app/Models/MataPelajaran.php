<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class MataPelajaran extends Model
{
    use SoftDeletes;

    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'deskripsi',
        'is_active',
    ];

    protected $appends = ['nama'];

    public function getNamaAttribute()
    {
        return $this->nama_mapel;
    }

    public function scopeForDropdown(Builder $query): Builder
    {
        return $query
            ->select(['id', 'kode_mapel', 'nama_mapel', 'is_active'])
            ->orderByRaw('LOWER(TRIM(nama_mapel))')
            ->orderBy('id');
    }

    public static function dropdownOptions(?Builder $query = null): Collection
    {
        $baseQuery = $query ?: static::query();

        return static::dedupeForDropdown($baseQuery->forDropdown()->get());
    }

    public static function equivalentIds(?int $mataPelajaranId): array
    {
        if (!$mataPelajaranId) {
            return [];
        }

        $selected = static::query()
            ->select(['id', 'nama_mapel'])
            ->find($mataPelajaranId);

        if (!$selected) {
            return [$mataPelajaranId];
        }

        $normalizedName = strtolower(trim((string) $selected->nama_mapel));
        if ($normalizedName === '') {
            return [$mataPelajaranId];
        }

        return static::query()
            ->whereRaw('LOWER(TRIM(nama_mapel)) = ?', [$normalizedName])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private static function dedupeForDropdown(Collection $mapels): Collection
    {
        return $mapels
            ->filter(fn ($mapel) => trim((string) $mapel->nama_mapel) !== '')
            ->groupBy(fn ($mapel) => strtolower(trim((string) $mapel->nama_mapel)))
            ->map(function (Collection $group) {
                return $group
                    ->sortBy([
                        fn ($mapel) => $mapel->is_active ? 0 : 1,
                        fn ($mapel) => strtolower((string) ($mapel->kode_mapel ?? '')),
                        fn ($mapel) => (int) $mapel->id,
                    ])
                    ->first();
            })
            ->sortBy(fn ($mapel) => strtolower(trim((string) $mapel->nama_mapel)))
            ->values();
    }
}
