<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\InstructorMiddleware;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookPurchaseController;

// Pages Routes
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about']);
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('contact.submit');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{courseId}', [PageController::class, 'courseDetail'])->name('course.detail');

// Instructor Profiles Routes
Route::get('/instructors', [App\Http\Controllers\InstructorProfileController::class, 'index'])->name('instructors.index');
Route::get('/instructors/{id}', [App\Http\Controllers\InstructorProfileController::class, 'show'])->name('instructors.show');
Route::get('/instructor/profile/{id?}', [App\Http\Controllers\InstructorProfileController::class, 'show'])->name('instructor.profile');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Parent Registration
    Route::get('/register/parent', [App\Http\Controllers\Auth\ParentRegistrationController::class, 'showRegistrationForm'])->name('register.parent');
    Route::post('/register/parent', [App\Http\Controllers\Auth\ParentRegistrationController::class, 'register']);

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
        Route::post('/profile/banner', [App\Http\Controllers\Instructor\ProfileController::class, 'updateBannerImage'])->name('instructor.profile.update.banner');
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

        // Top Students Analysis
        Route::get('/top-students', [InstructorDashboardController::class, 'topStudents'])->name('instructor.top-students');

        // Messages
        Route::get('/messages', [App\Http\Controllers\Instructor\MessagesController::class, 'index'])->name('instructor.messages.index');
        Route::get('/messages/{userId}', [App\Http\Controllers\Instructor\MessagesController::class, 'show'])->name('instructor.messages.show');
        Route::post('/messages', [App\Http\Controllers\Instructor\MessagesController::class, 'send'])->name('instructor.messages.send');
        Route::post('/messages/get-new', [App\Http\Controllers\Instructor\MessagesController::class, 'getNewMessages'])->name('instructor.messages.get-new');

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

        // Coupon Management
        Route::get('/coupons', [App\Http\Controllers\Instructor\CouponController::class, 'index'])->name('instructor.coupons.index');
        Route::get('/coupons/create', [App\Http\Controllers\Instructor\CouponController::class, 'create'])->name('instructor.coupons.create');
        Route::post('/coupons', [App\Http\Controllers\Instructor\CouponController::class, 'store'])->name('instructor.coupons.store');
        Route::get('/coupons/{id}/edit', [App\Http\Controllers\Instructor\CouponController::class, 'edit'])->name('instructor.coupons.edit');
        Route::patch('/coupons/{id}', [App\Http\Controllers\Instructor\CouponController::class, 'update'])->name('instructor.coupons.update');
        Route::delete('/coupons/{id}', [App\Http\Controllers\Instructor\CouponController::class, 'destroy'])->name('instructor.coupons.destroy');

        // Discount Management
        Route::get('/discounts', [App\Http\Controllers\Instructor\DiscountController::class, 'index'])->name('instructor.discounts.index');
        Route::get('/discounts/create', [App\Http\Controllers\Instructor\DiscountController::class, 'create'])->name('instructor.discounts.create');
        Route::post('/discounts', [App\Http\Controllers\Instructor\DiscountController::class, 'store'])->name('instructor.discounts.store');
        Route::get('/discounts/{id}/edit', [App\Http\Controllers\Instructor\DiscountController::class, 'edit'])->name('instructor.discounts.edit');
        Route::patch('/discounts/{id}', [App\Http\Controllers\Instructor\DiscountController::class, 'update'])->name('instructor.discounts.update');
        Route::delete('/discounts/{id}', [App\Http\Controllers\Instructor\DiscountController::class, 'destroy'])->name('instructor.discounts.destroy');

        // Book Management
        Route::get('/books', [App\Http\Controllers\Instructor\BookController::class, 'index'])->name('instructor.books.index');
        Route::get('/books/create', [App\Http\Controllers\Instructor\BookController::class, 'create'])->name('instructor.books.create');
        Route::post('/books', [App\Http\Controllers\Instructor\BookController::class, 'store'])->name('instructor.books.store');
        Route::get('/books/{book}/edit', [App\Http\Controllers\Instructor\BookController::class, 'edit'])->name('instructor.books.edit');
        Route::put('/books/{book}', [App\Http\Controllers\Instructor\BookController::class, 'update'])->name('instructor.books.update');
        Route::delete('/books/{book}', [App\Http\Controllers\Instructor\BookController::class, 'destroy'])->name('instructor.books.destroy');
    });

    // Payment Routes
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/checkout/{courseId}', [PaymentController::class, 'checkout'])->name('checkout');
        Route::post('/process/paymob/{courseId}', [PaymentController::class, 'processPaymobPayment'])->name('process.paymob');
        Route::post('/process/stripe/{courseId}', [PaymentController::class, 'processStripePayment'])->name('process.stripe');
        Route::post('/process/vodafone/{courseId}', [PaymentController::class, 'processVodafonePayment'])->name('process.vodafone');
        Route::get('/verify/vodafone/{reference}', [PaymentController::class, 'verifyVodafonePayment'])->name('verify.vodafone');
        Route::get('/pending/{paymentId}', [PaymentController::class, 'showPendingPayment'])->name('pending');
        Route::get('/success/{paymentId}', [PaymentController::class, 'showSuccessPayment'])->name('success');
        Route::get('/failed/{paymentId}', [PaymentController::class, 'showFailedPayment'])->name('failed');
        Route::get('/failed-payment', [PaymentController::class, 'showGenericFailure'])->name('generic-failed');

        // Coupon routes
        Route::post('/apply-coupon/{courseId}', [PaymentController::class, 'applyCoupon'])->name('apply-coupon');
        Route::post('/remove-coupon/{courseId}', [PaymentController::class, 'removeCoupon'])->name('remove-coupon');

        // Paymob webhook callback from dashboard
        Route::post('/paymob/callback', [PaymentController::class, 'paymobCallback'])->name('paymob.callback');

        // Paymob response pages (redirects to success/failure pages)
        Route::get('/paymob/response/{status}', [PaymentController::class, 'paymobResponse'])->name('paymob.response');

        // Test Routes - For simulation only
        Route::get('/test/simulate/{courseId}/{method?}', [PaymentController::class, 'simulatePayment'])->name('test.simulate');

        // Unknown transaction processing
        Route::post('/process-unknown-transaction', [PaymentController::class, 'processUnknownTransaction'])->name('process-unknown-transaction');
    });

    // Additional Paymob payment routes - These handle callbacks from Paymob to the default URL they expect
    // IMPORTANT: These routes receive the actual payment notifications from Paymob when users complete payment
    Route::post('/api/acceptance/post_pay', function(Request $request) {
        // Check if this is a book purchase transaction
        $transactionId = $request->input('merchant_order_id', '');

        if (Str::startsWith($transactionId, 'book_')) {
            return app()->make(BookPurchaseController::class)->paymobCallback($request);
        } else {
            return app()->make(PaymentController::class)->paymobCallback($request);
        }
    });

    Route::get('/api/acceptance/post_pay', function(Request $request) {
        // Check if this is a book purchase transaction
        $transactionId = $request->input('merchant_order_id', '');

        if (Str::startsWith($transactionId, 'book_')) {
            return app()->make(BookPurchaseController::class)->paymobCallback($request);
        } else {
            return app()->make(PaymentController::class)->paymobCallback($request);
        }
    });

    // Book purchase routes
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/checkout/{book}', [BookPurchaseController::class, 'checkout'])->name('checkout');
        Route::post('/purchase/paymob/{book}', [BookPurchaseController::class, 'processPaymobPayment'])->name('purchase.paymob');
        Route::get('/purchase/confirmation/{book}', [BookPurchaseController::class, 'showConfirmation'])->name('purchase.confirmation');
    });

    // Development-only route to manually complete a payment (only enable in development)
    Route::get('/payment/debug/complete/{transactionId}', [PaymentController::class, 'debugCompletePayment']);

    // Route for handling unknown transactions (when payment is received but transaction not found)
    Route::get('/payment/unknown-transaction', [PaymentController::class, 'showUnknownTransaction'])->name('payment.unknown-transaction');

    // Student Routes - Protected by student middleware
    Route::middleware(\App\Http\Middleware\StudentMiddleware::class)->prefix('student')->group(function () {
        // Dashboard/Profile
        Route::get('/', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('student.dashboard');
        Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('student.profile');
        Route::get('/profile/edit', [App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('student.profile.edit');
        Route::put('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('student.profile.update');
        Route::get('/profile/change-password', [App\Http\Controllers\Student\ProfileController::class, 'showChangePasswordForm'])->name('student.profile.change-password-form');
        Route::post('/profile/change-password', [App\Http\Controllers\Student\ProfileController::class, 'changePassword'])->name('student.profile.change-password');
        Route::get('/profile/index', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('student.profile.index');

        // مسارات تقدم الطالب في مشاهدة الفيديوهات
        Route::post('/save-progress', [App\Http\Controllers\Student\StudentProgressController::class, 'saveProgress'])->name('student.save-progress');

        // تحويل طلبات GET لحفظ التقدم إلى طلبات POST (redirect)
        Route::get('/save-progress', function() {
            // في حالة استخدام طريقة GET، نقوم بإعادة توجيه الطلب مع رسالة خطأ
            return response()->json([
                'success' => false,
                'error' => 'طريقة GET غير مسموح بها لحفظ التقدم. يجب استخدام POST.'
            ], 405);
        })->name('student.save-progress.get');

        Route::get('/course/{courseId}/progress', [App\Http\Controllers\Student\StudentProgressController::class, 'getCourseProgress'])->name('student.course.progress');
        Route::get('/video/{videoId}/progress', [App\Http\Controllers\Student\StudentProgressController::class, 'getVideoProgress'])->name('student.video.progress');

        // Certificates
        Route::get('/certificates', [App\Http\Controllers\Student\CertificateController::class, 'index'])->name('student.certificates.index');
        Route::get('/certificates/{id}', [App\Http\Controllers\Student\CertificateController::class, 'show'])->name('student.certificates.show');
        Route::get('/certificates/{id}/download', [App\Http\Controllers\Student\CertificateController::class, 'download'])->name('student.certificates.download');
        Route::post('/course/{courseId}/certificate/request', [App\Http\Controllers\Student\CertificateController::class, 'request'])->name('student.certificate.request');

        // Messages
        Route::get('/messages', [App\Http\Controllers\Student\MessagesController::class, 'index'])->name('student.messages.index');
        Route::get('/messages/{instructorId}', [App\Http\Controllers\Student\MessagesController::class, 'show'])->name('student.messages.show');
        Route::post('/messages', [App\Http\Controllers\Student\MessagesController::class, 'send'])->name('student.messages.send');
        Route::post('/messages/get-new', [App\Http\Controllers\Student\MessagesController::class, 'getNewMessages'])->name('student.messages.get-new');
        // My Courses
        Route::get('/my-courses', [App\Http\Controllers\Student\CourseController::class, 'myCourses'])->name('student.my-courses');
        Route::get('/course/{courseId}/content', [App\Http\Controllers\Student\CourseController::class, 'courseContent'])->name('student.course-content');
        Route::get('/course/{courseId}/video/{videoId?}', [App\Http\Controllers\Student\ContentController::class, 'show'])->name('student.course-video');

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
        Route::get('/quizzes/{id}', [App\Http\Controllers\Student\QuizController::class, 'show'])->name('student.quizzes.show');

        // Quiz Attempts routes
        Route::post('/quizzes/{id}/start', [App\Http\Controllers\Student\QuizAttemptController::class, 'start'])->name('student.quiz-attempts.start');
        Route::get('/quiz-attempts/{attemptId}/continue', [App\Http\Controllers\Student\QuizAttemptController::class, 'continue'])->name('student.quiz-attempts.continue');
        Route::get('/quiz-attempts/{attemptId}/take', [App\Http\Controllers\Student\QuizAttemptController::class, 'take'])->name('student.quiz-attempts.take');
        Route::put('/quiz-attempts/{attemptId}/save-progress', [App\Http\Controllers\Student\QuizAttemptController::class, 'saveProgress'])->name('student.quiz-attempts.save-progress');
        Route::post('/quiz-attempts/{attemptId}/submit', [App\Http\Controllers\Student\QuizAttemptController::class, 'submit'])->name('student.quiz-attempts.submit');
        Route::get('/quiz-attempts/{attemptId}', [App\Http\Controllers\Student\QuizAttemptController::class, 'show'])->name('student.quiz-attempts.show');

        // Motivation System Routes
        Route::get('/motivation', [App\Http\Controllers\Student\MotivationController::class, 'index'])->name('student.motivation.index');
        Route::get('/motivation/badges', [App\Http\Controllers\Student\MotivationController::class, 'badges'])->name('student.motivation.badges');
        Route::get('/motivation/achievements', [App\Http\Controllers\Student\MotivationController::class, 'achievements'])->name('student.motivation.achievements');

        // Notifications Routes
        Route::get('/notifications', [App\Http\Controllers\Student\NotificationsController::class, 'index'])->name('student.notifications.index');
        Route::get('/notifications/{id}', [App\Http\Controllers\Student\NotificationsController::class, 'show'])->name('student.notifications.show');
        Route::post('/notifications/{id}/mark-as-read', [App\Http\Controllers\Student\NotificationsController::class, 'markAsRead'])->name('student.notifications.mark-as-read');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\Student\NotificationsController::class, 'markAllAsRead'])->name('student.notifications.mark-all-read');
        Route::delete('/notifications/{id}', [App\Http\Controllers\Student\NotificationsController::class, 'destroy'])->name('student.notifications.destroy');
    });

    // Ruta adicional para guardar progreso (accesible desde /save-progress)
    Route::post('/save-progress', [App\Http\Controllers\Student\StudentProgressController::class, 'saveProgress'])
        ->middleware(['auth', \App\Http\Middleware\StudentMiddleware::class]);

    // Ruta para capturar peticiones mal direccionadas a /student/save-progress
    Route::post('/student/save-progress', [App\Http\Controllers\Student\StudentProgressController::class, 'saveProgress'])
        ->name('student.save-progress')
        ->middleware(['auth', \App\Http\Middleware\StudentMiddleware::class]);

    // Admin Routes - Protected by admin middleware
    Route::middleware(AdminMiddleware::class)->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Create Demo Data (for development only)
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
        Route::get('/instructor-verifications/{verification_id}', [AdminDashboardController::class, 'showInstructorVerification'])
            ->name('admin.instructor.verification.show');
        Route::post('/instructor-verifications/{verification_id}', [AdminDashboardController::class, 'processInstructorVerification'])
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

        // Admin Profile
        Route::get('/profile', [AdminDashboardController::class, 'profile'])->name('admin.profile');
        Route::post('/profile', [AdminDashboardController::class, 'updateProfile'])->name('admin.profile.update');
        Route::post('/profile/password', [AdminDashboardController::class, 'updatePassword'])->name('admin.profile.password');

        // Website Appearance Routes
        Route::get('/website-appearance', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'index'])->name('admin.website-appearance');
        Route::post('/website-appearance/hero', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'updateHero'])->name('admin.website-appearance.hero');
        Route::post('/website-appearance/features', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'updateFeatures'])->name('admin.website-appearance.features');
        Route::post('/website-appearance/stats', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'updateStats'])->name('admin.website-appearance.stats');
        Route::post('/website-appearance/navbar-banner', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'updateNavbarBanner'])->name('admin.website-appearance.navbar-banner');
        Route::post('/website-appearance/about', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'updateAbout'])->name('admin.website-appearance.about');
        Route::post('/website-appearance/video', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'updateVideo'])->name('admin.website-appearance.video');
        Route::post('/website-appearance/partners', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'updatePartners'])->name('admin.website-appearance.partners');
        Route::post('/website-appearance/footer', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'updateFooter'])->name('admin.website-appearance.update-footer');
        Route::post('/website-appearance/clear-cache', [App\Http\Controllers\Admin\WebsiteAppearanceController::class, 'clearCache'])->name('admin.website-appearance.clear-cache');

        // Parent-Student Relations Verifications
        Route::get('/parent-verifications', [App\Http\Controllers\ParentStudentController::class, 'index'])->name('admin.parent-verifications.index');
        Route::get('/parent-verifications/pending', [App\Http\Controllers\ParentStudentController::class, 'pendingVerifications'])->name('admin.parent-verifications.pending');
        Route::get('/parent-verifications/{id}', [App\Http\Controllers\ParentStudentController::class, 'show'])->name('admin.parent-verifications.show');
        Route::post('/parent-verifications/{id}/verify', [App\Http\Controllers\ParentStudentController::class, 'verify'])->name('admin.parent-verifications.verify');
        Route::get('/parent-verifications/{id}/document/{documentType}', [App\Http\Controllers\ParentStudentController::class, 'downloadDocument'])->name('admin.parent-verifications.document');

        // Instructor Earnings Management
        Route::get('/instructor-earnings', [App\Http\Controllers\Admin\InstructorEarningController::class, 'index'])->name('admin.instructor-earnings.index');
        Route::get('/instructor-earnings/withdrawals', [App\Http\Controllers\Admin\InstructorEarningController::class, 'withdrawals'])->name('admin.instructor-earnings.withdrawals');
        Route::get('/instructor-earnings/withdrawals/{id}', [App\Http\Controllers\Admin\InstructorEarningController::class, 'showWithdrawal'])->name('admin.instructor-earnings.show-withdrawal');
        Route::post('/instructor-earnings/withdrawals/{id}/process', [App\Http\Controllers\Admin\InstructorEarningController::class, 'processWithdrawal'])->name('admin.instructor-earnings.process-withdrawal');
        Route::get('/instructor-earnings/settings', [App\Http\Controllers\Admin\InstructorEarningController::class, 'settings'])->name('admin.instructor-earnings.settings');
        Route::post('/instructor-earnings/settings', [App\Http\Controllers\Admin\InstructorEarningController::class, 'updateSettings'])->name('admin.instructor-earnings.update-settings');
        Route::get('/instructor-earnings/instructors/{id}', [App\Http\Controllers\Admin\InstructorEarningController::class, 'instructorEarnings'])->name('admin.instructor-earnings.instructor');

        // Coupon Management
        Route::get('/coupons', [App\Http\Controllers\Admin\CouponController::class, 'index'])->name('admin.coupons.index');
        Route::get('/coupons/create', [App\Http\Controllers\Admin\CouponController::class, 'create'])->name('admin.coupons.create');
        Route::post('/coupons', [App\Http\Controllers\Admin\CouponController::class, 'store'])->name('admin.coupons.store');
        Route::get('/coupons/{id}/edit', [App\Http\Controllers\Admin\CouponController::class, 'edit'])->name('admin.coupons.edit');
        Route::patch('/coupons/{id}', [App\Http\Controllers\Admin\CouponController::class, 'update'])->name('admin.coupons.update');
        Route::delete('/coupons/{id}', [App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('admin.coupons.destroy');

        // Discount Management
        Route::get('/discounts', [App\Http\Controllers\Admin\DiscountController::class, 'index'])->name('admin.discounts.index');
        Route::get('/discounts/create', [App\Http\Controllers\Admin\DiscountController::class, 'create'])->name('admin.discounts.create');
        Route::post('/discounts', [App\Http\Controllers\Admin\DiscountController::class, 'store'])->name('admin.discounts.store');
        Route::get('/discounts/{id}/edit', [App\Http\Controllers\Admin\DiscountController::class, 'edit'])->name('admin.discounts.edit');
        Route::patch('/discounts/{id}', [App\Http\Controllers\Admin\DiscountController::class, 'update'])->name('admin.discounts.update');
        Route::delete('/discounts/{id}', [App\Http\Controllers\Admin\DiscountController::class, 'destroy'])->name('admin.discounts.destroy');

        // Banned Words Management
        Route::get('/banned-words', [App\Http\Controllers\Admin\BannedWordsController::class, 'index'])->name('admin.banned-words.index');
        Route::get('/banned-words/create', [App\Http\Controllers\Admin\BannedWordsController::class, 'create'])->name('admin.banned-words.create');
        Route::post('/banned-words', [App\Http\Controllers\Admin\BannedWordsController::class, 'store'])->name('admin.banned-words.store');
        Route::get('/banned-words/{bannedWord}/edit', [App\Http\Controllers\Admin\BannedWordsController::class, 'edit'])->name('admin.banned-words.edit');
        Route::put('/banned-words/{bannedWord}', [App\Http\Controllers\Admin\BannedWordsController::class, 'update'])->name('admin.banned-words.update');
        Route::delete('/banned-words/{bannedWord}', [App\Http\Controllers\Admin\BannedWordsController::class, 'destroy'])->name('admin.banned-words.destroy');
        Route::post('/banned-words/{bannedWord}/toggle-status', [App\Http\Controllers\Admin\BannedWordsController::class, 'toggleStatus'])->name('admin.banned-words.toggle-status');
        Route::get('/flagged-messages', [App\Http\Controllers\Admin\BannedWordsController::class, 'flaggedMessages'])->name('admin.banned-words.flagged-messages');
        Route::post('/banned-words/test-filter', [App\Http\Controllers\Admin\BannedWordsController::class, 'testFilter'])->name('admin.banned-words.test-filter');
        Route::post('/banned-words/bulk-import', [App\Http\Controllers\Admin\BannedWordsController::class, 'bulkImport'])->name('admin.banned-words.bulk-import');

        // Admin notifications routes
        Route::get('notifications/test-create', [\App\Http\Controllers\Admin\NotificationsController::class, 'createTestNotification'])->name('admin.notifications.test-create');
        Route::get('notifications', [\App\Http\Controllers\Admin\NotificationsController::class, 'index'])->name('admin.notifications.index');
        Route::get('notifications/{id}', [\App\Http\Controllers\Admin\NotificationsController::class, 'show'])->name('admin.notifications.show');
        Route::post('notifications/{id}/mark-read', [\App\Http\Controllers\Admin\NotificationsController::class, 'markAsRead'])->name('admin.notifications.mark-read');

        // Admin Messages routes
        Route::prefix('messages')->name('admin.messages.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\MessagesController::class, 'index'])->name('index');
            Route::get('/new', [\App\Http\Controllers\Admin\MessagesController::class, 'create'])->name('create');
            Route::post('/new', [\App\Http\Controllers\Admin\MessagesController::class, 'store'])->name('store');
            Route::get('/conversation/{userId}', [\App\Http\Controllers\Admin\MessagesController::class, 'conversation'])->name('conversation');
            Route::post('/send', [\App\Http\Controllers\Admin\MessagesController::class, 'send'])->name('send');
            Route::post('/get-new', [\App\Http\Controllers\Admin\MessagesController::class, 'getNewMessages'])->name('get-new');
        });
        Route::post('notifications/mark-multiple-read', [\App\Http\Controllers\Admin\NotificationsController::class, 'markMultipleAsRead'])->name('admin.notifications.mark-multiple-read');
        Route::post('notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationsController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-read');
        Route::delete('notifications/{id}', [\App\Http\Controllers\Admin\NotificationsController::class, 'destroy'])->name('admin.notifications.destroy');

        // Badges Management
        Route::get('/badges', [App\Http\Controllers\Admin\BadgeController::class, 'index'])->name('admin.badges.index');
        Route::get('/badges/create', [App\Http\Controllers\Admin\BadgeController::class, 'create'])->name('admin.badges.create');
        Route::post('/badges', [App\Http\Controllers\Admin\BadgeController::class, 'store'])->name('admin.badges.store');
        Route::get('/badges/{id}', [App\Http\Controllers\Admin\BadgeController::class, 'show'])->name('admin.badges.show');
        Route::get('/badges/{id}/edit', [App\Http\Controllers\Admin\BadgeController::class, 'edit'])->name('admin.badges.edit');
        Route::put('/badges/{id}', [App\Http\Controllers\Admin\BadgeController::class, 'update'])->name('admin.badges.update');
        Route::delete('/badges/{id}', [App\Http\Controllers\Admin\BadgeController::class, 'destroy'])->name('admin.badges.destroy');

        // Achievements Management
        Route::get('/achievements', [App\Http\Controllers\Admin\AchievementController::class, 'index'])->name('admin.achievements.index');
        Route::get('/achievements/create', [App\Http\Controllers\Admin\AchievementController::class, 'create'])->name('admin.achievements.create');
        Route::post('/achievements', [App\Http\Controllers\Admin\AchievementController::class, 'store'])->name('admin.achievements.store');
        Route::get('/achievements/{id}', [App\Http\Controllers\Admin\AchievementController::class, 'show'])->name('admin.achievements.show');
        Route::get('/achievements/{id}/edit', [App\Http\Controllers\Admin\AchievementController::class, 'edit'])->name('admin.achievements.edit');
        Route::put('/achievements/{id}', [App\Http\Controllers\Admin\AchievementController::class, 'update'])->name('admin.achievements.update');
        Route::delete('/achievements/{id}', [App\Http\Controllers\Admin\AchievementController::class, 'destroy'])->name('admin.achievements.destroy');
    });

    // Course Materials Download - Accessible to enrolled students and instructors
    Route::get('/courses/{courseId}/materials/{materialId}/download', [App\Http\Controllers\Instructor\MaterialController::class, 'download'])
        ->name('courses.materials.download');

    // Protected Video Routes
    Route::get('/video/token/{courseId}/{videoId}', [App\Http\Controllers\VideoStreamController::class, 'getAccessToken'])
        ->name('video.token');
    Route::get('/video/stream/{token}', [App\Http\Controllers\VideoStreamController::class, 'stream'])
        ->name('video.stream');
    Route::get('/video/stream/hls-segment/{token}', [App\Http\Controllers\VideoStreamController::class, 'hlsSegment'])
        ->name('video.hls-segment');
    Route::post('/video/progress', [App\Http\Controllers\VideoStreamController::class, 'updateProgress'])
        ->name('video.progress')
        ->middleware(['auth', 'verified']);
    Route::get('/api/videos/{videoId}/info', [App\Http\Controllers\VideoStreamController::class, 'getVideoInfo'])
        ->name('api.video.info')
        ->middleware(['auth', 'verified']);
    Route::get('/encrypted-video/{videoId}/{token}', [App\Http\Controllers\VideoStreamController::class, 'encryptedVideo'])
        ->name('encrypted-video');

    // Rutas adicionales para el progreso del video
    Route::get('/video/{videoId}/progress', [App\Http\Controllers\Student\StudentProgressController::class, 'getVideoProgress'])
        ->middleware(['auth', \App\Http\Middleware\StudentMiddleware::class]);

    // Blocked Access Route
    Route::get('/blocked-access', [App\Http\Controllers\BlockedAccessController::class, 'show'])
        ->name('blocked.access')
        ->middleware('auth');

    // Additional Paymob payment routes
    Route::get('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
    Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
    Route::get('/payment/response', [PaymentController::class, 'processResponse'])->name('payment.response');
    Route::get('/payment/form', function () {
        return view('payments.form');
    })->name('payment.form');
});

