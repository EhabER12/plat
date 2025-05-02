<?php

namespace App\Http\Controllers;

use App\Models\DownloadAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockedAccessController extends Controller
{
    /**
     * Show the blocked access page
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $videoId = $request->query('video_id');
        $courseId = $request->query('course_id');
        
        // Get the most recent block
        $block = DownloadAttempt::where('user_id', $user->user_id)
            ->where('is_blocked', true)
            ->where('blocked_until', '>', now())
            ->when($videoId, function ($query) use ($videoId) {
                return $query->where('video_id', $videoId);
            })
            ->orderBy('blocked_until', 'desc')
            ->first();
            
        if (!$block) {
            // If no active block is found, redirect to courses page
            return redirect()->route('student.my-courses')->with('warning', 'No active block found.');
        }
        
        // Get the video and course information
        $video = $block->video;
        $course = $video ? $video->course : null;
        
        // Calculate remaining time
        $remainingTime = $block->getRemainingBlockTime();
        
        return view('student.blocked-access', [
            'block' => $block,
            'video' => $video,
            'course' => $course,
            'remainingTime' => $remainingTime,
            'courseId' => $courseId ?: ($course ? $course->course_id : null)
        ]);
    }
}
