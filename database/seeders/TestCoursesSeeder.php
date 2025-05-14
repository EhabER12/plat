<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestCoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create a test instructor if no users exist
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@example.com'],
            [
                'name' => 'Test Instructor',
                'password' => Hash::make('password'),
                'role' => 'instructor',
            ]
        );

        // Create categories
        $categories = [
            [
                'name' => 'Programming',
                'description' => 'Learn programming languages and development skills',
                'slug' => 'programming',
            ],
            [
                'name' => 'Graphic',
                'description' => 'Learn graphic design and visual arts',
                'slug' => 'graphic',
            ],
            [
                'name' => 'AI',
                'description' => 'Learn artificial intelligence and machine learning',
                'slug' => 'ai',
            ],
            [
                'name' => 'تصنيف اختباري',
                'description' => 'هذا تصنيف اختباري للدورات',
                'slug' => 'test-category-arabic',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }

        // Get all categories
        $categoryIds = Category::pluck('category_id')->toArray();

        // Create courses
        $courses = [
            [
                'title' => 'Introduction to Web Development',
                'description' => 'Learn the basics of HTML, CSS, and JavaScript to build websites',
                'price' => 29.99,
                'instructor_id' => $instructor->user_id,
                'category_id' => $categoryIds[0], // Programming
                'approval_status' => 'approved',
                'thumbnail' => 'https://img.freepik.com/free-photo/programming-background-with-person-working-with-codes-computer_23-2150010127.jpg',
            ],
            [
                'title' => 'Advanced JavaScript Concepts',
                'description' => 'Deep dive into JavaScript with advanced concepts like promises, async/await, and more',
                'price' => 49.99,
                'instructor_id' => $instructor->user_id,
                'category_id' => $categoryIds[0], // Programming
                'approval_status' => 'approved',
                'thumbnail' => 'https://img.freepik.com/free-photo/programming-background-collage_23-2149901777.jpg',
            ],
            [
                'title' => 'Adobe Photoshop Masterclass',
                'description' => 'Learn to use Adobe Photoshop for professional graphic design',
                'price' => 39.99,
                'instructor_id' => $instructor->user_id,
                'category_id' => $categoryIds[1], // Graphic
                'approval_status' => 'approved',
                'thumbnail' => 'https://img.freepik.com/free-photo/woman-taking-notes-marketing-strategy_53876-167395.jpg',
            ],
            [
                'title' => 'Introduction to AI and Machine Learning',
                'description' => 'Learn the basics of artificial intelligence and machine learning',
                'price' => 59.99,
                'instructor_id' => $instructor->user_id,
                'category_id' => $categoryIds[2], // AI
                'approval_status' => 'approved',
                'thumbnail' => 'https://img.freepik.com/free-photo/ai-technology-microchip-background-digital-transformation-concept_53876-124669.jpg',
            ],
            [
                'title' => 'دورة البرمجة بلغة العربية',
                'description' => 'تعلم أساسيات البرمجة باللغة العربية',
                'price' => 19.99,
                'instructor_id' => $instructor->user_id,
                'category_id' => $categoryIds[3], // Arabic Category
                'approval_status' => 'approved',
                'thumbnail' => 'https://img.freepik.com/free-photo/person-front-computer-working-html_23-2150040420.jpg',
            ],
        ];

        foreach ($courses as $courseData) {
            Course::firstOrCreate(
                ['title' => $courseData['title']],
                $courseData
            );
        }

        $this->command->info('Test categories and courses created successfully!');
    }
}
