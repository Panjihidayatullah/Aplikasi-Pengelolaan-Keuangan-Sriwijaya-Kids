<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TranskripsNilai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transkrip_nilai';
    protected $fillable = ['siswa_id', 'mata_pelajaran_id', 'semester_id', 'tahun_ajaran_id', 'nilai_harian', 'nilai_uts', 'nilai_uas', 'nilai_akhir', 'grade', 'catatan'];
    protected $casts = [
        'nilai_harian' => 'decimal:2',
        'nilai_uts' => 'decimal:2',
        'nilai_uas' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public static function bobotPersenAktif(): array
    {
        $pengaturan = TranskripNilaiPengaturan::getOrCreateDefault();

        return [
            'tugas' => (float) $pengaturan->bobot_tugas,
            'uts' => (float) $pengaturan->bobot_uts,
            'uas' => (float) $pengaturan->bobot_uas,
        ];
    }

    public static function hitungNilaiAkhir(
        float $nilaiTugas,
        float $nilaiUts,
        float $nilaiUas,
        ?array $bobotPersen = null
    ): float {
        $bobot = $bobotPersen ?? static::bobotPersenAktif();

        $nilaiAkhir = ($nilaiTugas * ((float) ($bobot['tugas'] ?? 30) / 100))
            + ($nilaiUts * ((float) ($bobot['uts'] ?? 30) / 100))
            + ($nilaiUas * ((float) ($bobot['uas'] ?? 40) / 100));

        return round($nilaiAkhir, 2);
    }

    public static function tentukanGrade(float $nilaiAkhir): string
    {
        $pengaturan = TranskripNilaiPengaturan::getOrCreateDefault();

        foreach ($pengaturan->gradeRanges() as $grade => $range) {
            $min = (float) ($range['min'] ?? 0);
            $max = (float) ($range['max'] ?? 100);

            if ($nilaiAkhir >= $min && $nilaiAkhir <= $max) {
                return $grade;
            }
        }

        // Fallback if ranges are misconfigured.
        if ($nilaiAkhir >= 85) {
            return 'A';
        }
        if ($nilaiAkhir >= 70) {
            return 'B';
        }
        if ($nilaiAkhir >= 60) {
            return 'C';
        }
        if ($nilaiAkhir >= 50) {
            return 'D';
        }

        return 'E';
    }

    public static function hitungNilaiDanGrade(
        float $nilaiTugas,
        float $nilaiUts,
        float $nilaiUas,
        ?array $bobotPersen = null
    ): array {
        $nilaiAkhir = static::hitungNilaiAkhir($nilaiTugas, $nilaiUts, $nilaiUas, $bobotPersen);

        return [
            'nilai_akhir' => $nilaiAkhir,
            'grade' => static::tentukanGrade($nilaiAkhir),
        ];
    }

    /**
     * Calculate final grade
     * Uses active bobot configuration.
     */
    public function calculateNilaiAkhir()
    {
        if ($this->nilai_harian !== null && $this->nilai_uts !== null && $this->nilai_uas !== null) {
            $this->nilai_akhir = static::hitungNilaiAkhir(
                (float) $this->nilai_harian,
                (float) $this->nilai_uts,
                (float) $this->nilai_uas
            );
            $this->updateGrade();
        }
    }

    /**
     * Determine grade based on nilai_akhir
     */
    public function updateGrade()
    {
        $this->grade = static::tentukanGrade((float) $this->nilai_akhir);
    }
}
