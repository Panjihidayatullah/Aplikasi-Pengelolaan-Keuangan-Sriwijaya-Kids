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
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->foreignId('ruang_id')
                ->nullable()
                ->after('guru_id')
                ->constrained('ruang')
                ->nullOnDelete();

            $table->index(['ruang_id', 'hari', 'jam_mulai'], 'jadwal_ruang_hari_mulai_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->dropIndex('jadwal_ruang_hari_mulai_idx');
            $table->dropForeign(['ruang_id']);
            $table->dropColumn('ruang_id');
        });
    }
};
