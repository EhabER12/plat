<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseVideo;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    /**
     * عرض محتوى الكورس ومعلومات الفيديو الحالي
     *
     * @param int $courseId
     * @param int|null $videoId
     * @return \Illuminate\View\View
     */
    public function show($courseId, $videoId = null)
    {
        $student = Auth::user();
        
        // الحصول على الكورس ومعلوماته
        $course = Course::with(['sections.videos', 'instructor'])
            ->whereHas('enrollments', function($query) use ($student) {
                $query->where('student_id', $student->user_id);
            })
            ->findOrFail($courseId);
            
        // تحديد الفيديو الحالي
        $currentVideo = null;
        $currentSection = null;
        $previousVideo = null;
        $nextVideo = null;
        
        if ($videoId) {
            $currentVideo = CourseVideo::findOrFail($videoId);
            $currentSection = CourseSection::find($currentVideo->section_id);
            
            // تحميل معلومات تقدم الطالب في هذا الفيديو
            $videoProgress = StudentProgress::where('student_id', $student->user_id)
                ->where('course_id', $courseId)
                ->where('content_type', 'video')
                ->where('content_id', $videoId)
                ->first();
                
            // البحث عن الفيديو السابق والتالي
            $previousVideo = CourseVideo::whereHas('section', function($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })
            ->where('video_id', '<', $currentVideo->video_id)
            ->orderBy('video_id', 'desc')
            ->first();
            
            $nextVideo = CourseVideo::whereHas('section', function($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })
            ->where('video_id', '>', $currentVideo->video_id)
            ->orderBy('video_id', 'asc')
            ->first();
        } else {
            // إذا لم يتم تحديد فيديو، نستخدم أول فيديو في الكورس
            $firstSection = $course->sections->first();
            if ($firstSection) {
                $currentSection = $firstSection;
                $currentVideo = $firstSection->videos->first();
                if ($currentVideo) {
                    // تحميل معلومات تقدم الطالب في هذا الفيديو
                    $videoProgress = StudentProgress::where('student_id', $student->user_id)
                        ->where('course_id', $courseId)
                        ->where('content_type', 'video')
                        ->where('content_id', $currentVideo->video_id)
                        ->first();
                        
                    // البحث عن الفيديو التالي
                    $nextVideo = CourseVideo::whereHas('section', function($q) use ($courseId) {
                        $q->where('course_id', $courseId);
                    })
                    ->where('video_id', '>', $currentVideo->video_id)
                    ->orderBy('video_id', 'asc')
                    ->first();
                }
            }
        }
        
        // حساب نسبة التقدم في الكورس
        $totalVideos = CourseVideo::whereHas('section', function($q) use ($courseId) {
            $q->where('course_id', $courseId);
        })->count();
        
        $completedVideosCount = StudentProgress::where('student_id', $student->user_id)
            ->where('course_id', $courseId)
            ->where('content_type', 'video')
            ->where(function($query) {
                $query->whereNotNull('completed_at')
                    ->orWhere('progress_percentage', '>=', 90);
            })
            ->count();
            
        $courseProgress = $totalVideos > 0 ? round(($completedVideosCount / $totalVideos) * 100) : 0;
        
        // استخراج قائمة الفيديوهات المكتملة
        $completedVideos = StudentProgress::where('student_id', $student->user_id)
            ->where('course_id', $courseId)
            ->where('content_type', 'video')
            ->where(function($query) {
                $query->whereNotNull('completed_at')
                    ->orWhere('progress_percentage', '>=', 90);
            })
            ->pluck('content_id')
            ->toArray();
        
        return view('student.course-content', compact(
            'course',
            'currentVideo',
            'currentSection',
            'previousVideo',
            'nextVideo',
            'videoProgress',
            'courseProgress',
            'completedVideos'
        ));
    }
} 