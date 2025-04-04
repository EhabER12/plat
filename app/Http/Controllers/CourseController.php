<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

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
        $query = Course::with(['instructor', 'category'])
            ->where('approval_status', 'approved');
            
        // Apply filters
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Apply sorting
        $sort = $request->sort ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->withCount('enrollments')
                      ->orderBy('enrollments_count', 'desc');
                break;
            case 'highest_rated':
                $query->withCount(['reviews as average_rating' => function($query) {
                    $query->select(\DB::raw('coalesce(avg(rating),0)'));
                }])->orderBy('average_rating', 'desc');
                break;
            default:
                $query->latest();
                break;
        }
        
        $courses = $query->paginate(12);
        $categories = Category::all();
        
        return view('courses.index', compact('courses', 'categories'));
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
        
        // Check if user has already reviewed this course
        $existingReview = Review::where('user_id', Auth::id())
            ->where('course_id', $id)
            ->first();
            
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);
        
        if ($existingReview) {
            // Update existing review
            $existingReview->rating = $validated['rating'];
            $existingReview->comment = $validated['comment'];
            $existingReview->save();
            
            return redirect()->back()
                ->with('success', 'Your review has been updated');
        } else {
            // Create new review
            $review = new Review();
            $review->course_id = $id;
            $review->user_id = Auth::id();
            $review->rating = $validated['rating'];
            $review->comment = $validated['comment'];
            $review->save();
            
            return redirect()->back()
                ->with('success', 'Your review has been submitted');
        }
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
} 