// Certificate Verification (Public Route)
Route::get('/verify-certificate', function() {
    return view('certificate.verification-form');
})->name('certificate.verification.form');

Route::post('/verify-certificate', [App\Http\Controllers\Admin\CertificateController::class, 'verify'])
    ->name('certificate.verification.verify');

// Student-Parent Link
Route::get('/student/parent/link-request/{token}', [App\Http\Controllers\Student\ParentLinkController::class, 'showLinkRequest'])->name('student.parent-link-request');
Route::post('/student/parent/link-request/{token}/respond', [App\Http\Controllers\Student\ParentLinkController::class, 'respondToLinkRequest'])->name('student.respond-to-parent-link');

// Basic parent route with just role middleware
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class.':parent'])->prefix('parent')->group(function () {
    // Waiting approval page - accessible to any parent
    Route::get('/waiting-approval', [App\Http\Controllers\ParentDashboardController::class, 'waitingApproval'])->name('parent.waiting-approval');

    // Link request form - accessible to any parent
    Route::get('/link-request', [App\Http\Controllers\ParentDashboardController::class, 'linkRequestForm'])->name('parent.link-request');
    Route::post('/link-request', [App\Http\Controllers\ParentDashboardController::class, 'storeLinkRequest'])->name('parent.store-link-request');
    Route::post('/link-request/{requestId}/resubmit', [App\Http\Controllers\ParentDashboardController::class, 'resubmitLinkRequest'])->name('parent.resubmit-link-request');
});

