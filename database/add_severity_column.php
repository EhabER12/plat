<?php

// استدعاء الملفات الضرورية
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

echo "فحص جدول إشعارات المسؤول...\n";

// التحقق من وجود العمود
if (!Schema::hasColumn('admin_notifications', 'severity')) {
    echo "جاري إضافة العمود 'severity' إلى جدول 'admin_notifications'...\n";
    
    Schema::table('admin_notifications', function (Blueprint $table) {
        $table->tinyInteger('severity')->default(1)->after('is_read');
    });
    
    echo "تم إضافة العمود بنجاح.\n";
} else {
    echo "العمود 'severity' موجود بالفعل في الجدول.\n";
}

// إضافة إشعار تجريبي
if (DB::table('admin_notifications')->count() == 0) {
    echo "إضافة إشعار تجريبي للاختبار...\n";
    
    DB::table('admin_notifications')->insert([
        'title' => 'إشعار تجريبي',
        'message' => 'هذا إشعار تجريبي للتأكد من عمل النظام',
        'type' => 'info',
        'event_type' => 'system',
        'severity' => 3,
        'is_read' => false,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "تم إضافة الإشعار التجريبي بنجاح.\n";
}

echo "عرض بيانات جدول الإشعارات:\n";
$notifications = DB::table('admin_notifications')->get();
foreach ($notifications as $notification) {
    echo "----------------------------\n";
    echo "العنوان: " . $notification->title . "\n";
    echo "الأهمية: " . $notification->severity . "\n";
    echo "النوع: " . $notification->type . "\n";
    echo "حالة القراءة: " . ($notification->is_read ? 'مقروء' : 'غير مقروء') . "\n";
}

echo "تم الانتهاء من الفحص والإصلاح.\n"; 