<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('student_progress')) {
            // Check if the column already exists
            if (!Schema::hasColumn('student_progress', 'content_id')) {
                Schema::table('student_progress', function (Blueprint $table) {
                    $table->unsignedBigInteger('content_id')->after('content_type')->nullable();
                    echo "Added missing column 'content_id' to student_progress table.\n";
                    Log::info("Added missing column 'content_id' to student_progress table");
                });
            } else {
                echo "Column 'content_id' already exists in student_progress table.\n";
                Log::info("Column 'content_id' already exists in student_progress table");
            }
            
            // If column exists but is not nullable, change it to nullable to avoid issues
            try {
                // Can't directly check if a column is nullable in Laravel Schema builder
                // So we'll alter it to be nullable regardless, which is safe even if it already is
                Schema::table('student_progress', function (Blueprint $table) {
                    $table->unsignedBigInteger('content_id')->nullable()->change();
                    echo "Modified 'content_id' to be nullable in student_progress table.\n";
                    Log::info("Modified 'content_id' to be nullable in student_progress table");
                });
            } catch (\Exception $e) {
                echo "Error modifying 'content_id' column: " . $e->getMessage() . "\n";
                Log::error("Error modifying 'content_id' column: " . $e->getMessage());
            }
        } else {
            echo "Table 'student_progress' does not exist.\n";
            Log::warning("Table 'student_progress' does not exist");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't remove columns in down migrations to prevent data loss
    }
};
