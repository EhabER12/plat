<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Get reviews for a course.
     *
     * @param int $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourseReviews($courseId)
    {
        // Verify course exists
        $course = Course::findOrFail($courseId);

        // Determine which table to use for reviews
        if (Schema::hasTable('course_reviews')) {
            $reviews = CourseReview::where('course_id', $courseId)
                ->where('is_approved', true)
                ->with(['user:user_id,name,profile_image'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $averageRating = CourseReview::where('course_id', $courseId)
                ->where('is_approved', true)
                ->avg('rating') ?? 0;

            $ratingCounts = CourseReview::where('course_id', $courseId)
                ->where('is_approved', true)
                ->select('rating', DB::raw('count(*) as count'))
                ->groupBy('rating')
                ->get()
                ->pluck('count', 'rating')
                ->toArray();
        } else {
            // Fallback to ratings table
            $reviews = Rating::where('course_id', $courseId)
                ->where(function($query) {
                    // Check if is_published column exists
                    if (Schema::hasColumn('ratings', 'is_published')) {
                        $query->where('is_published', true);
                    }
                    // Check if admin_review_status column exists
                    if (Schema::hasColumn('ratings', 'admin_review_status')) {
                        $query->orWhere('admin_review_status', 'approved');
                    }
                })
                ->with(['user:user_id,name,profile_image'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $averageRating = Rating::where('course_id', $courseId)
                ->where(function($query) {
                    // Check if is_published column exists
                    if (Schema::hasColumn('ratings', 'is_published')) {
                        $query->where('is_published', true);
                    }
                    // Check if admin_review_status column exists
                    if (Schema::hasColumn('ratings', 'admin_review_status')) {
                        $query->orWhere('admin_review_status', 'approved');
                    }
                })
                ->avg(Schema::hasColumn('ratings', 'rating') ? 'rating' : 'rating_value') ?? 0;

            $ratingField = Schema::hasColumn('ratings', 'rating') ? 'rating' : 'rating_value';
            $ratingCounts = Rating::where('course_id', $courseId)
                ->where(function($query) {
                    // Check if is_published column exists
                    if (Schema::hasColumn('ratings', 'is_published')) {
                        $query->where('is_published', true);
                    }
                    // Check if admin_review_status column exists
                    if (Schema::hasColumn('ratings', 'admin_review_status')) {
                        $query->orWhere('admin_review_status', 'approved');
                    }
                })
                ->select($ratingField, DB::raw('count(*) as count'))
                ->groupBy($ratingField)
                ->get()
                ->pluck('count', $ratingField)
                ->toArray();
        }

        // Fill in missing ratings with 0
        for ($i = 1; $i <= 5; $i++) {
            if (!isset($ratingCounts[$i])) {
                $ratingCounts[$i] = 0;
            }
        }

        // Sort by rating
        ksort($ratingCounts);

        return response()->json([
            'reviews' => $reviews,
            'average_rating' => round($averageRating, 1),
            'rating_counts' => $ratingCounts,
            'total_reviews' => array_sum($ratingCounts),
            'message' => 'Reviews retrieved successfully'
        ]);
    }

    /**
     * Submit a review for a course.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitCourseReview(Request $request, $courseId)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'message' => 'You must be logged in to submit a review'
            ], 401);
        }

        $user = Auth::user();

        // Check if user is enrolled in the course
        $isEnrolled = Enrollment::where('course_id', $courseId)
            ->where('student_id', $user->user_id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'message' => 'You must be enrolled in the course to submit a review'
            ], 403);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Determine which table to use for reviews
        if (Schema::hasTable('course_reviews')) {
            // Check if user has already reviewed this course
            $existingReview = CourseReview::where('course_id', $courseId)
                ->where('user_id', $user->user_id)
                ->first();

            if ($existingReview) {
                // Update existing review
                $existingReview->rating = $request->rating;
                $existingReview->review = $request->review;
                $existingReview->is_approved = true; // Auto-approve or set to false if admin approval is required
                $existingReview->save();

                $review = $existingReview;
                $message = 'Review updated successfully';
            } else {
                // Create new review
                $review = new CourseReview();
                $review->course_id = $courseId;
                $review->user_id = $user->user_id;
                $review->rating = $request->rating;
                $review->review = $request->review;
                $review->is_approved = true; // Auto-approve or set to false if admin approval is required
                $review->save();

                $message = 'Review submitted successfully';
            }
        } else {
            // Fallback to ratings table
            // Check if user has already reviewed this course
            $existingRating = Rating::where('course_id', $courseId)
                ->where(function($query) use ($user) {
                    $query->where('user_id', $user->user_id)
                        ->orWhere('student_id', $user->user_id);
                })
                ->first();

            if ($existingRating) {
                // Update existing rating
                if (Schema::hasColumn('ratings', 'rating')) {
                    $existingRating->rating = $request->rating;
                } else {
                    $existingRating->rating_value = $request->rating;
                }

                if (Schema::hasColumn('ratings', 'review')) {
                    $existingRating->review = $request->review;
                } elseif (Schema::hasColumn('ratings', 'review_text')) {
                    $existingRating->review_text = $request->review;
                } elseif (Schema::hasColumn('ratings', 'comment')) {
                    $existingRating->comment = $request->review;
                }

                if (Schema::hasColumn('ratings', 'is_published')) {
                    $existingRating->is_published = true;
                }

                if (Schema::hasColumn('ratings', 'admin_review_status')) {
                    $existingRating->admin_review_status = 'approved';
                }

                $existingRating->save();

                $review = $existingRating;
                $message = 'Review updated successfully';
            } else {
                // Create new rating
                $rating = new Rating();
                $rating->course_id = $courseId;

                if (Schema::hasColumn('ratings', 'user_id')) {
                    $rating->user_id = $user->user_id;
                }

                if (Schema::hasColumn('ratings', 'student_id')) {
                    $rating->student_id = $user->user_id;
                }

                if (Schema::hasColumn('ratings', 'rating')) {
                    $rating->rating = $request->rating;
                } else {
                    $rating->rating_value = $request->rating;
                }

                if (Schema::hasColumn('ratings', 'review')) {
                    $rating->review = $request->review;
                } elseif (Schema::hasColumn('ratings', 'review_text')) {
                    $rating->review_text = $request->review;
                } elseif (Schema::hasColumn('ratings', 'comment')) {
                    $rating->comment = $request->review;
                }

                if (Schema::hasColumn('ratings', 'is_published')) {
                    $rating->is_published = true;
                }

                if (Schema::hasColumn('ratings', 'admin_review_status')) {
                    $rating->admin_review_status = 'approved';
                }

                $rating->save();

                $review = $rating;
                $message = 'Review submitted successfully';
            }
        }

        return response()->json([
            'message' => $message,
            'review' => $review
        ], 201);
    }
}
