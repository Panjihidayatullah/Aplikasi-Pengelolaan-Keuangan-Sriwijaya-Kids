<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranskripNilaiPengaturan extends Model
{
    protected $table = 'transkrip_nilai_pengaturan';

    protected $fillable = [
        'bobot_tugas',
        'bobot_uts',
        'bobot_uas',
        'grade_a_min',
        'grade_a_max',
        'grade_b_min',
        'grade_b_max',
        'grade_c_min',
        'grade_c_max',
        'grade_d_min',
        'grade_d_max',
        'grade_e_min',
        'grade_e_max',
    ];

    protected $casts = [
        'bobot_tugas' => 'decimal:2',
        'bobot_uts' => 'decimal:2',
        'bobot_uas' => 'decimal:2',
        'grade_a_min' => 'decimal:2',
        'grade_a_max' => 'decimal:2',
        'grade_b_min' => 'decimal:2',
        'grade_b_max' => 'decimal:2',
        'grade_c_min' => 'decimal:2',
        'grade_c_max' => 'decimal:2',
        'grade_d_min' => 'decimal:2',
        'grade_d_max' => 'decimal:2',
        'grade_e_min' => 'decimal:2',
        'grade_e_max' => 'decimal:2',
    ];

    public static function getOrCreateDefault(): self
    {
        return static::query()->firstOrCreate([], [
            'bobot_tugas' => 30,
            'bobot_uts' => 30,
            'bobot_uas' => 40,
            'grade_a_min' => 85,
            'grade_a_max' => 100,
            'grade_b_min' => 70,
            'grade_b_max' => 84.99,
            'grade_c_min' => 60,
            'grade_c_max' => 69.99,
            'grade_d_min' => 50,
            'grade_d_max' => 59.99,
            'grade_e_min' => 0,
            'grade_e_max' => 49.99,
        ]);
    }

    public function gradeRanges(): array
    {
        return [
            'A' => ['min' => (float) $this->grade_a_min, 'max' => (float) $this->grade_a_max],
            'B' => ['min' => (float) $this->grade_b_min, 'max' => (float) $this->grade_b_max],
            'C' => ['min' => (float) $this->grade_c_min, 'max' => (float) $this->grade_c_max],
            'D' => ['min' => (float) $this->grade_d_min, 'max' => (float) $this->grade_d_max],
            'E' => ['min' => (float) $this->grade_e_min, 'max' => (float) $this->grade_e_max],
        ];
    }
}
