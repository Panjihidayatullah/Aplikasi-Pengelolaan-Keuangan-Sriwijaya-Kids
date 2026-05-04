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
        Schema::create('kartu_pelajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->string('nomor_kartu')->unique();
            $table->string('nis_otomatis')->unique(); // Auto-generated NIS
            $table->date('tanggal_terbit');
            $table->date('tanggal_berlaku_akhir')->nullable();
            $table->string('status')->default('aktif'); // aktif, expired, dibatalkan
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_pelajar');
    }
};
