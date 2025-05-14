<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DiscountController extends Controller
{
    /**
     * Display a listing of the instructor's discounts.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $discounts = Discount::where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('instructor.discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new discount.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get only the instructor's courses
        $courses = Course::where('instructor_id', Auth::id())
            ->where('approval_status', 'approved')
            ->orderBy('title')
            ->get();
            
        return view('instructor.discounts.create', compact('courses'));
    }

    /**
     * Store a newly created discount in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'applies_to_all_courses' => 'boolean',
            'courses' => 'required_if:applies_to_all_courses,0|array',
            'courses.*' => 'exists:courses,course_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Ensure the instructor only applies discounts to their own courses
        $instructorCourseIds = Course::where('instructor_id', Auth::id())
            ->pluck('course_id')
            ->toArray();
        
        // Check if instructor wants to apply discount to all courses
        $appliesToAllCourses = $request->has('applies_to_all_courses');
        
        // If applying to specific courses, filter to ensure only instructor's courses
        $selectedCourses = $appliesToAllCourses ? null : 
            array_intersect($request->courses, $instructorCourseIds);
        
        if (!$appliesToAllCourses && empty($selectedCourses)) {
            return back()
                ->withErrors(['courses' => 'You must select at least one of your own courses.'])
                ->withInput();
        }

        // Create the discount
        $discount = new Discount();
        $discount->name = $request->name;
        $discount->description = $request->description;
        $discount->discount_type = $request->discount_type;
        $discount->discount_value = $request->discount_value;
        $discount->applies_to_all_courses = $appliesToAllCourses;
        
        // If applies to all courses, store all instructor course IDs
        $discount->courses = $appliesToAllCourses ? $instructorCourseIds : $selectedCourses;
        
        $discount->start_date = $request->start_date;
        $discount->end_date = $request->end_date;
        $discount->is_active = $request->has('is_active');
        $discount->created_by = Auth::id();
        
        $discount->save();

        return redirect()
            ->route('instructor.discounts.index')
            ->with('success', 'Discount created successfully!');
    }

    /**
     * Show the form for editing the specified discount.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $discount = Discount::where('created_by', Auth::id())
            ->findOrFail($id);
            
        // Get only the instructor's courses
        $courses = Course::where('instructor_id', Auth::id())
            ->where('approval_status', 'approved')
            ->orderBy('title')
            ->get();
            
        return view('instructor.discounts.edit', compact('discount', 'courses'));
    }

    /**
     * Update the specified discount in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $discount = Discount::where('created_by', Auth::id())
            ->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'applies_to_all_courses' => 'boolean',
            'courses' => 'required_if:applies_to_all_courses,0|array',
            'courses.*' => 'exists:courses,course_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Ensure the instructor only applies discounts to their own courses
        $instructorCourseIds = Course::where('instructor_id', Auth::id())
            ->pluck('course_id')
            ->toArray();
        
        // Check if instructor wants to apply discount to all courses
        $appliesToAllCourses = $request->has('applies_to_all_courses');
        
        // If applying to specific courses, filter to ensure only instructor's courses
        $selectedCourses = $appliesToAllCourses ? null : 
            array_intersect($request->courses, $instructorCourseIds);
        
        if (!$appliesToAllCourses && empty($selectedCourses)) {
            return back()
                ->withErrors(['courses' => 'You must select at least one of your own courses.'])
                ->withInput();
        }

        // Update the discount
        $discount->name = $request->name;
        $discount->description = $request->description;
        $discount->discount_type = $request->discount_type;
        $discount->discount_value = $request->discount_value;
        $discount->applies_to_all_courses = $appliesToAllCourses;
        
        // If applies to all courses, store all instructor course IDs
        $discount->courses = $appliesToAllCourses ? $instructorCourseIds : $selectedCourses;
        
        $discount->start_date = $request->start_date;
        $discount->end_date = $request->end_date;
        $discount->is_active = $request->has('is_active');
        
        $discount->save();

        return redirect()
            ->route('instructor.discounts.index')
            ->with('success', 'Discount updated successfully!');
    }

    /**
     * Remove the specified discount from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $discount = Discount::where('created_by', Auth::id())
            ->findOrFail($id);
            
        $discount->delete();

        return redirect()
            ->route('instructor.discounts.index')
            ->with('success', 'Discount deleted successfully!');
    }
}
