<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    /**
     * Display a listing of the discounts.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $discounts = Discount::with(['creator', 'courses'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new discount.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $courses = Course::where('approval_status', 'approved')
            ->orderBy('title')
            ->get();
            
        return view('admin.discounts.create', compact('courses'));
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

        // Create the discount
        $discount = new Discount();
        $discount->code = $request->code;
        $discount->description = $request->description;
        $discount->type = $request->type;
        $discount->value = $request->value;
        $discount->min_order_value = $request->min_order_value;
        $discount->max_discount_value = $request->max_discount_value;
        $discount->usage_limit = $request->usage_limit;
        $discount->usage_count = 0;
        $discount->start_date = $request->start_date;
        $discount->end_date = $request->end_date;
        $discount->is_active = $request->has('is_active');
        $discount->created_by = Auth::id();
        
        $discount->save();
        
        // Associate courses with the discount
        if ($request->has('courses') && is_array($request->courses)) {
            $discount->courses()->attach($request->courses);
        }

        return redirect()
            ->route('admin.discounts.index')
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
        $discount = Discount::with('courses')->findOrFail($id);
        $courses = Course::where('approval_status', 'approved')
            ->orderBy('title')
            ->get();
        
        $selectedCourses = $discount->courses ? $discount->courses->pluck('course_id')->toArray() : [];
            
        return view('admin.discounts.edit', compact('discount', 'courses', 'selectedCourses'));
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
        $discount = Discount::findOrFail($id);
        
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
        
        // Update course associations
        if ($request->has('courses')) {
            $discount->courses()->sync($request->courses);
        } else {
            $discount->courses()->detach();
        }

        return redirect()
            ->route('admin.discounts.index')
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
        $discount = Discount::findOrFail($id);
        
        // Delete course associations first
        $discount->courses()->detach();
        
        // Then delete the discount
        $discount->delete();

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'Discount deleted successfully!');
    }
}
