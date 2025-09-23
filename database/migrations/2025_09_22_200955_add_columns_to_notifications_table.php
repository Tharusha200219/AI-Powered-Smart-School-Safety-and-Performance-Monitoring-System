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
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('type')->after('id'); // 'created', 'updated', 'deleted'
            $table->string('title')->after('type'); // "Student Created", "Teacher Updated", etc.
            $table->text('message')->after('title'); // Detailed message
            $table->string('entity_type')->after('message'); // 'Student', 'Teacher', 'Subject', etc.
            $table->unsignedBigInteger('entity_id')->after('entity_type'); // ID of the affected entity
            $table->unsignedBigInteger('user_id')->after('entity_id'); // User who performed the action
            $table->string('user_name')->after('user_id'); // Name of the user (cached for performance)
            $table->boolean('is_read')->default(false)->after('user_name');
            $table->json('data')->nullable()->after('is_read'); // Additional data like old/new values

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['is_read', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['is_read', 'created_at']);
            $table->dropIndex(['entity_type', 'entity_id']);
            $table->dropColumn([
                'type',
                'title',
                'message',
                'entity_type',
                'entity_id',
                'user_id',
                'user_name',
                'is_read',
                'data'
            ]);
        });
    }
};