// Parent routes that require verification
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class.':parent', 'verified.parent'])->prefix('parent')->group(function () {
    // Dashboard and student monitoring - requires verification
    Route::get('/', [App\Http\Controllers\ParentDashboardController::class, 'index'])->name('parent.dashboard');
    Route::get('/student/{studentId}', [App\Http\Controllers\ParentDashboardController::class, 'studentActivity'])->name('parent.student-activity');
    Route::get('/activities', [App\Http\Controllers\ParentDashboardController::class, 'activities'])->name('parent.activities');
    Route::get('/reports', [App\Http\Controllers\ParentDashboardController::class, 'reports'])->name('parent.reports');

    // Profile management - requires verification
    Route::match(['get', 'post'], '/profile', [App\Http\Controllers\ParentDashboardController::class, 'profile'])->name('parent.profile');
    Route::post('/profile/password', [App\Http\Controllers\ParentDashboardController::class, 'updatePassword'])->name('parent.profile.update-password');
});

// Language Route - Public Route
Route::get('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');

// Book Routes
Route::get('/books', [App\Http\Controllers\BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [App\Http\Controllers\BookController::class, 'show'])->name('books.show');
Route::get('/books/{book}/download', [App\Http\Controllers\BookController::class, 'download'])->name('books.download')->middleware('auth');
Route::get('/books/{book}/cover', [App\Http\Controllers\BookController::class, 'showCover'])->name('books.cover');
Route::get('/books/{book}/pdf', [App\Http\Controllers\BookController::class, 'showPdf'])->name('books.pdf')->middleware('auth');

// Direct file access route for new book file structure
Route::get('/books/{bookId}/{type}/{filename}', function ($bookId, $type, $filename) {
    // Validate the type to prevent directory traversal
    if (!in_array($type, ['pdf', 'cover'])) {
        abort(404);
    }

    $path = storage_path("app/public/books/{$bookId}/{$type}/{$filename}");

    if (!file_exists($path)) {
        abort(404);
    }

    $contentType = $type == 'pdf' ? 'application/pdf' : 'image/jpeg';

    // Set appropriate headers
    $headers = [
        'Content-Type' => $contentType,
        'Cache-Control' => 'public, max-age=86400',
    ];

    if ($type == 'pdf') {
        $headers['Content-Disposition'] = 'inline; filename="' . $filename . '"';
        $headers['Accept-Ranges'] = 'bytes';
        $headers['X-Content-Type-Options'] = 'nosniff';
    }

    return response()->file($path, $headers);
})->middleware('auth')->name('books.direct.file');

// Direct file access route - Simple fallback method for old format
Route::get('/direct-books/{type}/{filename}', function ($type, $filename) {
    // Validate the type to prevent directory traversal
    if (!in_array($type, ['pdf', 'covers'])) {
        abort(404);
    }

    // First check the proper storage path
    $path = storage_path("app/public/books/{$type}/{$filename}");

    if (!file_exists($path)) {
        // Try public path as fallback
        $path = public_path("storage/books/{$type}/{$filename}");

        if (!file_exists($path)) {
            abort(404);
        }
    }

    // Determine content type and filename for headers
    if ($type == 'pdf') {
        $contentType = 'application/pdf';
        // Extract book name from filename for better display
        $displayName = preg_replace('/^\d+-/', '', $filename);
        $displayName = str_replace('.pdf', '', $displayName);
        $displayName = str_replace('-', ' ', $displayName);

        // Set headers for better PDF handling
        $headers = [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . $displayName . '.pdf"',
            'Cache-Control' => 'public, max-age=86400',
            'Accept-Ranges' => 'bytes',
            'X-Content-Type-Options' => 'nosniff',
        ];
    } else {
        $contentType = 'image/jpeg';
        $headers = [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=86400',
        ];
    }

    return response()->file($path, $headers);
});

// Student message read route
Route::post('student/messages/mark-read', [App\Http\Controllers\Student\MessageReadController::class, 'markRead'])
    ->name('student.messages.mark-read');

// Instructor message read route
Route::post('instructor/messages/mark-read', [App\Http\Controllers\Instructor\MessageReadController::class, 'markRead'])
    ->name('instructor.messages.mark-read');
