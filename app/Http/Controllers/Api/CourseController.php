<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Get participants for a course.
     */
    public function getParticipants($courseId)
    {
        $user = Auth::user();
        
        // Check if the user has access to this course
        $course = Course::where('course_id', $courseId)
            ->where(function ($query) use ($user) {
                $query->where('instructor_id', $user->user_id)
                    ->orWhereHas('enrollments', function ($q) use ($user) {
                        $q->where('student_id', $user->user_id);
                    });
            })
            ->firstOrFail();
        
        // Get the instructor
        $instructor = User::where('user_id', $course->instructor_id)->first();
        
        // Get enrolled students
        $students = User::whereHas('enrollments', function ($query) use ($courseId) {
            $query->where('course_id', $courseId);
        })->get();
        
        // Prepare the participants array
        $participants = [];
        
        if ($instructor) {
            $participants[] = [
                'user_id' => $instructor->user_id,
                'name' => $instructor->name,
                'role' => 'instructor',
                'profile_image' => $instructor->profile_image,
            ];
        }
        
        foreach ($students as $student) {
            $participants[] = [
                'user_id' => $student->user_id,
                'name' => $student->name,
                'role' => 'student',
                'profile_image' => $student->profile_image,
            ];
        }
        
        return response()->json([
            'participants' => $participants
        ]);
    }
}
