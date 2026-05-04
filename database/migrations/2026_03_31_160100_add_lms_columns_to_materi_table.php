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
        Schema::table('materi', function (Blueprint $table) {
            if (!Schema::hasColumn('materi', 'judul')) {
                $table->string('judul')->nullable();
            }

            if (!Schema::hasColumn('materi', 'tipe')) {
                $table->enum('tipe', ['pdf', 'video', 'ppt', 'link'])->default('pdf');
            }

            if (!Schema::hasColumn('materi', 'video_url')) {
                $table->string('video_url')->nullable();
            }

            if (!Schema::hasColumn('materi', 'kelas_id')) {
                $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            }

            if (!Schema::hasColumn('materi', 'mata_pelajaran_id')) {
                $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajaran')->nullOnDelete();
            }

            if (!Schema::hasColumn('materi', 'guru_id')) {
                $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            }

            if (!Schema::hasColumn('materi', 'tahun_ajaran_id')) {
                $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->nullOnDelete();
            }

            if (!Schema::hasColumn('materi', 'semester_id')) {
                $table->foreignId('semester_id')->nullable()->constrained('semester')->nullOnDelete();
            }

            if (!Schema::hasColumn('materi', 'is_published')) {
                $table->boolean('is_published')->default(true);
            }

            if (!Schema::hasColumn('materi', 'published_at')) {
                $table->timestamp('published_at')->nullable();
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
