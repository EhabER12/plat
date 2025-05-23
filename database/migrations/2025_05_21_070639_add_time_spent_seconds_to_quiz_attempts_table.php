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
            // Add time_spent_seconds column if it doesn't exist
            if (!Schema::hasColumn('quiz_attempts', 'time_spent_seconds')) {
                $table->integer('time_spent_seconds')->default(0)->after('is_passed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Remove time_spent_seconds column if it exists
            if (Schema::hasColumn('quiz_attempts', 'time_spent_seconds')) {
                $table->dropColumn('time_spent_seconds');
            }
        });
    }
};
