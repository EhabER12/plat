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
            'code' => 'required|string|max:50|unique:discounts,code',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount_value' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'courses' => 'array',
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

        // Ensure the instructor only selects their own courses
        $instructorCourseIds = Course::where('instructor_id', Auth::id())
            ->pluck('course_id')
            ->toArray();
        
        // Filter courses to ensure only instructor's courses are included
        $selectedCourses = $request->has('courses') ? 
            array_intersect($request->courses, $instructorCourseIds) : [];
        
        // Create the discount
        $discount = new Discount;
        $discount->code = $request->code;
        $discount->description = $request->description;
        $discount->type = $request->type;
        $discount->value = $request->value;
        $discount->min_order_value = $request->min_order_value;
        $discount->max_discount_value = $request->max_discount_value;
        $discount->usage_limit = $request->usage_limit;
        $discount->start_date = $request->start_date;
        $discount->end_date = $request->end_date;
        $discount->is_active = $request->has('is_active');
        $discount->created_by = Auth::id();
        
        $discount->save();
        
        // Sync courses with pivot table
        if (!empty($selectedCourses)) {
            $discount->courses()->sync($selectedCourses);
        }

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
        $discount = Discount::with('courses')
            ->where('created_by', Auth::id())
            ->findOrFail($id);
            
        // Get only the instructor's courses
        $courses = Course::where('instructor_id', Auth::id())
            ->where('approval_status', 'approved')
            ->orderBy('title')
            ->get();
            
        $selectedCourses = $discount->courses ? $discount->courses->pluck('course_id')->toArray() : [];
            
        return view('instructor.discounts.edit', compact('discount', 'courses', 'selectedCourses'));
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
            'code' => 'required|string|max:50|unique:discounts,code,' . $discount->discount_id . ',discount_id',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount_value' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'courses' => 'array',
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

        // Ensure the instructor only selects their own courses
        $instructorCourseIds = Course::where('instructor_id', Auth::id())
            ->pluck('course_id')
            ->toArray();
        
        // Filter courses to ensure only instructor's courses are included
        $selectedCourses = $request->has('courses') ? 
            array_intersect($request->courses, $instructorCourseIds) : [];
        
        // Update the discount
        $discount->code = $request->code;
        $discount->description = $request->description;
        $discount->type = $request->type;
        $discount->value = $request->value;
        $discount->min_order_value = $request->min_order_value;
        $discount->max_discount_value = $request->max_discount_value;
        $discount->usage_limit = $request->usage_limit;
        $discount->start_date = $request->start_date;
        $discount->end_date = $request->end_date;
        $discount->is_active = $request->has('is_active');
        
        $discount->save();
        
        // Sync courses with pivot table
        if (!empty($selectedCourses)) {
            $discount->courses()->sync($selectedCourses);
        } else {
            $discount->courses()->detach();
        }

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
