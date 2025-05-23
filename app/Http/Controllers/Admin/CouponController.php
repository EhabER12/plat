<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    /**
     * Display a listing of the coupons.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $coupons = Coupon::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new coupon.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $courses = Course::where('approval_status', 'approved')
            ->orderBy('title')
            ->get();
            
        return view('admin.coupons.create', compact('courses'));
    }

    /**
     * Store a newly created coupon in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'courses_applicable' => 'nullable|array',
            'courses_applicable.*' => 'exists:courses,course_id',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate a random code if not provided
        if (empty($request->code)) {
            $request->merge(['code' => Str::upper(Str::random(8))]);
        }

        // Convert courses applicable to JSON
        $coursesApplicable = $request->has('courses_applicable') ? $request->courses_applicable : null;

        // Create the coupon
        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->max_uses = $request->max_uses;
        $coupon->used_count = 0;
        $coupon->valid_from = $request->valid_from;
        $coupon->valid_to = $request->valid_to;
        $coupon->is_active = $request->has('is_active');
        $coupon->courses_applicable = $coursesApplicable;
        $coupon->minimum_order_amount = $request->minimum_order_amount ?? 0;
        $coupon->created_by = Auth::id();
        
        $coupon->save();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully!');
    }

    /**
     * Show the form for editing the specified coupon.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        $courses = Course::where('approval_status', 'approved')
            ->orderBy('title')
            ->get();
            
        return view('admin.coupons.edit', compact('coupon', 'courses'));
    }

    /**
     * Update the specified coupon in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:coupons,code,' . $id . ',coupon_id',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'courses_applicable' => 'nullable|array',
            'courses_applicable.*' => 'exists:courses,course_id',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Convert courses applicable to JSON
        $coursesApplicable = $request->has('courses_applicable') ? $request->courses_applicable : null;

        // Update the coupon
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->max_uses = $request->max_uses;
        $coupon->valid_from = $request->valid_from;
        $coupon->valid_to = $request->valid_to;
        $coupon->is_active = $request->has('is_active');
        $coupon->courses_applicable = $coursesApplicable;
        $coupon->minimum_order_amount = $request->minimum_order_amount ?? 0;
        
        $coupon->save();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully!');
    }

    /**
     * Remove the specified coupon from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully!');
    }
}
