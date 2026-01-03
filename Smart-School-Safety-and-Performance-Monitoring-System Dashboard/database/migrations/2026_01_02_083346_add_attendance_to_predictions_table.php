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
        Schema::table('student_performance_predictions', function (Blueprint $table) {
            $table->decimal('current_attendance', 5, 2)->nullable()->after('current_performance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_performance_predictions', function (Blueprint $table) {
            $table->dropColumn('current_attendance');
        });
    }
};
