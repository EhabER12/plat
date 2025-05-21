<?php

// استدعاء الملفات الضرورية
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

echo "فحص جدول مظاهر الموقع...\n";

// التحقق من وجود العمود
if (Schema::hasTable('website_appearances')) {
    if (!Schema::hasColumn('website_appearances', 'section')) {
        echo "جاري إضافة العمود 'section' إلى جدول 'website_appearances'...\n";
        
        Schema::table('website_appearances', function (Blueprint $table) {
            $table->string('section')->after('id');
        });
        
        echo "تم إضافة العمود بنجاح.\n";
        
        // إضافة بيانات تجريبية
        DB::table('website_appearances')->where('id', 1)->update(['section' => 'footer']);
        
        echo "تم تحديث البيانات التجريبية بنجاح.\n";
    } else {
        echo "العمود 'section' موجود بالفعل في الجدول.\n";
    }
} else {
    echo "جدول website_appearances غير موجود. جاري الإنشاء...\n";
    
    Schema::create('website_appearances', function (Blueprint $table) {
        $table->id();
        $table->string('section');
        $table->text('content')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    
    echo "تم إنشاء جدول website_appearances بنجاح.\n";
    
    // إضافة بيانات تجريبية
    DB::table('website_appearances')->insert([
        'section' => 'footer',
        'content' => json_encode([
            'text' => 'جميع الحقوق محفوظة © منصة التعليم الإلكتروني ' . date('Y'),
            'links' => [
                ['text' => 'الرئيسية', 'url' => '/'],
                ['text' => 'الدورات', 'url' => '/courses'],
                ['text' => 'من نحن', 'url' => '/about'],
                ['text' => 'اتصل بنا', 'url' => '/contact'],
            ]
        ]),
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "تم إضافة بيانات تجريبية للجدول بنجاح.\n";
}

// عرض بيانات الجدول
echo "عرض بيانات جدول مظاهر الموقع:\n";
$appearances = DB::table('website_appearances')->get();

if ($appearances->count() > 0) {
    foreach ($appearances as $appearance) {
        echo "----------------------------\n";
        echo "المعرف: " . $appearance->id . "\n";
        echo "القسم: " . $appearance->section . "\n";
        echo "نشط: " . ($appearance->is_active ? 'نعم' : 'لا') . "\n";
    }
} else {
    echo "لا توجد بيانات في جدول مظاهر الموقع.\n";
}

echo "تم الانتهاء من الفحص والإصلاح.\n"; 