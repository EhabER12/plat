<?php

// تحميل Laravel
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// قائمة الكلمات المحظورة
$arabicBannedWords = [
    "ابن العرص",
    "ابن المتناكة",
    "متناك",
    "متناكة",
    "ابن الخول",
    "ابن الكلب",
    "تعالي واتس",
    "واتساب",
    "خول",
    "ابن الخول",
    "معرص",
    "متناك"
];

// إضافة الكلمات إلى قاعدة البيانات
$added = 0;
$existing = 0;

foreach ($arabicBannedWords as $word) {
    // تحقق ما إذا كانت الكلمة موجودة بالفعل
    $exists = \App\Models\BannedWord::where('word', $word)->exists();
    
    if (!$exists) {
        // إضافة كلمة جديدة
        \App\Models\BannedWord::create([
            'word' => $word,
            'type' => 'profanity',
            'replacement' => str_repeat('*', mb_strlen($word)),
            'severity' => 5,
            'active' => true,
            'notes' => 'تمت الإضافة يدويًا'
        ]);
        
        $added++;
        echo "تمت إضافة كلمة: {$word}" . PHP_EOL;
    } else {
        $existing++;
        echo "الكلمة موجودة بالفعل: {$word}" . PHP_EOL;
    }
}

// مسح كاش الكلمات المحظورة
echo "تم مسح كاش الكلمات المحظورة" . PHP_EOL;
\Illuminate\Support\Facades\Cache::forget('banned_words');

echo "تمت العملية بنجاح: تمت إضافة {$added} كلمة، {$existing} كلمة كانت موجودة بالفعل" . PHP_EOL; 