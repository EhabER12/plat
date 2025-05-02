<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EnrollmentApiController extends Controller
{
    /**
     * Display a listing of the enrollments for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function myEnrollments()
    {
        $enrollments = Enrollment::where('student_id', Auth::id())
            ->with(['course', 'course.instructor'])
            ->orderBy('enrolled_at', 'desc')
            ->get();

        return response()->json([
            'enrollments' => $enrollments,
            'message' => 'Enrollments retrieved successfully'
        ]);
    }

    /**
     * Enroll in a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function enroll(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        // Check if user is already enrolled
        $existingEnrollment = Enrollment::where('student_id', Auth::id())
            ->where('course_id', $courseId)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'message' => 'You are already enrolled in this course',
                'enrollment' => $existingEnrollment
            ]);
        }

        // Check if course is free
        if ($course->price > 0) {
            return response()->json([
                'message' => 'This course requires payment. Please use the payment endpoint.',
                'course_price' => $course->price
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Create enrollment
            $enrollment = Enrollment::create([
                'student_id' => Auth::id(),
                'course_id' => $courseId,
                'enrolled_at' => now(),
                'status' => 'active',
            ]);

            // Create a free transaction record
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'amount' => 0,
                'currency' => 'USD',
                'status' => 'completed',
                'payment_method' => 'free',
                'transaction_type' => 'enrollment',
                'reference_id' => $courseId,
                'reference_type' => 'course',
                'description' => 'Free enrollment in course: ' . $course->title,
            ]);

            // Create a payment record
            $payment = Payment::create([
                'student_id' => Auth::id(),
                'course_id' => $courseId,
                'amount' => 0,
                'payment_method' => 'free',
                'payment_date' => now(),
                'status' => 'completed',
                'transaction_id' => $transaction->transaction_id,
                'notes' => 'Free course enrollment',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Enrolled successfully',
                'enrollment' => $enrollment
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Enrollment failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update enrollment progress.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $enrollmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProgress(Request $request, $enrollmentId)
    {
        $enrollment = Enrollment::where('enrollment_id', $enrollmentId)
            ->where('student_id', Auth::id())
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'progress' => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $enrollment->progress = $request->progress;
        
        // If progress is 100%, mark as completed
        if ($request->progress == 100 && !$enrollment->completed_at) {
            $enrollment->completed_at = now();
            $enrollment->status = 'completed';
        }
        
        $enrollment->save();

        return response()->json([
            'message' => 'Progress updated successfully',
            'enrollment' => $enrollment
        ]);
    }

    /**
     * Mark enrollment as completed.
     *
     * @param  int  $enrollmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markCompleted($enrollmentId)
    {
        $enrollment = Enrollment::where('enrollment_id', $enrollmentId)
            ->where('student_id', Auth::id())
            ->firstOrFail();

        $enrollment->completed_at = now();
        $enrollment->status = 'completed';
        $enrollment->progress = 100;
        $enrollment->save();

        return response()->json([
            'message' => 'Course marked as completed',
            'enrollment' => $enrollment
        ]);
    }

    /**
     * Get enrollment details.
     *
     * @param  int  $enrollmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($enrollmentId)
    {
        $enrollment = Enrollment::where('enrollment_id', $enrollmentId)
            ->where('student_id', Auth::id())
            ->with(['course', 'course.instructor', 'course.videos', 'course.materials'])
            ->firstOrFail();

        return response()->json([
            'enrollment' => $enrollment,
            'message' => 'Enrollment details retrieved successfully'
        ]);
    }

    /**
     * Get enrollments for a specific course (for instructors).
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function courseEnrollments($courseId)
    {
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        $enrollments = Enrollment::where('course_id', $courseId)
            ->with('student:user_id,name,email,profile_picture')
            ->orderBy('enrolled_at', 'desc')
            ->get();

        return response()->json([
            'enrollments' => $enrollments,
            'total_count' => $enrollments->count(),
            'message' => 'Course enrollments retrieved successfully'
        ]);
    }
}
