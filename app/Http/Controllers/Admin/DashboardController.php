<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\InstructorPaymentAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\InstructorVerification;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Basic statistics
        $stats = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_categories' => Category::count(),
            'pending_approvals' => Course::where('approval_status', 'pending')->count(),
            'pending_instructor_verifications' => InstructorVerification::where('status', 'pending')->count(),
        ];

        // Get latest users with their roles
        $latestUsers = User::latest()->take(5)->get();
        foreach ($latestUsers as $user) {
            $user->userRoles = $user->getUserRoles();
        }

        // Get user distribution by role
        $usersByRole = DB::table('user_roles')
            ->select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        // Get courses by category
        $coursesByCategory = DB::table('courses')
            ->join('categories', 'courses.category_id', '=', 'categories.category_id')
            ->select('categories.name', DB::raw('count(*) as count'))
            ->groupBy('categories.name', 'categories.category_id')
            ->pluck('count', 'name')
            ->toArray();

        // Get latest courses
        $latestCourses = Course::with('instructor', 'category')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'latestUsers', 'usersByRole', 'coursesByCategory', 'latestCourses'));
    }

    /**
     * Show the users management page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function users(Request $request)
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    /**
     * Show the courses management page.
     *
     * @return \Illuminate\View\View
     */
    public function courses()
    {
        $courses = Course::with(['instructor', 'category'])->paginate(10);
        $categories = Category::all();
        return view('admin.courses', compact('courses', 'categories'));
    }

    /**
     * Show the course approval management page.
     *
     * @return \Illuminate\View\View
     */
    public function courseApprovals()
    {
        $pendingCourses = Course::where('approval_status', 'pending')
            ->with(['instructor', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.course_approvals', compact('pendingCourses'));
    }

    /**
     * Approve or reject a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processCourseApproval(Request $request, $courseId)
    {
        $validated = $request->validate([
            'status' => 'required|in:published,rejected',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $course = Course::findOrFail($courseId);

        // Set status using the accessor method
        $course->status = $validated['status'];

        if (!empty($validated['feedback'])) {
            $course->approval_feedback = $validated['feedback'];
        }

        $course->save();

        return redirect()->route('admin.course.approvals')
            ->with('success', 'Course ' . ($validated['status'] === 'published' ? 'approved' : 'rejected') . ' successfully');
    }

    /**
     * Show the user creation form.
     *
     * @return \Illuminate\View\View
     */
    public function createUser()
    {
        return view('admin.create_user');
    }

    /**
     * Store a newly created user in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,instructor,student,parent',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'dob' => 'nullable|date',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
        ];

        // Add optional fields if present
        if (isset($validated['phone'])) $userData['phone'] = $validated['phone'];
        if (isset($validated['address'])) $userData['address'] = $validated['address'];
        if (isset($validated['bio'])) $userData['bio'] = $validated['bio'];
        if (isset($validated['dob'])) $userData['dob'] = $validated['dob'];

        $user = User::create($userData);

        // Add role using the direct DB query since we're using a composite primary key
        DB::table('user_roles')->insert([
            'user_id' => $user->user_id,
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    /**
     * Show the edit user form.
     *
     * @param  int  $userId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->userRoles = $user->getUserRoles();
        return view('admin.edit_user', compact('user'));
    }

    /**
     * Update the specified user in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId . ',user_id',
            'role' => 'required|in:admin,instructor,student,parent',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'dob' => 'nullable|date',
        ];

        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Add optional fields if present
        if (isset($validated['phone'])) $userData['phone'] = $validated['phone'];
        if (isset($validated['address'])) $userData['address'] = $validated['address'];
        if (isset($validated['bio'])) $userData['bio'] = $validated['bio'];
        if (isset($validated['dob'])) $userData['dob'] = $validated['dob'];

        // Update password if provided
        if (isset($validated['password'])) {
            $userData['password_hash'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // Update user role
        DB::table('user_roles')->where('user_id', $userId)->delete();
        DB::table('user_roles')->insert([
            'user_id' => $userId,
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    /**
     * Delete a user from the database.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

    /**
     * Show the categories management page.
     *
     * @return \Illuminate\View\View
     */
    public function categories()
    {
        $categories = Category::withCount('courses')->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    /**
     * Show the reports page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function reports(Request $request)
    {
        $reportType = $request->type ?? 'enrollment';
        $timeframe = $request->timeframe ?? 'week';

        if ($reportType === 'revenue') {
                $data = $this->getRevenueReportData($timeframe);
                $recentPayments = DB::table('payments')
                    ->join('users', 'payments.student_id', '=', 'users.user_id')
                    ->join('courses', 'payments.course_id', '=', 'courses.course_id')
                    ->select('payments.*', 'users.name as student_name', 'courses.title as course_title')
                    ->where('payments.status', 'completed')
                    ->orderBy('payments.paid_at', 'desc')
                    ->take(10)
                    ->get();

                // Transform the data for easy display
                foreach ($recentPayments as $payment) {
                    $payment->student = (object)[
                        'user_id' => $payment->student_id,
                        'name' => $payment->student_name
                    ];
                    $payment->course = (object)[
                        'course_id' => $payment->course_id,
                        'title' => $payment->course_title
                    ];
                    // Format date for display
                    if (property_exists($payment, 'paid_at')) {
                        $payment->payment_date = $payment->paid_at;
                        // Try to convert to Carbon date object if it's a string
                        if (is_string($payment->payment_date)) {
                            try {
                                $payment->payment_date = \Carbon\Carbon::parse($payment->payment_date);
                            } catch (\Exception $e) {
                                // Keep as string if parsing fails
                            }
                        }
                    }
                }

                return view('admin.reports', compact('data', 'reportType', 'timeframe', 'recentPayments'));
        } elseif ($reportType === 'users') {
                $data = $this->getUsersReportData($timeframe);
                return view('admin.reports', compact('data', 'reportType', 'timeframe'));
        } else { // enrollment
                $data = $this->getEnrollmentReportData($timeframe);
                $recentEnrollments = DB::table('enrollments')
                    ->join('users', 'enrollments.student_id', '=', 'users.user_id')
                    ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
                    ->select('enrollments.*', 'users.name as student_name', 'courses.title as course_title')
                    ->orderBy('enrollments.enrolled_at', 'desc')
                    ->take(10)
                    ->get();

                // Transform the data for easy display
                foreach ($recentEnrollments as $enrollment) {
                    $enrollment->student = (object)[
                        'user_id' => $enrollment->student_id,
                        'name' => $enrollment->student_name
                    ];
                    $enrollment->course = (object)[
                        'course_id' => $enrollment->course_id,
                        'title' => $enrollment->course_title
                    ];
                    // Format date for display
                    if (property_exists($enrollment, 'enrolled_at')) {
                        $enrollment->enrollment_date = $enrollment->enrolled_at;
                        // Try to convert to Carbon date object if it's a string
                        if (is_string($enrollment->enrollment_date)) {
                            try {
                                $enrollment->enrollment_date = \Carbon\Carbon::parse($enrollment->enrollment_date);
                            } catch (\Exception $e) {
                                // Keep as string if parsing fails
                            }
                        }
                    }
                }

                return view('admin.reports', compact('data', 'reportType', 'timeframe', 'recentEnrollments'));
        }
    }

    /**
     * Get enrollment report data.
     *
     * @param  string  $timeframe
     * @return array
     */
    private function getEnrollmentReportData($timeframe)
    {
        $query = Enrollment::select(
                DB::raw('DATE(enrolled_at) as date'),
                DB::raw('count(*) as count')
            )
            ->groupBy('date');

        $query = $this->applyTimeframeFilter($query, 'enrolled_at', $timeframe);

        $data = $query->get()->pluck('count', 'date')->toArray();

        // Fill in missing dates with zero enrollments
        $range = $this->getDateRangeForTimeframe($timeframe);

        foreach ($range as $date) {
            $formattedDate = $date->format('Y-m-d');
            if (!isset($data[$formattedDate])) {
                $data[$formattedDate] = 0;
            }
        }

        ksort($data);

        return [
            'labels' => array_keys($data),
            'data' => array_values($data),
        ];
    }

    /**
     * Get revenue report data.
     *
     * @param  string  $timeframe
     * @return array
     */
    private function getRevenueReportData($timeframe)
    {
        $query = DB::table('payments')
            ->select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('SUM(amount) as revenue')
            )
            ->where('status', 'completed')
            ->groupBy('date');

        $query = $this->applyTimeframeFilter($query, 'paid_at', $timeframe);

        $data = $query->get()->pluck('revenue', 'date')->toArray();

        // Fill in missing dates with zero revenue
        $range = $this->getDateRangeForTimeframe($timeframe);

        foreach ($range as $date) {
            $formattedDate = $date->format('Y-m-d');
            if (!isset($data[$formattedDate])) {
                $data[$formattedDate] = 0;
            }
        }

        ksort($data);

        return [
            'labels' => array_keys($data),
            'data' => array_values($data),
        ];
    }

    /**
     * Get new users report data.
     *
     * @param  string  $timeframe
     * @return array
     */
    private function getUsersReportData($timeframe)
    {
        $query = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->groupBy('date');

        $query = $this->applyTimeframeFilter($query, 'created_at', $timeframe);

        $data = $query->get()->pluck('count', 'date')->toArray();

        // Fill in missing dates with zero new users
        $range = $this->getDateRangeForTimeframe($timeframe);

        foreach ($range as $date) {
            $formattedDate = $date->format('Y-m-d');
            if (!isset($data[$formattedDate])) {
                $data[$formattedDate] = 0;
            }
        }

        ksort($data);

        return [
            'labels' => array_keys($data),
            'data' => array_values($data),
        ];
    }

    /**
     * Apply timeframe filter to a query.
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $dateColumn
     * @param  string  $timeframe
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    private function applyTimeframeFilter($query, $dateColumn, $timeframe)
    {
        $now = now();

        switch ($timeframe) {
            case 'week':
                $query->where($dateColumn, '>=', $now->copy()->subWeek());
                break;
            case 'month':
                $query->where($dateColumn, '>=', $now->copy()->subMonth());
                break;
            case 'year':
                $query->where($dateColumn, '>=', $now->copy()->subYear());
                break;
        }

        return $query;
    }

    /**
     * Get date range for timeframe.
     *
     * @param  string  $timeframe
     * @return array
     */
    private function getDateRangeForTimeframe($timeframe)
    {
        $now = now();
        $start = null;

        switch ($timeframe) {
            case 'week':
                $start = $now->copy()->subWeek();
                break;
            case 'month':
                $start = $now->copy()->subMonth();
                break;
            case 'year':
                $start = $now->copy()->subYear();
                break;
        }

        $dates = [];
        for ($i = 0; $i <= $start->diffInDays($now); $i++) {
            $dates[] = $start->copy()->addDays($i);
        }

        return $dates;
    }

    /**
     * Show the settings page.
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        // Instead of using the database, we'll use environment variables as fallback
        $settings = collect([
            'site_name' => [
                'value' => env('APP_NAME', 'Laravel App'),
                'updated_at' => now(),
                'updated_by' => null
            ],
            'site_description' => [
                'value' => env('SITE_DESCRIPTION', 'Your Online Learning Platform'),
                'updated_at' => now(),
                'updated_by' => null
            ],
            'contact_email' => [
                'value' => env('MAIL_FROM_ADDRESS', 'contact@example.com'),
                'updated_at' => now(),
                'updated_by' => null
            ],
            'maintenance_mode' => [
                'value' => env('APP_MAINTENANCE_MODE', false),
                'updated_at' => now(),
                'updated_by' => null
            ],
            'currency' => [
                'value' => env('PAYMENT_CURRENCY', 'USD'),
                'updated_at' => now(),
                'updated_by' => null
            ],
            'instructor_commission_rate' => [
                'value' => env('INSTRUCTOR_COMMISSION_RATE', 70),
                'updated_at' => now(),
                'updated_by' => null
            ],
            'payment_methods' => [
                'value' => env('PAYMENT_METHODS', 'credit_card'),
                'updated_at' => now(),
                'updated_by' => null
            ],
            'mail_driver' => [
                'value' => env('MAIL_MAILER', 'smtp'),
                'updated_at' => now(),
                'updated_by' => null
            ],
            'mail_host' => [
                'value' => env('MAIL_HOST', 'smtp.mailtrap.io'),
                'updated_at' => now(),
                'updated_by' => null
            ],
            'mail_port' => [
                'value' => env('MAIL_PORT', 2525),
                'updated_at' => now(),
                'updated_by' => null
            ],
            'mail_username' => [
                'value' => env('MAIL_USERNAME', ''),
                'updated_at' => now(),
                'updated_by' => null
            ],
            'mail_password' => [
                'value' => env('MAIL_PASSWORD', ''),
                'updated_at' => now(),
                'updated_by' => null
            ],
            'mail_encryption' => [
                'value' => env('MAIL_ENCRYPTION', 'tls'),
                'updated_at' => now(),
                'updated_by' => null
            ],
        ]);

        return view('admin.settings', compact('settings'));
    }

    /**
     * Update system settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'instructor_commission_rate' => 'required|numeric|min:0|max:100',
            'minimum_withdrawal' => 'required|numeric|min:0',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
        ]);

        // Since we can't write to environment variables easily,
        // we'll just show a success message for demo purposes
        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully (demo mode - changes not saved to environment)');
    }

    /**
     * Show instructor verifications page.
     *
     * @return \Illuminate\View\View
     */
    public function instructorVerifications()
    {
        $pendingVerifications = InstructorVerification::with('user')
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        return view('admin.instructor_verifications', compact('pendingVerifications'));
    }

    /**
     * Show instructor verification details.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showInstructorVerification($id)
    {
        $verification = InstructorVerification::with('user')->findOrFail($id);
        return view('admin.instructor_verification_detail', compact('verification'));
    }

    /**
     * Process instructor verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processInstructorVerification(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $verification = InstructorVerification::findOrFail($id);
        $verification->status = $validated['status'];
        $verification->admin_notes = $validated['admin_notes'];
        $verification->reviewed_at = now();
        $verification->save();

        if ($validated['status'] === 'approved') {
            try {
                // Add instructor role to user
                DB::table('user_roles')->updateOrInsert(
                    ['user_id' => $verification->user_id, 'role' => 'instructor'],
                    []
                );

                // Create payment account for instructor if payment details are provided
                if (!empty($verification->payment_details)) {
                    $paymentDetails = $verification->payment_details;

                    // Check if required payment details are provided
                    if (isset($paymentDetails['email']) && isset($paymentDetails['phone'])) {
                        // Create payment account
                        $accountDetails = [
                            'email' => $paymentDetails['email'],
                            'phone' => $paymentDetails['phone'],
                        ];

                        // Add optional fields if provided
                        if (isset($paymentDetails['bank_name'])) {
                            $accountDetails['bank_name'] = $paymentDetails['bank_name'];
                        }

                        if (isset($paymentDetails['account_number'])) {
                            $accountDetails['account_number'] = $paymentDetails['account_number'];
                        }

                        // Create the payment account
                        InstructorPaymentAccount::create([
                            'instructor_id' => $verification->user_id,
                            'payment_provider' => 'paymob',
                            'account_name' => 'Paymob Account',
                            'account_details' => $accountDetails,
                            'is_active' => true,
                            'is_default' => true,
                        ]);

                        Log::info('Payment account created for instructor', [
                            'instructor_id' => $verification->user_id,
                            'verification_id' => $verification->id,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error creating payment account for instructor: ' . $e->getMessage(), [
                    'instructor_id' => $verification->user_id,
                    'verification_id' => $verification->id,
                ]);
            }
        }

        return redirect()->route('admin.instructor.verifications')
            ->with('success', 'Instructor verification ' . $validated['status'] . ' successfully');
    }

    /**
     * Store a new category in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,category_id',
        ]);

        $category = new Category();
        $category->name = $validated['name'];
        $category->slug = Str::slug($validated['name']);
        $category->description = $validated['description'] ?? null;
        $category->parent_id = $validated['parent_id'] ?? null;
        $category->save();

        return redirect()->route('admin.categories')->with('success', 'Category created successfully');
    }

    /**
     * Update the specified category in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $categoryId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCategory(Request $request, $categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId . ',category_id',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,category_id',
        ]);

        // Ensure category cannot become its own parent or descendant
        if (!empty($validated['parent_id']) && $validated['parent_id'] == $categoryId) {
            return redirect()->route('admin.categories')->with('error', 'A category cannot be its own parent.');
        }

        $category->name = $validated['name'];
        $category->slug = Str::slug($validated['name']);
        $category->description = $validated['description'] ?? null;
        $category->parent_id = $validated['parent_id'] ?? null;
        $category->save();

        return redirect()->route('admin.categories')->with('success', 'Category updated successfully');
    }

    /**
     * Delete the specified category from the database.
     *
     * @param  int  $categoryId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);

        // Check for subcategories
        $hasSubcategories = Category::where('parent_id', $categoryId)->exists();

        if ($hasSubcategories) {
            return redirect()->route('admin.categories')->with('error', 'Cannot delete category with subcategories. Please delete or reassign subcategories first.');
        }

        // Option 1: Prevent deletion if category has courses
        if ($category->courses()->count() > 0) {
            return redirect()->route('admin.categories')
                ->with('error', 'Cannot delete category that has courses. Please reassign courses first.');
        }

        /* Option 2: Reassign courses to parent category or NULL
        if ($category->courses()->count() > 0) {
            $newCategoryId = $category->parent_id; // Can be NULL
            $category->courses()->update(['category_id' => $newCategoryId]);
        }
        */

        $category->delete();

        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully');
    }

    /**
     * Show the form for creating a new course.
     *
     * @return \Illuminate\View\View
     */
    public function createCourse()
    {
        $categories = Category::all();
        $instructors = User::whereHas('roles', function($query) {
            $query->where('role', 'instructor');
        })->get();

        return view('admin.create_course', compact('categories', 'instructors'));
    }

    /**
     * Store a newly created course in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructor_id' => 'required|exists:users,user_id',
            'category_id' => 'required|exists:categories,category_id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'level' => 'required|in:beginner,intermediate,advanced',
            'language' => 'required|string|max:50',
            'featured' => 'boolean',
            'approval_status' => 'required|in:pending,approved,rejected',
        ]);

        $course = new Course();
        $course->title = $validated['title'];
        $course->description = $validated['description'];
        $course->instructor_id = $validated['instructor_id'];
        $course->category_id = $validated['category_id'];
        $course->price = $validated['price'];
        $course->duration = $validated['duration'] ?? null;
        $course->level = $validated['level'];
        $course->language = $validated['language'];
        $course->featured = $request->has('featured');
        $course->approval_status = $validated['approval_status'];
        $course->save();

        return redirect()->route('admin.courses')->with('success', 'Course created successfully');
    }

    /**
     * Show the form for editing a course.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View
     */
    public function editCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        $categories = Category::all();
        $instructors = User::whereHas('roles', function($query) {
            $query->where('role', 'instructor');
        })->get();

        return view('admin.edit_course', compact('course', 'categories', 'instructors'));
    }

    /**
     * Update the specified course in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCourse(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructor_id' => 'required|exists:users,user_id',
            'category_id' => 'required|exists:categories,category_id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'level' => 'required|in:beginner,intermediate,advanced',
            'language' => 'required|string|max:50',
            'featured' => 'boolean',
            'approval_status' => 'required|in:pending,approved,rejected',
        ]);

        $course->title = $validated['title'];
        $course->description = $validated['description'];
        $course->instructor_id = $validated['instructor_id'];
        $course->category_id = $validated['category_id'];
        $course->price = $validated['price'];
        $course->duration = $validated['duration'] ?? null;
        $course->level = $validated['level'];
        $course->language = $validated['language'];
        $course->featured = $request->has('featured');
        $course->approval_status = $validated['approval_status'];
        $course->save();

        return redirect()->route('admin.courses')->with('success', 'Course updated successfully');
    }

    /**
     * Delete the specified course from the database.
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteCourse($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Check if course has enrollments
        $hasEnrollments = Enrollment::where('course_id', $courseId)->exists();

        if ($hasEnrollments) {
            return redirect()->route('admin.courses')
                ->with('error', 'Cannot delete course with active enrollments.');
        }

        // Delete course videos and materials
        // This would need to be implemented based on your application's structure

        $course->delete();

        return redirect()->route('admin.courses')->with('success', 'Course deleted successfully');
    }

    /**
     * Display the specified course.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View
     */
    public function showCourse($courseId)
    {
        $course = Course::with(['instructor', 'category'])->findOrFail($courseId);
        $enrollmentsCount = Enrollment::where('course_id', $courseId)->count();

        // Get course content summary
        $videoCount = DB::table('course_videos')->where('course_id', $courseId)->count();
        $materialCount = DB::table('course_materials')->where('course_id', $courseId)->count();

        // Get student ratings
        $ratings = DB::table('course_reviews')
            ->where('course_id', $courseId)
            ->select(
                DB::raw('AVG(rating) as average_rating'),
                DB::raw('COUNT(*) as total_reviews')
            )
            ->first();

        return view('admin.show_course', compact('course', 'enrollmentsCount', 'videoCount', 'materialCount', 'ratings'));
    }

    /**
     * Reset the database and add demo data.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetDatabase()
    {
        try {
            // إنشاء مدرس جديد
            $instructorId = DB::table('users')->insertGetId([
                'name' => 'أحمد المدرس ' . time(),
                'email' => 'instructor' . time() . '@example.com',
                'password_hash' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // ربط المدرس بدور المدرس
            DB::table('user_roles')->insert([
                'user_id' => $instructorId,
                'role' => 'instructor',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // إنشاء تصنيفات
            $categories = [
                'البرمجة والتطوير',
                'تطوير المواقع',
                'تطوير التطبيقات',
                'الذكاء الاصطناعي',
                'قواعد البيانات'
            ];

            $categoryIds = [];
            foreach ($categories as $category) {
                $categoryIds[] = DB::table('categories')->insertGetId([
                    'name' => $category,
                    'description' => 'دورات في مجال ' . $category,
                    'slug' => Str::slug($category),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // إنشاء الكورسات
            $courses = [
                [
                    'title' => 'أساسيات البرمجة بلغة PHP',
                    'description' => 'تعلم أساسيات لغة PHP وكيفية استخدامها في تطوير تطبيقات الويب.',
                    'price' => 299.99,
                    'duration' => 24,
                    'level' => 'beginner',
                    'language' => 'العربية',
                    'featured' => 1,
                    'approval_status' => 'approved',
                ],
                [
                    'title' => 'تطوير تطبيقات الويب باستخدام Laravel',
                    'description' => 'دورة شاملة في تطوير تطبيقات الويب باستخدام إطار العمل Laravel.',
                    'price' => 499.99,
                    'duration' => 36,
                    'level' => 'intermediate',
                    'language' => 'العربية',
                    'featured' => 1,
                    'approval_status' => 'approved',
                ],
                [
                    'title' => 'تطوير واجهات المستخدم باستخدام React',
                    'description' => 'تعلم كيفية تطوير واجهات مستخدم تفاعلية وديناميكية باستخدام مكتبة React.js.',
                    'price' => 399.99,
                    'duration' => 30,
                    'level' => 'intermediate',
                    'language' => 'العربية',
                    'featured' => 1,
                    'approval_status' => 'approved',
                ],
                [
                    'title' => 'تطوير تطبيقات الهاتف باستخدام Flutter',
                    'description' => 'تعلم كيفية تطوير تطبيقات الهاتف المحمول لنظامي Android و iOS باستخدام إطار العمل Flutter.',
                    'price' => 449.99,
                    'duration' => 32,
                    'level' => 'intermediate',
                    'language' => 'العربية',
                    'featured' => 1,
                    'approval_status' => 'approved',
                ],
                [
                    'title' => 'مقدمة في علم البيانات والذكاء الاصطناعي',
                    'description' => 'استكشف عالم علم البيانات والذكاء الاصطناعي وتعلم المفاهيم الأساسية والتقنيات المستخدمة في هذا المجال المتنامي.',
                    'price' => 599.99,
                    'duration' => 40,
                    'level' => 'beginner',
                    'language' => 'العربية',
                    'featured' => 1,
                    'approval_status' => 'approved',
                ]
            ];

            // إضافة الكورسات
            foreach ($courses as $index => $course) {
                DB::table('courses')->insert([
                    'title' => $course['title'],
                    'description' => $course['description'],
                    'instructor_id' => $instructorId,
                    'category_id' => $categoryIds[$index % count($categoryIds)], // توزيع الفئات بشكل منتظم
                    'price' => $course['price'],
                    'duration' => $course['duration'],
                    'level' => $course['level'],
                    'language' => $course['language'],
                    'featured' => $course['featured'],
                    'approval_status' => $course['approval_status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()->route('admin.dashboard')->with('success', 'تم إعادة تهيئة قاعدة البيانات وإضافة البيانات الوهمية بنجاح!');
        } catch (\Exception $e) {
            Log::error('حدث خطأ أثناء إعادة تهيئة قاعدة البيانات: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->route('admin.dashboard')->with('error', 'حدث خطأ أثناء إعادة تهيئة قاعدة البيانات: ' . $e->getMessage());
        }
    }
}
