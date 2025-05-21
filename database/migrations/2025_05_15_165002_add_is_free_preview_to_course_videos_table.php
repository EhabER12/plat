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
            if (!Schema::hasColumn('course_videos', 'is_free_preview')) {
                $table->boolean('is_free_preview')->default(false)->after('sequence_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            if (Schema::hasColumn('course_videos', 'is_free_preview')) {
                $table->dropColumn('is_free_preview');
            }
        });
    }
};
