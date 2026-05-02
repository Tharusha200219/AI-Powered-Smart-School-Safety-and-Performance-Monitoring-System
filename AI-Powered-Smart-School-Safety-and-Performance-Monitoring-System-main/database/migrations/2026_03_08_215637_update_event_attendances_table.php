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
        Schema::table('event_attendances', function (Blueprint $table) {
            $table->renameColumn('scanned_at', 'check_in_time');
            $table->timestamp('check_out_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_attendances', function (Blueprint $table) {
            $table->renameColumn('check_in_time', 'scanned_at');
            $table->dropColumn('check_out_time');
        });
    }
};
