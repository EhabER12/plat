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
            if (!Schema::hasColumn('instructor_verifications', 'linkedin_profile')) {
                $table->string('linkedin_profile')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructor_verifications', function (Blueprint $table) {
            $table->dropColumn('linkedin_profile');
        });
    }
};
