<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds the content_type column to the student_progress table
     * to fix the "Unknown column 'content_type' in 'where clause'" error.
     */
    public function up(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            // Add content_type column if it doesn't exist
            if (!Schema::hasColumn('student_progress', 'content_type')) {
                $table->string('content_type')->nullable()->after('course_id');
                
                // Add index for faster queries
                $table->index(['user_id', 'course_id', 'content_type']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            // Drop the index first
            $table->dropIndex(['user_id', 'course_id', 'content_type']);
            
            // Then drop the column
            $table->dropColumn('content_type');
        });
    }
};
