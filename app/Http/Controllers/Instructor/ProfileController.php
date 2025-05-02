<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the instructor profile settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        return view('instructor.profile.index', compact('user'));
    }

    /**
     * Update the instructor's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        
        // Update basic profile information using DB facade
        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'name' => $request->name,
                'bio' => $request->bio,
                'phone' => $request->phone,
                'address' => $request->address,
                'updated_at' => now()
            ]);
        
        return redirect()->route('instructor.profile.index')->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Update the instructor's profile image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image && !str_contains($user->profile_image, 'default')) {
                $oldPath = str_replace('storage/', 'public/', $user->profile_image);
                Storage::delete($oldPath);
            }
            
            // Store new profile image
            $imagePath = $request->file('profile_image')->store('public/profile_images');
            
            // Update using DB facade
            DB::table('users')
                ->where('user_id', $user->user_id)
                ->update([
                    'profile_image' => 'storage/' . str_replace('public/', '', $imagePath),
                    'updated_at' => now()
                ]);
        }
        
        return redirect()->route('instructor.profile.index')->with('success', 'Profile picture updated successfully!');
    }
    
    /**
     * Update the instructor's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        
        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        
        // Update password using DB facade
        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'password_hash' => Hash::make($request->password),
                'updated_at' => now()
            ]);
        
        return redirect()->route('instructor.profile.index')->with('success', 'Password updated successfully!');
    }
} 