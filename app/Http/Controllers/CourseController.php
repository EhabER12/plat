<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\CourseReview;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Course Filter Request', [
                'categories' => $request->categories,
                'sort' => $request->sort,
                'search' => $request->search
            ]);
            
            $query = Course::with(['instructor', 'category'])
                ->where('approval_status', 'approved');
                
            // معالجة تصفية الفئات
            if ($request->has('categories') && is_array($request->categories) && !empty(array_filter($request->categories))) {
                $filteredCategories = array_filter($request->categories); // إزالة القيم الفارغة
                if (!empty($filteredCategories)) {
                    $query->whereIn('category_id', $filteredCategories);
                    \Illuminate\Support\Facades\Log::info('Filtering by categories', $filteredCategories);
                }
            }
            
            // البحث النصي
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            // تطبيق الترتيب
            $sort = $request->sort ?? 'newest';
            switch ($sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'popular':
                    $query->withCount('enrollments')
                        ->orderBy('enrollments_count', 'desc');
                    break;
                case 'rating':
                    $query->withCount(['reviews as average_rating' => function($query) {
                        $query->select(DB::raw('coalesce(avg(rating),0)'));
                    }])->orderBy('average_rating', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
            
            // للتتبع: طباعة الاستعلام للتصحيح 
            // Log full query for debugging
            $bindings = $query->getBindings();
            $querySql = str_replace(['?'], array_map(function ($binding) {
                return is_numeric($binding) ? $binding : "'" . $binding . "'";
            }, $bindings), $query->toSql());
            
            \Illuminate\Support\Facades\Log::info('Course Query: ' . $querySql);
            
            // تنفيذ الاستعلام وتقسيم النتائج
            $courses = $query->paginate(12)->withQueryString();
            
            \Illuminate\Support\Facades\Log::info('Courses Count', ['count' => $courses->count()]);
            
            // معالجة طلبات AJAX
            if ($request->ajax()) {
                // قم بتتبع عملية التصفية في السجلات لأغراض التصحيح
                \Illuminate\Support\Facades\Log::info('AJAX Filter Request', [
                    'categories' => $request->categories,
                    'sort' => $request->sort,
                    'count' => $courses->count()
                ]);
                
                return view('pages.partials.courses_list', compact('courses'))->render();
            }

            // الحصول على جميع الفئات للفلترة
            $categories = Category::all();
            
            // للتأكد من وجود الفئات
            \Illuminate\Support\Facades\Log::info('Categories Count', ['count' => $categories->count()]);
            
            // إضافة بيانات تجريبية للتصحيح إذا لم تكن هناك دورات
            if ($courses->isEmpty() && !$request->has('categories') && !$request->has('sort') && !$request->has('search')) {
                // If no courses found and no filters applied, check if we need to seed demo data
                $totalCourses = Course::count();
                if ($totalCourses == 0) {
                    \Illuminate\Support\Facades\Log::info('No courses found in database, adding demo courses');
                    $this->seedDemoCourses();
                    return redirect()->route('courses.index')
                        ->with('info', 'لقد تم إضافة دورات تجريبية لعرضها. هذه الدورات للعرض فقط.');
                }
            }
            
            // عرض صفحة الدورات الكاملة
            return view('pages.courses', compact('courses', 'categories'));
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in courses index', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('pages.courses', [
                'error' => $e->getMessage(),
                'courses' => collect([]),
                'categories' => Category::all()
            ]);
        }
    }

    /**
     * Display the specified course.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $course = Course::with([
            'instructor', 
            'category', 
            'lessons' => function($query) {
                $query->orderBy('order');
            },
            'reviews.user'
        ])->where('approval_status', 'approved')
            ->findOrFail($id);
        
        // Calculate average rating
        $avgRating = $course->reviews->avg('rating');
        $totalReviews = $course->reviews->count();
        
        // Check if user is enrolled in this course
        $isEnrolled = false;
        if (Auth::check()) {
            $isEnrolled = Enrollment::where('student_id', Auth::id())
                ->where('course_id', $id)
                ->exists();
        }
        
        // Get similar courses from the same category
        $similarCourses = Course::where('category_id', $course->category_id)
            ->where('course_id', '!=', $id)
            ->where('approval_status', 'approved')
            ->take(4)
            ->get();
            
        return view('courses.show', compact(
            'course', 
            'avgRating', 
            'totalReviews', 
            'isEnrolled', 
            'similarCourses'
        ));
    }
    
    /**
     * Display courses by category.
     *
     * @param  int  $categoryId
     * @return \Illuminate\View\View
     */
    public function byCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        
        $courses = Course::with(['instructor', 'category'])
            ->where('category_id', $categoryId)
            ->where('approval_status', 'approved')
            ->paginate(12);
            
        return view('courses.by_category', compact('courses', 'category'));
    }
    
    /**
     * Display courses by instructor.
     *
     * @param  int  $instructorId
     * @return \Illuminate\View\View
     */
    public function byInstructor($instructorId)
    {
        $instructor = \App\Models\User::findOrFail($instructorId);
        
        $courses = Course::with(['category'])
            ->where('instructor_id', $instructorId)
            ->where('approval_status', 'approved')
            ->paginate(12);
            
        return view('courses.by_instructor', compact('courses', 'instructor'));
    }
    
    /**
     * Enroll in a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enroll(Request $request, $id)
    {
        $course = Course::where('approval_status', 'approved')->findOrFail($id);
        
        // Check if already enrolled
        $existingEnrollment = Enrollment::where('student_id', Auth::id())
            ->where('course_id', $id)
            ->first();
            
        if ($existingEnrollment) {
            return redirect()->route('courses.learn', $id)
                ->with('info', 'You are already enrolled in this course');
        }
        
        // Process payment for paid courses
        if ($course->price > 0) {
            // For now, we'll just redirect to a payment page
            // In a real application, this would integrate with a payment gateway
            return redirect()->route('payment.checkout', $id);
        }
        
        // Create enrollment record for free courses
        $enrollment = new Enrollment();
        $enrollment->course_id = $id;
        $enrollment->student_id = Auth::id();
        $enrollment->enrollment_date = now();
        $enrollment->save();
        
        return redirect()->route('courses.learn', $id)
            ->with('success', 'You have successfully enrolled in this course');
    }
    
    /**
     * Display course learning page.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function learn($id)
    {
        $course = Course::with([
            'lessons' => function($query) {
                $query->orderBy('order');
            }
        ])->findOrFail($id);
        
        // Check if user is enrolled
        $enrollment = Enrollment::where('student_id', Auth::id())
            ->where('course_id', $id)
            ->first();
            
        if (!$enrollment && $course->price > 0) {
            return redirect()->route('courses.show', $id)
                ->with('error', 'You must enroll in this course to access the content');
        }
        
        // Get current lesson (first lesson or lesson from query param)
        $currentLessonId = request('lesson', $course->lessons->first()->lesson_id ?? null);
        $currentLesson = null;
        
        if ($currentLessonId) {
            foreach ($course->lessons as $lesson) {
                if ($lesson->lesson_id == $currentLessonId) {
                    $currentLesson = $lesson;
                    break;
                }
            }
        }
        
        // If no lesson found, redirect to course page
        if (!$currentLesson) {
            return redirect()->route('courses.show', $id)
                ->with('error', 'The requested lesson could not be found');
        }
        
        // Update progress if enrolled
        if ($enrollment) {
            // Here we would update the user's progress for this lesson
            // For now, this is a placeholder
        }
        
        return view('courses.learn', compact('course', 'currentLesson'));
    }
    
    /**
     * Submit a review for a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitReview(Request $request, $id)
    {
        // Check if user is enrolled in the course
        $enrollment = Enrollment::where('student_id', Auth::id())
            ->where('course_id', $id)
            ->first();
            
        if (!$enrollment) {
            return redirect()->back()
                ->with('error', 'You must be enrolled in this course to submit a review');
        }
        
        $course = Course::findOrFail($id);
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);
        
        // Check which table to use for reviews
        if (Schema::hasTable('course_reviews')) {
            // Use CourseReview model
            CourseReview::updateOrCreate(
                [
                    'course_id' => $id,
                    'user_id' => Auth::id()
                ],
                [
                    'rating' => $validated['rating'],
                    'review' => $validated['comment'],
                    'is_approved' => true
                ]
            );
        } else {
            // Use Review model as fallback
            Review::updateOrCreate(
                [
                    'course_id' => $id,
                    'user_id' => Auth::id()
                ],
                [
                    'rating' => $validated['rating'],
                    'comment' => $validated['comment'],
                    'visibility' => 'public'
                ]
            );
        }
        
        return redirect()->back()
            ->with('success', 'Your review has been submitted');
    }

    /**
     * Display featured courses on homepage.
     *
     * @return \Illuminate\View\View
     */
    public function featured()
    {
        $featuredCourses = Course::with(['instructor', 'category'])
            ->where('approval_status', 'approved')
            ->where('is_featured', true)
            ->take(6)
            ->get();
            
        $popularCourses = Course::with(['instructor', 'category'])
            ->where('approval_status', 'approved')
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(6)
            ->get();
            
        $categories = Category::withCount(['courses' => function($query) {
            $query->where('approval_status', 'approved');
        }])->take(8)->get();
        
        return view('homepage', compact('featuredCourses', 'popularCourses', 'categories'));
    }

    /**
     * Seed demo courses if none exist
     */
    private function seedDemoCourses()
    {
        try {
            // Create some categories if none exist
            if (Category::count() == 0) {
                $categories = [
                    ['name' => 'Programming', 'description' => 'Computer programming courses'],
                    ['name' => 'Graphic Design', 'description' => 'Graphic design courses'],
                    ['name' => 'Business', 'description' => 'Business and entrepreneurship courses'],
                    ['name' => 'Marketing', 'description' => 'Digital marketing courses'],
                ];
                
                foreach ($categories as $category) {
                    Category::create($category);
                }
            }
            
            // Get instructor ID (first admin or create one)
            $instructor = \App\Models\User::where('role', 'admin')
                ->orWhere('role', 'instructor')
                ->first();
                
            if (!$instructor) {
                $instructor = \App\Models\User::create([
                    'name' => 'Demo Instructor',
                    'email' => 'instructor@example.com',
                    'password' => bcrypt('password'),
                    'role' => 'instructor',
                ]);
            }
            
            // Get category IDs
            $categories = Category::all();
            
            // Create 6 demo courses
            $courses = [
                [
                    'title' => 'Introduction to Web Development',
                    'description' => 'Learn the basics of HTML, CSS, and JavaScript to build websites.',
                    'price' => 29.99,
                    'category_id' => $categories->where('name', 'Programming')->first()->category_id ?? $categories->first()->category_id,
                    'instructor_id' => $instructor->user_id,
                    'approval_status' => 'approved',
                ],
                [
                    'title' => 'Advanced JavaScript Concepts',
                    'description' => 'Deep dive into JavaScript with advanced concepts like promises, async/await, and more.',
                    'price' => 49.99,
                    'category_id' => $categories->where('name', 'Programming')->first()->category_id ?? $categories->first()->category_id,
                    'instructor_id' => $instructor->user_id,
                    'approval_status' => 'approved',
                ],
                [
                    'title' => 'Graphic Design Fundamentals',
                    'description' => 'Learn the fundamentals of graphic design including typography, color theory, and composition.',
                    'price' => 39.99,
                    'category_id' => $categories->where('name', 'Graphic Design')->first()->category_id ?? $categories->first()->category_id,
                    'instructor_id' => $instructor->user_id,
                    'approval_status' => 'approved',
                ],
                [
                    'title' => 'Digital Marketing Essentials',
                    'description' => 'Essential skills for digital marketing including SEO, social media, and content marketing.',
                    'price' => 59.99,
                    'category_id' => $categories->where('name', 'Marketing')->first()->category_id ?? $categories->first()->category_id,
                    'instructor_id' => $instructor->user_id,
                    'approval_status' => 'approved',
                ],
                [
                    'title' => 'Business Strategy and Planning',
                    'description' => 'Learn how to create effective business strategies and plans for your organization.',
                    'price' => 79.99,
                    'category_id' => $categories->where('name', 'Business')->first()->category_id ?? $categories->first()->category_id,
                    'instructor_id' => $instructor->user_id,
                    'approval_status' => 'approved',
                ],
                [
                    'title' => 'Mobile App Development with React Native',
                    'description' => 'Build cross-platform mobile apps using React Native framework.',
                    'price' => 69.99,
                    'category_id' => $categories->where('name', 'Programming')->first()->category_id ?? $categories->first()->category_id,
                    'instructor_id' => $instructor->user_id,
                    'approval_status' => 'approved',
                ]
            ];
            
            foreach ($courses as $course) {
                Course::create($course);
            }
            
            \Illuminate\Support\Facades\Log::info('Created 6 demo courses successfully');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating demo courses', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}