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
            'identification_type' => 'nullable|string|max:50',
            'identification_number' => 'nullable|string|max:50',
            'payment_email' => 'required|email|max:255',
            'payment_phone' => 'required|string|max:20',
            'payment_bank_name' => 'nullable|string|max:255',
            'payment_account_number' => 'nullable|string|max:255',
            'payment_terms' => 'required|accepted',
        ]);

        // Process verification data
        $verificationData = [
            'user_id' => $user->user_id,
            'education' => $request->education,
            'expertise' => $request->expertise,
            'years_of_experience' => $request->years_of_experience,
            'linkedin_profile' => $request->linkedin_profile,
            'additional_info' => $request->additional_info,
            'qualifications' => $request->education . ', ' . $request->expertise,
            'status' => 'pending',
            'submitted_at' => now(),
            'identification_type' => $request->identification_type ?? 'passport',
            'identification_number' => $request->identification_number ?? '',
            'identification_image' => null,
        ];

        // Process payment account data
        $paymentDetails = [
            'email' => $request->payment_email,
            'phone' => $request->payment_phone,
        ];

        // Add optional payment fields if provided
        if ($request->filled('payment_bank_name')) {
            $paymentDetails['bank_name'] = $request->payment_bank_name;
        }

        if ($request->filled('payment_account_number')) {
            $paymentDetails['account_number'] = $request->payment_account_number;
        }

        // Convert payment details to JSON for storage
        $verificationData['payment_details'] = json_encode($paymentDetails);

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

        // Handle identification image upload
        if ($request->hasFile('identification_image')) {
            $imagePath = $request->file('identification_image')->store('instructor-ids', 'public');
            $verificationData['identification_image'] = $imagePath;
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
            InstructorVerification::create(array_merge($verificationData, ['user_id' => $user->user_id]));
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
