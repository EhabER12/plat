<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدم اختباري إذا لم يكن موجوداً
        $userId = DB::table('users')->where('email', 'test@example.com')->value('user_id');
        
        if (!$userId) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'مستخدم اختباري',
                'email' => 'test@example.com',
                'password_hash' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // إضافة دور للمستخدم
            DB::table('user_roles')->insert([
                'user_id' => $userId,
                'role' => 'student',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "تم إنشاء مستخدم اختباري برقم: $userId\n";
        } else {
            echo "المستخدم الاختباري موجود بالفعل برقم: $userId\n";
        }

        // إنشاء مدرس اختباري إذا لم يكن موجوداً
        $instructorId = DB::table('users')->where('email', 'instructor@example.com')->value('user_id');
        
        if (!$instructorId) {
            $instructorId = DB::table('users')->insertGetId([
                'name' => 'مدرس اختباري',
                'email' => 'instructor@example.com',
                'password_hash' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // إضافة دور للمدرس
            DB::table('user_roles')->insert([
                'user_id' => $instructorId,
                'role' => 'instructor',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "تم إنشاء مدرس اختباري برقم: $instructorId\n";
        } else {
            echo "المدرس الاختباري موجود بالفعل برقم: $instructorId\n";
        }

        // إنشاء تصنيف اختباري إذا لم يكن موجوداً
        $categoryId = DB::table('categories')->where('slug', 'test-category')->value('category_id');
        
        if (!$categoryId) {
            $categoryId = DB::table('categories')->insertGetId([
                'name' => 'تصنيف اختباري',
                'description' => 'هذا تصنيف للاختبار فقط',
                'slug' => 'test-category',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "تم إنشاء تصنيف اختباري برقم: $categoryId\n";
        } else {
            echo "التصنيف الاختباري موجود بالفعل برقم: $categoryId\n";
        }

        // إنشاء دورة اختبارية إذا لم تكن موجودة
        $courseId = DB::table('courses')->where('title', 'دورة اختبار الدفع')->value('course_id');
        
        if (!$courseId) {
            $courseId = DB::table('courses')->insertGetId([
                'title' => 'دورة اختبار الدفع',
                'description' => 'هذه دورة مخصصة لاختبار نظام الدفع. سعرها 100 جنيه فقط.',
                'instructor_id' => $instructorId,
                'category_id' => $categoryId,
                'price' => 100,
                'duration' => 10,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "تم إنشاء دورة اختبارية برقم: $courseId\n";
        } else {
            echo "الدورة الاختبارية موجودة بالفعل برقم: $courseId\n";
        }

        // إنشاء دورة مجانية اختبارية
        $freeCourseId = DB::table('courses')->where('title', 'دورة مجانية اختبارية')->value('course_id');
        
        if (!$freeCourseId) {
            $freeCourseId = DB::table('courses')->insertGetId([
                'title' => 'دورة مجانية اختبارية',
                'description' => 'هذه دورة مجانية للاختبار. يمكن التسجيل فيها بدون دفع.',
                'instructor_id' => $instructorId,
                'category_id' => $categoryId,
                'price' => 0,
                'duration' => 5,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "تم إنشاء دورة مجانية اختبارية برقم: $freeCourseId\n";
        } else {
            echo "الدورة المجانية الاختبارية موجودة بالفعل برقم: $freeCourseId\n";
        }

        echo "تم إنشاء جميع البيانات الاختبارية بنجاح!\n";
        echo "للتجربة، قم بتسجيل الدخول باستخدام:\n";
        echo "- مستخدم طالب: test@example.com / password123\n";
        echo "- مستخدم مدرس: instructor@example.com / password123\n";
        echo "لاختبار عملية دفع وهمية، استخدم الرابط: /payment/test/simulate/{courseId}/{payment_method}\n";
        echo "مثال: /payment/test/simulate/$courseId/paymob\n";
    }
}
