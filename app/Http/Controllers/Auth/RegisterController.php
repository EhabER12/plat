<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return redirect('/login?show=register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:student,parent,instructor',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Start a transaction to ensure both user and role are created
        DB::beginTransaction();

        try {
            // Handle profile image upload if provided
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/profile_images'), $imageName);
                $profileImagePath = 'uploads/profile_images/' . $imageName;
            }

            // Create user with proper password field
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'profile_image' => $profileImagePath,
                'status' => true,
            ]);

            // Add user role from selection
            DB::table('user_roles')->insert([
                'user_id' => $user->user_id,
                'role' => $request->role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Log the user in
            Auth::login($user);

            // Redirect based on user role
            if ($request->role === 'instructor') {
                return redirect()->route('instructor.verification.form')
                    ->with('success', 'Your account has been created successfully. Please complete your instructor profile for verification.');
            }

            return redirect('/');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput();
        }
    }
}
