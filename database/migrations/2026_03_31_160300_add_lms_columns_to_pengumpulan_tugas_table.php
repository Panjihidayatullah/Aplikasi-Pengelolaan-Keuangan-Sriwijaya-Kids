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
        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            if (!Schema::hasColumn('pengumpulan_tugas', 'file_jawaban_path')) {
                $table->string('file_jawaban_path')->nullable();
            }

            if (!Schema::hasColumn('pengumpulan_tugas', 'catatan_siswa')) {
                $table->text('catatan_siswa')->nullable();
            }

            if (!Schema::hasColumn('pengumpulan_tugas', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable();
            }

            if (!Schema::hasColumn('pengumpulan_tugas', 'graded_by_guru_id')) {
                $table->foreignId('graded_by_guru_id')->nullable()->constrained('guru')->nullOnDelete();
            }

            if (!Schema::hasColumn('pengumpulan_tugas', 'graded_at')) {
                $table->timestamp('graded_at')->nullable();
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
