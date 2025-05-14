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
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Add the correct_answers_count column if it doesn't exist
            if (!Schema::hasColumn('quiz_attempts', 'correct_answers_count')) {
                $table->integer('correct_answers_count')->nullable()->default(0)->after('is_passed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Remove the correct_answers_count column if it exists
            if (Schema::hasColumn('quiz_attempts', 'correct_answers_count')) {
                $table->dropColumn('correct_answers_count');
            }
        });
    }
};
