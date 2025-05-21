<?php

// استدعاء الملفات الضرورية
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

echo "فحص جدول العلاقات بين الخصومات والدورات...\n";

// التحقق من وجود الجدول
if (!Schema::hasTable('discount_courses')) {
    echo "جدول العلاقات بين الخصومات والدورات غير موجود. جاري الإنشاء...\n";
    
    Schema::create('discount_courses', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('discount_id');
        $table->unsignedBigInteger('course_id');
        $table->timestamps();
        
        $table->unique(['discount_id', 'course_id']);
        
        // إضافة المفاتيح الأجنبية
        if (Schema::hasTable('discounts')) {
            $table->foreign('discount_id')->references('discount_id')->on('discounts')->onDelete('cascade');
        }
        
        if (Schema::hasTable('courses')) {
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
        }
    });
    
    echo "تم إنشاء جدول العلاقات بين الخصومات والدورات بنجاح.\n";
    
    // إضافة بيانات تجريبية
    echo "جاري إضافة بيانات تجريبية للعلاقات...\n";
    
    // استرجاع الخصومات
    $discounts = DB::table('discounts')->get();
    
    // استرجاع الدورات
    $courses = DB::table('courses')->get();
    
    if ($discounts->count() > 0 && $courses->count() > 0) {
        // تعيين الخصم الأول لجميع الدورات (الخصم العام)
        $welcomeDiscount = $discounts->where('code', 'WELCOME25')->first();
        if ($welcomeDiscount) {
            foreach ($courses as $course) {
                DB::table('discount_courses')->insert([
                    'discount_id' => $welcomeDiscount->discount_id,
                    'course_id' => $course->course_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            echo "تم تعيين كود الخصم WELCOME25 لجميع الدورات.\n";
        }
        
        // تعيين الخصم الثاني لبعض الدورات
        $flatDiscount = $discounts->where('code', 'FLAT50')->first();
        if ($flatDiscount && $courses->count() >= 2) {
            for ($i = 0; $i < min(2, $courses->count()); $i++) {
                DB::table('discount_courses')->insert([
                    'discount_id' => $flatDiscount->discount_id,
                    'course_id' => $courses[$i]->course_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            echo "تم تعيين كود الخصم FLAT50 لأول دورتين.\n";
        }
        
        // تعيين الخصم الثالث لدورة واحدة
        $summerDiscount = $discounts->where('code', 'SUMMER30')->first();
        if ($summerDiscount && $courses->count() >= 3) {
            DB::table('discount_courses')->insert([
                'discount_id' => $summerDiscount->discount_id,
                'course_id' => $courses[2]->course_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "تم تعيين كود الخصم SUMMER30 للدورة الثالثة.\n";
        }
        
        echo "تم الانتهاء من إضافة جميع العلاقات بنجاح.\n";
    } else {
        echo "لا توجد خصومات أو دورات في قاعدة البيانات. قم بإنشائها أولاً.\n";
    }
} else {
    echo "جدول العلاقات بين الخصومات والدورات موجود بالفعل.\n";
    
    // التحقق من وجود البيانات
    $relationCount = DB::table('discount_courses')->count();
    
    if ($relationCount == 0) {
        echo "لا توجد بيانات في جدول العلاقات. جاري إضافة بيانات تجريبية...\n";
        
        // استرجاع الخصومات
        $discounts = DB::table('discounts')->get();
        
        // استرجاع الدورات
        $courses = DB::table('courses')->get();
        
        if ($discounts->count() > 0 && $courses->count() > 0) {
            // تعيين الخصم الأول لجميع الدورات (الخصم العام)
            $welcomeDiscount = $discounts->where('code', 'WELCOME25')->first();
            if ($welcomeDiscount) {
                foreach ($courses as $course) {
                    DB::table('discount_courses')->insert([
                        'discount_id' => $welcomeDiscount->discount_id,
                        'course_id' => $course->course_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                echo "تم تعيين كود الخصم WELCOME25 لجميع الدورات.\n";
            }
            
            // تعيين الخصم الثاني لبعض الدورات
            $flatDiscount = $discounts->where('code', 'FLAT50')->first();
            if ($flatDiscount && $courses->count() >= 2) {
                for ($i = 0; $i < min(2, $courses->count()); $i++) {
                    DB::table('discount_courses')->insert([
                        'discount_id' => $flatDiscount->discount_id,
                        'course_id' => $courses[$i]->course_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                echo "تم تعيين كود الخصم FLAT50 لأول دورتين.\n";
            }
            
            // تعيين الخصم الثالث لدورة واحدة
            $summerDiscount = $discounts->where('code', 'SUMMER30')->first();
            if ($summerDiscount && $courses->count() >= 3) {
                DB::table('discount_courses')->insert([
                    'discount_id' => $summerDiscount->discount_id,
                    'course_id' => $courses[2]->course_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                echo "تم تعيين كود الخصم SUMMER30 للدورة الثالثة.\n";
            }
            
            echo "تم الانتهاء من إضافة جميع العلاقات بنجاح.\n";
        } else {
            echo "لا توجد خصومات أو دورات في قاعدة البيانات. قم بإنشائها أولاً.\n";
        }
    } else {
        echo "توجد بالفعل $relationCount علاقة في الجدول.\n";
    }
}

// عرض العلاقات
echo "عرض العلاقات بين الخصومات والدورات:\n";

$relations = DB::table('discount_courses')
    ->join('discounts', 'discount_courses.discount_id', '=', 'discounts.discount_id')
    ->join('courses', 'discount_courses.course_id', '=', 'courses.course_id')
    ->select('discounts.code', 'discounts.type', 'discounts.value', 'courses.title')
    ->get();

if ($relations->count() > 0) {
    foreach ($relations as $relation) {
        echo "----------------------------\n";
        echo "كود الخصم: " . $relation->code . "\n";
        echo "نوع الخصم: " . $relation->type . "\n";
        echo "قيمة الخصم: " . $relation->value . "\n";
        echo "الدورة: " . $relation->title . "\n";
    }
} else {
    echo "لا توجد علاقات بين الخصومات والدورات.\n";
}

// التحقق من أن كل خصم له علاقات مع دورات
$discountsWithNoCourses = DB::table('discounts')
    ->leftJoin('discount_courses', 'discounts.discount_id', '=', 'discount_courses.discount_id')
    ->whereNull('discount_courses.id')
    ->select('discounts.*')
    ->get();

if ($discountsWithNoCourses->count() > 0) {
    echo "\nالخصومات التي ليس لها علاقات مع دورات:\n";
    foreach ($discountsWithNoCourses as $discount) {
        echo "- " . $discount->code . " (" . $discount->description . ")\n";
    }
}

echo "تم الانتهاء من الفحص والإصلاح.\n"; 