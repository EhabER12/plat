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
        Schema::table('course_videos', function (Blueprint $table) {
            if (!Schema::hasColumn('course_videos', 'duration_seconds')) {
                $table->integer('duration_seconds')->default(0)->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            if (Schema::hasColumn('course_videos', 'duration_seconds')) {
                $table->dropColumn('duration_seconds');
            }
        });
    }
};
