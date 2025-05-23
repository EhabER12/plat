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
        Schema::create('course_sections', function (Blueprint $table) {
            $table->id('section_id');
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('position')->default(0);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
        });

        // Add section_id to course_videos and course_materials tables
        Schema::table('course_videos', function (Blueprint $table) {
            $table->unsignedBigInteger('section_id')->nullable()->after('course_id');
            $table->foreign('section_id')->references('section_id')->on('course_sections')->onDelete('set null');
        });

        Schema::table('course_materials', function (Blueprint $table) {
            $table->unsignedBigInteger('section_id')->nullable()->after('course_id');
            $table->foreign('section_id')->references('section_id')->on('course_sections')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });

        Schema::table('course_videos', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });

        Schema::dropIfExists('course_sections');
    }
}; 