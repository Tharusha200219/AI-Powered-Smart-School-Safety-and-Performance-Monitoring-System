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
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events', 'id')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');
            $table->string('nfc_tag_id', 50);
            $table->timestamp('scanned_at')->useCurrent();
            $table->timestamps();

            // Unique constraint to prevent duplicate scans for the same student in the same event
            $table->unique(['event_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
    }
};
