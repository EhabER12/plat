<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseApiController;
use App\Http\Controllers\Api\EnrollmentApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\ExamApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\VideoStreamController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Categories (public)
Route::get('/categories', [CategoryApiController::class, 'index']);
Route::get('/categories/{id}', [CategoryApiController::class, 'show']);
Route::get('/categories/{id}/courses', [CategoryApiController::class, 'getCourses']);

// Courses (public)
Route::get('/courses', [CourseApiController::class, 'index']);
Route::get('/courses/{id}', [CourseApiController::class, 'show']);
Route::get('/courses/{courseId}/reviews', [CourseApiController::class, 'getReviews']);
Route::get('/courses/{courseId}/ratings', [CourseController::class, 'getCourseReviews']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Video Info
    Route::get('/videos/{videoId}/info', [App\Http\Controllers\Api\VideoInfoController::class, 'getVideoInfo']);

    // Download Attempt Reporting
    Route::post('/download-attempts/report', [App\Http\Controllers\Api\DownloadAttemptController::class, 'report']);
    Route::get('/download-attempts/check', [App\Http\Controllers\Api\DownloadAttemptController::class, 'checkBlocked']);

    // Courses
    Route::post('/courses', [CourseApiController::class, 'store']);
    Route::put('/courses/{id}', [CourseApiController::class, 'update']);
    Route::delete('/courses/{id}', [CourseApiController::class, 'destroy']);
    Route::get('/instructor/courses', [CourseApiController::class, 'myCourses']);
    Route::post('/courses/{courseId}/videos', [CourseApiController::class, 'addVideo']);
    Route::post('/courses/{courseId}/materials', [CourseApiController::class, 'addMaterial']);
    Route::post('/courses/{courseId}/reviews', [CourseApiController::class, 'submitReview']);

    // Enrollments
    Route::get('/enrollments', [EnrollmentApiController::class, 'myEnrollments']);
    Route::post('/courses/{courseId}/enroll', [EnrollmentApiController::class, 'enroll']);
    Route::put('/enrollments/{enrollmentId}/progress', [EnrollmentApiController::class, 'updateProgress']);
    Route::post('/enrollments/{enrollmentId}/complete', [EnrollmentApiController::class, 'markCompleted']);
    Route::get('/enrollments/{enrollmentId}', [EnrollmentApiController::class, 'show']);
    Route::get('/courses/{courseId}/enrollments', [EnrollmentApiController::class, 'courseEnrollments']);

    // Payments
    Route::get('/payments', [PaymentApiController::class, 'paymentHistory']);
    Route::post('/courses/{courseId}/payment', [PaymentApiController::class, 'initiatePayment']);
    Route::get('/payments/{paymentId}/status', [PaymentApiController::class, 'checkPaymentStatus']);

    // Exams
    Route::get('/exams', [ExamApiController::class, 'availableExams']);
    Route::get('/exams/{examId}', [ExamApiController::class, 'show']);
    Route::post('/exams/{examId}/start', [ExamApiController::class, 'startExam']);
    Route::post('/exam-attempts/{attemptId}/submit', [ExamApiController::class, 'submitExam']);
    Route::get('/exam-attempts/{attemptId}/results', [ExamApiController::class, 'getAttemptResults']);
    Route::get('/courses/{courseId}/exams', [ExamApiController::class, 'courseExams']);
    Route::post('/exams', [ExamApiController::class, 'store']);
    Route::put('/exams/{examId}', [ExamApiController::class, 'update']);
    Route::delete('/exams/{examId}', [ExamApiController::class, 'destroy']);

    // Categories (admin only)
    Route::post('/categories', [CategoryApiController::class, 'store']);
    Route::put('/categories/{id}', [CategoryApiController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryApiController::class, 'destroy']);

    // Legacy routes
    Route::get('/courses/{courseId}/participants', [CourseController::class, 'getParticipants']);
    Route::post('/courses/{courseId}/ratings', [CourseController::class, 'submitCourseReview']);

    // Video API Routes
    Route::get('/videos/{videoId}/check-access', [VideoStreamController::class, 'checkAccess']);
    Route::post('/videos/{videoId}/view', [VideoStreamController::class, 'recordView']);
});

// Paymob webhook
Route::post('/payments/paymob/callback', [PaymentApiController::class, 'paymobCallback']);
