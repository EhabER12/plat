<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CourseVideo;
use App\Models\Course;

class CleanVideoStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:clean {course_id?} {--all : Clean all videos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean video storage directories and reset video paths in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $courseId = $this->argument('course_id');
        $cleanAll = $this->option('all');
        
        if (!$courseId && !$cleanAll) {
            $this->error('Please provide a course_id or use --all option');
            return 1;
        }
        
        $this->info('Starting video storage cleanup...');
        
        try {
            if ($cleanAll) {
                $courses = Course::all();
                foreach ($courses as $course) {
                    $this->cleanCourseVideos($course->course_id);
                }
            } else {
                $this->cleanCourseVideos($courseId);
            }
            
            $this->info('Video storage cleaned successfully');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error cleaning video storage: ' . $e->getMessage());
            Log::error('Error in CleanVideoStorage command: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return 1;
        }
    }
    
    /**
     * Clean videos for a specific course
     * 
     * @param int $courseId
     */
    protected function cleanCourseVideos($courseId)
    {
        $this->info("Cleaning videos for course ID: $courseId");
        
        // 1. Get all videos for this course
        $videos = CourseVideo::where('course_id', $courseId)->get();
        $this->info("Found " . count($videos) . " videos for course");
        
        // 2. Clean all potential video directories
        $potentialDirectories = [
            "videos/$courseId",
            "public/videos/$courseId",
            "private/videos/$courseId",
            "courses/$courseId/videos",
            "public/courses/$courseId/videos",
        ];
        
        foreach ($potentialDirectories as $directory) {
            if (Storage::exists($directory)) {
                $this->info("Cleaning directory: $directory");
                Storage::deleteDirectory($directory);
                Storage::makeDirectory($directory);
            } else {
                $this->info("Directory does not exist: $directory");
            }
        }
        
        // 3. Reset video paths in database
        foreach ($videos as $video) {
            // Skip videos with external URLs
            if ($video->video_url) {
                $this->info("Skipping external video: {$video->video_id} - {$video->title}");
                continue;
            }
            
            $this->info("Resetting path for video: {$video->video_id} - {$video->title}");
            
            // Reset video_path to null so it will be regenerated on next upload
            $video->video_path = null;
            $video->save();
        }
        
        $this->info("Cleaned videos for course ID: $courseId");
    }
} 