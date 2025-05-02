<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseVideo;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VideoInfoController extends Controller
{
    /**
     * Get video information
     *
     * @param Request $request
     * @param int $videoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVideoInfo(Request $request, $videoId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the video
        $video = CourseVideo::find($videoId);
        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        // Check if the user is the instructor of the course or enrolled in the course
        $course = Course::find($video->course_id);
        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $isInstructor = $course->instructor_id == $user->user_id;
        $isEnrolled = Enrollment::where('student_id', $user->user_id)
            ->where('course_id', $video->course_id)
            ->exists();

        // Check if the video is a free preview
        $isFreePreview = $video->is_free_preview;

        if (!$isInstructor && !$isEnrolled && !$isFreePreview) {
            return response()->json(['error' => 'You do not have access to this video'], 403);
        }

        // Get video information
        $videoInfo = [
            'video_id' => $video->video_id,
            'title' => $video->title,
            'description' => $video->description,
            'duration' => $video->duration_seconds,
            'is_encrypted' => $video->is_encrypted,
            'segments' => 1, // Default for non-encrypted videos
        ];

        // If the video is encrypted, get the number of segments
        if ($video->is_encrypted) {
            $manifestPath = $video->video_path . '/manifest.json';
            if (Storage::exists($manifestPath)) {
                $manifest = json_decode(Storage::get($manifestPath), true);
                if ($manifest && isset($manifest['total_segments'])) {
                    $videoInfo['segments'] = $manifest['total_segments'];
                }
            }
        }

        return response()->json($videoInfo);
    }
}
