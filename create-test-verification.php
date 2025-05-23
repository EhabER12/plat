<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\InstructorVerification;
use Illuminate\Support\Facades\DB;

try {
    // البحث عن مستخدم موجود
    $user = User::first();
    
    if (!$user) {
        echo "No users found. Creating a test user...\n";
        
        // إنشاء مستخدم جديد
        $user = User::create([
            'name' => 'Test Instructor',
            'email' => 'test.instructor'.time().'@example.com',
            'password' => bcrypt('password123'),
        ]);
        
        // إضافة دور له
        DB::table('user_roles')->insert([
            'user_id' => $user->user_id,
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "User created with ID: " . $user->user_id . "\n";
    } else {
        echo "Using existing user with ID: " . $user->user_id . "\n";
    }
    
    // إنشاء سجل تحقق المدرس
    $verification = InstructorVerification::create([
        'user_id' => $user->user_id,
        'status' => 'pending',
        'qualifications' => 'أربع سنوات من الخبرة في مجال التدريس، شهادة في اللغة العربية والكيمياء',
        'submitted_at' => now(),
        'identification_type' => 'passport', 
        'identification_number' => 'ABC123456',
        'id_document' => 'documents/passport.pdf',
        'cv_document' => 'documents/cv.pdf',
    ]);
    
    echo "Instructor verification record created with ID: " . $verification->verification_id . "\n";
    echo "Success!\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 