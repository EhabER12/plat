<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user()) {
            return redirect('/login')->with('error', 'يجب تسجيل الدخول للوصول إلى هذه الصفحة.');
        }
        
        // Check if the user has the required role using the User model's hasRole method
        if (!$request->user()->hasRole($role)) {
            return redirect('/')->with('error', 'غير مسموح لك بالوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
} 