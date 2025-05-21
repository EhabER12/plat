<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Log user ID and attributes for debugging
            Log::info('User logged in:', [
                'id' => $user->id,
                'user_id' => $user->user_id,
                'email' => $user->email,
                'name' => $user->name
            ]);
            
            // Get user roles directly from database
            $userRoles = DB::table('user_roles')
                ->where('user_id', $user->user_id)
                ->pluck('role')
                ->toArray();
            
            // Log roles for debugging
            Log::info('User roles:', [
                'user_id' => $user->user_id,
                'roles' => $userRoles
            ]);
            
            // Check if instructor is verified before allowing login
            if (in_array('instructor', $userRoles)) {
                // إضافة رسالة لتوضيح الدخول كمدرس
                session(['login_debug' => 'تم تسجيل الدخول بنجاح كمدرس']);
                
                // Get the verification record
                $verification = $user->instructorVerification;
                
                // Log verification status
                Log::info('Instructor verification:', [
                    'user_id' => $user->user_id,
                    'has_verification' => $verification ? true : false,
                    'status' => $verification ? $verification->status : 'no record'
                ]);
                
                // إضافة معلومات حالة التحقق في السيشن
                if (!$verification) {
                    session(['verification_status' => 'لا يوجد سجل تحقق للمدرس']);
                } else {
                    session(['verification_status' => 'حالة التحقق: ' . $verification->status]);
                }
                
                // If no verification record or status is not approved, redirect to verification form
                if (!$verification || $verification->status !== 'approved') {
                    return redirect()->route('instructor.verification.form')->with('warning', 'Please complete your instructor verification before accessing the dashboard.');
                }
                
                return redirect()->route('instructor.dashboard');
            }
            
            // Redirect based on user role
            if (in_array('admin', $userRoles)) {
                return redirect()->route('admin.dashboard');
            } elseif (in_array('student', $userRoles)) {
                return redirect()->route('student.my-courses');
            } else {
                Log::warning('User has no recognized role:', [
                    'user_id' => $user->user_id,
                    'roles' => $userRoles
                ]);
                return redirect()->intended('/')->with('info', 'تم تسجيل الدخول ولكن لم يتم تحديد دور للمستخدم');
            }
        }

        Log::warning('Login failed for email: ' . $request->email);
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
