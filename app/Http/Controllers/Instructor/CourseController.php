<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $courses = Course::where('instructor_id', $user->user_id)->paginate(10);
        
        // Group by status for dashboard overview
        $courseStats = [
            'total' => $courses->total(),
            'published' => Course::where('instructor_id', $user->user_id)
                ->where('approval_status', 'approved')->count(),
            'pending' => Course::where('instructor_id', $user->user_id)
                ->where('approval_status', 'pending')->count(),
            'rejected' => Course::where('instructor_id', $user->user_id)
                ->where('approval_status', 'rejected')->count(),
        ];
        
        return view('instructor.courses.index', compact('courses', 'courseStats'));
    }

    /**
     * Show the form for creating a new course.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('instructor.courses.create', compact('categories'));
    }

    /**
     * Store a newly created course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'certificate_available' => 'nullable|boolean',
            'certificate_type' => 'required_if:certificate_available,1|in:default,custom',
            'custom_certificate' => 'required_if:certificate_type,custom|nullable|image|mimes:jpeg,png,jpg|max:5120',
            'certificate_text' => 'nullable|string|max:500',
        ]);
        
        // Create course
        $course = new Course();
        $course->title = $validated['title'];
        $course->description = $validated['description'];
        $course->price = $validated['price'];
        $course->category_id = $validated['category_id'];
        $course->instructor_id = Auth::id();
        $course->approval_status = 'pending'; // Default status
        
        // إعدادات الشهادة
        $course->certificate_available = $request->has('certificate_available');
        
        if ($request->has('certificate_available') && $request->certificate_type) {
            $course->certificate_type = $request->certificate_type;
            $course->certificate_text = $request->certificate_text;
            
            // معالجة ملف الشهادة المخصصة إذا كان متوفراً
            if ($request->certificate_type === 'custom' && $request->hasFile('custom_certificate')) {
                $file = $request->file('custom_certificate');
                $filename = time() . '_certificate_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/certificates'), $filename);
                $course->custom_certificate_path = 'uploads/certificates/' . $filename;
            }
        }
        
        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/courses'), $filename);
            $course->thumbnail = 'uploads/courses/' . $filename;
        }
        
        $course->save();
        
        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course created successfully and is pending approval');
    }

    /**
     * Display the specified course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = Course::with(['category', 'lessons', 'enrollments.student'])
            ->where('instructor_id', Auth::id())
            ->findOrFail($id);
            
        return view('instructor.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $course = Course::where('instructor_id', Auth::id())->findOrFail($id);
        $categories = Category::all();
        
        return view('instructor.courses.edit', compact('course', 'categories'));
    }

    /**
     * Update the specified course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $course = Course::where('instructor_id', Auth::id())->findOrFail($id);
        
        // Prevent editing if course is already published and instructor isn't making a minor update
        if ($course->approval_status === 'approved' && !$request->has('is_minor_update')) {
            return redirect()->back()
                ->with('error', 'This course is already published. For major changes, please contact admin.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'certificate_available' => 'nullable|boolean',
            'certificate_type' => 'required_if:certificate_available,1|in:default,custom',
            'custom_certificate' => 'required_if:certificate_type,custom|nullable|image|mimes:jpeg,png,jpg|max:5120',
            'certificate_text' => 'nullable|string|max:500',
        ]);
        
        $course->title = $validated['title'];
        $course->description = $validated['description'];
        $course->price = $validated['price'];
        $course->category_id = $validated['category_id'];
        
        // إعدادات الشهادة
        $course->certificate_available = $request->has('certificate_available');
        
        if ($request->has('certificate_available') && $request->certificate_type) {
            $course->certificate_type = $request->certificate_type;
            $course->certificate_text = $request->certificate_text;
            
            // معالجة ملف الشهادة المخصصة إذا كان متوفراً
            if ($request->certificate_type === 'custom' && $request->hasFile('custom_certificate')) {
                // حذف ملف الشهادة القديم إذا كان موجوداً
                if ($course->custom_certificate_path) {
                    $oldPath = public_path($course->custom_certificate_path);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $file = $request->file('custom_certificate');
                $filename = time() . '_certificate_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/certificates'), $filename);
                $course->custom_certificate_path = 'uploads/certificates/' . $filename;
            }
        }
        
        // If it's not a minor update, set status back to pending
        if ($course->approval_status === 'approved' && !$request->has('is_minor_update')) {
            $course->approval_status = 'pending';
        }
        
        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Remove old thumbnail if exists
            if ($course->thumbnail && file_exists(public_path($course->thumbnail))) {
                unlink(public_path($course->thumbnail));
            }
            
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/courses'), $filename);
            $course->thumbnail = 'uploads/courses/' . $filename;
        }
        
        $course->save();
        
        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course updated successfully' . ($course->approval_status === 'pending' ? ' and is pending approval' : ''));
    }

    /**
     * Remove the specified course from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = Course::where('instructor_id', Auth::id())->findOrFail($id);
        
        // Check if course has enrollments
        if ($course->enrollments->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete course with active enrollments');
        }
        
        // Remove thumbnail if exists
        if ($course->thumbnail && file_exists(public_path($course->thumbnail))) {
            unlink(public_path($course->thumbnail));
        }
        
        $course->delete();
        
        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course deleted successfully');
    }
    
    /**
     * Manage lessons for a course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function lessons($id)
    {
        $course = Course::with('lessons')
            ->where('instructor_id', Auth::id())
            ->findOrFail($id);
            
        return view('instructor.courses.lessons', compact('course'));
    }
    
    /**
     * Show the form for creating a new lesson.
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function createLesson($courseId)
    {
        $course = Course::where('instructor_id', Auth::id())->findOrFail($courseId);
        return view('instructor.lessons.create', compact('course'));
    }
    
    /**
     * Store a newly created lesson in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function storeLesson(Request $request, $courseId)
    {
        $course = Course::where('instructor_id', Auth::id())->findOrFail($courseId);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer|min:1',
            'video_url' => 'nullable|url',
            'is_free_preview' => 'boolean',
        ]);
        
        $lesson = new Lesson();
        $lesson->course_id = $course->course_id;
        $lesson->title = $validated['title'];
        $lesson->content = $validated['content'];
        $lesson->order = $validated['order'];
        
        if (isset($validated['video_url'])) {
            $lesson->video_url = $validated['video_url'];
        }
        
        $lesson->is_free_preview = $request->has('is_free_preview') ? 1 : 0;
        $lesson->save();
        
        return redirect()->route('instructor.courses.lessons', $course->course_id)
            ->with('success', 'Lesson created successfully');
    }
    
    /**
     * Show feedback for rejected courses.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showFeedback($id)
    {
        $course = Course::where('instructor_id', Auth::id())
            ->findOrFail($id);
            
        return view('instructor.courses.feedback', compact('course'));
    }
    
    /**
     * Submit course for review.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function submitForReview($id)
    {
        $course = Course::where('instructor_id', Auth::id())
            ->where('approval_status', '!=', 'pending')
            ->findOrFail($id);
            
        $course->approval_status = 'pending';
        $course->save();
        
        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course submitted for review');
    }
} 