<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->boolean('is_tingkat_akhir')->default(false)->after('tingkat');
        });

        // Set existing classes with tingkat 6 to be tingkat_akhir
        DB::table('kelas')->where('tingkat', 6)->update(['is_tingkat_akhir' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('is_tingkat_akhir');
        });
    }
};
