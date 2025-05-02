<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    /**
     * Display a listing of student's certificates.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $certificates = Certificate::with(['course'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
            
        return view('student.certificates.index', compact('certificates'));
    }

    /**
     * Display a specific certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $certificate = Certificate::with(['course'])
            ->where('user_id', Auth::id())
            ->where('certificate_id', $id)
            ->firstOrFail();
            
        return view('student.certificates.show', compact('certificate'));
    }

    /**
     * Request a certificate for a completed course.
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function request($courseId)
    {
        // Check if the course offers certificates
        $course = Course::where('course_id', $courseId)
            ->where('certificate_available', true)
            ->firstOrFail();
            
        // Check if the user is enrolled and has completed the course
        $enrollment = Enrollment::where('student_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->firstOrFail();
            
        // Check if the user has already completed the course (implement your completion logic)
        $progress = StudentProgress::where('student_id', Auth::id())
            ->where('course_id', $courseId)
            ->first();
            
        // You may need to adapt this check based on your progress tracking system
        if (!$progress || $progress->completion_percentage < 100) {
            return redirect()->back()->with('error', 'You must complete the course before requesting a certificate.');
        }
        
        // Check if certificate already exists
        $existingCertificate = Certificate::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->first();
            
        if ($existingCertificate) {
            return redirect()->route('student.certificates.show', $existingCertificate->certificate_id)
                ->with('info', 'You already have a certificate for this course.');
        }
        
        // Generate unique certificate number
        $certificateNumber = 'CERT-' . Str::upper(Str::random(8)) . '-' . time();
        
        // Create certificate
        $certificate = new Certificate([
            'user_id' => Auth::id(),
            'course_id' => $courseId,
            'completion_date' => now(),
            'certificate_number' => $certificateNumber,
            'is_valid' => true
        ]);
        
        $certificate->save();
        
        // Generate certificate PDF (implement your PDF generation logic)
        // For now, we'll just set a placeholder URL
        $certificate->certificate_url = '/certificates/' . $certificateNumber . '.pdf';
        $certificate->save();
        
        return redirect()->route('student.certificates.show', $certificate->certificate_id)
            ->with('success', 'Certificate has been generated successfully!');
    }

    /**
     * Download a certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $certificate = Certificate::where('user_id', Auth::id())
            ->where('certificate_id', $id)
            ->where('is_valid', true)
            ->firstOrFail();
            
        // In a real implementation, you would generate or fetch the PDF here
        // For now, we'll just redirect to the certificate view
        return redirect()->route('student.certificates.show', $certificate->certificate_id);
    }
} 