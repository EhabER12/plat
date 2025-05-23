<?php

// استدعاء الملفات الضرورية
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// استخدام Eloquent للتفاعل مع قاعدة البيانات
use Illuminate\Support\Facades\DB;

echo "=== قائمة المستخدمين في النظام ===\n\n";

// جلب جميع المستخدمين
$users = DB::table('users')->get();

foreach ($users as $user) {
    echo "المستخدم رقم: " . $user->user_id . "\n";
    echo "الاسم: " . $user->name . "\n";
    echo "البريد الإلكتروني: " . $user->email . "\n";
    
    // جلب أدوار المستخدم
    $roles = DB::table('user_roles')
                ->where('user_id', $user->user_id)
                ->pluck('role')
                ->toArray();
    
    echo "الأدوار: " . implode(', ', $roles) . "\n";
    echo "------------------------\n";
}

echo "\n=== قائمة علاقات أولياء الأمور والطلاب ===\n\n";

// جلب علاقات أولياء الأمور والطلاب
$relations = DB::table('parent_student_relations')
            ->join('users as parents', 'parents.user_id', '=', 'parent_student_relations.parent_id')
            ->leftJoin('users as students', 'students.user_id', '=', 'parent_student_relations.student_id')
            ->select(
                'parent_student_relations.id',
                'parents.name as parent_name',
                'parents.email as parent_email',
                'students.name as student_name',
                'students.email as student_email',
                'parent_student_relations.verification_status'
            )
            ->get();

foreach ($relations as $relation) {
    echo "العلاقة رقم: " . $relation->id . "\n";
    echo "ولي الأمر: " . $relation->parent_name . " (" . $relation->parent_email . ")\n";
    echo "الطالب: " . ($relation->student_name ?? $relation->student_name) . " (" . ($relation->student_email ?? 'غير مسجل') . ")\n";
    echo "حالة التحقق: " . $relation->verification_status . "\n";
    echo "------------------------\n";
} 