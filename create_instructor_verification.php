<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

// الأول نتأكد من أن user_id في جدول users يساوي id للسجلات التي قد تكون فارغة أو صفر
DB::update('UPDATE users SET user_id = id WHERE user_id IS NULL OR user_id = 0');
echo "تم تحديث user_id في جدول users\n";

// إنشاء سجل verification للمدرس
$now = Carbon::now()->format('Y-m-d H:i:s');
$result = DB::table('instructor_verifications')->insertOrIgnore([
    'user_id' => 2,
    'education' => 'Bachelor Degree',
    'expertise' => 'Programming',
    'years_of_experience' => '5',
    'qualifications' => 'Teaching Experience',
    'status' => 'approved',
    'submitted_at' => $now,
    'created_at' => $now,
    'updated_at' => $now,
]);

echo "تم إضافة سجل instructor_verification للمدرس: " . ($result ? "نجاح" : "فشل أو سجل موجود مسبقاً") . "\n";

// طباعة معلومات المدرس الحالي
$instructor = DB::table('users')->where('user_id', 2)->first();
if ($instructor) {
    echo "معلومات المدرس:\n";
    echo "ID: " . $instructor->id . "\n";
    echo "User ID: " . $instructor->user_id . "\n";
    echo "Name: " . $instructor->name . "\n";
    echo "Email: " . $instructor->email . "\n";
}

// طباعة معلومات التحقق الخاصة بالمدرس
$verification = DB::table('instructor_verifications')->where('user_id', 2)->first();
if ($verification) {
    echo "معلومات التحقق:\n";
    echo "ID: " . $verification->id . "\n";
    echo "User ID: " . $verification->user_id . "\n";
    echo "Status: " . $verification->status . "\n";
} 