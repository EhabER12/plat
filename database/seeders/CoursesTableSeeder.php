<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // We can't truncate due to foreign key constraints
        
        // Get category IDs
        $jsCategory = DB::table('categories')->where('name', 'JavaScript')->value('category_id');
        $phpCategory = DB::table('categories')->where('name', 'PHP')->value('category_id');
        $reactCategory = DB::table('categories')->where('name', 'React')->value('category_id');
        $laravelCategory = DB::table('categories')->where('name', 'Laravel')->value('category_id');
        $iosCategory = DB::table('categories')->where('name', 'iOS Development')->value('category_id');
        $androidCategory = DB::table('categories')->where('name', 'Android Development')->value('category_id');
        $mlCategory = DB::table('categories')->where('name', 'Machine Learning')->value('category_id');
        $pythonDsCategory = DB::table('categories')->where('name', 'Python for Data Science')->value('category_id');
        
        $courses = [
            // Web Development Courses - Instructor 2 (John Smith)
            [
                'title' => 'Modern JavaScript From The Beginning',
                'description' => 'Learn modern JavaScript from the beginning - ES6, OOP, AJAX, Webpack and more!',
                'price' => 49.99,
                'instructor_id' => 2, // John Smith
                'category_id' => $jsCategory,
                'approval_status' => 'approved',
                'created_at' => Carbon::now()->subDays(30),
            ],
            [
                'title' => 'PHP for Beginners - Become a PHP Master',
                'description' => 'Learn PHP from scratch and build real-world applications',
                'price' => 59.99,
                'instructor_id' => 2, // John Smith
                'category_id' => $phpCategory,
                'approval_status' => 'approved',
                'created_at' => Carbon::now()->subDays(45),
            ],
            
            // More Web Development Courses - Instructor 3 (Sarah Johnson)
            [
                'title' => 'Complete React Developer Course',
                'description' => 'Learn React by building real projects. Includes React hooks, Redux, and more!',
                'price' => 69.99,
                'instructor_id' => 3, // Sarah Johnson
                'category_id' => $reactCategory,
                'approval_status' => 'approved',
                'created_at' => Carbon::now()->subDays(15),
            ],
            [
                'title' => 'Laravel From Scratch to Advanced',
                'description' => 'Learn to build complete web applications with Laravel PHP framework',
                'price' => 79.99,
                'instructor_id' => 3, // Sarah Johnson
                'category_id' => $laravelCategory,
                'approval_status' => 'approved',
                'created_at' => Carbon::now()->subDays(60),
            ],
            
            // Mobile Development Courses - Instructor 4 (Michael Brown)
            [
                'title' => 'iOS App Development with Swift',
                'description' => 'Learn to build iOS apps using Swift and Xcode',
                'price' => 89.99,
                'instructor_id' => 4, // Michael Brown
                'category_id' => $iosCategory,
                'approval_status' => 'approved',
                'created_at' => Carbon::now()->subDays(20),
            ],
            [
                'title' => 'Android App Development with Kotlin',
                'description' => 'Learn to build Android apps using Kotlin and Android Studio',
                'price' => 84.99,
                'instructor_id' => 4, // Michael Brown
                'category_id' => $androidCategory,
                'approval_status' => 'approved',
                'created_at' => Carbon::now()->subDays(25),
            ],
            
            // Data Science Courses - Instructor 3 (Sarah Johnson)
            [
                'title' => 'Machine Learning A-Z: Hands-On Python',
                'description' => 'Learn to create Machine Learning Algorithms in Python and R',
                'price' => 99.99,
                'instructor_id' => 3, // Sarah Johnson
                'category_id' => $mlCategory,
                'approval_status' => 'approved',
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'title' => 'Python for Data Science and Machine Learning',
                'description' => 'Learn how to use Python for Data Science and Machine Learning',
                'price' => 94.99,
                'instructor_id' => 3, // Sarah Johnson
                'category_id' => $pythonDsCategory,
                'approval_status' => 'approved',
                'created_at' => Carbon::now()->subDays(5),
            ],
        ];
        
        DB::table('courses')->insert($courses);
    }
}
