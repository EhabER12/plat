<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class CreateTestCourseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test course';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Attempting to create a test course...');

        try {
            // Get first instructor ID
            $instructorId = DB::table('users')->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                ->where('user_roles.role', 'instructor')
                ->value('users.user_id');
            
            if (!$instructorId) {
                $instructorId = DB::table('users')->first()->user_id ?? 1;
                $this->warn('No instructor found, using user ID: ' . $instructorId);
            }

            // Get first category ID
            $categoryId = DB::table('categories')->first()->category_id ?? 1;
            
            // Insert test course directly using DB
            $courseId = DB::table('courses')->insertGetId([
                'title' => 'Test Course ' . time(),
                'description' => 'This is a test course created for debugging purposes',
                'status' => 'published',
                'instructor_id' => $instructorId,
                'category_id' => $categoryId,
                'price' => 99.99,
                'duration' => 20,
                'level' => 'beginner',
                'language' => 'en',
                'featured' => 0,
                'certificate_available' => 0,
                'approval_status' => 'approved',
                'thumbnail' => 'images/courses/default.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info('Test course created successfully with ID: ' . $courseId);
            $this->info('You can access it at: /courses/' . $courseId);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error creating test course: ' . $e->getMessage());
            return 1;
        }
    }
}
