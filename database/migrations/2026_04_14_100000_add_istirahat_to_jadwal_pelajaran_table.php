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
            if (!Schema::hasColumn('jadwal_pelajaran', 'is_istirahat')) {
                $table->boolean('is_istirahat')->default(false)->after('ruang_id');
            }
        });

        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->dropForeign(['mata_pelajaran_id']);
            $table->dropForeign(['guru_id']);
        });

        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->unsignedBigInteger('mata_pelajaran_id')->nullable()->change();
            $table->unsignedBigInteger('guru_id')->nullable()->change();
        });

        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajaran')->nullOnDelete();
            $table->foreign('guru_id')->references('id')->on('guru')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->dropForeign(['mata_pelajaran_id']);
            $table->dropForeign(['guru_id']);
        });

        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->unsignedBigInteger('mata_pelajaran_id')->nullable(false)->change();
            $table->unsignedBigInteger('guru_id')->nullable(false)->change();
        });

        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajaran')->cascadeOnDelete();
            $table->foreign('guru_id')->references('id')->on('guru')->cascadeOnDelete();

            if (Schema::hasColumn('jadwal_pelajaran', 'is_istirahat')) {
                $table->dropColumn('is_istirahat');
            }
        });
    }
};
