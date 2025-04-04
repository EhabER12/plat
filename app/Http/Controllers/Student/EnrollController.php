<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollController extends Controller
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
            $user = auth()->user();
            $course = Course::findOrFail($courseId);

            // Check if the course is approved
            if ($course->approval_status !== 'approved') {
                return back()->with('error', 'This course is not available for enrollment');
            }

            // Check if user is already enrolled
            $alreadyEnrolled = DB::table('enrollments')
                ->where('student_id', $user->user_id)
                ->where('course_id', $course->course_id)
                ->exists();

            if ($alreadyEnrolled) {
                return back()->with('info', 'You are already enrolled in this course');
            }

            // If the course is paid, redirect to payment page
            if ($course->price > 0) {
                return redirect()->route('payment.checkout', $course->course_id);
            }

            // For free courses, create enrollment directly
            $enrollment = new Enrollment();
            $enrollment->student_id = $user->user_id;
            $enrollment->course_id = $course->course_id;
            $enrollment->status = 'active';
            $enrollment->enrolled_at = now();
            $enrollment->save();

            return redirect()->route('student.my-courses')
                ->with('success', 'Successfully enrolled in the course');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to enroll in the course: ' . $e->getMessage());
        }
    }
}