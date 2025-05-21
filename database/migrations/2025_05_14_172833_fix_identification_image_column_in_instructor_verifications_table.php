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
        Schema::table('instructor_verifications', function (Blueprint $table) {
            // First check if the column exists
            if (Schema::hasColumn('instructor_verifications', 'identification_image')) {
                // Modify the column to make it nullable
                $table->string('identification_image')->nullable()->change();
            } else {
                // Add the column as nullable if it doesn't exist
                $table->string('identification_image')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not removing the column in the down method for safety
    }
};
