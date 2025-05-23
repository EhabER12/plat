<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds the student_id column to the exam_attempts table
     * to fix the "Unknown column 'student_id' in 'where clause'" error.
     */
    public function up(): void
    {
        // First, add the column
        Schema::table('exam_attempts', function (Blueprint $table) {
            // Add student_id column if it doesn't exist
            if (!Schema::hasColumn('exam_attempts', 'student_id')) {
                $table->string('student_id')->nullable()->after('user_id');
                
                // Add index for faster querying
                $table->index('student_id');
            }
        });
        
        // Then update the values (only after the column exists)
        if (Schema::hasColumn('exam_attempts', 'student_id')) {
            DB::statement('UPDATE exam_attempts SET student_id = user_id');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            // Drop index first
            if (Schema::hasColumn('exam_attempts', 'student_id')) {
                $table->dropIndex(['student_id']);
                $table->dropColumn('student_id');
            }
        });
    }
};
