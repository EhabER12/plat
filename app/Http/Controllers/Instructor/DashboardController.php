<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\CourseVideo;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Show the instructor dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $instructor = Auth::user();

        // Dashboard stats
        $totalCourses = $instructor->courses()->count();

        // Check if status column exists
        $courseColumns = Schema::getColumnListing('courses');
        $hasStatusColumn = in_array('status', $courseColumns);
        $hasApprovalStatusColumn = in_array('approval_status', $courseColumns);

        // Use the appropriate column for filtering
        if ($hasStatusColumn) {
            $publishedCourses = $instructor->courses()->where('status', 'published')->count();
            $pendingCourses = $instructor->courses()->where('status', 'pending')->count();
            $approvedCourses = $publishedCourses; // Using published as approved courses
        } elseif ($hasApprovalStatusColumn) {
            $publishedCourses = $instructor->courses()->where('approval_status', 'approved')->count();
            $pendingCourses = $instructor->courses()->where('approval_status', 'pending')->count();
            $approvedCourses = $publishedCourses;
        } else {
            // Default values if neither column exists
            $publishedCourses = 0;
            $pendingCourses = 0;
            $approvedCourses = 0;
        }

        // Get recent courses for this instructor
        $recentCourses = $instructor->courses()
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Check if tables exist
        $enrollmentsTableExists = Schema::hasTable('enrollments');
        $courseVideosTableExists = Schema::hasTable('course_videos');
        $courseMaterialsTableExists = Schema::hasTable('course_materials');

        // Get all courses with counts
        $coursesQuery = $instructor->courses();

        // Define counts array
        $countsArray = [];

        if ($enrollmentsTableExists) {
            $countsArray[] = 'students as students_count';
        }

        if ($courseVideosTableExists) {
            $countsArray[] = 'videos';
        }

        if ($courseMaterialsTableExists) {
            $countsArray[] = 'materials';
        }

        // Add any available counts
        if (!empty($countsArray)) {
            $coursesQuery->withCount($countsArray);
        }

        $courses = $coursesQuery->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Add default values for counts if tables don't exist
        if (!$enrollmentsTableExists) {
            $courses->each(function($course) {
                $course->students_count = 0;
            });
        }

        if (!$courseVideosTableExists) {
            $courses->each(function($course) {
                $course->videos_count = 0;
            });
        }

        if (!$courseMaterialsTableExists) {
            $courses->each(function($course) {
                $course->materials_count = 0;
            });
        }

        // Total students (0 if enrollments table doesn't exist)
        $totalStudents = $enrollmentsTableExists ?
            $instructor->courses()->withCount('students')->get()->sum('students_count') : 0;

        // Total revenue (simplified calculation)
        $totalRevenue = $instructor->courses()
            ->where('approval_status', 'approved')
            ->sum('price');

        // Recent enrollments (empty collection)
        $recentEnrollments = collect([]);

        // Recent ratings (empty collection)
        $recentRatings = collect([]);

        return view('instructor.dashboard', [
            'totalCourses' => $totalCourses,
            'publishedCourses' => $publishedCourses,
            'pendingCourses' => $pendingCourses,
            'approvedCourses' => $approvedCourses,
            'recentCourses' => $recentCourses,
            'courses' => $courses,
            'totalStudents' => $totalStudents,
            'totalRevenue' => $totalRevenue,
            'recentEnrollments' => $recentEnrollments,
            'recentRatings' => $recentRatings
        ]);
    }

    /**
     * Show the instructor's courses.
     *
     * @return \Illuminate\View\View
     */
    public function courses()
    {
        // Check if enrollments table exists
        $enrollmentsTableExists = Schema::hasTable('enrollments');

        $coursesQuery = Auth::user()->courses()
            ->with(['category']);

        // Add students relationship only if enrollments table exists
        if ($enrollmentsTableExists) {
            $coursesQuery->with(['students']);
        }

        $courses = $coursesQuery->latest()->paginate(10);

        // If enrollments table doesn't exist, add a students collection
        if (!$enrollmentsTableExists) {
            $courses->each(function($course) {
                $course->students = collect([]);
            });
        }

        return view('instructor.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     *
     * @return \Illuminate\View\View
     */
    public function createCourse()
    {
        $categories = DB::table('categories')->get();
        return view('instructor.courses.create', compact('categories'));
    }

    /**
     * Store a newly created course in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'thumbnail' => 'required|image|max:2048',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/courses'), $filename);
            $thumbnailPath = 'uploads/courses/' . $filename;
        }

        $course = Course::create([
            'instructor_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'thumbnail' => $thumbnailPath,
            'approval_status' => 'pending',
        ]);

        return redirect()->route('instructor.courses.edit', $course->course_id)
            ->with('success', 'Course created successfully. Please add content to your course.');
    }

    /**
     * Show the form for editing a course.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View
     */
    public function editCourse($courseId)
    {
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();
        $categories = DB::table('categories')->get();
        return view('instructor.courses.edit', compact('course', 'categories'));
    }

    /**
     * Update the specified course in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCourse(Request $request, $courseId)
    {
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $courseData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
        ];

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/courses'), $filename);
            $courseData['thumbnail'] = 'uploads/courses/' . $filename;
        }

        $course->update($courseData);

        return redirect()->route('instructor.courses.edit', $course->course_id)
            ->with('success', 'Course updated successfully');
    }

    /**
     * Show the course management page (videos, materials, etc).
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View
     */
    public function manageCourse($courseId)
    {
        // Check if tables exist
        $enrollmentsTableExists = Schema::hasTable('enrollments');
        $courseVideosTableExists = Schema::hasTable('course_videos');
        $courseMaterialsTableExists = Schema::hasTable('course_materials');

        $courseQuery = Course::where('instructor_id', Auth::id());

        // Add relationships based on table existence
        $relationships = ['category'];

        if ($enrollmentsTableExists) {
            $relationships[] = 'students';
        }

        if ($courseVideosTableExists) {
            $relationships[] = 'videos';
        }

        if ($courseMaterialsTableExists) {
            $relationships[] = 'materials';
        }

        if (!empty($relationships)) {
            $courseQuery->with($relationships);
        }

        $course = $courseQuery->findOrFail($courseId);

        // Add empty collections for missing relationships
        if (!$enrollmentsTableExists) {
            $course->students = collect([]);
        }

        if (!$courseVideosTableExists) {
            $course->videos = collect([]);
        }

        if (!$courseMaterialsTableExists) {
            $course->materials = collect([]);
        }

        return view('instructor.courses.manage', compact('course'));
    }

    /**
     * Show the instructor reports page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function reports(Request $request)
    {
        $instructor = Auth::user();
        $reportType = $request->type ?? 'enrollment';
        $timeframe = $request->timeframe ?? 'week';
        $courseId = $request->course_id ?? null;

        // Get instructor courses for the filter
        $courses = Course::where('instructor_id', $instructor->user_id)
            ->where('approval_status', 'approved')
            ->get();

        // Initialize data array
        $data = [];

        // Apply course filter if selected
        $courseFilter = $courseId ? ['course_id' => $courseId] : [];

        if ($reportType === 'revenue') {
            $data = $this->getInstructorRevenueData($instructor->user_id, $timeframe, $courseFilter);
            $recentPayments = DB::table('payments')
                ->join('users', 'payments.user_id', '=', 'users.user_id')
                ->join('courses', 'payments.course_id', '=', 'courses.course_id')
                ->select('payments.*', 'users.name as student_name', 'courses.title as course_title')
                ->where('payments.status', 'completed')
                ->where('courses.instructor_id', $instructor->user_id);

            if ($courseId) {
                $recentPayments->where('payments.course_id', $courseId);
            }

            $recentPayments = $recentPayments->orderBy('payments.paid_at', 'desc')
                ->take(10)
                ->get();

            return view('instructor.reports', compact('data', 'reportType', 'timeframe', 'courses', 'courseId', 'recentPayments'));
        } elseif ($reportType === 'ratings') {
            $data = $this->getInstructorRatingsData($instructor->user_id, $timeframe, $courseFilter);
            $recentRatings = DB::table('course_reviews')
                ->join('users', 'course_reviews.user_id', '=', 'users.user_id')
                ->join('courses', 'course_reviews.course_id', '=', 'courses.course_id')
                ->select('course_reviews.*', 'users.name as student_name', 'courses.title as course_title')
                ->where('courses.instructor_id', $instructor->user_id);

            if ($courseId) {
                $recentRatings->where('course_reviews.course_id', $courseId);
            }

            $recentRatings = $recentRatings->orderBy('course_reviews.created_at', 'desc')
                ->take(10)
                ->get();

            return view('instructor.reports', compact('data', 'reportType', 'timeframe', 'courses', 'courseId', 'recentRatings'));
        } else { // enrollment
            $data = $this->getInstructorEnrollmentData($instructor->user_id, $timeframe, $courseFilter);
            $recentEnrollments = DB::table('enrollments')
                ->join('users', 'enrollments.student_id', '=', 'users.user_id')
                ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
                ->select('enrollments.*', 'users.name as student_name', 'courses.title as course_title')
                ->where('courses.instructor_id', $instructor->user_id);

            if ($courseId) {
                $recentEnrollments->where('enrollments.course_id', $courseId);
            }

            $recentEnrollments = $recentEnrollments->orderBy('enrollments.enrolled_at', 'desc')
                ->take(10)
                ->get();

            return view('instructor.reports', compact('data', 'reportType', 'timeframe', 'courses', 'courseId', 'recentEnrollments'));
        }
    }

    /**
     * Get instructor enrollment report data.
     *
     * @param  int  $instructorId
     * @param  string  $timeframe
     * @param  array  $courseFilter
     * @return array
     */
    private function getInstructorEnrollmentData($instructorId, $timeframe, $courseFilter = [])
    {
        $query = DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
            ->where('courses.instructor_id', $instructorId)
            ->select(
                DB::raw('DATE(enrollments.enrolled_at) as date'),
                DB::raw('count(*) as count')
            )
            ->groupBy('date');

        // Apply course filter if provided
        if (!empty($courseFilter)) {
            $query->where('enrollments.course_id', $courseFilter['course_id']);
        }

        $query = $this->applyTimeframeFilter($query, 'enrollments.enrolled_at', $timeframe);

        $data = $query->get()->pluck('count', 'date')->toArray();

        // Fill in missing dates with zero enrollments
        $range = $this->getDateRangeForTimeframe($timeframe);

        foreach ($range as $date) {
            $formattedDate = $date->format('Y-m-d');
            if (!isset($data[$formattedDate])) {
                $data[$formattedDate] = 0;
            }
        }

        ksort($data);

        return [
            'labels' => array_keys($data),
            'data' => array_values($data),
        ];
    }

    /**
     * Get instructor revenue report data.
     *
     * @param  int  $instructorId
     * @param  string  $timeframe
     * @param  array  $courseFilter
     * @return array
     */
    private function getInstructorRevenueData($instructorId, $timeframe, $courseFilter = [])
    {
        $query = DB::table('payments')
            ->join('courses', 'payments.course_id', '=', 'courses.course_id')
            ->where('courses.instructor_id', $instructorId)
            ->where('payments.status', 'completed')
            ->select(
                DB::raw('DATE(payments.paid_at) as date'),
                DB::raw('SUM(payments.amount) as revenue')
            )
            ->groupBy('date');

        // Apply course filter if provided
        if (!empty($courseFilter)) {
            $query->where('payments.course_id', $courseFilter['course_id']);
        }

        $query = $this->applyTimeframeFilter($query, 'payments.paid_at', $timeframe);

        $data = $query->get()->pluck('revenue', 'date')->toArray();

        // Fill in missing dates with zero revenue
        $range = $this->getDateRangeForTimeframe($timeframe);

        foreach ($range as $date) {
            $formattedDate = $date->format('Y-m-d');
            if (!isset($data[$formattedDate])) {
                $data[$formattedDate] = 0;
            }
        }

        ksort($data);

        return [
            'labels' => array_keys($data),
            'data' => array_values($data),
        ];
    }

    /**
     * Get instructor ratings report data.
     *
     * @param  int  $instructorId
     * @param  string  $timeframe
     * @param  array  $courseFilter
     * @return array
     */
    private function getInstructorRatingsData($instructorId, $timeframe, $courseFilter = [])
    {
        $query = DB::table('course_reviews')
            ->join('courses', 'course_reviews.course_id', '=', 'courses.course_id')
            ->where('courses.instructor_id', $instructorId)
            ->select(
                DB::raw('DATE(course_reviews.created_at) as date'),
                DB::raw('AVG(course_reviews.rating) as avg_rating'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date');

        // Apply course filter if provided
        if (!empty($courseFilter)) {
            $query->where('course_reviews.course_id', $courseFilter['course_id']);
        }

        $query = $this->applyTimeframeFilter($query, 'course_reviews.created_at', $timeframe);

        $ratings = $query->get();

        $avgRatings = $ratings->pluck('avg_rating', 'date')->toArray();
        $counts = $ratings->pluck('count', 'date')->toArray();

        // Fill in missing dates
        $range = $this->getDateRangeForTimeframe($timeframe);

        foreach ($range as $date) {
            $formattedDate = $date->format('Y-m-d');
            if (!isset($avgRatings[$formattedDate])) {
                $avgRatings[$formattedDate] = 0;
                $counts[$formattedDate] = 0;
            }
        }

        ksort($avgRatings);
        ksort($counts);

        return [
            'labels' => array_keys($avgRatings),
            'avg_ratings' => array_values($avgRatings),
            'counts' => array_values($counts),
        ];
    }

    /**
     * Apply timeframe filter to a query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $dateColumn
     * @param  string  $timeframe
     * @return \Illuminate\Database\Query\Builder
     */
    private function applyTimeframeFilter($query, $dateColumn, $timeframe)
    {
        $now = now();

        switch ($timeframe) {
            case 'week':
                $query->where($dateColumn, '>=', $now->copy()->subWeek());
                break;
            case 'month':
                $query->where($dateColumn, '>=', $now->copy()->subMonth());
                break;
            case 'year':
                $query->where($dateColumn, '>=', $now->copy()->subYear());
                break;
        }

        return $query;
    }

    /**
     * Get date range for timeframe.
     *
     * @param  string  $timeframe
     * @return array
     */
    private function getDateRangeForTimeframe($timeframe)
    {
        $now = now();
        $start = null;

        switch ($timeframe) {
            case 'week':
                $start = $now->copy()->subWeek();
                break;
            case 'month':
                $start = $now->copy()->subMonth();
                break;
            case 'year':
                $start = $now->copy()->subYear();
                break;
        }

        $dates = [];
        for ($i = 0; $i <= $start->diffInDays($now); $i++) {
            $dates[] = $start->copy()->addDays($i);
        }

        return $dates;
    }
}