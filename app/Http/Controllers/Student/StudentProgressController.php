<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentProgress;
use App\Models\Course;
use App\Models\CourseVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentProgressController extends Controller
{
    /**
     * حفظ تقدم الطالب في مشاهدة فيديو
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveProgress(Request $request)
    {
        try {
            // بدء تتبع تنفيذ الدالة
            Log::debug('-------- Start saveProgress method --------');
            Log::debug('Input data:', $request->all());
            
            // استخراج البيانات من الطلب
            $videoId = $request->input('video_id');
            $progressPercent = $request->input('progress', $request->input('progress_percentage'));
            $currentTime = $request->input('current_time', 0);
            $completed = filter_var($request->input('completed'), FILTER_VALIDATE_BOOLEAN);
            
            if (!$videoId) {
                Log::warning('Missing video_id in request');
                return response()->json(['success' => false, 'error' => 'Missing video ID'], 422);
            }
            
            if ($progressPercent === null) {
                $progressPercent = 0;
                Log::warning('Progress value not provided, using default 0');
            }
            
            // تسجيل القيم المستخرجة
            Log::debug('Parsed values:', [
                'video_id' => $videoId,
                'progress' => $progressPercent,
                'current_time' => $currentTime,
                'completed' => $completed
            ]);
            
            // الحصول على معلومات المستخدم والفيديو
            $user = Auth::user();
            if (!$user) {
                Log::warning('User not authenticated');
                return response()->json(['success' => false, 'error' => 'User not authenticated'], 401);
            }
            
            // الحصول على معلومات الفيديو
            try {
                $video = CourseVideo::find($videoId);
                if (!$video) {
                    Log::warning("Video not found with ID: $videoId");
                    return response()->json(['success' => false, 'error' => 'Video not found'], 404);
                }
                
                // حفظ التقدم باستخدام محاولة مباشرة باستخدام DB::table
                try {
                    // تحضير بيانات التحديث
                    $courseId = $video->course_id;
                    $userId = $user->user_id;
                    $updateData = [
                        'student_id' => $userId,
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'content_type' => 'video',
                        'content_id' => $videoId,
                        'progress_percentage' => min((int)$progressPercent, 100),
                        'last_position' => (float)$currentTime,
                        'updated_at' => now()
                    ];
                    
                    if ($completed || (int)$progressPercent >= 90) {
                        $updateData['completed_at'] = now();
                    }
                    
                    // التحقق من وجود السجل أولاً
                    $exists = DB::table('student_progress')
                        ->where('student_id', $userId)
                        ->where('course_id', $courseId)
                        ->where('content_type', 'video')
                        ->where('content_id', $videoId)
                        ->exists();
                    
                    if ($exists) {
                        // تحديث السجل الموجود
                        DB::table('student_progress')
                            ->where('student_id', $userId)
                            ->where('course_id', $courseId)
                            ->where('content_type', 'video')
                            ->where('content_id', $videoId)
                            ->update($updateData);
                        
                        Log::debug('Updated existing progress record');
                    } else {
                        // إنشاء سجل جديد
                        $updateData['created_at'] = now();
                        DB::table('student_progress')->insert($updateData);
                        Log::debug('Created new progress record');
                    }
                    
                    // اختياري: تحديث نسبة التقدم الكلية للكورس
                    $this->updateCourseProgress($userId, $courseId);
                    
                    // تخزين التقدم في ذاكرة التخزين المؤقت
                    session()->put("video.{$videoId}.progress", [
                        'percentage' => $updateData['progress_percentage'],
                        'last_position' => $updateData['last_position'],
                        'completed' => isset($updateData['completed_at'])
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'تم حفظ التقدم بنجاح',
                        'data' => [
                            'progress_percentage' => $updateData['progress_percentage'],
                            'completed' => isset($updateData['completed_at']),
                            'last_position' => $updateData['last_position']
                        ]
                    ]);
                    
                } catch (\Exception $dbError) {
                    Log::error('Database error: ' . $dbError->getMessage(), [
                        'exception' => $dbError->__toString(),
                        'video_id' => $videoId
                    ]);
                    
                    throw $dbError;
                }
                
            } catch (\Exception $videoException) {
                Log::error('Error finding video: ' . $videoException->getMessage(), [
                    'exception' => $videoException->__toString(),
                    'video_id' => $videoId
                ]);
                throw $videoException;
            }
            
        } catch (\Exception $e) {
            Log::error('Uncaught exception in saveProgress: ' . $e->getMessage(), [
                'exception' => $e->__toString(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء حفظ التقدم',
                'debug_message' => $e->getMessage()
            ], 500);
        } finally {
            Log::debug('-------- End saveProgress method --------');
        }
    }

    /**
     * الحصول على تقدم الطالب في دورة معينة
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourseProgress($courseId)
    {
        try {
            $user = Auth::user();
            
            // التحقق من أن الطالب مسجل في الدورة
            $course = Course::findOrFail($courseId);
            
            // الحصول على جميع سجلات تقدم الطالب للدورة
            $progress = StudentProgress::where('student_id', $user->user_id)
                ->where('course_id', $courseId)
                ->where('content_type', 'video')
                ->get();
            
            // الحصول على قائمة الفيديوهات المكتملة
            $completedVideos = $progress->filter(function($item) {
                return $item->isCompleted();
            })->pluck('content_id')->toArray();
            
            // حساب نسبة التقدم الكلية
            $totalVideos = CourseVideo::where('course_id', $courseId)->count();
            $totalPercentage = $totalVideos > 0 
                ? round(count($completedVideos) / $totalVideos * 100) 
                : 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'completed_videos' => $completedVideos,
                    'total_videos' => $totalVideos,
                    'total_percentage' => $totalPercentage,
                    'progress_records' => $progress
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في استرجاع تقدم الطالب: ' . $e->getMessage(), [
                'exception' => $e,
                'course_id' => $courseId,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استرجاع التقدم: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على تقدم الطالب في فيديو معين
     *
     * @param  int  $videoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVideoProgress($videoId)
    {
        try {
            $user = Auth::user();
            
            // الحصول على معلومات الفيديو
            $video = CourseVideo::find($videoId);
            
            if (!$video) {
                Log::warning("Video not found with ID: $videoId");
                return response()->json([
                    'success' => false,
                    'error' => 'Video not found'
                ], 404);
            }
            
            // تحقق أولاً من وجود المعلومات في الجلسة للسرعة
            $sessionProgress = session("video.{$videoId}.progress");
            if ($sessionProgress) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'exists' => true,
                        'percentage' => $sessionProgress['percentage'],
                        'last_position' => $sessionProgress['last_position'],
                        'completed' => $sessionProgress['completed'],
                        'source' => 'session'
                    ]
                ]);
            }
            
            // البحث عن سجل تقدم موجود
            $progress = DB::table('student_progress')
                ->where('student_id', $user->user_id)
                ->where('course_id', $video->course_id)
                ->where('content_type', 'video')
                ->where('content_id', $videoId)
                ->first();
            
            if (!$progress) {
                // تخزين القيم الافتراضية في الجلسة
                session()->put("video.{$videoId}.progress", [
                    'percentage' => 0,
                    'last_position' => 0,
                    'completed' => false
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'exists' => false,
                        'percentage' => 0,
                        'last_position' => 0,
                        'completed' => false,
                        'source' => 'default'
                    ]
                ]);
            }
            
            // تحديد ما إذا كان الفيديو مكتملاً
            $isCompleted = !empty($progress->completed_at) || $progress->progress_percentage >= 90;
            
            // تخزين المعلومات في الجلسة للوصول السريع لاحقاً
            session()->put("video.{$videoId}.progress", [
                'percentage' => $progress->progress_percentage,
                'last_position' => $progress->last_position,
                'completed' => $isCompleted
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'exists' => true,
                    'percentage' => $progress->progress_percentage,
                    'last_position' => $progress->last_position,
                    'completed' => $isCompleted,
                    'source' => 'database'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('خطأ في استرجاع تقدم الفيديو: ' . $e->getMessage(), [
                'exception' => $e,
                'video_id' => $videoId,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استرجاع تقدم الفيديو: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث نسبة التقدم الكلية للكورس
     * 
     * @param int $studentId
     * @param int $courseId
     * @return float نسبة التقدم المئوية
     */
    protected function updateCourseProgress($studentId, $courseId)
    {
        try {
            // الحصول على إجمالي عدد فيديوهات الكورس
            $totalVideos = CourseVideo::where('course_id', $courseId)->count();
            
            if ($totalVideos === 0) {
                return 0; // لا يوجد فيديوهات في الكورس
            }
            
            // حساب عدد الفيديوهات المكتملة
            $completedVideos = DB::table('student_progress')
                ->where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->where('content_type', 'video')
                ->where(function($query) {
                    $query->whereNotNull('completed_at')
                          ->orWhere('progress_percentage', '>=', 90);
                })
                ->count();
            
            // حساب إجمالي التقدم (النسبة المئوية)
            $totalProgress = ($completedVideos / $totalVideos) * 100;
            
            // تحديث نسبة التقدم في جدول enrollments إذا كان موجوداً
            if (Schema::hasTable('enrollments') && 
                Schema::hasColumn('enrollments', 'progress_percentage')) {
                
                DB::table('enrollments')
                    ->where('student_id', $studentId)
                    ->where('course_id', $courseId)
                    ->update([
                        'progress_percentage' => $totalProgress,
                        'updated_at' => now()
                    ]);
            }
            
            // تخزين نسبة التقدم في الجلسة للوصول السريع
            session()->put("course.{$courseId}.progress", $totalProgress);
            
            return $totalProgress;
            
        } catch (\Exception $e) {
            Log::error('Error updating course progress: ' . $e->getMessage(), [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'exception' => $e->getMessage()
            ]);
            
            return 0; // إرجاع صفر في حالة حدوث خطأ
        }
    }
} 