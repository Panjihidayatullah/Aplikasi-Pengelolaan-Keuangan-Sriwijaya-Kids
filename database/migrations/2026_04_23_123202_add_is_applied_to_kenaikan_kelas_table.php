<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kenaikan_kelas', function (Blueprint $table) {
            $table->boolean('is_applied')->default(false)->after('tanggal_penetapan');
        });
    }

    public function down(): void
    {
        Schema::table('kenaikan_kelas', function (Blueprint $table) {
            $table->dropColumn('is_applied');
        });
    }
};
