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
            // Add is_passed column if it doesn't exist
            if (!Schema::hasColumn('quiz_attempts', 'is_passed')) {
                $table->boolean('is_passed')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // Remove is_passed column if it exists
            if (Schema::hasColumn('quiz_attempts', 'is_passed')) {
                $table->dropColumn('is_passed');
            }
        });
    }
};
