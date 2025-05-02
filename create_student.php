<?php

// بدء محاكاة تطبيق Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// إنشاء مستخدم جديد
$email = 'student_demo_' . Str::random(5) . '@example.com';
$password = 'student123';

try {
    // التحقق مما إذا كان المستخدم موجودًا بالفعل
    $existingUser = User::where('email', $email)->first();
    
    if ($existingUser) {
        echo "المستخدم موجود بالفعل: {$email}\n";
        echo "كلمة المرور: {$password}\n";
        $user = $existingUser;
    } else {
        // إنشاء المستخدم الجديد
        $user = new User();
        $user->name = 'طالب تجريبي';
        $user->email = $email;
        $user->password_hash = Hash::make($password);
        $user->save();
        
        echo "تم إنشاء مستخدم جديد:\n";
        echo "البريد الإلكتروني: {$email}\n";
        echo "كلمة المرور: {$password}\n";
        
        // إضافة دور "طالب"
        UserRole::create([
            'user_id' => $user->user_id,
            'role' => 'student'
        ]);
        
        echo "تم تعيين دور 'طالب' للمستخدم\n";
    }
    
    // الحصول على جميع الدورات المعتمدة
    $courses = Course::where('approval_status', 'approved')->get();
    
    if ($courses->isEmpty()) {
        echo "لا توجد دورات معتمدة متاحة للتسجيل\n";
    } else {
        $enrollmentCount = 0;
        
        // تسجيل المستخدم في جميع الدورات
        foreach ($courses as $course) {
            // التحقق مما إذا كان المستخدم مسجلاً بالفعل
            $existingEnrollment = Enrollment::where('student_id', $user->user_id)
                ->where('course_id', $course->course_id)
                ->first();
                
            if (!$existingEnrollment) {
                Enrollment::create([
                    'student_id' => $user->user_id,
                    'course_id' => $course->course_id,
                    'status' => 'active',
                    'enrolled_at' => now()
                ]);
                
                $enrollmentCount++;
            }
        }
        
        echo "تم تسجيل المستخدم في {$enrollmentCount} دورات جديدة\n";
        echo "إجمالي الدورات المتاحة: {$courses->count()}\n";
    }
    
    echo "\nتم إنشاء المستخدم بنجاح وتسجيله في جميع الدورات المتاحة.\n";
    echo "معلومات تسجيل الدخول:\n";
    echo "البريد الإلكتروني: {$email}\n";
    echo "كلمة المرور: {$password}\n";
    
} catch (Exception $e) {
    echo "حدث خطأ: " . $e->getMessage() . "\n";
} 