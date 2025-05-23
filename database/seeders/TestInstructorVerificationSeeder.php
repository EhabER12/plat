<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestInstructorVerificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تأكد من وجود مستخدم على الأقل
        $userId = DB::table('users')->insertGetId([
            'name' => 'Test Instructor',
            'email' => 'test.instructor@example.com',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إضافة دور المستخدم
        DB::table('user_roles')->insert([
            'user_id' => $userId,
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // إنشاء سجل تحقق المدرس
        DB::table('instructor_verifications')->insert([
            'user_id' => $userId,
            'status' => 'pending',
            'qualifications' => 'أربع سنوات من الخبرة في مجال التدريس، شهادة في اللغة العربية والكيمياء',
            'id_document' => 'documents/passport_copy.pdf',
            'cv_document' => 'documents/instructor_cv.pdf',
            'certificate_document' => 'documents/teaching_certificate.pdf',
            'payment_details' => json_encode([
                'email' => 'payment.email@example.com',
                'phone' => '+1234567890',
                'bank_name' => 'Test Bank',
                'account_number' => '1234567890'
            ]),
            'created_at' => now(),
            'updated_at' => now(),
            'submitted_at' => now(),
        ]);

        // إنشاء سجل تحقق آخر للاختبار
        DB::table('instructor_verifications')->insert([
            'user_id' => $userId,
            'status' => 'pending',
            'qualifications' => 'خمس سنوات من الخبرة في تدريس البرمجة، شهادة ماجستير في علوم الحاسب',
            'id_document' => 'documents/id_copy.pdf',
            'cv_document' => 'documents/developer_cv.pdf',
            'certificate_document' => 'documents/masters_certificate.pdf',
            'payment_details' => json_encode([
                'email' => 'another.email@example.com',
                'phone' => '+0987654321',
                'bank_name' => 'Another Bank',
                'account_number' => '0987654321'
            ]),
            'created_at' => now(),
            'updated_at' => now(),
            'submitted_at' => now(),
        ]);
    }
} 