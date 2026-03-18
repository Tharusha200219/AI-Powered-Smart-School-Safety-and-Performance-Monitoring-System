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
            $table->integer('grade_level');
            $table->string('section', 10);
            $table->json('arrangement');
            $table->json('students_data');
            $table->timestamp('generated_at')->nullable();
            $table->unsignedBigInteger('generated_by');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for faster querying
            $table->index(['grade_level', 'section', 'is_active']);
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seating_arrangements');
    }
};
