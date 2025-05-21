<?php

// استدعاء الملفات الضرورية
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

echo "فحص جدول التسجيلات (enrollments)...\n";

// التحقق من وجود الجدول
if (!Schema::hasTable('enrollments')) {
    echo "جدول التسجيلات غير موجود. جاري الإنشاء...\n";
    
    Schema::create('enrollments', function (Blueprint $table) {
        $table->id('enrollment_id');
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('student_id');
        $table->unsignedBigInteger('course_id');
        $table->decimal('paid_amount', 10, 2)->nullable();
        $table->timestamp('enrolled_at')->useCurrent();
        $table->timestamp('completed_at')->nullable();
        $table->decimal('progress', 5, 2)->default(0.00);
        $table->timestamp('last_activity_at')->nullable();
        $table->timestamps();
        
        $table->unique(['student_id', 'course_id']);
        
        if (Schema::hasTable('users')) {
            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        }
        
        if (Schema::hasTable('courses')) {
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
        }
    });
    
    echo "تم إنشاء جدول التسجيلات بنجاح.\n";
} else {
    echo "جدول التسجيلات موجود بالفعل. جاري التحقق من البنية...\n";
    
    // التحقق من وجود عمود user_id
    if (!Schema::hasColumn('enrollments', 'user_id')) {
        echo "عمود user_id غير موجود. جاري إضافته...\n";
        
        Schema::table('enrollments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('enrollment_id');
            
            if (Schema::hasTable('users')) {
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            }
        });
        
        echo "تم إضافة عمود user_id بنجاح.\n";
    } else {
        echo "عمود user_id موجود بالفعل.\n";
    }
    
    // التحقق من وجود عمود student_id
    if (!Schema::hasColumn('enrollments', 'student_id')) {
        echo "عمود student_id غير موجود. جاري إضافته...\n";
        
        // إضافة عمود student_id
        Schema::table('enrollments', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id')->after('user_id');
            
            // إضافة foreign key إذا كان جدول المستخدمين موجودا
            if (Schema::hasTable('users')) {
                $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            }
        });
        
        echo "تم إضافة عمود student_id بنجاح.\n";
        
        // تحديث البيانات الموجودة
        $enrollments = DB::table('enrollments')->get();
        
        if ($enrollments->count() > 0) {
            echo "تحديث بيانات التسجيلات الموجودة...\n";
            
            // البحث عن الطلاب
            $students = DB::table('users')
                ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                ->where('user_roles.role', 'student')
                ->get();
            
            if ($students->count() > 0) {
                $studentId = $students->first()->user_id;
                
                // تحديث جميع التسجيلات لتكون مرتبطة بأول طالب
                DB::table('enrollments')->update(['student_id' => $studentId]);
                
                echo "تم تحديث التسجيلات لتكون مرتبطة بالطالب ذو المعرف: $studentId\n";
            } else {
                echo "لم يتم العثور على أي طلاب في النظام. يرجى إضافة طلاب أولاً.\n";
            }
        }
    } else {
        echo "عمود student_id موجود بالفعل.\n";
    }
    
    // التحقق من وجود عمود paid_amount
    if (!Schema::hasColumn('enrollments', 'paid_amount')) {
        echo "عمود paid_amount غير موجود. جاري إضافته...\n";
        
        Schema::table('enrollments', function (Blueprint $table) {
            $table->decimal('paid_amount', 10, 2)->nullable()->after('course_id');
        });
        
        echo "تم إضافة عمود paid_amount بنجاح.\n";
    } else {
        echo "عمود paid_amount موجود بالفعل.\n";
    }
    
    // التحقق من وجود عمود progress
    if (!Schema::hasColumn('enrollments', 'progress')) {
        echo "عمود progress غير موجود. جاري إضافته...\n";
        
        Schema::table('enrollments', function (Blueprint $table) {
            $table->decimal('progress', 5, 2)->default(0.00)->after('completed_at');
        });
        
        echo "تم إضافة عمود progress بنجاح.\n";
    } else {
        echo "عمود progress موجود بالفعل.\n";
    }
    
    // التحقق من وجود عمود last_activity_at
    if (!Schema::hasColumn('enrollments', 'last_activity_at')) {
        echo "عمود last_activity_at غير موجود. جاري إضافته...\n";
        
        Schema::table('enrollments', function (Blueprint $table) {
            $table->timestamp('last_activity_at')->nullable()->after('progress');
        });
        
        echo "تم إضافة عمود last_activity_at بنجاح.\n";
    } else {
        echo "عمود last_activity_at موجود بالفعل.\n";
    }
    
    // التحقق من وجود عمود enrolled_at
    if (!Schema::hasColumn('enrollments', 'enrolled_at')) {
        echo "عمود enrolled_at غير موجود. جاري إضافته...\n";
        
        Schema::table('enrollments', function (Blueprint $table) {
            $table->timestamp('enrolled_at')->useCurrent()->after('paid_amount');
        });
        
        echo "تم إضافة عمود enrolled_at بنجاح.\n";
    } else {
        echo "عمود enrolled_at موجود بالفعل.\n";
    }
    
    // التحقق من وجود عمود completed_at
    if (!Schema::hasColumn('enrollments', 'completed_at')) {
        echo "عمود completed_at غير موجود. جاري إضافته...\n";
        
        Schema::table('enrollments', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('enrolled_at');
        });
        
        echo "تم إضافة عمود completed_at بنجاح.\n";
    } else {
        echo "عمود completed_at موجود بالفعل.\n";
    }
}

