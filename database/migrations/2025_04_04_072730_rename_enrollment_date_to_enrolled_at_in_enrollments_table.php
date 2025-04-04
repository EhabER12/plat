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
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'enrollment_date') && !Schema::hasColumn('enrollments', 'enrolled_at')) {
                $table->renameColumn('enrollment_date', 'enrolled_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'enrolled_at') && !Schema::hasColumn('enrollments', 'enrollment_date')) {
                $table->renameColumn('enrolled_at', 'enrollment_date');
            }
        });
    }
};
