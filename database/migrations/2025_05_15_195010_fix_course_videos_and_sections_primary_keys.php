<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix course_videos table
        Schema::table('course_videos', function (Blueprint $table) {
            // Rename 'id' to 'video_id'
            $table->renameColumn('id', 'video_id');
        });

        // Fix course_sections table
        Schema::table('course_sections', function (Blueprint $table) {
            // Rename 'id' to 'section_id'
            $table->renameColumn('id', 'section_id');
        });

        // Update the model relationships - update section_id foreign key in course_videos
        Schema::table('course_videos', function (Blueprint $table) {
            // Make sure the foreign key uses the correct column name
            if (Schema::hasColumn('course_videos', 'section_id')) {
                // Check if there's an existing foreign key and drop it
                $foreignKeys = DB::select("SHOW CREATE TABLE course_videos")[0]->{"Create Table"};
                if (strpos($foreignKeys, 'FOREIGN KEY (`section_id`)') !== false) {
                    $table->dropForeign(['section_id']);
                }
                
                // Re-add the foreign key with the correct reference
                $table->foreign('section_id')
                      ->references('section_id')
                      ->on('course_sections')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore course_videos table
        Schema::table('course_videos', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('course_videos', 'section_id')) {
                $foreignKeys = DB::select("SHOW CREATE TABLE course_videos")[0]->{"Create Table"};
                if (strpos($foreignKeys, 'FOREIGN KEY (`section_id`)') !== false) {
                    $table->dropForeign(['section_id']);
                }
            }
            
            // Rename 'video_id' back to 'id'
            $table->renameColumn('video_id', 'id');
        });

        // Restore course_sections table
        Schema::table('course_sections', function (Blueprint $table) {
            // Rename 'section_id' back to 'id'
            $table->renameColumn('section_id', 'id');
        });
    }
}; 