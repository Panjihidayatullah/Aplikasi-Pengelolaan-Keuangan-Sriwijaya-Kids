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
        Schema::create('lms_pertemuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semester')->cascadeOnDelete();
            $table->date('tanggal');
            $table->foreignId('selected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['semester_id', 'tanggal'], 'lms_pertemuan_semester_tanggal_unique');
            $table->index(['tanggal'], 'lms_pertemuan_tanggal_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_pertemuan');
    }
};
