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
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'attendance_rfid_enabled')) {
                $table->boolean('attendance_rfid_enabled')->default(true)->after('language');
            }
            if (!Schema::hasColumn('settings', 'attendance_face_enabled')) {
                $table->boolean('attendance_face_enabled')->default(false)->after('attendance_rfid_enabled');
            }
            if (!Schema::hasColumn('settings', 'attendance_two_factor')) {
                $table->boolean('attendance_two_factor')->default(false)->after('attendance_face_enabled');
            }
            if (!Schema::hasColumn('settings', 'face_recognition_api_url')) {
                $table->string('face_recognition_api_url')->nullable()->after('attendance_two_factor');
            }
            if (!Schema::hasColumn('settings', 'face_recognition_api_key')) {
                $table->string('face_recognition_api_key')->nullable()->after('face_recognition_api_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'attendance_rfid_enabled')) {
                $table->dropColumn('attendance_rfid_enabled');
            }
            if (Schema::hasColumn('settings', 'attendance_face_enabled')) {
                $table->dropColumn('attendance_face_enabled');
            }
            if (Schema::hasColumn('settings', 'attendance_two_factor')) {
                $table->dropColumn('attendance_two_factor');
            }
            if (Schema::hasColumn('settings', 'face_recognition_api_url')) {
                $table->dropColumn('face_recognition_api_url');
            }
            if (Schema::hasColumn('settings', 'face_recognition_api_key')) {
                $table->dropColumn('face_recognition_api_key');
            }
        });
    }
};
