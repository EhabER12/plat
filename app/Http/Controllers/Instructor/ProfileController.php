<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            'detailed_description' => 'nullable|string|max:5000',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255|url',
            'linkedin_profile' => 'nullable|string|max:255|url',
            'twitter_profile' => 'nullable|string|max:255|url',
        ]);

        $user = Auth::user();
        
        // Update basic profile information using DB facade
        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'name' => $request->name,
                'bio' => $request->bio,
                'detailed_description' => $request->detailed_description,
                'phone' => $request->phone,
                'address' => $request->address,
                'website' => $request->website,
                'linkedin_profile' => $request->linkedin_profile,
                'twitter_profile' => $request->twitter_profile,
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
                $oldPath = public_path(str_replace(url('/'), '', $user->profile_image));
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Generate a unique filename
            $fileName = time() . '_' . $request->file('profile_image')->getClientOriginalName();
            
            // Save directly to public folder instead of using storage
            $request->file('profile_image')->move(public_path('profile_images'), $fileName);
            
            // Store the public URL path
            $imagePath = '/profile_images/' . $fileName;
            
            // Update using DB facade
            DB::table('users')
                ->where('user_id', $user->user_id)
                ->update([
                    'profile_image' => $imagePath,
                    'updated_at' => now()
                ]);
            
            // Clear any cached user data
            if (function_exists('cache')) {
                cache()->forget('user_' . $user->user_id);
            }
        }
        
        return redirect()->route('instructor.profile.index')->with('success', 'Profile picture updated successfully!');
    }

    /**
     * Update the instructor's banner image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBannerImage(Request $request)
    {
        $request->validate([
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $user = Auth::user();
        
        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            // Delete old banner image if exists
            if ($user->banner_image) {
                $oldPath = public_path(str_replace(url('/'), '', $user->banner_image));
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            // Generate a unique filename
            $fileName = time() . '_banner_' . $request->file('banner_image')->getClientOriginalName();
            
            // Save directly to public folder
            $request->file('banner_image')->move(public_path('banner_images'), $fileName);
            
            // Store the public URL path
            $imagePath = '/banner_images/' . $fileName;
            
            // Update using DB facade
            DB::table('users')
                ->where('user_id', $user->user_id)
                ->update([
                    'banner_image' => $imagePath,
                    'updated_at' => now()
                ]);
            
            // Clear any cached user data
            if (function_exists('cache')) {
                cache()->forget('user_' . $user->user_id);
            }
            
            // Log the information for debugging
            Log::info('Banner image uploaded', [
                'user_id' => $user->user_id,
                'file_path' => $imagePath,
                'full_path' => public_path('banner_images/' . $fileName)
            ]);
        }
        
        return redirect()->route('instructor.profile.index')->with('success', 'Profile banner updated successfully!');
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
        
        // Verify current password
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        
        // Update password
        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'password_hash' => Hash::make($request->password),
                'updated_at' => now()
            ]);
        
        return redirect()->route('instructor.profile.index')->with('success', 'Password updated successfully!');
    }
} 