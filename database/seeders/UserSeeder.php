<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('password123'),
            'phone' => '1234567890',
            'address' => 'Admin Address',
            'bio' => 'System Administrator',
            'status' => true,
        ]);
        $admin->roles()->attach('admin');

        // Create Instructor
        $instructor = User::create([
            'name' => 'Instructor User',
            'email' => 'instructor@example.com',
            'password_hash' => Hash::make('password123'),
            'phone' => '1234567891',
            'address' => 'Instructor Address',
            'bio' => 'Professional Instructor',
            'status' => true,
        ]);
        $instructor->roles()->attach('instructor');

        // Create Student
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password_hash' => Hash::make('password123'),
            'phone' => '1234567892',
            'address' => 'Student Address',
            'bio' => 'Eager Learner',
            'status' => true,
        ]);
        $student->roles()->attach('student');

        // Create Parent
        $parent = User::create([
            'name' => 'Parent User',
            'email' => 'parent@example.com',
            'password_hash' => Hash::make('password123'),
            'phone' => '1234567893',
            'address' => 'Parent Address',
            'bio' => 'Student Parent',
            'status' => true,
        ]);
        $parent->roles()->attach('parent');
    }
} 