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
        Schema::table('transkrip_nilai_pengaturan', function (Blueprint $table) {
            $table->decimal('grade_a_min', 5, 2)->default(85)->after('bobot_uas');
            $table->decimal('grade_a_max', 5, 2)->default(100)->after('grade_a_min');
            $table->decimal('grade_b_min', 5, 2)->default(70)->after('grade_a_max');
            $table->decimal('grade_b_max', 5, 2)->default(84.99)->after('grade_b_min');
            $table->decimal('grade_c_min', 5, 2)->default(60)->after('grade_b_max');
            $table->decimal('grade_c_max', 5, 2)->default(69.99)->after('grade_c_min');
            $table->decimal('grade_d_min', 5, 2)->default(50)->after('grade_c_max');
            $table->decimal('grade_d_max', 5, 2)->default(59.99)->after('grade_d_min');
            $table->decimal('grade_e_min', 5, 2)->default(0)->after('grade_d_max');
            $table->decimal('grade_e_max', 5, 2)->default(49.99)->after('grade_e_min');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transkrip_nilai_pengaturan', function (Blueprint $table) {
            $table->dropColumn([
                'grade_a_min',
                'grade_a_max',
                'grade_b_min',
                'grade_b_max',
                'grade_c_min',
                'grade_c_max',
                'grade_d_min',
                'grade_d_max',
                'grade_e_min',
                'grade_e_max',
            ]);
        });
    }
};
