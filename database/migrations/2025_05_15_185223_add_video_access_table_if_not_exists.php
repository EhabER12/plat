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
        // First check if the table already exists to avoid errors
        if (!Schema::hasTable('video_access')) {
            Schema::create('video_access', function (Blueprint $table) {
                $table->id('access_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('video_id');
                $table->string('token', 64)->unique();
                $table->timestamp('expires_at');
                $table->timestamp('last_accessed_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('fingerprint')->nullable();
                $table->timestamps();

                // Add unique constraint to prevent duplicate entries
                $table->unique(['user_id', 'video_id']);
                
                // Add foreign keys only if the referenced tables exist
                if (Schema::hasTable('users') && Schema::hasColumn('users', 'user_id')) {
                    $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                }
                
                if (Schema::hasTable('course_videos') && Schema::hasColumn('course_videos', 'video_id')) {
                    $table->foreign('video_id')->references('video_id')->on('course_videos')->onDelete('cascade');
                }
            });
            
            DB::statement('ALTER TABLE video_access COMMENT = "Stores access tokens for video streaming"');
            
            echo "Created video_access table successfully.\n";
        } else {
            echo "Table video_access already exists, no changes made.\n";
            
            // If the table exists but missing the fingerprint column, add it
            if (!Schema::hasColumn('video_access', 'fingerprint')) {
                Schema::table('video_access', function (Blueprint $table) {
                    $table->string('fingerprint')->nullable()->after('user_agent');
                });
                echo "Added missing fingerprint column to video_access table.\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do nothing in down() method to prevent accidentally dropping the table
        // This is safer than attempting to drop the table
    }
};
