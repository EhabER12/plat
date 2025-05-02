<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the sending of password reset emails using Laravel's
    | password reset features.
    |
    */

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Find the user by email
        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => [__('We can\'t find a user with that email address.')],
            ])->withInput($request->only('email'));
        }

        // Create a new password reset token
        $token = \Illuminate\Support\Str::random(60);
        $expiresAt = now()->addMinutes(60);

        // Store the token in the password_resets table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => bcrypt($token),
                'created_at' => now(),
            ]
        );

        // Create the reset link
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $request->email,
        ], false));

        // Send the email
        \Illuminate\Support\Facades\Mail::send('emails.reset-password', [
            'resetUrl' => $resetUrl,
            'user' => $user,
        ], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject(__('Reset Password Notification'));
        });

        return back()->with('status', __('We have emailed your password reset link!'));
    }

    /**
     * Direct password reset option without email (Emergency method)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function directReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Find the user by email
        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => [__('لا يمكننا العثور على مستخدم بهذا البريد الإلكتروني.')],
            ])->withInput($request->only('email'));
        }

        // Create a direct reset token
        $token = \Illuminate\Support\Str::random(60);
        
        // Store the token in the password_resets table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => bcrypt($token),
                'created_at' => now(),
            ]
        );

        // Create a direct reset link
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $request->email,
        ], false));

        // Show direct reset link on screen instead of sending email
        return back()->with('direct_reset_link', $resetUrl);
    }
} 