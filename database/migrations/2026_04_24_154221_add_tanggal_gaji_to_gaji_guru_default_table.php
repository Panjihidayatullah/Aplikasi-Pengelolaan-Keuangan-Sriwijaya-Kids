<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gaji_guru_default', function (Blueprint $table) {
            // Tanggal penggajian dalam sebulan (1-28)
            $table->tinyInteger('tanggal_gaji')->nullable()->after('keterangan')
                  ->comment('Tanggal penggajian otomatis tiap bulan (1-28), null = tidak otomatis');
            // Toggle aktif otomatis
            $table->boolean('auto_gaji')->default(false)->after('tanggal_gaji');
        });
    }

    public function down(): void
    {
        Schema::table('gaji_guru_default', function (Blueprint $table) {
            $table->dropColumn(['tanggal_gaji', 'auto_gaji']);
        });
    }
};
