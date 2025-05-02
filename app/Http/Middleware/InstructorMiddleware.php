<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\InstructorVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstructorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this area.');
        }

        // Check if user has instructor role
        $hasInstructorRole = DB::table('user_roles')
            ->where('user_id', Auth::id())
            ->where('role', 'instructor')
            ->exists();

        if (!$hasInstructorRole) {
            return redirect()->route('login')->with('error', 'You do not have permission to access this area.');
        }

        // Allow access to verification form and pending page for all instructors
        if ($request->routeIs('instructor.verification.form') || 
            $request->routeIs('instructor.verification.submit') || 
            $request->routeIs('instructor.verification.pending')) {
            return $next($request);
        }

        // Check if instructor is verified
        $verification = InstructorVerification::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->first();

        if (!$verification) {
            // Redirect to verification form if not verified
            return redirect()->route('instructor.verification.form')
                ->with('warning', 'You need to complete your instructor verification before accessing other areas.');
        }

        return $next($request);
    }
} 