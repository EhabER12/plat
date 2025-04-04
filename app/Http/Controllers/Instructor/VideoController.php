<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class VideoController extends Controller
{
    /**
     * Store a newly created video resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $courseId)
    {
        $user = Auth::user();
        
        // Verify the instructor owns this course
        $course = Course::where('id', $courseId)
            ->where('instructor_id', $user->id)
            ->firstOrFail();
        
        // Check if course_videos table exists
        if (!Schema::hasTable('course_videos')) {
            return redirect()->back()->with('error', 'Video feature is not available at the moment.');
        }
        
        // Validate request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_file' => 'required|file|mimes:mp4,webm|max:102400', // 100MB max
            'thumbnail' => 'nullable|image|max:2048',
            'duration' => 'required|integer|min:1',
            'position' => 'required|integer|min:0',
        ]);
        
        // Create video record
        $video = new CourseVideo();
        $video->course_id = $courseId;
        $video->title = $validated['title'];
        $video->description = $validated['description'];
        $video->duration = $validated['duration'];
        $video->position = $validated['position'];
        
        // Store video file
        $video->video_url = $request->file('video_file')->store('course-videos', 'public');
        
        // Store thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            $video->thumbnail = $request->file('thumbnail')->store('video-thumbnails', 'public');
        }
        
        $video->save();
        
        return redirect()->back()->with('success', 'Video added successfully');
    }
    
    /**
     * Update the specified video resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @param  int  $videoId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $courseId, $videoId)
    {
        $user = Auth::user();
        
        // Verify the instructor owns this course
        $course = Course::where('id', $courseId)
            ->where('instructor_id', $user->id)
            ->firstOrFail();
        
        // Find the video
        $video = CourseVideo::where('id', $videoId)
            ->where('course_id', $courseId)
            ->firstOrFail();
        
        // Validate request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_file' => 'nullable|file|mimes:mp4,webm|max:102400',
            'thumbnail' => 'nullable|image|max:2048',
            'duration' => 'required|integer|min:1',
            'position' => 'required|integer|min:0',
        ]);
        
        // Update video details
        $video->title = $validated['title'];
        $video->description = $validated['description'];
        $video->duration = $validated['duration'];
        $video->position = $validated['position'];
        
        // Update video file if provided
        if ($request->hasFile('video_file')) {
            // Delete old video file
            Storage::disk('public')->delete($video->video_url);
            $video->video_url = $request->file('video_file')->store('course-videos', 'public');
        }
        
        // Update thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($video->thumbnail) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $video->thumbnail = $request->file('thumbnail')->store('video-thumbnails', 'public');
        }
        
        $video->save();
        
        return redirect()->back()->with('success', 'Video updated successfully');
    }
    
    /**
     * Remove the specified video resource.
     *
     * @param  int  $courseId
     * @param  int  $videoId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy($courseId, $videoId)
    {
        $user = Auth::user();
        
        // Verify the instructor owns this course
        $course = Course::where('id', $courseId)
            ->where('instructor_id', $user->id)
            ->firstOrFail();
        
        // Find the video
        $video = CourseVideo::where('id', $videoId)
            ->where('course_id', $courseId)
            ->firstOrFail();
        
        // Delete video file
        Storage::disk('public')->delete($video->video_url);
        
        // Delete thumbnail if exists
        if ($video->thumbnail) {
            Storage::disk('public')->delete($video->thumbnail);
        }
        
        // Delete the record
        $video->delete();
        
        return redirect()->back()->with('success', 'Video deleted successfully');
    }
    
    /**
     * Update the positions of multiple videos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePositions(Request $request, $courseId)
    {
        $user = Auth::user();
        
        // Verify the instructor owns this course
        $course = Course::where('id', $courseId)
            ->where('instructor_id', $user->id)
            ->firstOrFail();
        
        $validated = $request->validate([
            'positions' => 'required|array',
            'positions.*.id' => 'required|exists:course_videos,id',
            'positions.*.position' => 'required|integer|min:0',
        ]);
        
        foreach ($validated['positions'] as $position) {
            CourseVideo::where('id', $position['id'])
                ->where('course_id', $courseId)
                ->update(['position' => $position['position']]);
        }
        
        return response()->json(['message' => 'Video positions updated successfully']);
    }
} 