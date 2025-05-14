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
            // Start tracking method execution
            Log::debug('-------- Start saveProgress method --------');
            Log::debug('Input data:', $request->all());
            
            // Check database table structure for potential issues
            try {
                $columns = Schema::getColumnListing('student_progress');
                Log::debug('Student Progress table columns:', $columns);
                
                // Verify key columns exist
                $requiredColumns = [
                    'student_id', 'course_id', 'content_type', 
                    'content_id', 'progress_percentage', 'last_position',
                    'completed_at'
                ];
                
                $missingColumns = array_diff($requiredColumns, $columns);
                if (!empty($missingColumns)) {
                    Log::error('Missing required columns in student_progress table:', $missingColumns);
                }
            } catch (\Exception $schemaException) {
                Log::error('Error checking schema: ' . $schemaException->getMessage());
            }
            
            // First try the simplified approach for diagnosis
            $videoId = $request->input('video_id');
            $progressPercent = $request->input('progress', $request->input('progress_percentage'));
            $currentTime = $request->input('current_time', 0);
            $completed = $request->input('completed') == true || $request->input('completed') == "1" || $request->input('completed') == 1;
            
            if (!$videoId) {
                Log::warning('Missing video_id in request');
                return response()->json(['success' => false, 'error' => 'Missing video ID'], 422);
            }
            
            if (!$progressPercent) {
                Log::warning('Missing progress value in request');
                return response()->json(['success' => false, 'error' => 'Missing progress value'], 422);
            }
            
            Log::debug('Parsed values:', [
                'video_id' => $videoId,
                'progress' => $progressPercent,
                'current_time' => $currentTime,
                'completed' => $completed
            ]);
            
            // Get the user and video information
            $user = Auth::user();
            if (!$user) {
                Log::warning('User not authenticated');
                return response()->json(['success' => false, 'error' => 'User not authenticated'], 401);
            }
            
            Log::debug('User found:', [
                'user_id' => $user->user_id,
                'name' => $user->name
            ]);
            
            try {
                $video = CourseVideo::find($videoId);
                if (!$video) {
                    Log::warning("Video not found with ID: $videoId");
                    return response()->json(['success' => false, 'error' => 'Video not found'], 404);
                }
                
                Log::debug('Video found:', [
                    'video_id' => $video->video_id,
                    'title' => $video->title,
                    'course_id' => $video->course_id
                ]);
                
                // Create basic data arrays
                $searchParams = [
                    'student_id' => $user->user_id,
                    'course_id' => $video->course_id,
                    'content_type' => 'video',
                    'content_id' => $videoId,
                    'user_id' => $user->user_id,
                ];
                
                $updateParams = [
                    'progress_percentage' => min((int)$progressPercent, 100),
                    'last_position' => (float)$currentTime,
                    'completed_at' => ($completed || (int)$progressPercent >= 90) ? now() : null,
                    'user_id' => $user->user_id,
                ];
                
                Log::debug('Search and update parameters:', [
                    'search_params' => $searchParams,
                    'update_params' => $updateParams
                ]);
                
                // Simple approach - just use updateOrCreate
                try {
                    Log::debug('Attempting updateOrCreate to save progress');
                    
                    // Try to find an existing record
                    $progress = StudentProgress::where([
                        'student_id' => $user->user_id,
                        'course_id' => $video->course_id,
                        'content_type' => 'video',
                        'content_id' => $videoId,
                    ])->first();

                    // Update or create new
                    if ($progress) {
                        // Update existing
                        $progress->progress_percentage = min((int)$progressPercent, 100);
                        $progress->last_position = (float)$currentTime;
                        $progress->user_id = $user->user_id;
                        if ($completed || (int)$progressPercent >= 90) {
                            $progress->completed_at = now();
                        }
                        $progress->save();
                    } else {
                        // Create new record
                        $progress = new StudentProgress();
                        $progress->student_id = $user->user_id;
                        $progress->user_id = $user->user_id;
                        $progress->course_id = $video->course_id;
                        $progress->content_type = 'video';
                        $progress->content_id = $videoId;
                        $progress->progress_percentage = min((int)$progressPercent, 100);
                        $progress->last_position = (float)$currentTime;
                        if ($completed || (int)$progressPercent >= 90) {
                            $progress->completed_at = now();
                        }
                        $progress->save();
                    }
                    
                    Log::debug('Successfully saved student progress');
                    return response()->json(['success' => true]);
                } catch (\Exception $dbError) {
                    Log::error('Error in updateOrCreate operation: ' . $dbError->getMessage(), [
                        'exception' => $dbError->__toString(),
                        'search_params' => $searchParams,
                        'update_params' => $updateParams
                    ]);
                    
                    // Last resort direct SQL
                    try {
                        Log::debug('Attempting direct SQL update as fallback');
                        
                        DB::table('student_progress')->updateOrInsert(
                            $searchParams,
                            array_merge($updateParams, ['updated_at' => now()])
                        );
                        
                        Log::debug('Direct SQL update successful');
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'تم حفظ التقدم باستخدام الخطة البديلة',
                            'data' => [
                                'progress_percentage' => $updateParams['progress_percentage'],
                                'completed' => $updateParams['completed_at'] !== null,
                                'last_position' => $updateParams['last_position']
                            ]
                        ]);
                        
                    } catch (\Exception $sqlException) {
                        Log::error('Even direct SQL update failed: ' . $sqlException->getMessage(), [
                            'exception' => $sqlException->__toString(),
                            'search_params' => $searchParams
                        ]);
                        
                        throw $sqlException;
                    }
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
                'debug_message' => env('APP_DEBUG') ? $e->getMessage() : null
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
            $video = CourseVideo::findOrFail($videoId);
            
            // البحث عن سجل تقدم موجود
            $progress = StudentProgress::where('student_id', $user->user_id)
                ->where('course_id', $video->course_id)
                ->where('content_type', 'video')
                ->where('content_id', $videoId)
                ->first();
            
            if (!$progress) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'exists' => false,
                        'percentage' => 0,
                        'last_position' => 0,
                        'completed' => false
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'exists' => true,
                    'percentage' => $progress->progress_percentage,
                    'last_position' => $progress->last_position,
                    'completed' => $progress->isCompleted()
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
} 