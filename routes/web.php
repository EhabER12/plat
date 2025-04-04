<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\InstructorMiddleware;

// Pages Routes
Route::get('/', [PageController::class, 'home']);
Route::get('/about', [PageController::class, 'about']);
Route::get('/contact', [PageController::class, 'contact']);
Route::post('/contact', [PageController::class, 'submitContact']);
Route::get('/courses', [PageController::class, 'courses']);
Route::get('/courses/{courseId}', [PageController::class, 'courseDetail'])->name('course.detail');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Instructor Verification Routes
    Route::get('/instructor/verification', [App\Http\Controllers\Instructor\VerificationController::class, 'showForm'])
        ->name('instructor.verification.form');
    Route::post('/instructor/verification', [App\Http\Controllers\Instructor\VerificationController::class, 'submit'])
        ->name('instructor.verification.submit');
    Route::get('/instructor/verification/pending', [App\Http\Controllers\Instructor\VerificationController::class, 'pending'])
        ->name('instructor.verification.pending');

    // Instructor Routes - Protected by instructor middleware
    Route::middleware(\App\Http\Middleware\InstructorMiddleware::class)->prefix('instructor')->group(function () {
        // Dashboard
        Route::get('/', [InstructorDashboardController::class, 'index'])->name('instructor.dashboard');

        // Course Management
        Route::get('/courses', [InstructorDashboardController::class, 'courses'])->name('instructor.courses');
        Route::get('/courses/create', [InstructorDashboardController::class, 'createCourse'])->name('instructor.courses.create');
        Route::post('/courses', [InstructorDashboardController::class, 'storeCourse'])->name('instructor.courses.store');
        Route::get('/courses/{courseId}/edit', [InstructorDashboardController::class, 'editCourse'])->name('instructor.courses.edit');
        Route::put('/courses/{courseId}', [InstructorDashboardController::class, 'updateCourse'])->name('instructor.courses.update');
        Route::get('/courses/{courseId}/manage', [InstructorDashboardController::class, 'manageCourse'])->name('instructor.courses.manage');

        // Course Videos Management
        Route::post('/courses/{courseId}/videos', [App\Http\Controllers\Instructor\VideoController::class, 'store'])->name('instructor.courses.videos.store');
        Route::put('/courses/{courseId}/videos/{videoId}', [App\Http\Controllers\Instructor\VideoController::class, 'update'])->name('instructor.courses.videos.update');
        Route::delete('/courses/{courseId}/videos/{videoId}', [App\Http\Controllers\Instructor\VideoController::class, 'destroy'])->name('instructor.courses.videos.destroy');
        Route::post('/courses/{courseId}/videos/positions', [App\Http\Controllers\Instructor\VideoController::class, 'updatePositions'])->name('instructor.courses.videos.positions');

        // Course Materials Management
        Route::post('/courses/{courseId}/materials', [App\Http\Controllers\Instructor\MaterialController::class, 'store'])->name('instructor.courses.materials.store');
        Route::delete('/courses/{courseId}/materials/{materialId}', [App\Http\Controllers\Instructor\MaterialController::class, 'destroy'])->name('instructor.courses.materials.destroy');

        // Reports
        Route::get('/reports', [InstructorDashboardController::class, 'reports'])->name('instructor.reports');
    });

    // Payment Routes
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/checkout/{courseId}', [PaymentController::class, 'checkout'])->name('checkout');
        Route::post('/process/stripe/{courseId}', [PaymentController::class, 'processStripePayment'])->name('process.stripe');
        Route::post('/process/vodafone/{courseId}', [PaymentController::class, 'processVodafonePayment'])->name('process.vodafone');
        Route::get('/pending/{paymentId}', [PaymentController::class, 'showPendingPayment'])->name('pending');
        Route::get('/success/{paymentId}', [PaymentController::class, 'showSuccessPayment'])->name('success');
        Route::get('/failed/{paymentId}', [PaymentController::class, 'showFailedPayment'])->name('failed');
    });

    // Student Routes - Protected by student middleware
    Route::middleware(\App\Http\Middleware\StudentMiddleware::class)->prefix('student')->group(function () {
        // My Courses
        Route::get('/my-courses', [App\Http\Controllers\Student\CourseController::class, 'myCourses'])->name('student.my-courses');
        Route::get('/course/{courseId}/content', [App\Http\Controllers\Student\CourseController::class, 'courseContent'])->name('student.course-content');

        // Enrollment
        Route::post('/course/{courseId}/enroll', [App\Http\Controllers\Student\EnrollController::class, 'enroll'])->name('student.enroll');

        // Reviews
        Route::post('/course/{courseId}/review', [App\Http\Controllers\Student\CourseController::class, 'review'])->name('student.review');
    });

    // Admin Routes - Protected by admin middleware
    Route::middleware(AdminMiddleware::class)->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Create Demo Data
        Route::get('/create-demo-data', [AdminDashboardController::class, 'createDemoData'])->name('admin.create-demo-data');

        // Reset Database and Add Demo Data
        Route::get('/reset-database', [AdminDashboardController::class, 'resetDatabase'])->name('admin.reset-database');

        // User Management
        Route::get('/users', [AdminDashboardController::class, 'users'])->name('admin.users');
        Route::get('/users/create', [AdminDashboardController::class, 'createUser'])->name('admin.users.create');
        Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/users/{userId}/edit', [AdminDashboardController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/users/{userId}', [AdminDashboardController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/users/{userId}', [AdminDashboardController::class, 'deleteUser'])->name('admin.users.delete');

        // Instructor Verification Management
        Route::get('/instructor-verifications', [AdminDashboardController::class, 'instructorVerifications'])
            ->name('admin.instructor.verifications');
        Route::get('/instructor-verifications/{id}', [AdminDashboardController::class, 'showInstructorVerification'])
            ->name('admin.instructor.verification.show');
        Route::post('/instructor-verifications/{id}', [AdminDashboardController::class, 'processInstructorVerification'])
            ->name('admin.instructor.verification.process');

        // Course Management
        Route::get('/courses', [AdminDashboardController::class, 'courses'])->name('admin.courses');
        Route::get('/courses/create', [AdminDashboardController::class, 'createCourse'])->name('admin.courses.create');
        Route::post('/courses', [AdminDashboardController::class, 'storeCourse'])->name('admin.courses.store');
        Route::get('/courses/{courseId}', [AdminDashboardController::class, 'showCourse'])->name('admin.courses.show');
        Route::get('/courses/{courseId}/edit', [AdminDashboardController::class, 'editCourse'])->name('admin.courses.edit');
        Route::put('/courses/{courseId}', [AdminDashboardController::class, 'updateCourse'])->name('admin.courses.update');
        Route::delete('/courses/{courseId}', [AdminDashboardController::class, 'deleteCourse'])->name('admin.courses.destroy');
        Route::get('/course-approvals', [AdminDashboardController::class, 'courseApprovals'])->name('admin.course.approvals');
        Route::post('/course-approvals/{courseId}', [AdminDashboardController::class, 'processCourseApproval'])->name('admin.course.process-approval');

        // Category Management
        Route::get('/categories', [AdminDashboardController::class, 'categories'])->name('admin.categories');
        Route::post('/categories', [AdminDashboardController::class, 'storeCategory'])->name('admin.categories.store');
        Route::put('/categories/{categoryId}', [AdminDashboardController::class, 'updateCategory'])->name('admin.categories.update');
        Route::delete('/categories/{categoryId}', [AdminDashboardController::class, 'deleteCategory'])->name('admin.categories.delete');

        // Reports & Analytics
        Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('admin.reports');

        // Settings
        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('admin.settings.update');
    });

    // Course Materials Download - Accessible to enrolled students and instructors
    Route::get('/courses/{courseId}/materials/{materialId}/download', [App\Http\Controllers\Instructor\MaterialController::class, 'download'])
        ->name('courses.materials.download');

    // Chat Routes
    Route::prefix('chats')->name('chats.')->group(function () {
        Route::get('/', [App\Http\Controllers\ChatController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\ChatController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ChatController::class, 'store'])->name('store');
        Route::get('/{chatId}', [App\Http\Controllers\ChatController::class, 'show'])->name('show');
        Route::post('/{chatId}/messages', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send-message');
        Route::post('/{chatId}/leave', [App\Http\Controllers\ChatController::class, 'leave'])->name('leave');
        Route::post('/{chatId}/participants', [App\Http\Controllers\ChatController::class, 'addParticipants'])->name('add-participants');
    });
});
