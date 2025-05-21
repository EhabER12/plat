<?php

// استدعاء الملفات الضرورية
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;

echo "فحص جدول السحوبات المالية...\n";

// الحصول على معرف المستخدم المعلم
$instructorId = null;

// البحث عن مستخدم من نوع معلم
$instructor = DB::table('users')
    ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
    ->where('user_roles.role', 'instructor')
    ->first();

if ($instructor) {
    $instructorId = $instructor->user_id;
    echo "تم العثور على معلم بمعرف: " . $instructorId . "\n";
} else {
    // البحث عن مستخدم من نوع admin (احتياطي)
    $admin = DB::table('users')
        ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
        ->where('user_roles.role', 'admin')
        ->first();
        
    if ($admin) {
        $instructorId = $admin->user_id;
        echo "لم يتم العثور على معلم، استخدام معرف المسؤول بدلاً: " . $instructorId . "\n";
    } else {
        // إنشاء مستخدم معلم إذا لم يتم العثور على أي مستخدم مناسب
        $instructorId = DB::table('users')->insertGetId([
            'name' => 'مدرس تجريبي',
            'email' => 'instructor@example.com',
            'password' => Hash::make('instructor123'),
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // إضافة دور المعلم
        DB::table('user_roles')->insert([
            'user_id' => $instructorId,
            'role' => 'instructor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "تم إنشاء معلم جديد بمعرف: " . $instructorId . "\n";
    }
}

// التحقق من وجود الجدول
if (!Schema::hasTable('withdrawals')) {
    echo "جدول السحوبات المالية غير موجود. جاري الإنشاء...\n";
    
    Schema::create('withdrawals', function (Blueprint $table) {
        $table->id('withdrawal_id');
        $table->unsignedBigInteger('instructor_id');
        $table->decimal('amount', 10, 2);
        $table->enum('status', ['pending', 'completed', 'rejected', 'failed'])->default('pending');
        $table->string('payment_method')->nullable();
        $table->string('bank_account')->nullable();
        $table->string('paypal_email')->nullable();
        $table->text('notes')->nullable();
        $table->timestamp('requested_at')->default(now());
        $table->timestamp('processed_at')->nullable();
        $table->timestamps();
        
        // إضافة المفتاح الأجنبي إذا كان جدول المستخدمين موجوداً
        if (Schema::hasTable('users')) {
            $table->foreign('instructor_id')->references('user_id')->on('users')->onDelete('cascade');
        }
    });
    
    echo "تم إنشاء جدول السحوبات المالية بنجاح.\n";
} else {
    echo "جدول السحوبات المالية موجود بالفعل. جاري التحقق من البنية...\n";
    
    // تعديل بنية الجدول إذا لزم الأمر
    try {
        // لا يمكننا تعديل تعداد ENUM بسهولة، لذا سنحتاج أوامر SQL مباشرة
        echo "جاري تحديث تعداد الحالة status للتأكد من وجود خيار 'rejected'...\n";
        
        $currentEnum = DB::selectOne('SHOW COLUMNS FROM withdrawals WHERE Field = ?', ['status']);
        
        if ($currentEnum && strpos($currentEnum->Type, 'rejected') === false) {
            // التعداد لا يحتوي على rejected، نقوم بتعديله
            DB::statement("ALTER TABLE withdrawals MODIFY COLUMN status ENUM('pending', 'completed', 'rejected', 'failed') DEFAULT 'pending'");
            echo "تم تحديث تعداد الحالة بنجاح.\n";
        } else {
            echo "تعداد الحالة يحتوي بالفعل على جميع الخيارات المطلوبة.\n";
        }
        
        // التحقق من وجود عمود processed_at
        if (!Schema::hasColumn('withdrawals', 'processed_at')) {
            Schema::table('withdrawals', function (Blueprint $table) {
                $table->timestamp('processed_at')->nullable()->after('requested_at');
            });
            echo "تم إضافة عمود processed_at إلى الجدول.\n";
        }
    } catch (Exception $e) {
        echo "حدث خطأ أثناء تعديل بنية الجدول: " . $e->getMessage() . "\n";
    }
}

// عدد طلبات السحب الحالية
$withdrawalsCount = DB::table('withdrawals')->count();
echo "عدد طلبات السحب الحالية: " . $withdrawalsCount . "\n";

// إضافة بيانات تجريبية إذا لم تكن موجودة بالفعل
if ($withdrawalsCount < 3 && $instructorId) {
    echo "إضافة بيانات تجريبية للسحوبات المالية...\n";
    
    // حذف البيانات الموجودة (إن وجدت) لتفادي التكرار
    DB::table('withdrawals')->delete();
    
    // إضافة طلب سحب مكتمل
    DB::table('withdrawals')->insert([
        'instructor_id' => $instructorId,
        'amount' => 500.00,
        'status' => 'completed',
        'payment_method' => 'bank_transfer',
        'bank_account' => 'IBAN1234567890',
        'notes' => 'طلب سحب مكتمل',
        'requested_at' => now()->subDays(30),
        'processed_at' => now()->subDays(28),
        'created_at' => now()->subDays(30),
        'updated_at' => now()->subDays(28)
    ]);
    
    // إضافة طلب سحب مرفوض
    DB::table('withdrawals')->insert([
        'instructor_id' => $instructorId,
        'amount' => 300.00,
        'status' => 'rejected',
        'payment_method' => 'bank_transfer',
        'bank_account' => 'IBAN1234567890',
        'notes' => 'بيانات الحساب غير صحيحة',
        'requested_at' => now()->subDays(15),
        'processed_at' => now()->subDays(14),
        'created_at' => now()->subDays(15),
        'updated_at' => now()->subDays(14)
    ]);
    
    // إضافة طلب سحب معلق
    DB::table('withdrawals')->insert([
        'instructor_id' => $instructorId,
        'amount' => 700.00,
        'status' => 'pending',
        'payment_method' => 'bank_transfer',
        'bank_account' => 'IBAN1234567890',
        'notes' => 'طلب سحب جديد',
        'requested_at' => now()->subDays(2),
        'processed_at' => null,
        'created_at' => now()->subDays(2),
        'updated_at' => now()->subDays(2)
    ]);
    
    echo "تم إضافة 3 طلبات سحب تجريبية بنجاح.\n";
}

echo "عرض بيانات جدول السحوبات المالية:\n";
$withdrawals = DB::table('withdrawals')->get();

if ($withdrawals->count() > 0) {
    foreach ($withdrawals as $withdrawal) {
        echo "----------------------------\n";
        echo "رقم الطلب: " . $withdrawal->withdrawal_id . "\n";
        echo "المبلغ: " . $withdrawal->amount . "\n";
        echo "الحالة: " . $withdrawal->status . "\n";
        echo "تاريخ الطلب: " . $withdrawal->requested_at . "\n";
        echo "تاريخ المعالجة: " . ($withdrawal->processed_at ?? 'لم تتم المعالجة بعد') . "\n";
    }
} else {
    echo "لا توجد بيانات في جدول السحوبات المالية.\n";
}

echo "تم الانتهاء من الفحص والإصلاح.\n"; 