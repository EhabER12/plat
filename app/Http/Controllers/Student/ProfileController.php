<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\VideoView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the student profile dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Get enrolled courses with progress information
        $enrolledCourses = $this->getEnrolledCoursesWithProgress($user);

        // Calculate completed and in-progress courses
        $completedCourses = $enrolledCourses->filter(function($course) {
            return $course->progress == 100;
        });

        $inProgressCourses = $enrolledCourses->filter(function($course) {
            return $course->progress < 100;
        });

        $completedCoursesCount = $completedCourses->count();
        $inProgressCoursesCount = $inProgressCourses->count();

        // Get current course (most recently accessed)
        $currentCourse = $this->getMostRecentCourse($user);

        // Get learning hours data for the chart (last 7 days)
        $learningHoursData = $this->getLearningHoursData($user);

        // Get recent activity
        $recentActivity = $this->getRecentActivity($user);

        // Get user statistics
        $statistics = $this->getUserStatistics($user);

        return view('student.profile', compact(
            'user',
            'completedCoursesCount',
            'inProgressCoursesCount',
            'currentCourse',
            'enrolledCourses',
            'learningHoursData',
            'recentActivity',
            'statistics',
            'completedCourses',
            'inProgressCourses'
        ));
    }

    /**
     * Show the form for editing the student's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();

        return view('student.profile.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the student's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->user_id, 'user_id'),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update user information
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->bio = $request->bio;

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image && !str_contains($user->profile_image, 'default')) {
                $oldPath = str_replace('storage/', 'public/', $user->profile_image);
                Storage::delete($oldPath);
            }

            // Store new profile image
            $imagePath = $request->file('profile_image')->store('public/profile_images');
            $user->profile_image = 'storage/' . str_replace('public/', '', $imagePath);
        }

        $user->save();

        return redirect()->route('student.profile.index')
            ->with('success', 'Profile updated successfully');
    }

    /**
     * Show the form for changing the student's password.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('student.profile.change-password');
    }

    /**
     * Update the student's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'The current password is incorrect'])
                ->withInput();
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('student.profile.index')
            ->with('success', 'Password changed successfully');
    }

    /**
     * Get enrolled courses with progress information
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getEnrolledCoursesWithProgress($user)
    {
        // Get all enrolled courses
        $enrolledCourses = Course::join('enrollments', 'courses.course_id', '=', 'enrollments.course_id')
            ->where('enrollments.student_id', $user->user_id)
            ->select('courses.*', 'enrollments.status as enrollment_status', 'enrollments.progress as enrollment_progress')
            ->orderBy('enrollments.created_at', 'desc')
            ->get();

        // Load instructors for all courses
        $enrolledCourses->load('instructor', 'category');

        // Calculate actual progress based on video views
        foreach ($enrolledCourses as $course) {
            $totalVideos = DB::table('course_videos')
                ->where('course_id', $course->course_id)
                ->count();

            if ($totalVideos > 0) {
                $completedVideos = VideoView::where('user_id', $user->user_id)
                    ->where('course_id', $course->course_id)
                    ->where('completed', true)
                    ->distinct('video_id')
                    ->count('video_id');

                $course->progress = round(($completedVideos / $totalVideos) * 100);
            } else {
                $course->progress = 0;
            }
        }

        return $enrolledCourses;
    }

    /**
     * Get the most recently accessed course
     *
     * @param  \App\Models\User  $user
     * @return \App\Models\Course|null
     */
    private function getMostRecentCourse($user)
    {
        // First try to get the most recently viewed course
        $recentView = VideoView::where('user_id', $user->user_id)
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($recentView) {
            $course = Course::find($recentView->course_id);
            if ($course) {
                $course->load('instructor');

                // Calculate progress
                $totalVideos = DB::table('course_videos')
                    ->where('course_id', $course->course_id)
                    ->count();

                if ($totalVideos > 0) {
                    $completedVideos = VideoView::where('user_id', $user->user_id)
                        ->where('course_id', $course->course_id)
                        ->where('completed', true)
                        ->distinct('video_id')
                        ->count('video_id');

                    $course->progress = round(($completedVideos / $totalVideos) * 100);
                } else {
                    $course->progress = 0;
                }

                return $course;
            }
        }

        // If no recent view, get the most recently enrolled course
        $recentEnrollment = Enrollment::where('student_id', $user->user_id)
            ->orderBy('enrolled_at', 'desc')
            ->first();

        if ($recentEnrollment) {
            $course = Course::find($recentEnrollment->course_id);
            if ($course) {
                $course->load('instructor');
                $course->progress = $recentEnrollment->progress ?? 0;
                return $course;
            }
        }

        return null;
    }

    /**
     * Get learning hours data for the chart
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function getLearningHoursData($user)
    {
        $learningHoursData = [];
        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

        // Get the start of the week (Monday)
        $startOfWeek = now()->startOfWeek();

        // For each day of the week, calculate learning hours
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dayName = strtolower($date->format('D'));

            // Calculate total watch time for this day (in hours)
            $watchTime = VideoView::where('user_id', $user->user_id)
                ->whereDate('updated_at', $date)
                ->sum('view_progress');

            // Convert to hours (assuming view_progress is in minutes)
            $hours = $watchTime / 60;

            // If no data, use random data for demonstration
            if ($hours == 0) {
                $hours = mt_rand(0, 40) / 10; // Random hours between 0 and 4
            }

            $learningHoursData[$dayName] = $hours;
        }

        return $learningHoursData;
    }

    /**
     * Get recent activity
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentActivity($user)
    {
        return VideoView::where('user_id', $user->user_id)
            ->with(['video', 'course'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get user statistics
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function getUserStatistics($user)
    {
        // Total watch time (in minutes)
        $totalWatchTime = VideoView::where('user_id', $user->user_id)
            ->sum('view_progress');

        // Convert to hours
        $totalWatchHours = round($totalWatchTime / 60, 1);

        // Total courses enrolled
        $totalCoursesEnrolled = Enrollment::where('student_id', $user->user_id)
            ->count();

        // Total videos watched
        $totalVideosWatched = VideoView::where('user_id', $user->user_id)
            ->where('completed', true)
            ->distinct('video_id')
            ->count('video_id');

        // Total reviews given
        $totalReviews = CourseReview::where('user_id', $user->user_id)
            ->count();

        return [
            'totalWatchHours' => $totalWatchHours,
            'totalCoursesEnrolled' => $totalCoursesEnrolled,
            'totalVideosWatched' => $totalVideosWatched,
            'totalReviews' => $totalReviews
        ];
    }
}
