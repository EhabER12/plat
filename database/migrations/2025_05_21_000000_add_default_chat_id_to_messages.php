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
        // Check if messages table exists
        if (Schema::hasTable('messages')) {
            // Check if chat_id column exists
            if (Schema::hasColumn('messages', 'chat_id')) {
                // Modify chat_id to have a default value of 0
                Schema::table('messages', function (Blueprint $table) {
                    $table->unsignedBigInteger('chat_id')->default(0)->change();
                });
            } else {
                // Add chat_id column with default value of 0
                Schema::table('messages', function (Blueprint $table) {
                    $table->unsignedBigInteger('chat_id')->default(0)->after('message_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to remove the column or change it back
    }
};
