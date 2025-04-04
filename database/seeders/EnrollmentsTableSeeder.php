<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnrollmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // We can't truncate due to foreign key constraints

        $enrollments = [
            // Alice is enrolled in JavaScript course
            [
                'student_id' => 5, // Alice
                'course_id' => 1, // JavaScript
                'enrolled_at' => Carbon::now()->subDays(20),
                'status' => 'active',
            ],
            // Alice is enrolled in PHP course
            [
                'student_id' => 5, // Alice
                'course_id' => 2, // PHP
                'enrolled_at' => Carbon::now()->subDays(15),
                'status' => 'active',
            ],
            // Bob is enrolled in JavaScript course
            [
                'student_id' => 6, // Bob
                'course_id' => 1, // JavaScript
                'enrolled_at' => Carbon::now()->subDays(25),
                'status' => 'completed',
            ],
            // Bob is enrolled in React course
            [
                'student_id' => 6, // Bob
                'course_id' => 3, // React
                'enrolled_at' => Carbon::now()->subDays(10),
                'status' => 'active',
            ],
            // Cathy is enrolled in PHP course
            [
                'student_id' => 7, // Cathy
                'course_id' => 2, // PHP
                'enrolled_at' => Carbon::now()->subDays(30),
                'status' => 'completed',
            ],
            // Cathy is enrolled in Laravel course
            [
                'student_id' => 7, // Cathy
                'course_id' => 4, // Laravel
                'enrolled_at' => Carbon::now()->subDays(5),
                'status' => 'active',
            ],
            // David is enrolled in iOS Development course
            [
                'student_id' => 8, // David
                'course_id' => 5, // iOS Development
                'enrolled_at' => Carbon::now()->subDays(15),
                'status' => 'active',
            ],
            // Emily is enrolled in Machine Learning course
            [
                'student_id' => 9, // Emily
                'course_id' => 7, // Machine Learning
                'enrolled_at' => Carbon::now()->subDays(7),
                'status' => 'active',
            ],
        ];

        DB::table('enrollments')->insert($enrollments);
    }
}
