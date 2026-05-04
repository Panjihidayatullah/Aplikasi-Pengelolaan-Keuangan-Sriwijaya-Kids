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
        Schema::create('transkrip_nilai_pengaturan', function (Blueprint $table) {
            $table->id();
            $table->decimal('bobot_tugas', 5, 2)->default(30);
            $table->decimal('bobot_uts', 5, 2)->default(30);
            $table->decimal('bobot_uas', 5, 2)->default(40);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transkrip_nilai_pengaturan');
    }
};
