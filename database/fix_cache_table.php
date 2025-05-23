<?php

// استدعاء الملفات الضرورية
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

echo "فحص جداول الكاش...\n";

// التحقق من وجود جدول cache
if (!Schema::hasTable('cache')) {
    echo "جدول cache غير موجود. جاري الإنشاء...\n";
    
    Schema::create('cache', function (Blueprint $table) {
        $table->string('key')->primary();
        $table->mediumText('value');
        $table->integer('expiration');
    });
    
    echo "تم إنشاء جدول cache بنجاح.\n";
} else {
    echo "جدول cache موجود بالفعل.\n";
}

// التحقق من وجود جدول cache_locks
if (!Schema::hasTable('cache_locks')) {
    echo "جدول cache_locks غير موجود. جاري الإنشاء...\n";
    
    Schema::create('cache_locks', function (Blueprint $table) {
        $table->string('key')->primary();
        $table->string('owner');
        $table->integer('expiration');
    });
    
    echo "تم إنشاء جدول cache_locks بنجاح.\n";
} else {
    echo "جدول cache_locks موجود بالفعل.\n";
}

// التحقق من وجود جدول sessions
if (!Schema::hasTable('sessions')) {
    echo "جدول sessions غير موجود. جاري الإنشاء...\n";
    
    Schema::create('sessions', function (Blueprint $table) {
        $table->string('id')->primary();
        $table->foreignId('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->text('payload');
        $table->integer('last_activity')->index();
    });
    
    echo "تم إنشاء جدول sessions بنجاح.\n";
} else {
    echo "جدول sessions موجود بالفعل.\n";
}

echo "تم الانتهاء من إنشاء وفحص جداول الكاش.\n"; 