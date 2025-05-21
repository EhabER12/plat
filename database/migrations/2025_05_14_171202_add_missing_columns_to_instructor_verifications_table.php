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
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('instructor_verifications', 'education')) {
                $table->string('education')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'expertise')) {
                $table->string('expertise')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'years_of_experience')) {
                $table->string('years_of_experience')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'qualifications')) {
                $table->text('qualifications')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'payment_details')) {
                $table->json('payment_details')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not removing columns in down migration for safety
    }
};
