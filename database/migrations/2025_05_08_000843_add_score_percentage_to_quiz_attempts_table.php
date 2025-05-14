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
            // Add the score_percentage column if it doesn't exist
            if (!Schema::hasColumn('quiz_attempts', 'score_percentage')) {
                $table->float('score_percentage', 8, 2)->nullable()->after('score');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Remove the score_percentage column if it exists
            if (Schema::hasColumn('quiz_attempts', 'score_percentage')) {
                $table->dropColumn('score_percentage');
            }
        });
    }
};
