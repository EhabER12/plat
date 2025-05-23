<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ParentStudentRelation;
use Illuminate\Support\Facades\Auth;

class VerifiedParentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return redirect('/login')->with('error', 'يجب تسجيل الدخول للوصول إلى هذه الصفحة.');
        }
        
        // Check if the user has the parent role
        if (!$request->user()->hasRole('parent')) {
            return redirect('/')->with('error', 'غير مسموح لك بالوصول إلى هذه الصفحة.');
        }
        
        // Check if at least one parent-student relation is approved
        $parentId = Auth::id();
        $hasApprovedRelation = ParentStudentRelation::where('parent_id', $parentId)
            ->where('verification_status', 'approved')
            ->exists();
            
        if (!$hasApprovedRelation) {
            // If no approved relations, redirect to waiting page
            return redirect()->route('parent.waiting-approval')->with('info', 'في انتظار موافقة الإدارة على طلب التحقق من هويتك كولي أمر.');
        }

        return $next($request);
    }
} 