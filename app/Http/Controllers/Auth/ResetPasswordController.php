<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify the token is valid for the given email
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenData || !Hash::check($request->token, $tokenData->token)) {
            return back()->withErrors([
                'email' => [__('The provided token is invalid.')],
            ])->withInput($request->only('email'));
        }

        // Get the user by email
        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => [__('We can\'t find a user with that email address.')],
            ])->withInput($request->only('email'));
        }

        // Update the user's password
        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'password_hash' => Hash::make($request->password),
                'updated_at' => now(),
            ]);

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Redirect to login with success message
        return redirect()->route('login')
            ->with('status', __('Your password has been reset successfully!'));
    }
} 