<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    /**
     * Show the instructor verification form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        $user = Auth::user();
        $verification = $user->instructorVerification;

        return view('instructor.verification', compact('user', 'verification'));
    }

    /**
     * Process the instructor verification submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'education' => 'required|string|max:255',
            'expertise' => 'required|string|max:255',
            'years_of_experience' => 'required|string|max:50',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'linkedin_profile' => 'nullable|url|max:255',
            'additional_info' => 'nullable|string',
        ]);

        // Process verification data
        $verificationData = [
            'user_id' => $user->user_id,
            'education' => $request->education,
            'expertise' => $request->expertise,
            'years_of_experience' => $request->years_of_experience,
            'linkedin_profile' => $request->linkedin_profile,
            'additional_info' => $request->additional_info,
            'status' => 'pending',
            'submitted_at' => now(),
        ];

        // Handle certificate file upload
        if ($request->hasFile('certificate_file')) {
            $certificatePath = $request->file('certificate_file')->store('instructor-certificates', 'public');
            $verificationData['certificate_file'] = $certificatePath;
        }

        // Handle CV file upload
        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('instructor-cvs', 'public');
            $verificationData['cv_file'] = $cvPath;
        }

        // Update or create verification record
        if ($user->instructorVerification) {
            // Delete old files if new ones are uploaded
            if ($request->hasFile('certificate_file') && $user->instructorVerification->certificate_file) {
                Storage::disk('public')->delete($user->instructorVerification->certificate_file);
            }

            if ($request->hasFile('cv_file') && $user->instructorVerification->cv_file) {
                Storage::disk('public')->delete($user->instructorVerification->cv_file);
            }

            $user->instructorVerification->update($verificationData);
        } else {
            // Create new verification record
            $user->instructorVerification()->create($verificationData);
        }

        return redirect()->route('instructor.verification.pending')
            ->with('success', 'Your verification information has been submitted successfully. It is now pending approval from our team.');
    }

    /**
     * Show the pending verification page.
     *
     * @return \Illuminate\View\View
     */
    public function pending()
    {
        $user = Auth::user();
        $verification = $user->instructorVerification;

        if (!$verification || $verification->status === 'approved') {
            return redirect()->route('instructor.dashboard');
        }

        return view('instructor.verification_pending', compact('user', 'verification'));
    }
}
