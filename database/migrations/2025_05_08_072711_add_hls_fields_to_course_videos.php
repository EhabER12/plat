<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            if (!Schema::hasColumn('course_videos', 'hls_path')) {
                $table->string('hls_path')->nullable()->after('video_path')->comment('مسار ملف HLS الرئيسي (m3u8)');
            }
            
            if (!Schema::hasColumn('course_videos', 'hls_url')) {
                $table->string('hls_url')->nullable()->after('hls_path')->comment('عنوان URL لملف HLS');
            }
            
            if (!Schema::hasColumn('course_videos', 'is_hls_enabled')) {
                $table->boolean('is_hls_enabled')->default(false)->after('is_encrypted')->comment('هل تم تمكين تدفق HLS');
            }
            
            if (!Schema::hasColumn('course_videos', 'encryption_key')) {
                $table->string('encryption_key')->nullable()->after('is_hls_enabled')->comment('مفتاح التشفير المستخدم (مشفر)');
            }
            
            if (!Schema::hasColumn('course_videos', 'hls_segments_path')) {
                $table->string('hls_segments_path')->nullable()->after('encryption_key')->comment('مسار دليل المقاطع');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            $columns = [
                'hls_path',
                'hls_url',
                'is_hls_enabled',
                'encryption_key',
                'hls_segments_path'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('course_videos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
