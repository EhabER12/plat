<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParentStudentRelation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ParentLinkController extends Controller
{
    /**
     * Show the link request form for a student.
     *
     * @param  string  $token
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showLinkRequest($token)
    {
        // Find the parent-student relation by token
        $relation = ParentStudentRelation::where('token', $token)
            ->where('verification_status', 'pending')
            ->first();
            
        if (!$relation) {
            return redirect()->route('home')->with('error', 'رابط غير صالح أو منتهي الصلاحية.');
        }
        
        // Get parent info
        $parent = User::where('user_id', $relation->parent_id)->first();
        
        if (!$parent) {
            return redirect()->route('home')->with('error', 'لم يتم العثور على ولي الأمر.');
        }
        
        return view('student.parent_link_request', compact('relation', 'parent'));
    }
    
    /**
     * Process the student's response to a parent link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function respondToLinkRequest(Request $request, $token)
    {
        $request->validate([
            'response' => 'required|in:approve,reject',
        ]);
        
        // Find the parent-student relation by token
        $relation = ParentStudentRelation::where('token', $token)
            ->where('verification_status', 'pending')
            ->first();
            
        if (!$relation) {
            return redirect()->route('home')->with('error', 'رابط غير صالح أو منتهي الصلاحية.');
        }
        
        // Ensure the student is logged in
        if (!Auth::check() || Auth::user()->role !== 'student') {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول كطالب للرد على طلب الربط.');
        }
        
        // Update the relation
        if ($request->response === 'approve') {
            $relation->student_id = Auth::id();
            $relation->verification_status = 'approved';
            $relation->verified_at = now();
            $relation->save();
            
            return redirect()->route('student.profile')->with('success', 'تم قبول طلب الربط بنجاح. يمكن لولي الأمر الآن متابعة تقدمك في الدراسة.');
        } else {
            $relation->verification_status = 'rejected';
            $relation->verification_notes = 'تم الرفض من قبل الطالب.';
            $relation->save();
            
            return redirect()->route('student.profile')->with('success', 'تم رفض طلب الربط.');
        }
    }
} 