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
        Schema::create('gaji_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengeluaran_id')->unique()->constrained('pengeluaran')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('guru')->restrictOnDelete();
            $table->unsignedTinyInteger('periode_bulan');
            $table->unsignedSmallInteger('periode_tahun');
            $table->text('detail')->nullable();
            $table->foreignId('dibayar_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['guru_id', 'periode_tahun', 'periode_bulan'], 'gaji_guru_periode_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji_guru');
    }
};