// إضافة تسجيلات تجريبية إذا كان الجدول فارغا
$enrollmentsCount = DB::table('enrollments')->count();

if ($enrollmentsCount == 0) {
    echo "لا توجد تسجيلات في الجدول. جاري إضافة بيانات تجريبية...\n";
    
    // البحث عن طلاب
    $students = DB::table('users')
        ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
        ->where('user_roles.role', 'student')
        ->get();
    
    // البحث عن دورات
    $courses = DB::table('courses')->get();
    
    if ($students->count() > 0 && $courses->count() > 0) {
        $studentId = $students->first()->user_id;
        
        // تحقق من المعلومات المطلوبة قبل الإدراج
        echo "التحقق من الأعمدة المتوفرة قبل الإدراج...\n";
        
        // استرجاع جميع أسماء الأعمدة في جدول enrollments
        $columns = Schema::getColumnListing('enrollments');
        echo "الأعمدة المتوفرة في جدول enrollments: " . implode(', ', $columns) . "\n";
        
        // التجهيز لإدراج البيانات
        foreach ($courses->take(3) as $index => $course) {
            $data = [
                'student_id' => $studentId,
                'user_id' => $studentId, // نستخدم نفس معرف الطالب لحقل user_id
                'course_id' => $course->course_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // إضافة الأعمدة الأخرى إذا كانت موجودة في الجدول
            if (in_array('status', $columns)) {
                $data['status'] = 'active';
            }
            
            if (in_array('enrolled_at', $columns)) {
                $data['enrolled_at'] = now()->subDays(30 - $index * 5);
            }
            
            if (in_array('progress', $columns)) {
                $data['progress'] = rand(0, 100);
            }
            
            if (in_array('last_activity_at', $columns)) {
                $data['last_activity_at'] = now()->subDays(rand(1, 10));
            }
            
            if (in_array('paid_amount', $columns) && isset($course->price)) {
                $data['paid_amount'] = $course->price;
            }
            
            // إدراج البيانات
            DB::table('enrollments')->insert($data);
        }
        
        echo "تم إضافة تسجيلات تجريبية بنجاح.\n";
    } else {
        echo "لا يمكن إضافة تسجيلات تجريبية لعدم وجود طلاب أو دورات.\n";
    }
}

echo "عرض بيانات جدول التسجيلات:\n";
$enrollments = DB::table('enrollments')
    ->leftJoin('users', 'enrollments.student_id', '=', 'users.user_id')
    ->leftJoin('courses', 'enrollments.course_id', '=', 'courses.course_id')
    ->select('enrollments.*', 'users.name as student_name', 'courses.title as course_title')
    ->get();

if ($enrollments->count() > 0) {
    foreach ($enrollments as $enrollment) {
        echo "----------------------------\n";
        echo "رقم التسجيل: " . $enrollment->enrollment_id . "\n";
        echo "الطالب: " . ($enrollment->student_name ?? 'غير معروف') . "\n";
        echo "الدورة: " . ($enrollment->course_title ?? 'غير معروفة') . "\n";
        if (property_exists($enrollment, 'enrolled_at')) {
            echo "تاريخ التسجيل: " . $enrollment->enrolled_at . "\n";
        }
        if (property_exists($enrollment, 'progress')) {
            echo "نسبة التقدم: " . $enrollment->progress . "%\n";
        }
    }
} else {
    echo "لا توجد بيانات في جدول التسجيلات.\n";
}

echo "تم الانتهاء من الفحص والإصلاح.\n"; 