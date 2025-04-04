<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إضافة مستخدم مسؤول
        $adminId = DB::table('users')->insertGetId([
            'name' => 'مسؤول النظام',
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة دور مسؤول
        DB::table('user_roles')->insert([
            'user_id' => $adminId,
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة مدرس
        $instructor1Id = DB::table('users')->insertGetId([
            'name' => 'أحمد محمد',
            'email' => 'instructor1@example.com',
            'password_hash' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة دور مدرس
        DB::table('user_roles')->insert([
            'user_id' => $instructor1Id,
            'role' => 'instructor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة مدرس آخر
        $instructor2Id = DB::table('users')->insertGetId([
            'name' => 'سارة أحمد',
            'email' => 'instructor2@example.com',
            'password_hash' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة دور مدرس
        DB::table('user_roles')->insert([
            'user_id' => $instructor2Id,
            'role' => 'instructor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة طالب
        $student1Id = DB::table('users')->insertGetId([
            'name' => 'محمد علي',
            'email' => 'student1@example.com',
            'password_hash' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة دور طالب
        DB::table('user_roles')->insert([
            'user_id' => $student1Id,
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة طالب آخر
        $student2Id = DB::table('users')->insertGetId([
            'name' => 'فاطمة محمد',
            'email' => 'student2@example.com',
            'password_hash' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة دور طالب
        DB::table('user_roles')->insert([
            'user_id' => $student2Id,
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة ولي أمر
        $parentId = DB::table('users')->insertGetId([
            'name' => 'كريم السيد',
            'email' => 'parent@example.com',
            'password_hash' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة دور ولي أمر
        DB::table('user_roles')->insert([
            'user_id' => $parentId,
            'role' => 'parent',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ربط ولي الأمر بالطالب - إذا كان الجدول موجود
        try {
            DB::table('parent_student')->insert([
                'parent_id' => $parentId,
                'student_id' => $student2Id,
                'relation' => 'أب',
                'is_approved' => 1,
            ]);
        } catch (\Exception $e) {
            // تجاهل الخطأ إذا كان الجدول غير موجود
        }
    }

    /**
     * Create a user if it doesn't exist and assign role
     */
    private function createUserIfNotExists(array $userData, string $role)
    {
        // Check if user exists
        $exists = DB::table('users')->where('email', $userData['email'])->exists();

        if (!$exists) {
            // Convert password_hash to password
            if (isset($userData['password_hash'])) {
                $userData['password'] = $userData['password_hash'];
                unset($userData['password_hash']);
            }

            // Insert user
            $userId = DB::table('users')->insertGetId($userData);

            // Assign role
            DB::table('user_roles')->insert([
                'user_id' => $userId,
                'role' => $role,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->command->info("Created {$role} user: {$userData['email']}");
        } else {
            // Get user ID
            $user = DB::table('users')->where('email', $userData['email'])->first();
            $userId = $user->id;

            // Check if role exists
            $roleExists = DB::table('user_roles')
                ->where('user_id', $userId)
                ->where('role', $role)
                ->exists();

            if (!$roleExists) {
                // Assign role if not exists
                DB::table('user_roles')->insert([
                    'user_id' => $userId,
                    'role' => $role,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $this->command->info("Added {$role} role to: {$userData['email']}");
            } else {
                $this->command->info("User already exists with role: {$userData['email']}");
            }
        }
    }
}
