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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('isi');
            $table->string('tipe'); // jadwal, tugas, nilai, pengumuman, lainnya
            $table->string('terkait_dengan')->nullable(); // Model reference
            $table->unsignedBigInteger('terkait_id')->nullable(); // Model ID reference
            $table->boolean('is_read')->default(false);
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
