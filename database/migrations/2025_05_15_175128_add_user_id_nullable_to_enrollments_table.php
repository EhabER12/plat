<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration makes the user_id field nullable or adds it if it doesn't exist,
     * to fix the "Field 'user_id' doesn't have a default value" error.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Add user_id as nullable if it doesn't exist
            if (!Schema::hasColumn('enrollments', 'user_id')) {
                $table->string('user_id')->nullable()->after('enrollment_id');
            } else {
                // If it exists, modify it to be nullable
                $table->string('user_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Don't remove the column, just make it required again if needed
            if (Schema::hasColumn('enrollments', 'user_id')) {
                $table->string('user_id')->nullable(false)->change();
            }
        });
    }
};
