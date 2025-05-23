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
        Schema::table('quizzes', function (Blueprint $table) {
            // Add start_date column if it doesn't exist
            if (!Schema::hasColumn('quizzes', 'start_date')) {
                $table->timestamp('start_date')->nullable();
            }
            
            // Add end_date column if it doesn't exist
            if (!Schema::hasColumn('quizzes', 'end_date')) {
                $table->timestamp('end_date')->nullable();
            }
            
            // Add is_published column if it doesn't exist
            if (!Schema::hasColumn('quizzes', 'is_published')) {
                $table->boolean('is_published')->default(false);
            }
            
            // Add questions_json column if it doesn't exist
            if (!Schema::hasColumn('quizzes', 'questions_json')) {
                $table->json('questions_json')->nullable();
            }
            
            // Add max_attempts column if it doesn't exist
            if (!Schema::hasColumn('quizzes', 'max_attempts')) {
                $table->integer('max_attempts')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // List of columns to drop
            $columns = [
                'start_date',
                'end_date',
                'is_published',
                'questions_json',
                'max_attempts'
            ];
            
            // Drop each column if it exists
            foreach ($columns as $column) {
                if (Schema::hasColumn('quizzes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
