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
            // Add all remaining fields for instructor verification
            if (!Schema::hasColumn('instructor_verifications', 'identification_image')) {
                $table->string('identification_image')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'cv_file')) {
                $table->string('cv_file')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'certificate_file')) {
                $table->string('certificate_file')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'id_document')) {
                $table->string('id_document')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'cv_document')) {
                $table->string('cv_document')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'certificate_document')) {
                $table->string('certificate_document')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'admin_notes')) {
                $table->text('admin_notes')->nullable();
            }
            if (!Schema::hasColumn('instructor_verifications', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not removing columns for safety
    }
};
