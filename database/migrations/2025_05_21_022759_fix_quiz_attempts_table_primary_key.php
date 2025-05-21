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
        // Check if quiz_attempts table exists
        if (Schema::hasTable('quiz_attempts')) {
            // Check if the table has 'id' column but not 'attempt_id'
            if (Schema::hasColumn('quiz_attempts', 'id') && !Schema::hasColumn('quiz_attempts', 'attempt_id')) {
                // Rename 'id' to 'attempt_id'
                Schema::table('quiz_attempts', function (Blueprint $table) {
                    $table->renameColumn('id', 'attempt_id');
                });
            }
            // If both 'id' and 'attempt_id' exist, keep 'attempt_id' and drop 'id'
            elseif (Schema::hasColumn('quiz_attempts', 'id') && Schema::hasColumn('quiz_attempts', 'attempt_id')) {
                Schema::table('quiz_attempts', function (Blueprint $table) {
                    $table->dropColumn('id');
                });
            }
            // If only 'attempt_id' exists, we're good
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if quiz_attempts table exists
        if (Schema::hasTable('quiz_attempts')) {
            // If the table has 'attempt_id' column but not 'id'
            if (Schema::hasColumn('quiz_attempts', 'attempt_id') && !Schema::hasColumn('quiz_attempts', 'id')) {
                // Rename 'attempt_id' back to 'id'
                Schema::table('quiz_attempts', function (Blueprint $table) {
                    $table->renameColumn('attempt_id', 'id');
                });
            }
        }
    }
};
