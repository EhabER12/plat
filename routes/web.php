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
Route::get('/courses', [PageController::class, 'courses'])->name('courses');
Route::get('/courses/{courseId}', [PageController::class, 'courseDetail'])->name('course.detail');

// Instructor Profiles Routes
Route::get('/instructors', [App\Http\Controllers\InstructorProfileController::class, 'index'])->name('instructors.index');
Route::get('/instructors/{id}', [App\Http\Controllers\InstructorProfileController::class, 'show'])->name('instructors.show');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    // Password Reset Routes
    Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::post('/direct-reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'directReset'])
        ->name('password.direct-reset');
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Group Chat Routes - Accessible to all authenticated users
    Route::prefix('chats')->name('chats.')->group(function () {
        Route::get('/', [App\Http\Controllers\ChatController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\ChatController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ChatController::class, 'store'])->name('store');
        Route::get('/{chatId}', [App\Http\Controllers\ChatController::class, 'show'])->name('show');
        Route::post('/{chatId}/messages', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send-message');
        Route::post('/{chatId}/leave', [App\Http\Controllers\ChatController::class, 'leave'])->name('leave');
        Route::post('/{chatId}/participants', [App\Http\Controllers\ChatController::class, 'addParticipants'])->name('add-participants');
    });

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

        // Profile Management
        Route::get('/profile', [App\Http\Controllers\Instructor\ProfileController::class, 'index'])->name('instructor.profile.index');
        Route::put('/profile', [App\Http\Controllers\Instructor\ProfileController::class, 'update'])->name('instructor.profile.update');
        Route::post('/profile/image', [App\Http\Controllers\Instructor\ProfileController::class, 'updateImage'])->name('instructor.profile.update.image');
        Route::put('/profile/password', [App\Http\Controllers\Instructor\ProfileController::class, 'updatePassword'])->name('instructor.profile.update.password');

        // Course Management
        Route::get('/courses', [InstructorDashboardController::class, 'courses'])->name('instructor.courses');
        Route::get('/courses/create', [InstructorDashboardController::class, 'createCourse'])->name('instructor.courses.create');
        Route::post('/courses', [InstructorDashboardController::class, 'storeCourse'])->name('instructor.courses.store');
        Route::get('/courses/{courseId}/edit', [InstructorDashboardController::class, 'editCourse'])->name('instructor.courses.edit');
        Route::put('/courses/{courseId}', [InstructorDashboardController::class, 'updateCourse'])->name('instructor.courses.update');
        Route::get('/courses/{courseId}/manage', [InstructorDashboardController::class, 'manageCourse'])->name('instructor.courses.manage');

        // Course Sections Management
        Route::post('/courses/{courseId}/sections', [App\Http\Controllers\Instructor\SectionController::class, 'store'])->name('instructor.courses.sections.store');
        Route::put('/courses/{courseId}/sections/{sectionId}', [App\Http\Controllers\Instructor\SectionController::class, 'update'])->name('instructor.courses.sections.update');
        Route::delete('/courses/{courseId}/sections/{sectionId}', [App\Http\Controllers\Instructor\SectionController::class, 'destroy'])->name('instructor.courses.sections.destroy');
        Route::post('/courses/{courseId}/sections/positions', [App\Http\Controllers\Instructor\SectionController::class, 'updatePositions'])->name('instructor.courses.sections.positions');

        // Payment Accounts Management
        Route::get('/payment-accounts', [App\Http\Controllers\Instructor\PaymentAccountController::class, 'index'])->name('instructor.payment-accounts.index');
        Route::get('/payment-accounts/create', [App\Http\Controllers\Instructor\PaymentAccountController::class, 'create'])->name('instructor.payment-accounts.create');
        Route::post('/payment-accounts', [App\Http\Controllers\Instructor\PaymentAccountController::class, 'store'])->name('instructor.payment-accounts.store');
        Route::get('/payment-accounts/{id}/edit', [App\Http\Controllers\Instructor\PaymentAccountController::class, 'edit'])->name('instructor.payment-accounts.edit');
        Route::put('/payment-accounts/{id}', [App\Http\Controllers\Instructor\PaymentAccountController::class, 'update'])->name('instructor.payment-accounts.update');
        Route::delete('/payment-accounts/{id}', [App\Http\Controllers\Instructor\PaymentAccountController::class, 'destroy'])->name('instructor.payment-accounts.destroy');
        Route::post('/payment-accounts/{id}/set-default', [App\Http\Controllers\Instructor\PaymentAccountController::class, 'setDefault'])->name('instructor.payment-accounts.set-default');

        // Earnings Management
        Route::get('/earnings', [App\Http\Controllers\Instructor\EarningController::class, 'index'])->name('instructor.earnings.index');
        Route::get('/earnings/history', [App\Http\Controllers\Instructor\EarningController::class, 'history'])->name('instructor.earnings.history');
        Route::get('/earnings/withdrawals', [App\Http\Controllers\Instructor\EarningController::class, 'withdrawals'])->name('instructor.earnings.withdrawals');
        Route::get('/earnings/withdrawals/create', [App\Http\Controllers\Instructor\EarningController::class, 'createWithdrawal'])->name('instructor.earnings.create-withdrawal');
        Route::post('/earnings/withdrawals', [App\Http\Controllers\Instructor\EarningController::class, 'storeWithdrawal'])->name('instructor.earnings.store-withdrawal');
        Route::get('/earnings/withdrawals/{id}', [App\Http\Controllers\Instructor\EarningController::class, 'showWithdrawal'])->name('instructor.earnings.show-withdrawal');
        Route::post('/earnings/withdrawals/{id}/cancel', [App\Http\Controllers\Instructor\EarningController::class, 'cancelWithdrawal'])->name('instructor.earnings.cancel-withdrawal');

        // Course Videos Management
        Route::post('/courses/{courseId}/videos', [App\Http\Controllers\Instructor\VideoController::class, 'store'])->name('instructor.courses.videos.store');
        Route::get('/courses/{courseId}/videos/{videoId}/edit', [App\Http\Controllers\Instructor\VideoController::class, 'edit'])->name('instructor.courses.videos.edit');
        Route::put('/courses/{courseId}/videos/{videoId}', [App\Http\Controllers\Instructor\VideoController::class, 'update'])->name('instructor.courses.videos.update');
        Route::delete('/courses/{courseId}/videos/{videoId}', [App\Http\Controllers\Instructor\VideoController::class, 'destroy'])->name('instructor.courses.videos.destroy');
        Route::post('/courses/{courseId}/videos/{videoId}/delete', [App\Http\Controllers\Instructor\VideoController::class, 'deleteVideo'])->name('instructor.courses.videos.delete');
        Route::post('/courses/{courseId}/videos/positions', [App\Http\Controllers\Instructor\VideoController::class, 'updatePositions'])->name('instructor.courses.videos.positions');

        // Course Materials Management
        Route::post('/courses/{courseId}/materials', [App\Http\Controllers\Instructor\MaterialController::class, 'store'])->name('instructor.courses.materials.store');
        Route::delete('/courses/{courseId}/materials/{materialId}', [App\Http\Controllers\Instructor\MaterialController::class, 'destroy'])->name('instructor.courses.materials.destroy');

        // Exams Management
        Route::get('/exams', [App\Http\Controllers\Instructor\ExamController::class, 'index'])->name('instructor.exams.index');
        Route::get('/exams/create/{courseId?}', [App\Http\Controllers\Instructor\ExamController::class, 'create'])->name('instructor.exams.create');
        Route::post('/exams', [App\Http\Controllers\Instructor\ExamController::class, 'store'])->name('instructor.exams.store');
        Route::get('/exams/{id}', [App\Http\Controllers\Instructor\ExamController::class, 'show'])->name('instructor.exams.show');
        Route::get('/exams/{id}/edit', [App\Http\Controllers\Instructor\ExamController::class, 'edit'])->name('instructor.exams.edit');
        Route::put('/exams/{id}', [App\Http\Controllers\Instructor\ExamController::class, 'update'])->name('instructor.exams.update');
        Route::delete('/exams/{id}', [App\Http\Controllers\Instructor\ExamController::class, 'destroy'])->name('instructor.exams.destroy');
        Route::get('/exams/{id}/results', [App\Http\Controllers\Instructor\ExamController::class, 'results'])->name('instructor.exams.results');
        Route::get('/exam-attempts/{attemptId}', [App\Http\Controllers\Instructor\ExamController::class, 'viewAttempt'])->name('instructor.exams.view-attempt');
        Route::post('/exams/{examId}/questions', [App\Http\Controllers\Instructor\ExamController::class, 'addQuestion'])->name('instructor.exams.add-question');
        Route::put('/questions/{questionId}', [App\Http\Controllers\Instructor\ExamController::class, 'updateQuestion'])->name('instructor.exams.update-question');
        Route::delete('/questions/{questionId}', [App\Http\Controllers\Instructor\ExamController::class, 'removeQuestion'])->name('instructor.exams.remove-question');

        // Reports
        Route::get('/reports', [InstructorDashboardController::class, 'reports'])->name('instructor.reports');

        // Messages
        Route::get('/messages', [App\Http\Controllers\Instructor\MessagesController::class, 'index'])->name('instructor.messages.index');
        Route::get('/messages/{userId}', [App\Http\Controllers\Instructor\MessagesController::class, 'show'])->name('instructor.messages.show');
        Route::post('/messages', [App\Http\Controllers\Instructor\MessagesController::class, 'send'])->name('instructor.messages.send');

        // Quiz routes
        Route::get('/quizzes', [App\Http\Controllers\Instructor\QuizController::class, 'index'])->name('instructor.quizzes.index');
        Route::get('/quizzes/create/{courseId?}', [App\Http\Controllers\Instructor\QuizController::class, 'create'])->name('instructor.quizzes.create');
        Route::post('/quizzes', [App\Http\Controllers\Instructor\QuizController::class, 'store'])->name('instructor.quizzes.store');
        Route::get('/quizzes/{id}', [App\Http\Controllers\Instructor\QuizController::class, 'show'])->name('instructor.quizzes.show');
        Route::get('/quizzes/{id}/edit', [App\Http\Controllers\Instructor\QuizController::class, 'edit'])->name('instructor.quizzes.edit');
        Route::put('/quizzes/{id}', [App\Http\Controllers\Instructor\QuizController::class, 'update'])->name('instructor.quizzes.update');
        Route::put('/quizzes/{id}/questions', [App\Http\Controllers\Instructor\QuizController::class, 'updateQuestions'])->name('instructor.quizzes.update-questions');
        Route::delete('/quizzes/{id}', [App\Http\Controllers\Instructor\QuizController::class, 'destroy'])->name('instructor.quizzes.destroy');
        Route::get('/quiz-attempts/{attemptId}', [App\Http\Controllers\Instructor\QuizController::class, 'viewAttempt'])->name('instructor.quizzes.attempt');
        Route::post('/quiz-attempts/{attemptId}/feedback', [App\Http\Controllers\Instructor\QuizController::class, 'provideFeedback'])->name('instructor.quizzes.provide-feedback');
    });

    // Payment Routes
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/checkout/{courseId}', [PaymentController::class, 'checkout'])->name('checkout');
        Route::post('/process/paymob/{courseId}', [PaymentController::class, 'processPaymobPayment'])->name('process.paymob');
        Route::get('/pending/{paymentId}', [PaymentController::class, 'showPendingPayment'])->name('pending');
        Route::get('/success/{paymentId}', [PaymentController::class, 'showSuccessPayment'])->name('success');
        Route::get('/failed/{paymentId}', [PaymentController::class, 'showFailedPayment'])->name('failed');

        // Paymob webhook and response routes
        Route::post('/paymob/callback', [PaymentController::class, 'paymobCallback'])->name('paymob.callback');
        Route::get('/paymob/response/{status}', [PaymentController::class, 'paymobResponse'])->name('paymob.response');
    });

    // Student Routes - Protected by student middleware
    Route::middleware(\App\Http\Middleware\StudentMiddleware::class)->prefix('student')->group(function () {
        // Dashboard/Profile
        Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('student.profile');
        Route::get('/profile/edit', [App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('student.profile.edit');
        Route::put('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('student.profile.update');
        Route::get('/profile/change-password', [App\Http\Controllers\Student\ProfileController::class, 'showChangePasswordForm'])->name('student.profile.change-password-form');
        Route::post('/profile/change-password', [App\Http\Controllers\Student\ProfileController::class, 'changePassword'])->name('student.profile.change-password');
        Route::get('/profile/index', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('student.profile.index');

        // Certificates
        Route::get('/certificates', [App\Http\Controllers\Student\CertificateController::class, 'index'])->name('student.certificates.index');
        Route::get('/certificates/{id}', [App\Http\Controllers\Student\CertificateController::class, 'show'])->name('student.certificates.show');
        Route::get('/certificates/{id}/download', [App\Http\Controllers\Student\CertificateController::class, 'download'])->name('student.certificates.download');
        Route::post('/course/{courseId}/certificate/request', [App\Http\Controllers\Student\CertificateController::class, 'request'])->name('student.certificate.request');
        
        // Messages
        Route::get('/messages', [App\Http\Controllers\Student\MessagesController::class, 'index'])->name('student.messages.index');
        Route::get('/messages/{instructorId}', [App\Http\Controllers\Student\MessagesController::class, 'show'])->name('student.messages.show');
        Route::post('/messages', [App\Http\Controllers\Student\MessagesController::class, 'send'])->name('student.messages.send');
        // My Courses
        Route::get('/my-courses', [App\Http\Controllers\Student\CourseController::class, 'myCourses'])->name('student.my-courses');
        Route::get('/course/{courseId}/content', [App\Http\Controllers\Student\CourseController::class, 'courseContent'])->name('student.course-content');

        // Enrollment
        Route::post('/course/{courseId}/enroll', [App\Http\Controllers\Student\EnrollController::class, 'enroll'])->name('student.enroll');

        // Reviews
        Route::post('/course/{courseId}/review', [App\Http\Controllers\Student\CourseController::class, 'review'])->name('student.review');

        // Exams
        Route::get('/exams', [App\Http\Controllers\Student\ExamController::class, 'index'])->name('student.exams.index');
        Route::get('/exams/{examId}', [App\Http\Controllers\Student\ExamController::class, 'show'])->name('student.exams.show');
        Route::post('/exams/{examId}/start', [App\Http\Controllers\Student\ExamController::class, 'startAttempt'])->name('student.exams.start');
        Route::get('/exam-attempts/{attemptId}', [App\Http\Controllers\Student\ExamController::class, 'takeExam'])->name('student.exams.take');
        Route::post('/exam-attempts/{attemptId}/submit', [App\Http\Controllers\Student\ExamController::class, 'submitExam'])->name('student.exams.submit');
        Route::get('/exam-results/{attemptId}', [App\Http\Controllers\Student\ExamController::class, 'showResult'])->name('student.exams.result');

        // Quiz routes
        Route::get('/quizzes', [App\Http\Controllers\Student\QuizController::class, 'index'])->name('student.quizzes.index');
        Route::get('/quizzes/{quizId}', [App\Http\Controllers\Student\QuizController::class, 'show'])->name('student.quizzes.show');
        
        // Quiz Attempts routes
        Route::post('/quizzes/{quizId}/start', [App\Http\Controllers\Student\QuizAttemptController::class, 'start'])->name('student.quiz-attempts.start');
        Route::get('/quiz-attempts/{attemptId}/continue', [App\Http\Controllers\Student\QuizAttemptController::class, 'continue'])->name('student.quiz-attempts.continue');
        Route::get('/quiz-attempts/{attemptId}/take', [App\Http\Controllers\Student\QuizAttemptController::class, 'take'])->name('student.quiz-attempts.take');
        Route::put('/quiz-attempts/{attemptId}/save-progress', [App\Http\Controllers\Student\QuizAttemptController::class, 'saveProgress'])->name('student.quiz-attempts.save-progress');
        Route::post('/quiz-attempts/{attemptId}/submit', [App\Http\Controllers\Student\QuizAttemptController::class, 'submit'])->name('student.quiz-attempts.submit');
        Route::get('/quiz-attempts/{attemptId}', [App\Http\Controllers\Student\QuizAttemptController::class, 'show'])->name('student.quiz-attempts.show');
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

        // Certificates Management
        Route::get('/certificates', [App\Http\Controllers\Admin\CertificateController::class, 'index'])->name('admin.certificates.index');
        Route::get('/certificates/create', [App\Http\Controllers\Admin\CertificateController::class, 'create'])->name('admin.certificates.create');
        Route::post('/certificates', [App\Http\Controllers\Admin\CertificateController::class, 'store'])->name('admin.certificates.store');
        Route::get('/certificates/{id}', [App\Http\Controllers\Admin\CertificateController::class, 'show'])->name('admin.certificates.show');
        Route::post('/certificates/{id}/invalidate', [App\Http\Controllers\Admin\CertificateController::class, 'invalidate'])->name('admin.certificates.invalidate');
        
        // Instructor Verification Management
        Route::get('/instructor-verifications', [AdminDashboardController::class, 'instructorVerifications'])
            ->name('admin.instructor.verifications');
        Route::get('/instructor-verifications/{id}', [AdminDashboardController::class, 'showInstructorVerification'])
            ->name('admin.instructor.verification.show');
        Route::post('/instructor-verifications/{id}', [AdminDashboardController::class, 'processInstructorVerification'])
            ->name('admin.instructor.verification.process');

        // Course Management
        Route::get('/courses', [AdminDashboardController::class, 'courses'])->name('admin.courses');
        Route::get('/course/create', [AdminDashboardController::class, 'createCourse'])->name('admin.course.create');
        Route::get('/courses/create', [AdminDashboardController::class, 'createCourse'])->name('admin.courses.create');
        Route::post('/courses', [AdminDashboardController::class, 'storeCourse'])->name('admin.courses.store');
        Route::get('/courses/{courseId}', [AdminDashboardController::class, 'showCourse'])->name('admin.courses.show');

        // Course Videos Management
        Route::post('/courses/{courseId}/videos', [\App\Http\Controllers\Admin\VideoController::class, 'store'])->name('admin.courses.videos.store');
        Route::get('/courses/{courseId}/videos/{videoId}/edit', [\App\Http\Controllers\Admin\VideoController::class, 'edit'])->name('admin.courses.videos.edit');
        Route::put('/courses/{courseId}/videos/{videoId}', [\App\Http\Controllers\Admin\VideoController::class, 'update'])->name('admin.courses.videos.update');
        Route::delete('/courses/{courseId}/videos/{videoId}', [\App\Http\Controllers\Admin\VideoController::class, 'destroy'])->name('admin.courses.videos.destroy');
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

        // Website Appearance Settings
        Route::get('/website-appearance', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'index'])->name('admin.website-appearance');
        Route::post('/website-appearance/hero', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'updateHero'])->name('admin.website-appearance.hero');
        Route::post('/website-appearance/features', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'updateFeatures'])->name('admin.website-appearance.features');
        Route::post('/website-appearance/stats', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'updateStats'])->name('admin.website-appearance.stats');
        Route::post('/website-appearance/navbar-banner', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'updateNavbarBanner'])->name('admin.website-appearance.navbar-banner');

        // Instructor Earnings Management
        Route::get('/instructor-earnings', [App\Http\Controllers\Admin\InstructorEarningController::class, 'index'])->name('admin.instructor-earnings.index');
        Route::get('/instructor-earnings/withdrawals', [App\Http\Controllers\Admin\InstructorEarningController::class, 'withdrawals'])->name('admin.instructor-earnings.withdrawals');
        Route::get('/instructor-earnings/withdrawals/{id}', [App\Http\Controllers\Admin\InstructorEarningController::class, 'showWithdrawal'])->name('admin.instructor-earnings.show-withdrawal');
        Route::post('/instructor-earnings/withdrawals/{id}/process', [App\Http\Controllers\Admin\InstructorEarningController::class, 'processWithdrawal'])->name('admin.instructor-earnings.process-withdrawal');
        Route::get('/instructor-earnings/settings', [App\Http\Controllers\Admin\InstructorEarningController::class, 'settings'])->name('admin.instructor-earnings.settings');
        Route::post('/instructor-earnings/settings', [App\Http\Controllers\Admin\InstructorEarningController::class, 'updateSettings'])->name('admin.instructor-earnings.update-settings');
        Route::get('/instructor-earnings/instructors/{id}', [App\Http\Controllers\Admin\InstructorEarningController::class, 'instructorEarnings'])->name('admin.instructor-earnings.instructor');
    });

    // Course Materials Download - Accessible to enrolled students and instructors
    Route::get('/courses/{courseId}/materials/{materialId}/download', [App\Http\Controllers\Instructor\MaterialController::class, 'download'])
        ->name('courses.materials.download');

    // Protected Video Routes
    Route::get('/video/token/{courseId}/{videoId}', [App\Http\Controllers\VideoStreamController::class, 'getAccessToken'])
        ->name('video.token')
        ->middleware(['auth', 'verified']);
    Route::get('/video/stream/{token}', [App\Http\Controllers\VideoStreamController::class, 'stream'])
        ->name('video.stream');
    Route::post('/video/progress', [App\Http\Controllers\VideoStreamController::class, 'updateProgress'])
        ->name('video.progress')
        ->middleware(['auth', 'verified']);
    Route::get('/api/videos/{videoId}/info', [App\Http\Controllers\VideoStreamController::class, 'getVideoInfo'])
        ->name('api.video.info')
        ->middleware(['auth', 'verified']);
    Route::get('/encrypted-video/{videoId}/{token}', [App\Http\Controllers\VideoStreamController::class, 'encryptedVideo'])
        ->name('encrypted-video');

    // Blocked Access Route
    Route::get('/blocked-access', [App\Http\Controllers\BlockedAccessController::class, 'show'])
        ->name('blocked.access')
        ->middleware('auth');
});

// Certificate Verification (Public Route)
Route::get('/verify-certificate', function() {
    return view('certificate.verification-form');
})->name('certificate.verification.form');

Route::post('/verify-certificate', [App\Http\Controllers\Admin\CertificateController::class, 'verify'])
    ->name('certificate.verification.verify');
