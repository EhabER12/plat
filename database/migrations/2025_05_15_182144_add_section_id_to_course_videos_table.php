<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * هذا الترحيل يضيف عمود section_id إلى جدول course_videos
     * لإصلاح مشكلة عدم ظهور الفيديوهات في السكشن.
     */
    public function up(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            // إضافة عمود section_id إذا لم يكن موجوداً
            if (!Schema::hasColumn('course_videos', 'section_id')) {
                $table->unsignedBigInteger('section_id')->nullable()->after('course_id');
                
                // إضافة فهرس للبحث السريع
                $table->index('section_id');
                
                // إضافة مفتاح أجنبي للتحقق من وجود السكشن في جدول course_sections
                $table->foreign('section_id')
                      ->references('section_id')
                      ->on('course_sections')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            // حذف المفتاح الأجنبي أولا
            if (Schema::hasColumn('course_videos', 'section_id')) {
                $table->dropForeign(['section_id']);
                $table->dropIndex(['section_id']);
                $table->dropColumn('section_id');
            }
        });
    }
};
