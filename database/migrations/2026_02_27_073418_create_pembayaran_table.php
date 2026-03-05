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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('jenis_pembayaran_id')->constrained('jenis_pembayaran')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal_bayar');
            $table->integer('bulan')->nullable();
            $table->integer('tahun')->nullable();
            $table->decimal('jumlah', 12, 2);
            $table->enum('metode_bayar', ['Tunai', 'Transfer', 'QRIS'])->default('Tunai');
            $table->enum('status', ['Pending', 'Lunas', 'Dibatalkan'])->default('Lunas');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
