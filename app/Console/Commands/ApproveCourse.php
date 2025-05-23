<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;

class ApproveCourse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:approve {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve a course by ID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $courseId = $this->argument('id');
        
        $course = Course::find($courseId);
        
        if (!$course) {
            $this->error("Course with ID {$courseId} not found");
            return 1;
        }
        
        $course->approval_status = 'approved';
        $course->status = 'active';
        $course->save();
        
        $this->info("Course with ID {$courseId} has been approved and activated");
        return 0;
    }
} 