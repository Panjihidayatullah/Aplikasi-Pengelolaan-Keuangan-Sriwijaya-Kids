<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            if (!Schema::hasColumn('tugas', 'judul')) {
                $table->string('judul')->nullable();
            }

            if (!Schema::hasColumn('tugas', 'deskripsi')) {
                $table->text('deskripsi')->nullable();
            }

            if (!Schema::hasColumn('tugas', 'instruksi')) {
                $table->text('instruksi')->nullable();
            }

            if (!Schema::hasColumn('tugas', 'lampiran_path')) {
                $table->string('lampiran_path')->nullable();
            }

            if (!Schema::hasColumn('tugas', 'tanggal_deadline')) {
                $table->dateTime('tanggal_deadline')->nullable();
            }

            if (!Schema::hasColumn('tugas', 'max_nilai')) {
                $table->decimal('max_nilai', 5, 2)->default(100);
            }

            if (!Schema::hasColumn('tugas', 'kelas_id')) {
                $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            }

            if (!Schema::hasColumn('tugas', 'mata_pelajaran_id')) {
                $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajaran')->nullOnDelete();
            }

            if (!Schema::hasColumn('tugas', 'guru_id')) {
                $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            }

            if (!Schema::hasColumn('tugas', 'tahun_ajaran_id')) {
                $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->nullOnDelete();
            }

            if (!Schema::hasColumn('tugas', 'semester_id')) {
                $table->foreignId('semester_id')->nullable()->constrained('semester')->nullOnDelete();
            }

            if (!Schema::hasColumn('tugas', 'is_published')) {
                $table->boolean('is_published')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank to avoid dropping existing production columns.
    }
};
