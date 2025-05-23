<?php

// استدعاء الملفات الضرورية
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

echo "فحص جدول الخصومات (discounts)...\n";

// التحقق من وجود الجدول
if (!Schema::hasTable('discounts')) {
    echo "جدول الخصومات غير موجود. جاري الإنشاء...\n";
    
    Schema::create('discounts', function (Blueprint $table) {
        $table->id('discount_id');
        $table->string('code', 50)->unique();
        $table->string('type', 20); // percentage, fixed
        $table->decimal('value', 10, 2); // قيمة الخصم (نسبة مئوية أو مبلغ ثابت)
        $table->decimal('min_order_value', 10, 2)->nullable();
        $table->decimal('max_discount_value', 10, 2)->nullable();
        $table->unsignedInteger('usage_limit')->nullable();
        $table->unsignedInteger('usage_count')->default(0);
        $table->timestamp('start_date')->nullable();
        $table->timestamp('end_date')->nullable();
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(true);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->timestamps();
        
        // إضافة المفتاح الأجنبي إذا كان جدول المستخدمين موجوداً
        if (Schema::hasTable('users')) {
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
        }
    });
    
    echo "تم إنشاء جدول الخصومات بنجاح.\n";
    
    // إضافة بيانات تجريبية
    echo "إضافة بيانات تجريبية للخصومات...\n";
    
    // البحث عن مستخدم من نوع admin
    $adminId = null;
    $admin = DB::table('users')
        ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
        ->where('user_roles.role', 'admin')
        ->first();
    
    if ($admin) {
        $adminId = $admin->user_id;
    }
    
    // إضافة خصم بنسبة مئوية
    DB::table('discounts')->insert([
        'code' => 'WELCOME25',
        'type' => 'percentage',
        'value' => 25.00,
        'min_order_value' => 100.00,
        'max_discount_value' => 200.00,
        'usage_limit' => 1000,
        'usage_count' => 231,
        'start_date' => now()->subDays(30),
        'end_date' => now()->addDays(30),
        'description' => 'خصم 25% للمستخدمين الجدد',
        'is_active' => true,
        'created_by' => $adminId,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    // إضافة خصم بقيمة ثابتة
    DB::table('discounts')->insert([
        'code' => 'FLAT50',
        'type' => 'fixed',
        'value' => 50.00,
        'min_order_value' => 200.00,
        'max_discount_value' => 50.00,
        'usage_limit' => 500,
        'usage_count' => 129,
        'start_date' => now()->subDays(15),
        'end_date' => now()->addDays(15),
        'description' => 'خصم ثابت بقيمة 50 على جميع الدورات',
        'is_active' => true,
        'created_by' => $adminId,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    // إضافة خصم منتهي الصلاحية
    DB::table('discounts')->insert([
        'code' => 'SUMMER30',
        'type' => 'percentage',
        'value' => 30.00,
        'min_order_value' => 150.00,
        'max_discount_value' => 300.00,
        'usage_limit' => 1000,
        'usage_count' => 856,
        'start_date' => now()->subDays(60),
        'end_date' => now()->subDays(5),
        'description' => 'خصم نهاية الصيف',
        'is_active' => false,
        'created_by' => $adminId,
        'created_at' => now()->subDays(60),
        'updated_at' => now()->subDays(5)
    ]);
    
    echo "تم إضافة 3 خصومات تجريبية بنجاح.\n";
} else {
    echo "جدول الخصومات موجود بالفعل.\n";
    
    // فحص هيكل الجدول والتأكد من وجود جميع الأعمدة المطلوبة
    $columnsToCheck = [
        'type' => 'string',
        'value' => 'decimal',
        'code' => 'string',
        'is_active' => 'boolean',
        'usage_count' => 'integer'
    ];
    
    foreach ($columnsToCheck as $column => $type) {
        if (!Schema::hasColumn('discounts', $column)) {
            echo "عمود $column غير موجود. جاري إضافته...\n";
            
            Schema::table('discounts', function (Blueprint $table) use ($column, $type) {
                switch ($type) {
                    case 'string':
                        $table->string($column)->nullable();
                        break;
                    case 'decimal':
                        $table->decimal($column, 10, 2)->nullable();
                        break;
                    case 'boolean':
                        $table->boolean($column)->default(true);
                        break;
                    case 'integer':
                        $table->unsignedInteger($column)->default(0);
                        break;
                }
            });
            
            echo "تم إضافة عمود $column بنجاح.\n";
        }
    }
}

// عرض بيانات الجدول
echo "عرض بيانات جدول الخصومات:\n";
$discounts = DB::table('discounts')->get();

if ($discounts->count() > 0) {
    foreach ($discounts as $discount) {
        echo "----------------------------\n";
        echo "كود الخصم: " . $discount->code . "\n";
        echo "النوع: " . $discount->type . "\n";
        echo "القيمة: " . $discount->value . "\n";
        echo "الحالة: " . ($discount->is_active ? 'نشط' : 'غير نشط') . "\n";
        echo "تاريخ البدء: " . $discount->start_date . "\n";
        echo "تاريخ الانتهاء: " . $discount->end_date . "\n";
        echo "عدد مرات الاستخدام: " . $discount->usage_count . "\n";
    }
} else {
    echo "لا توجد بيانات في جدول الخصومات.\n";
}

echo "تم الانتهاء من الفحص والإصلاح.\n"; 