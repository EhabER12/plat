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
        // First, check if the user_roles table exists
        if (Schema::hasTable('user_roles')) {
            // Get all user IDs
            $userIds = DB::table('users')->pluck('user_id');
            
            // Add student role to each user
            foreach ($userIds as $userId) {
                // Check if the user already has a student role
                $exists = DB::table('user_roles')
                    ->where('user_id', $userId)
                    ->where('role', 'student')
                    ->exists();
                
                // If not, add it
                if (!$exists) {
                    DB::table('user_roles')->insert([
                        'user_id' => $userId,
                        'role' => 'student'
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove student roles
        if (Schema::hasTable('user_roles')) {
            DB::table('user_roles')
                ->where('role', 'student')
                ->delete();
        }
    }
};
