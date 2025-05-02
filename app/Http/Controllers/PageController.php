<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\InstructorProfileController;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PageController extends Controller
{
    public function home()
    {
        try {
            // Check if tables exist before querying
            $coursesTableExists = Schema::hasTable('courses');
            $usersTableExists = Schema::hasTable('users');
            $categoriesTableExists = Schema::hasTable('categories');

            Log::info('Tables exist check: courses=' . ($coursesTableExists ? 'yes' : 'no') .
                      ', users=' . ($usersTableExists ? 'yes' : 'no') .
                      ', categories=' . ($categoriesTableExists ? 'yes' : 'no'));

            // If any required table doesn't exist, return empty results
            if (!$coursesTableExists) {
                Log::error('Courses table does not exist');
                return view('pages.home', [
                    'courses' => collect([]),
                    'categories' => collect([]),
                    'error' => 'Database tables not set up properly. Please run migrations.'
                ]);
            }

            // Get all courses (for displaying on the home page)
            $courses = Course::query();
            
            if ($usersTableExists) {
                $courses->with('instructor');
            }
            
            if ($categoriesTableExists) {
                $courses->with('category');
            }
            
            // Order courses by created date
            $courses->orderBy('created_at', 'desc');
            
            // Get the courses
            $courses = $courses->get();
            Log::info('Total courses count: ' . $courses->count());
            
            // Get all categories with course count
            if ($categoriesTableExists) {
                $categories = Category::query();
                
                // Check if withCount can be used
                if ($coursesTableExists && method_exists(Category::class, 'courses')) {
                    $categories->withCount('courses');
                }
                
                $categories = $categories->get();
            } else {
                $categories = collect([]);
            }

            // Debug information
            Log::info('Total categories count: ' . $categories->count());
            
            return view('pages.home', [
                'courses' => $courses,
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading home page: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return view('pages.home', [
                'courses' => collect([]),
                'categories' => collect([]),
                'error' => 'Unable to load courses: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the courses page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function courses(Request $request)
    {
        try {
            // Enable query log for debugging
            \DB::enableQueryLog();

            // Log query parameters
            \Log::info('Courses page accessed with parameters: ' . json_encode($request->all()));

            // Check if tables exist before querying
            $coursesTableExists = \Schema::hasTable('courses');
            $usersTableExists = \Schema::hasTable('users');
            $categoriesTableExists = \Schema::hasTable('categories');

            \Log::info('Tables exist check: courses=' . ($coursesTableExists ? 'yes' : 'no') .
                      ', users=' . ($usersTableExists ? 'yes' : 'no') .
                      ', categories=' . ($categoriesTableExists ? 'yes' : 'no'));

            // If any required table doesn't exist, return empty results
            if (!$coursesTableExists || !$usersTableExists || !$categoriesTableExists) {
                \Log::error('Required tables do not exist');
                return view('pages.courses', [
                    'courses' => collect([]),
                    'categories' => collect([]),
                    'error' => 'Database tables not set up properly. Please run migrations.'
                ]);
            }

            // Get column information to ensure we're querying existing columns
            $courseColumns = \Schema::getColumnListing('courses');
            $userColumns = \Schema::getColumnListing('users');
            $categoryColumns = \Schema::getColumnListing('categories');

            \Log::info('Course columns: ' . json_encode($courseColumns));
            \Log::info('User columns: ' . json_encode($userColumns));
            \Log::info('Category columns: ' . json_encode($categoryColumns));

            // Check if required columns exist
            $hasInstructorIdColumn = in_array('instructor_id', $courseColumns);
            $hasCategoryIdColumn = in_array('category_id', $courseColumns);
            $hasUserIdColumn = in_array('user_id', $userColumns);
            $hasCategoryIdPK = in_array('id', $categoryColumns);

            // Build the query based on available columns
            $query = \DB::table('courses');

            // Only join if the foreign key columns exist
            if ($hasInstructorIdColumn && $hasUserIdColumn && $usersTableExists) {
                $query->leftJoin('users', 'courses.instructor_id', '=', 'users.user_id');
                $query->addSelect('users.name as instructor_name');
            } else {
                $query->addSelect(\DB::raw("'Unknown' as instructor_name"));
            }

            if ($hasCategoryIdColumn && $hasCategoryIdPK && $categoriesTableExists) {
                $query->leftJoin('categories', 'courses.category_id', '=', 'categories.id');
                $query->addSelect('categories.name as category_name');
            } else {
                $query->addSelect(\DB::raw("'Uncategorized' as category_name"));
            }

            // Add course columns
            $query->addSelect('courses.*');

            // Apply filters if provided
            if ($request->has('category') && $request->input('category') != 'all' && $hasCategoryIdColumn) {
                $query->where('courses.category_id', $request->input('category'));
            }

            if ($request->has('search') && !empty($request->input('search'))) {
                $searchTerm = '%' . $request->input('search') . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->where('courses.title', 'like', $searchTerm)
                      ->orWhere('courses.description', 'like', $searchTerm);
                });
            }

            // Apply sorting
            $sort = $request->input('sort', 'newest');
            switch ($sort) {
                case 'price_low':
                    $query->orderBy('courses.price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('courses.price', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('courses.created_at', 'asc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('courses.created_at', 'desc');
                    break;
            }

            // Execute the query
            $courses = $query->get();

            // Log query for debugging
            \Log::info('Raw SQL query: ' . \DB::getQueryLog()[count(\DB::getQueryLog())-1]['query']);
            \Log::info('SQL bindings: ' . json_encode(\DB::getQueryLog()[count(\DB::getQueryLog())-1]['bindings']));

            // Log course count and data
            \Log::info('Total courses found: ' . $courses->count());
            if ($courses->count() > 0) {
                \Log::info('First course: ' . json_encode($courses->first()));
            }

            // Get categories for the filter dropdown
            $categories = $categoriesTableExists ? \DB::table('categories')->get() : collect([]);

            // Convert the raw DB results to a paginator
            $perPage = 9;
            $page = $request->input('page', 1);
            $offset = ($page - 1) * $perPage;

            $coursesCollection = collect($courses);
            $paginatedItems = $coursesCollection->slice($offset, $perPage)->all();

            $paginatedCourses = new \Illuminate\Pagination\LengthAwarePaginator(
                $paginatedItems,
                $coursesCollection->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('pages.courses', [
                'courses' => $paginatedCourses,
                'categories' => $categories,
                'currentCategory' => $request->input('category'),
                'currentSearch' => $request->input('search'),
                'currentSort' => $request->input('sort', 'newest')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in courses page: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return view('pages.courses', [
                'courses' => collect([]),
                'categories' => collect([]),
                'error' => 'Unable to load courses: ' . $e->getMessage()
            ]);
        }
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request)
    {
        // Validate the form data
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|max:255',
            'message' => 'required',
        ]);

        // Here you would typically save to database or send an email
        // This is just a demo so we'll just redirect with a success message

        return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }

    /**
     * Display the course detail page.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function courseDetail($courseId)
    {
        try {
            // Check if tables exist before querying
            $coursesTableExists = \Schema::hasTable('courses');
            $usersTableExists = \Schema::hasTable('users');
            $categoriesTableExists = \Schema::hasTable('categories');
            $videosTableExists = \Schema::hasTable('course_videos');
            $materialsTableExists = \Schema::hasTable('course_materials');

            \Log::info('Tables exist check for course detail: courses=' . ($coursesTableExists ? 'yes' : 'no') .
                      ', users=' . ($usersTableExists ? 'yes' : 'no') .
                      ', categories=' . ($categoriesTableExists ? 'yes' : 'no') .
                      ', videos=' . ($videosTableExists ? 'yes' : 'no') .
                      ', materials=' . ($materialsTableExists ? 'yes' : 'no'));

            // If courses table doesn't exist, redirect with error
            if (!$coursesTableExists) {
                \Log::error('Courses table does not exist');
                return redirect('/courses')->with('error', 'Database tables not set up properly. Please run migrations.');
            }

            // Get column information to ensure we're querying existing columns
            $courseColumns = \Schema::getColumnListing('courses');
            \Log::info('Course columns: ' . json_encode($courseColumns));

            // Check if required columns exist
            $hasApprovalStatusColumn = in_array('approval_status', $courseColumns);
            $hasCategoryIdColumn = in_array('category_id', $courseColumns);

            // Build the query based on available columns
            $query = Course::where('course_id', $courseId);

            // Only filter by approval_status if the column exists
            if ($hasApprovalStatusColumn) {
                $query->where('approval_status', 'approved');
            }

            // Add relationships if tables exist
            $relationships = [];

            if ($usersTableExists) {
                $relationships[] = 'instructor';
            }

            if ($categoriesTableExists) {
                $relationships[] = 'category';
            }

            if ($videosTableExists) {
                $relationships[] = 'videos';
            }

            if ($materialsTableExists) {
                $relationships[] = 'materials';
            }

            if (!empty($relationships)) {
                $query->with($relationships);
            }

            // Execute the query
            $course = $query->first();

            if (!$course) {
                return redirect('/courses')->with('error', 'Course not found or not available.');
            }

            // Get related courses in the same category if the category_id column exists
            $relatedCourses = collect([]);

            if ($hasCategoryIdColumn && isset($course->category_id)) {
                $relatedQuery = Course::where('category_id', $course->category_id)
                    ->where('course_id', '!=', $course->course_id);

                if ($hasApprovalStatusColumn) {
                    $relatedQuery->where('approval_status', 'approved');
                }

                if ($usersTableExists) {
                    $relatedQuery->with('instructor');
                }

                $relatedCourses = $relatedQuery->take(4)->get();
            }

            // Check if ratings/reviews tables exist
            $courseReviewsTableExists = \Schema::hasTable('course_reviews');
            $ratingsTableExists = \Schema::hasTable('ratings');

            // Initialize rating variables
            $averageRating = 0;
            $totalRatings = 0;
            $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
            $reviews = collect([]);

            // Get ratings data based on available tables
            if ($courseReviewsTableExists) {
                // Use course_reviews table
                $reviews = \App\Models\CourseReview::where('course_id', $courseId)
                    ->where('is_approved', true)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

                $averageRating = \App\Models\CourseReview::where('course_id', $courseId)
                    ->where('is_approved', true)
                    ->avg('rating') ?? 0;

                $totalRatings = \App\Models\CourseReview::where('course_id', $courseId)
                    ->where('is_approved', true)
                    ->count();

                // Count ratings by value
                $ratingDistribution = \App\Models\CourseReview::where('course_id', $courseId)
                    ->where('is_approved', true)
                    ->select('rating', \DB::raw('count(*) as count'))
                    ->groupBy('rating')
                    ->get()
                    ->pluck('count', 'rating')
                    ->toArray();

                // Fill in the rating counts array
                foreach ($ratingDistribution as $rating => $count) {
                    if (isset($ratingCounts[$rating])) {
                        $ratingCounts[$rating] = $count;
                    }
                }
            } elseif ($ratingsTableExists) {
                // Use ratings table
                $ratingField = \Schema::hasColumn('ratings', 'rating') ? 'rating' : 'rating_value';
                $reviewField = \Schema::hasColumn('ratings', 'review') ? 'review' :
                              (\Schema::hasColumn('ratings', 'review_text') ? 'review_text' :
                              (\Schema::hasColumn('ratings', 'comment') ? 'comment' : null));

                $reviewsQuery = \App\Models\Rating::where('course_id', $courseId);

                // Add conditions based on available columns
                if (\Schema::hasColumn('ratings', 'is_published')) {
                    $reviewsQuery->where('is_published', true);
                }

                if (\Schema::hasColumn('ratings', 'admin_review_status')) {
                    $reviewsQuery->orWhere('admin_review_status', 'approved');
                }

                // Get reviews
                $reviews = $reviewsQuery->with('user')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

                // Calculate average rating
                $averageRating = \App\Models\Rating::where('course_id', $courseId);

                if (\Schema::hasColumn('ratings', 'is_published')) {
                    $averageRating->where('is_published', true);
                }

                if (\Schema::hasColumn('ratings', 'admin_review_status')) {
                    $averageRating->orWhere('admin_review_status', 'approved');
                }

                $averageRating = $averageRating->avg($ratingField) ?? 0;

                // Count total ratings
                $totalRatingsQuery = \App\Models\Rating::where('course_id', $courseId);

                if (\Schema::hasColumn('ratings', 'is_published')) {
                    $totalRatingsQuery->where('is_published', true);
                }

                if (\Schema::hasColumn('ratings', 'admin_review_status')) {
                    $totalRatingsQuery->orWhere('admin_review_status', 'approved');
                }

                $totalRatings = $totalRatingsQuery->count();

                // Count ratings by value
                $ratingDistributionQuery = \App\Models\Rating::where('course_id', $courseId);

                if (\Schema::hasColumn('ratings', 'is_published')) {
                    $ratingDistributionQuery->where('is_published', true);
                }

                if (\Schema::hasColumn('ratings', 'admin_review_status')) {
                    $ratingDistributionQuery->orWhere('admin_review_status', 'approved');
                }

                $ratingDistribution = $ratingDistributionQuery
                    ->select($ratingField, \DB::raw('count(*) as count'))
                    ->groupBy($ratingField)
                    ->get()
                    ->pluck('count', $ratingField)
                    ->toArray();

                // Fill in the rating counts array
                foreach ($ratingDistribution as $rating => $count) {
                    $rating = min(5, max(1, round($rating))); // Ensure it's between 1-5
                    if (isset($ratingCounts[$rating])) {
                        $ratingCounts[$rating] = $count;
                    }
                }
            }

            // Format the average rating
            $averageRating = number_format($averageRating, 1);

            return view('pages.course-detail', [
                'course' => $course,
                'relatedCourses' => $relatedCourses,
                'averageRating' => $averageRating,
                'totalRatings' => $totalRatings,
                'ratingCounts' => $ratingCounts,
                'reviews' => $reviews,
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in course detail page: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            // If there's an error or course not found, redirect to courses page
            return redirect('/courses')->with('error', 'Course not found or unavailable: ' . $e->getMessage());
        }
    }
}
