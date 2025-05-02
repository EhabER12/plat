<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\StudentProgress;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            // Get authenticated user
            $user = Auth::user();

            // Check if user has student role
            if (!$user->hasRole('student')) {
                return back()->with('error', 'Only students can enroll in courses');
            }

            // Find the course with validation
            $course = Course::where('course_id', $courseId)
                ->where('approval_status', 'approved')
                ->firstOrFail();

            // Log the enrollment attempt
            Log::info('Enrollment attempt', [
                'user_id' => $user->user_id,
                'course_id' => $course->course_id,
                'course_title' => $course->title,
                'course_price' => $course->price
            ]);

            // Check if user is already enrolled
            $existingEnrollment = Enrollment::where('student_id', $user->user_id)
                ->where('course_id', $course->course_id)
                ->first();

            if ($existingEnrollment) {
                // If already enrolled, redirect to course content
                return redirect()->route('student.course-content', $course->course_id)
                    ->with('info', 'You are already enrolled in this course');
            }

            // If the course is paid, redirect to payment page
            if ($course->price > 0) {
                return redirect()->route('payment.checkout', $course->course_id);
            }

            // For free courses, create enrollment directly
            DB::beginTransaction();
            try {
                // Create enrollment record
                $enrollment = new Enrollment();
                $enrollment->student_id = $user->user_id;
                $enrollment->course_id = $course->course_id;
                $enrollment->status = 'active';
                $enrollment->enrolled_at = now();
                $enrollment->save();

                // Initialize student progress
                $this->initializeStudentProgress($user->user_id, $course->course_id);

                // Record user activity
                $this->recordEnrollmentActivity($user->user_id, $course->course_id);

                DB::commit();

                // Send enrollment notification (if needed)
                // $this->sendEnrollmentNotification($user, $course);

                return redirect()->route('student.course-content', $course->course_id)
                    ->with('success', 'You have successfully enrolled in "' . $course->title . '"!');
            } catch (\Exception $innerException) {
                DB::rollBack();
                Log::error('Enrollment transaction failed', [
                    'error' => $innerException->getMessage(),
                    'user_id' => $user->user_id,
                    'course_id' => $course->course_id
                ]);
                throw $innerException;
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Course not found', ['course_id' => $courseId]);
            return back()->with('error', 'The course you are trying to enroll in does not exist or is not available.');
        } catch (\Exception $e) {
            Log::error('Enrollment failed', [
                'error' => $e->getMessage(),
                'course_id' => $courseId
            ]);
            return back()->with('error', 'Failed to enroll in the course. Please try again later.');
        }
    }

    /**
     * Initialize student progress for a course.
     *
     * @param int $studentId
     * @param int $courseId
     * @return void
     */
    private function initializeStudentProgress($studentId, $courseId)
    {
        try {
            // Check if StudentProgress model exists
            if (class_exists('App\\Models\\StudentProgress')) {
                // Create an initial progress record
                StudentProgress::create([
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'progress_percentage' => 0,
                    'last_accessed_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to initialize student progress', [
                'error' => $e->getMessage(),
                'student_id' => $studentId,
                'course_id' => $courseId
            ]);
            // Don't throw the exception, as this is a non-critical operation
        }
    }

    /**
     * Record enrollment activity.
     *
     * @param int $userId
     * @param int $courseId
     * @return void
     */
    private function recordEnrollmentActivity($userId, $courseId)
    {
        try {
            // Check if UserActivity model exists
            if (class_exists('App\\Models\\UserActivity')) {
                UserActivity::create([
                    'user_id' => $userId,
                    'activity_type' => UserActivity::TYPE_COURSE_ENROLL,
                    'entity_type' => 'Course',
                    'entity_id' => $courseId,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to record enrollment activity', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'course_id' => $courseId
            ]);
            // Don't throw the exception, as this is a non-critical operation
        }
    }
}