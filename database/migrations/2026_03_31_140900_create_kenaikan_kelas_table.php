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
        Schema::create('kenaikan_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('kelas_sekarang_id')->constrained('kelas')->onDelete('restrict');
            $table->foreignId('kelas_tujuan_id')->nullable()->constrained('kelas')->onDelete('restrict');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('restrict');
            $table->string('status'); // naik, tinggal, lulus
            $table->decimal('rata_rata_nilai', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->date('tanggal_penetapan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kenaikan_kelas');
    }
};
