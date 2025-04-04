<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseVideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // We can't truncate due to foreign key constraints
        
        // JavaScript Course Videos (Course ID 1)
        $jsVideos = [
            [
                'course_id' => 1,
                'title' => 'Introduction to JavaScript',
                'video_url' => 'https://example.com/videos/js/intro.mp4',
                'duration' => 15, // minutes
                'position' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'course_id' => 1,
                'title' => 'JavaScript Variables and Data Types',
                'video_url' => 'https://example.com/videos/js/variables.mp4',
                'duration' => 25,
                'position' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'course_id' => 1,
                'title' => 'JavaScript Functions',
                'video_url' => 'https://example.com/videos/js/functions.mp4',
                'duration' => 30,
                'position' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'course_id' => 1,
                'title' => 'DOM Manipulation',
                'video_url' => 'https://example.com/videos/js/dom.mp4',
                'duration' => 35,
                'position' => 4,
                'created_at' => Carbon::now(),
            ],
        ];
        
        DB::table('course_videos')->insert($jsVideos);
        
        // PHP Course Videos (Course ID 2)
        $phpVideos = [
            [
                'course_id' => 2,
                'title' => 'Introduction to PHP',
                'video_url' => 'https://example.com/videos/php/intro.mp4',
                'duration' => 20,
                'position' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'course_id' => 2,
                'title' => 'PHP Variables and Data Types',
                'video_url' => 'https://example.com/videos/php/variables.mp4',
                'duration' => 30,
                'position' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'course_id' => 2,
                'title' => 'PHP Functions',
                'video_url' => 'https://example.com/videos/php/functions.mp4',
                'duration' => 35,
                'position' => 3,
                'created_at' => Carbon::now(),
            ],
        ];
        
        DB::table('course_videos')->insert($phpVideos);
        
        // React Course Videos (Course ID 3)
        $reactVideos = [
            [
                'course_id' => 3,
                'title' => 'Introduction to React',
                'video_url' => 'https://example.com/videos/react/intro.mp4',
                'duration' => 25,
                'position' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'course_id' => 3,
                'title' => 'React Components',
                'video_url' => 'https://example.com/videos/react/components.mp4',
                'duration' => 40,
                'position' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'course_id' => 3,
                'title' => 'React Hooks',
                'video_url' => 'https://example.com/videos/react/hooks.mp4',
                'duration' => 45,
                'position' => 3,
                'created_at' => Carbon::now(),
            ],
        ];
        
        DB::table('course_videos')->insert($reactVideos);
    }
}
