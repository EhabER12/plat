<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;
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
        $courses = Course::with(['instructor', 'category'])->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $instructors = User::whereHas('roles', function($query) {
            $query->where('role', 'instructor');
        })->get();
        
        return view('admin.courses.create', compact('categories', 'instructors'));
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
            'instructor_id' => 'required|exists:users,user_id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean',
        ]);
        
        // Create course
        $course = new Course();
        $course->title = $validated['title'];
        $course->description = $validated['description'];
        $course->price = $validated['price'];
        $course->category_id = $validated['category_id'];
        $course->instructor_id = $validated['instructor_id'];
        $course->approval_status = 'pending'; // Default status
        $course->is_featured = $request->has('is_featured') ? 1 : 0;
        
        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/courses'), $filename);
            $course->thumbnail = 'uploads/courses/' . $filename;
        }
        
        $course->save();
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully');
    }

    /**
     * Display the specified course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = Course::with(['instructor', 'category', 'lessons', 'enrollments'])->findOrFail($id);
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $categories = Category::all();
        $instructors = User::whereHas('roles', function($query) {
            $query->where('role', 'instructor');
        })->get();
        
        return view('admin.courses.edit', compact('course', 'categories', 'instructors'));
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'instructor_id' => 'required|exists:users,user_id',
            'status' => 'required|in:published,pending,draft,rejected',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean',
            'feedback' => 'nullable|string',
        ]);
        
        $course = Course::findOrFail($id);
        $course->title = $validated['title'];
        $course->description = $validated['description'];
        $course->price = $validated['price'];
        $course->category_id = $validated['category_id'];
        $course->instructor_id = $validated['instructor_id'];
        $course->status = $validated['status']; // Using the accessor
        $course->is_featured = $request->has('is_featured') ? 1 : 0;
        
        if (isset($validated['feedback'])) {
            $course->feedback = $validated['feedback'];
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
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully');
    }

    /**
     * Remove the specified course from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        
        // Remove thumbnail if exists
        if ($course->thumbnail && file_exists(public_path($course->thumbnail))) {
            unlink(public_path($course->thumbnail));
        }
        
        $course->delete();
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully');
    }
    
    /**
     * Show all pending course approvals.
     *
     * @return \Illuminate\Http\Response
     */
    public function approvals()
    {
        $pendingCourses = Course::where('approval_status', 'pending')
            ->with(['instructor', 'category'])
            ->paginate(10);
            
        return view('admin.courses.approvals', compact('pendingCourses'));
    }
    
    /**
     * Process a course approval request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function processApproval(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:published,rejected',
            'feedback' => 'nullable|string|max:1000',
        ]);
        
        $course = Course::findOrFail($id);
        
        // Set status using the accessor method
        $course->status = $validated['status'];
        
        if (!empty($validated['feedback'])) {
            $course->feedback = $validated['feedback'];
        }
        
        $course->save();
        
        return redirect()->route('admin.courses.approvals')
            ->with('success', 'Course ' . ($validated['status'] === 'published' ? 'approved' : 'rejected') . ' successfully');
    }

    /**
     * Toggle featured status of a course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleFeatured($id)
    {
        $course = Course::findOrFail($id);
        $course->is_featured = !$course->is_featured;
        $course->save();
        
        return redirect()->back()
            ->with('success', 'Course featured status updated');
    }
} 