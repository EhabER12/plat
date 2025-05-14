<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\Rating;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Enroll the authenticated user in a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enroll(Request $request, $courseId)
    {
        try {
            // Find the course
            $course = Course::where('course_id', $courseId)
                ->where('approval_status', 'approved')
                ->firstOrFail();

            $user = Auth::user();

            // Check if the user is already enrolled
            $existingEnrollment = Enrollment::where('student_id', $user->user_id)
                ->where('course_id', $courseId)
                ->first();

            if ($existingEnrollment) {
                return redirect()->route('student.my-courses')->with('error', 'You are already enrolled in this course.');
            }

            // Create enrollment record
            $enrollment = new Enrollment();
            $enrollment->student_id = $user->user_id;
            $enrollment->course_id = $courseId;
            $enrollment->enrolled_at = now();
            $enrollment->status = 'active';
            $enrollment->save();

            return redirect()->route('student.my-courses')->with('success', 'You have successfully enrolled in this course!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to enroll in the course. Please try again.');
        }
    }

    /**
     * Display my courses page.
     *
     * @return \Illuminate\View\View
     */
    public function myCourses()
    {
        $user = Auth::user();

        // Get enrolled courses for student
        $enrolledCourses = Course::join('enrollments', 'courses.course_id', '=', 'enrollments.course_id')
            ->where('enrollments.student_id', $user->user_id)
            ->where('approval_status', 'approved')
            ->with('instructor', 'category')
            ->select('courses.*', 'enrollments.enrolled_at as enrolled_at')
            ->paginate(9);

        return view('student.my-courses', [
            'enrolledCourses' => $enrolledCourses
        ]);
    }

    /**
     * Rate and review a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function review(Request $request, $courseId)
    {
        $student = Auth::user();
        
        // Get enrolled courses for the student
        $course = Course::whereHas('enrollments', function($query) use ($student) {
            $query->where('student_id', $student->user_id);
        })->findOrFail($courseId);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:500',
        ]);

        // Check which table to use for reviews
        if (Schema::hasTable('course_reviews')) {
            // Use course_reviews table
            CourseReview::updateOrCreate(
                [
                    'course_id' => $courseId,
                    'user_id' => $student->user_id
                ],
                [
                    'rating' => $validated['rating'],
                    'review' => $validated['comment'],
                    'is_approved' => true
                ]
            );
        } else {
            // Use ratings table
            $course->ratings()->updateOrCreate(
                [
                    'user_id' => $student->user_id
                ],
                [
                    'rating' => $validated['rating'],
                    'review' => $validated['comment'],
                ]
            );
        }

        return redirect()->back()->with('success', 'Review submitted successfully');
    }

    /**
     * Show the course content.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function courseContent($courseId)
    {
        $student = Auth::user();
        
        // Get the course with its relationships
        $course = Course::whereHas('enrollments', function($query) use ($student) {
            $query->where('student_id', $student->user_id);
        })
        ->with(['videos', 'materials', 'instructor', 'sections.videos'])
        ->findOrFail($courseId);

        // Count total videos in the course
        $totalVideos = $course->videos()->count();
        
        // Get progress records for this course
        $progressRecords = DB::table('student_progress')
            ->where('user_id', $student->user_id)
            ->where('course_id', $courseId)
            ->where('content_type', 'video')
            ->get();
        
        // Count completed videos (either by having completed_at not null or progress_percentage >= 90)
        $completedVideos = $progressRecords->filter(function($record) {
            return $record->completed_at != null || $record->progress_percentage >= 90;
        })->count();
        
        // Calculate total percentage
        $totalPercentage = $totalVideos > 0 ? round(($completedVideos / $totalVideos) * 100) : 0;
        
        // Create progress object with total_percentage
        $progress = (object)[
            'total_percentage' => $totalPercentage,
            'completed_videos' => $completedVideos,
            'total_videos' => $totalVideos
        ];

        return view('student.courses.content', compact('course', 'progress'));
    }
}
