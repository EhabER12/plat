<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // إنشاء مدرس
        $instructorId = DB::table('users')->insertGetId([
            'name' => 'أحمد المدرس',
            'email' => 'instructor' . time() . '@example.com',
            'password_hash' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // ربط المدرس بدور المدرس
        DB::table('user_roles')->insert([
            'user_id' => $instructorId,
            'role' => 'instructor',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // إنشاء تصنيفات
        $categories = [
            'البرمجة والتطوير',
            'تطوير المواقع',
            'تطوير التطبيقات',
            'الذكاء الاصطناعي',
            'قواعد البيانات'
        ];
        
        $categoryIds = [];
        foreach ($categories as $category) {
            $categoryIds[] = DB::table('categories')->insertGetId([
                'name' => $category,
                'description' => 'دورات في مجال ' . $category,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // إنشاء الكورسات
        $courses = [
            [
                'title' => 'أساسيات البرمجة بلغة PHP',
                'description' => 'تعلم أساسيات لغة PHP وكيفية استخدامها في تطوير تطبيقات الويب.',
                'price' => 299.99,
                'duration' => 24,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => 1,
                'approval_status' => 'approved',
            ],
            [
                'title' => 'تطوير تطبيقات الويب باستخدام Laravel',
                'description' => 'دورة شاملة في تطوير تطبيقات الويب باستخدام إطار العمل Laravel.',
                'price' => 499.99,
                'duration' => 36,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => 1,
                'approval_status' => 'approved',
            ],
            [
                'title' => 'تطوير واجهات المستخدم باستخدام React',
                'description' => 'تعلم كيفية تطوير واجهات مستخدم تفاعلية وديناميكية باستخدام مكتبة React.js.',
                'price' => 399.99,
                'duration' => 30,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => 1,
                'approval_status' => 'approved',
            ],
        ];
        
        foreach ($courses as $course) {
            DB::table('courses')->insert([
                'title' => $course['title'],
                'description' => $course['description'],
                'instructor_id' => $instructorId,
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'price' => $course['price'],
                'duration' => $course['duration'],
                'level' => $course['level'],
                'language' => $course['language'],
                'featured' => $course['featured'],
                'approval_status' => $course['approval_status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        echo "تم إضافة البيانات الوهمية بنجاح!\n";
    }
} 