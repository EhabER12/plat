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
            // Adding all potentially missing columns
            if (!Schema::hasColumn('instructor_verifications', 'years_of_experience')) {
                $table->string('years_of_experience')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'qualifications')) {
                $table->text('qualifications')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'payment_details')) {
                $table->json('payment_details')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'certificate_file')) {
                $table->string('certificate_file')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'cv_file')) {
                $table->string('cv_file')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            }
            if (!Schema::hasColumn('instructor_verifications', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'additional_info')) {
                $table->text('additional_info')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'education')) {
                $table->string('education')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'expertise')) {
                $table->string('expertise')->nullable();
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
