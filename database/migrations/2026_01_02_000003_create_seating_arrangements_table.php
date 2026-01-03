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
        Schema::create('seating_arrangements', function (Blueprint $table) {
            $table->id();
            $table->string('grade_level', 20);
            $table->string('section', 10)->nullable();
            $table->foreignId('class_id')->nullable()->constrained('school_classes', 'id')->onDelete('cascade');
            $table->string('academic_year', 20);
            $table->integer('term');
            $table->integer('total_rows')->default(6);
            $table->integer('seats_per_row')->default(5);
            $table->json('arrangement_data'); // Complete seating arrangement data
            $table->foreignId('generated_by')->constrained('users', 'id')->onDelete('cascade');
            $table->timestamp('generated_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['grade_level', 'section', 'academic_year', 'term'], 'seat_arr_grade_section_year_term');
            $table->index('is_active');
        });

        Schema::create('student_seat_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seating_arrangement_id')->constrained('seating_arrangements')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');
            $table->integer('row_number');
            $table->integer('seat_number');
            $table->string('seat_position', 20); // e.g., 'Row 1 - Seat 3'
            $table->timestamps();

            // Indexes and unique constraint
            $table->index('student_id');
            $table->unique(['seating_arrangement_id', 'row_number', 'seat_number'], 'unique_seat_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_seat_assignments');
        Schema::dropIfExists('seating_arrangements');
    }
};
