<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all videos without section_id
        $videos = DB::table('course_videos')
            ->whereNull('section_id')
            ->get();
        
        foreach ($videos as $video) {
            // Find the first section for this course
            $section = DB::table('course_sections')
                ->where('course_id', $video->course_id)
                ->orderBy('position', 'asc')
                ->first();
            
            if ($section) {
                // Update the video with the section_id
                // Use the correct primary key column names after the rename
                DB::table('course_videos')
                    ->where('video_id', $video->video_id)
                    ->update(['section_id' => $section->section_id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all section_id values to NULL
        DB::table('course_videos')->update(['section_id' => null]);
    }
}; 