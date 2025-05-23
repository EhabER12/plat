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
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Import statistics for dashboard
        $stats = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_categories' => Category::count(),
            'pending_approvals' => Course::where('approval_status', 'pending')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'payment_count' => Payment::where('status', 'completed')->count(),
            'students_count' => User::whereHas('roles', function($query) {
                $query->where('name', 'student');
            })->count(),
            'instructors_count' => User::whereHas('roles', function($query) {
                $query->where('name', 'instructor');
            })->count(),
            'admins_count' => User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })->count(),
        ];

        // Load recent transactions, users, courses
        $latest_users = User::latest()->take(5)->get();
        $latest_courses = Course::with('instructor')->latest()->take(5)->get();
        $recentTransactions = Payment::latest()->take(10)->get();

        // Get categorized revenue by payment method
        $revenueByMethod = Payment::where('status', 'completed')
            ->selectRaw('payment_method, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->orderByRaw('SUM(amount) DESC')
            ->get();

        // Get courses grouped by category for chart
        $course_categories = Category::withCount('courses')->take(8)->get();

        // Get latest notifications
        $importantNotifications = \App\Models\AdminNotification::where('severity', 'high')
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'latest_users',
            'latest_courses',
            'recentTransactions',
            'revenueByMethod',
            'course_categories',
            'importantNotifications'
        ));
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
                    ->join('users', 'payments.student_id', '=', 'users.id')
                    ->join('courses', 'payments.course_id', '=', 'courses.course_id')
                    ->select('payments.id as payment_id', 'payments.amount', 'payments.payment_method', 'payments.payment_date', 'payments.status',
                            'payments.student_id', 'payments.course_id', 'users.name as student_name', 'courses.title as course_title')
                    ->where('payments.status', 'completed')
                    ->orderBy('payments.payment_date', 'desc')
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
                    if (property_exists($payment, 'payment_date')) {
                        $payment->payment_date = $payment->payment_date;
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
        }
        elseif ($reportType === 'users') {
                $data = $this->getUsersReportData($timeframe);
                return view('admin.reports', compact('data', 'reportType', 'timeframe'));
        }
        else { // enrollment
                $data = $this->getEnrollmentReportData($timeframe);
                $recentEnrollments = DB::table('enrollments')
                    ->join('users', 'enrollments.user_id', '=', 'users.id')
                    ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
                    ->select('enrollments.*', 'users.name as student_name', 'courses.title as course_title')
                    ->orderBy('enrollments.enrolled_at', 'desc')
                    ->take(10)
                    ->get();

                // Transform the data for easy display
                foreach ($recentEnrollments as $enrollment) {
                    $enrollment->student = (object)[
                        'user_id' => $enrollment->user_id,
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
                                $enrollment->enrollment_date = \Carbon\Carbon::parse($enrollment->enrolled_at);
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
        $dateColumn = 'created_at';

        // Get total revenue
        $totalRevenue = DB::table('transactions')
            ->where('status', 'completed')
            ->where('transaction_type', 'payment')
            ->sum('amount');

        // Get payment method distribution
        $paymentMethods = DB::table('transactions')
            ->where('status', 'completed')
            ->where('transaction_type', 'payment')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('payment_method')
            ->get();

        // Format payment methods data for chart
        $methodLabels = [];
        $methodData = [];
        $methodColors = [
            'paymob' => '#3498db',
            'stripe' => '#9b59b6',
            'vodafone' => '#e74c3c',
            'cash' => '#2ecc71',
            'bank_transfer' => '#f39c12',
            'default' => '#95a5a6'
        ];

        $paymentMethodsColors = [];

        foreach ($paymentMethods as $method) {
            $methodLabels[] = ucfirst($method->payment_method);
            $methodData[] = $method->total_amount;
            $paymentMethodsColors[] = $methodColors[$method->payment_method] ?? $methodColors['default'];
        }

        // Get revenue by date
        $query = DB::table('transactions')
            ->where('status', 'completed')
            ->where('transaction_type', 'payment');

        $query = $this->applyTimeframeFilter($query, $dateColumn, $timeframe);

        $results = $query->select(
                DB::raw($this->getDateFormatSql($dateColumn, $timeframe) . ' as date'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get recent payments
        $recentPayments = DB::table('transactions')
            ->where('status', 'completed')
            ->where('transaction_type', 'payment')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Prepare data for the view
        $labels = [];
        $data = [];

        // Fill in missing dates with zero values
        $dateRange = $this->getDateRangeForTimeframe($timeframe);
        $dateFormat = $this->getDateFormatPHP($timeframe);

        $formattedResults = [];
        foreach ($results as $result) {
            $formattedResults[$result->date] = $result->total_amount;
        }

        foreach ($dateRange as $date) {
            $formattedDate = $date->format($dateFormat);
            $labels[] = $formattedDate;
            $data[] = isset($formattedResults[$formattedDate]) ? $formattedResults[$formattedDate] : 0;
            }

        return [
            'labels' => $labels,
            'data' => $data,
            'total_revenue' => $totalRevenue,
            'payment_methods' => $paymentMethods,
            'method_labels' => $methodLabels,
            'method_data' => $methodData,
            'method_colors' => $paymentMethodsColors,
            'recent_payments' => $recentPayments
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
     * Show the admin profile page.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    /**
     * Update the admin profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id . ',id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->hasFile('profile_image')) {
            // Store the new profile image
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }

        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Update the admin password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if the current password matches
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect']);
        }

        // Update the password
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully');
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
            'maintenance_mode' => 'nullable|boolean',
            'currency' => 'required|string|max:10',
            'instructor_commission_rate' => 'required|numeric|min:0|max:100',
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'string|in:credit_card,paypal,bank_transfer,paymob,vodafone_cash',
            'default_payment_method' => 'required|string|in:credit_card,paypal,bank_transfer,paymob,vodafone_cash',
            'mail_driver' => 'nullable|string',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|numeric',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Get the current user ID if authenticated
            $userId = $request->user() ? $request->user()->user_id : null;

            // Convert checkbox array to comma-separated string for payment methods
            $paymentMethods = $request->has('payment_methods') ? implode(',', $request->payment_methods) : 'credit_card';

            // Update settings in the database
            foreach ([
                'site_name', 'site_description', 'contact_email', 'currency',
                'instructor_commission_rate', 'mail_driver', 'mail_host',
                'mail_port', 'mail_username', 'mail_password', 'mail_encryption'
            ] as $key) {
                if ($request->has($key)) {
                    DB::table('settings')->updateOrInsert(
                        ['key' => $key],
                        [
                            'value' => $request->$key,
                            'updated_at' => now(),
                            'updated_by' => $userId
                        ]
                    );
                }
            }

            // Handle maintenance mode separately as it's a boolean
            DB::table('settings')->updateOrInsert(
                ['key' => 'maintenance_mode'],
                [
                    'value' => $request->has('maintenance_mode') ? '1' : '0',
                    'updated_at' => now(),
                    'updated_by' => $userId
                ]
            );

            // Update payment methods
            DB::table('settings')->updateOrInsert(
                ['key' => 'payment_methods'],
                [
                    'value' => $paymentMethods,
                    'description' => 'Comma-separated list of enabled payment methods',
                    'updated_at' => now(),
                    'updated_by' => $userId
                ]
            );

            // Update default payment method
            DB::table('settings')->updateOrInsert(
                ['key' => 'default_payment_method'],
                [
                    'value' => $request->default_payment_method,
                    'description' => 'Default payment method for the checkout page',
                    'updated_at' => now(),
                    'updated_by' => $userId
                ]
            );

            DB::commit();
            return redirect()->route('admin.settings')->with('success', 'Settings updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update settings: ' . $e->getMessage());
            return redirect()->route('admin.settings')->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Show instructor verifications list.
     *
     * @return \Illuminate\View\View
     */
    public function instructorVerifications()
    {
        $pendingVerifications = InstructorVerification::with('user')
            ->where('status', 'pending')
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        return view('admin.instructor_verifications', compact('pendingVerifications'));
    }

    /**
     * Show instructor verification details.
     *
     * @param  int  $verification_id
     * @return \Illuminate\View\View
     */
    public function showInstructorVerification($verification_id)
    {
        $verification = InstructorVerification::with('user')->findOrFail($verification_id);
        return view('admin.instructor_verification_detail', compact('verification'));
    }

    /**
     * Process instructor verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $verification_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processInstructorVerification(Request $request, $verification_id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $verification = InstructorVerification::findOrFail($verification_id);
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
        try {
            $category = Category::findOrFail($categoryId);
            $category->delete();
            return redirect()->route('admin.categories')->with('success', 'Category deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.categories')->with('error', 'Unable to delete category: ' . $e->getMessage());
        }
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

    /**
     * Get date format string for SQL based on timeframe.
     *
     * @param  string  $column
     * @param  string  $timeframe
     * @return string
     */
    private function getDateFormatSql($column, $timeframe)
    {
        switch ($timeframe) {
            case 'week':
                return "DATE($column)";
            case 'month':
                return "DATE_FORMAT($column, '%Y-%m-%d')";
            case 'year':
                return "DATE_FORMAT($column, '%Y-%m')";
            default:
                return "DATE($column)";
        }
    }

    /**
     * Get date format string for PHP based on timeframe.
     *
     * @param  string  $timeframe
     * @return string
     */
    private function getDateFormatPHP($timeframe)
    {
        switch ($timeframe) {
            case 'week':
                return 'Y-m-d';
            case 'month':
                return 'Y-m-d';
            case 'year':
                return 'Y-m';
            default:
                return 'Y-m-d';
        }
    }

    /**
     * Create demo data for testing.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createDemoData()
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
