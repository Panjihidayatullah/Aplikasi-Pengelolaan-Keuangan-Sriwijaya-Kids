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
        Schema::table('lms_pertemuan', function (Blueprint $table) {
            $table->dropUnique('lms_pertemuan_semester_tanggal_unique');

            $table->foreignId('kelas_id')
                ->nullable()
                ->after('semester_id')
                ->constrained('kelas')
                ->nullOnDelete();

            $table->unique(['semester_id', 'kelas_id', 'tanggal'], 'lms_pertemuan_semester_kelas_tanggal_unique');
            $table->index(['kelas_id', 'tanggal'], 'lms_pertemuan_kelas_tanggal_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_pertemuan', function (Blueprint $table) {
            $table->dropUnique('lms_pertemuan_semester_kelas_tanggal_unique');
            $table->dropIndex('lms_pertemuan_kelas_tanggal_idx');
            $table->dropConstrainedForeignId('kelas_id');

            $table->unique(['semester_id', 'tanggal'], 'lms_pertemuan_semester_tanggal_unique');
        });
    }
};
