<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $certificates = Certificate::with(['user', 'course'])->latest()->paginate(15);
        return view('admin.certificates.index', compact('certificates'));
    }

    /**
     * Show the form for creating a new certificate.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $courses = Course::where('certificate_available', true)->get();
        $users = User::whereHas('roles', function($query) {
            $query->where('role', 'student');
        })->get();
        
        return view('admin.certificates.create', compact('courses', 'users'));
    }

    /**
     * Store a newly created certificate in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'course_id' => 'required|exists:courses,course_id',
        ]);

        // Check if the user is enrolled in the course and has completed it
        $enrollment = Enrollment::where('student_id', $validated['user_id'])
            ->where('course_id', $validated['course_id'])
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'User is not enrolled in this course.');
        }

        // Check if certificate already exists
        $existingCertificate = Certificate::where('user_id', $validated['user_id'])
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($existingCertificate) {
            return back()->with('error', 'Certificate already exists for this student and course.');
        }

        // Generate unique certificate number
        $certificateNumber = 'CERT-' . Str::upper(Str::random(8)) . '-' . time();

        // Create certificate
        $certificate = new Certificate([
            'user_id' => $validated['user_id'],
            'course_id' => $validated['course_id'],
            'completion_date' => now(),
            'certificate_number' => $certificateNumber,
            'is_valid' => true
        ]);

        $certificate->save();

        // Generate certificate as PDF would go here in a real implementation
        // For now, we'll just set a placeholder URL
        $certificate->certificate_url = '/certificates/' . $certificateNumber . '.pdf';
        $certificate->save();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate created successfully');
    }

    /**
     * Display the specified certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $certificate = Certificate::with(['user', 'course'])->findOrFail($id);
        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Invalidate a certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invalidate($id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->is_valid = false;
        $certificate->save();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate has been invalidated');
    }

    /**
     * Validate a certificate by certificate number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'certificate_number' => 'required|string'
        ]);

        $certificate = Certificate::with(['user', 'course'])
            ->where('certificate_number', $validated['certificate_number'])
            ->first();

        if (!$certificate) {
            return view('certificate.verification', [
                'verified' => false,
                'message' => 'Certificate not found'
            ]);
        }

        return view('certificate.verification', [
            'verified' => $certificate->is_valid,
            'certificate' => $certificate,
            'message' => $certificate->is_valid 
                ? 'Certificate is valid' 
                : 'Certificate has been invalidated'
        ]);
    }
} 