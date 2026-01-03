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
        Schema::create('student_performance_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects', 'id')->onDelete('cascade');
            $table->string('academic_year', 20);
            $table->integer('term');
            $table->decimal('current_performance', 5, 2); // Current marks/percentage
            $table->decimal('predicted_performance', 5, 2); // Predicted marks/percentage
            $table->enum('prediction_trend', ['improving', 'stable', 'declining']);
            $table->decimal('confidence', 5, 2); // Confidence score (0-100)
            $table->text('recommendations')->nullable(); // AI recommendations
            $table->timestamp('predicted_at');
            $table->timestamps();

            // Indexes
            $table->index(['student_id', 'subject_id', 'academic_year', 'term'], 'perf_pred_student_subject_year_term');
            $table->index('predicted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_performance_predictions');
    }
};
