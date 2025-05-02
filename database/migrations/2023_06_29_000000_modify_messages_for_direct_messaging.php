<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('messages')) {
            // First copy user_id to sender_id if user_id exists and sender_id doesn't
            if (Schema::hasColumn('messages', 'user_id') && !Schema::hasColumn('messages', 'sender_id')) {
                Schema::table('messages', function (Blueprint $table) {
                    $table->unsignedBigInteger('sender_id')->nullable()->after('user_id');
                });
                
                // Copy data from user_id to sender_id
                DB::statement('UPDATE messages SET sender_id = user_id WHERE sender_id IS NULL');
            }
            
            // Add receiver_id if it doesn't exist
            if (!Schema::hasColumn('messages', 'receiver_id')) {
                Schema::table('messages', function (Blueprint $table) {
                    $table->unsignedBigInteger('receiver_id')->nullable()->after('sender_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('messages')) {
            if (Schema::hasColumn('messages', 'receiver_id')) {
                Schema::table('messages', function (Blueprint $table) {
                    // Check if foreign key exists before dropping
                    try {
                        $table->dropForeign(['receiver_id']);
                    } catch (\Exception $e) {
                        // Foreign key doesn't exist or other error
                    }
                    $table->dropColumn('receiver_id');
                });
            }
            
            if (Schema::hasColumn('messages', 'sender_id')) {
                Schema::table('messages', function (Blueprint $table) {
                    // Check if foreign key exists before dropping
                    try {
                        $table->dropForeign(['sender_id']);
                    } catch (\Exception $e) {
                        // Foreign key doesn't exist or other error
                    }
                    $table->dropColumn('sender_id');
                });
            }
        }
    }
}; 