<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;

class InstructorProfileController extends Controller
{
    /**
     * Display a listing of featured instructors.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get instructors with their courses count and average rating
        $instructors = User::whereHas('roles', function($query) {
            $query->where('role', 'instructor');
        })
        ->where('status', 1)
        ->withCount('courses')
        ->withCount(['enrollments' => function($query) {
            $query->whereHas('course', function($q) {
                $q->where('instructor_id', DB::raw('users.user_id'));
            });
        }])
        ->with(['courses' => function($query) {
            $query->with('reviews');
        }])
        ->get()
        ->map(function($instructor) {
            // Calculate average rating across all courses
            $totalReviews = 0;
            $totalRating = 0;

            foreach ($instructor->courses as $course) {
                $totalReviews += $course->reviews->count();
                $totalRating += $course->reviews->sum('rating');
            }

            $instructor->average_rating = $totalReviews > 0 ? round($totalRating / $totalReviews, 1) : 0;
            $instructor->total_reviews = $totalReviews;
            
            return $instructor;
        })
        ->sortByDesc('average_rating')
        ->take(8);

        return view('instructors.index', compact('instructors'));
    }

    /**
     * Display the specified instructor's profile.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $instructor = User::whereHas('roles', function($query) {
            $query->where('role', 'instructor');
        })
        ->where('user_id', $id)
        ->where('status', 1)
        ->withCount('courses')
        ->withCount(['enrollments' => function($query) use ($id) {
            $query->whereHas('course', function($q) use ($id) {
                $q->where('instructor_id', $id);
            });
        }])
        ->select('user_id', 'name', 'email', 'profile_image', 'banner_image', 'bio', 'detailed_description', 'created_at', 'phone', 'website', 'linkedin_profile', 'twitter_profile')
        ->firstOrFail();

        // Get instructor's courses with their reviews
        $courses = Course::where('instructor_id', $id)
            ->where('approval_status', 'approved')
            ->withCount('enrollments')
            ->withCount('reviews')
            ->with([
                'reviews' => function($query) {
                    $query->with('user');
                },
                'category'
            ])
            ->get()
            ->map(function($course) {
                $course->average_rating = $course->reviews_count > 0 ?
                    round($course->reviews->sum('rating') / $course->reviews_count, 1) : 0;
                
                // Ensure thumbnail path is correct
                if ($course->thumbnail && !str_starts_with($course->thumbnail, 'storage/')) {
                    $course->thumbnail = 'storage/' . $course->thumbnail;
                }
                
                return $course;
            });

        // Calculate overall instructor rating
        $totalReviews = $courses->sum('reviews_count');
        $totalRating = 0;

        foreach ($courses as $course) {
            $totalRating += $course->reviews->sum('rating');
        }

        $averageRating = $totalReviews > 0 ? round($totalRating / $totalReviews, 1) : 0;

        // Get recent reviews across all courses
        $recentReviews = Rating::whereIn('course_id', $courses->pluck('course_id'))
            ->with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('instructors.show', compact('instructor', 'courses', 'averageRating', 'totalReviews', 'recentReviews'));
    }

    /**
     * Display featured instructors for the homepage.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function featured()
    {
        // Get top 4 instructors based on ratings and student count
        $featuredInstructors = User::whereHas('roles', function($query) {
            $query->where('role', 'instructor');
        })
        ->where('status', 1)
        ->withCount('courses')
        ->withCount(['enrollments' => function($query) {
            $query->whereHas('course', function($q) {
                $q->where('instructor_id', DB::raw('users.user_id'));
            });
        }])
        ->with(['courses' => function($query) {
            $query->with('reviews');
        }])
        ->get()
        ->map(function($instructor) {
            // Calculate average rating across all courses
            $totalReviews = 0;
            $totalRating = 0;

            foreach ($instructor->courses as $course) {
                $totalReviews += $course->reviews->count();
                $totalRating += $course->reviews->sum('rating');
            }

            $instructor->average_rating = $totalReviews > 0 ? round($totalRating / $totalReviews, 1) : 0;
            $instructor->total_reviews = $totalReviews;

            return $instructor;
        })
        ->sortByDesc(function($instructor) {
            // Sort by a combination of rating and student count
            return $instructor->average_rating * 10 + $instructor->enrollments_count / 10;
        })
        ->take(4);

        return $featuredInstructors;
    }
}
