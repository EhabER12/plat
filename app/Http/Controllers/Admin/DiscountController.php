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
        $discounts = Discount::with('creator')
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

        // Create the discount
        $discount = new Discount();
        $discount->name = $request->name;
        $discount->description = $request->description;
        $discount->discount_type = $request->discount_type;
        $discount->discount_value = $request->discount_value;
        $discount->applies_to_all_courses = $request->has('applies_to_all_courses');
        $discount->courses = $discount->applies_to_all_courses ? null : $request->courses;
        $discount->start_date = $request->start_date;
        $discount->end_date = $request->end_date;
        $discount->is_active = $request->has('is_active');
        $discount->created_by = Auth::id();
        
        $discount->save();

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
        $discount = Discount::findOrFail($id);
        $courses = Course::where('approval_status', 'approved')
            ->orderBy('title')
            ->get();
            
        return view('admin.discounts.edit', compact('discount', 'courses'));
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

        // Update the discount
        $discount->name = $request->name;
        $discount->description = $request->description;
        $discount->discount_type = $request->discount_type;
        $discount->discount_value = $request->discount_value;
        $discount->applies_to_all_courses = $request->has('applies_to_all_courses');
        $discount->courses = $discount->applies_to_all_courses ? null : $request->courses;
        $discount->start_date = $request->start_date;
        $discount->end_date = $request->end_date;
        $discount->is_active = $request->has('is_active');
        
        $discount->save();

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
        $discount->delete();

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'Discount deleted successfully!');
    }
}
