<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Rating;
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
        $user = auth()->user();

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
        $student = auth()->user();
        $course = $student->enrolledCourses()->findOrFail($courseId);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:500',
        ]);

        $rating = $course->ratings()->updateOrCreate(
            ['user_id' => $student->user_id],
            [
                'rating' => $validated['rating'],
                'review' => $validated['comment'],
            ]
        );

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
        $student = auth()->user();
        $course = $student->enrolledCourses()
            ->with(['videos', 'materials', 'instructor'])
            ->findOrFail($courseId);

        $progress = $student->progress()
            ->where('course_id', $courseId)
            ->first();

        return view('student.courses.content', compact('course', 'progress'));
    }
}
