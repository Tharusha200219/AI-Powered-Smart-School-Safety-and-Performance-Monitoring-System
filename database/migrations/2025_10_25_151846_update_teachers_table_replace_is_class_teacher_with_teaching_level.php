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
        Schema::table('teachers', function (Blueprint $table) {
            // Remove is_class_teacher field
            if (Schema::hasColumn('teachers', 'is_class_teacher')) {
                $table->dropColumn('is_class_teacher');
            }

            // Add teaching_level field
            $table->enum('teaching_level', [
                'Primary',
                'Secondary',
                'A/L-Arts',
                'A/L-Commerce',
                'A/L-Science',
                'A/L-Technology'
            ])->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Drop teaching_level field
            if (Schema::hasColumn('teachers', 'teaching_level')) {
                $table->dropColumn('teaching_level');
            }

            // Add back is_class_teacher field
            $table->boolean('is_class_teacher')->default(false)->after('is_active');
        });
    }
};
