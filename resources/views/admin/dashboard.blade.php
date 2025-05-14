@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Withdrawal Notifications -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-info mb-3">
                    <div class="card-header bg-info text-white d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-bell me-2"></i> إشعارات السحب الأخيرة</span>
                        <a href="{{ route('admin.instructor-earnings.withdrawals') }}" class="btn btn-sm btn-light">عرض كل السحوبات</a>
                    </div>
                    <div class="card-body p-2">
                        @php
                            $withdrawalNotifications = \App\Models\Withdrawal::orderBy('processed_at', 'desc')->whereIn('status', ['completed','rejected'])->with('instructor')->take(5)->get();
                        @endphp
                        @if($withdrawalNotifications->isEmpty())
                            <div class="text-muted">لا توجد إشعارات سحب حديثة.</div>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($withdrawalNotifications as $w)
                                    <li class="list-group-item d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas {{ $w->status == 'completed' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-2"></i>
                                            <strong>{{ $w->instructor->name }}</strong>
                                            <span class="mx-2">طلب سحب بقيمة</span>
                                            <strong>${{ number_format($w->amount,2) }}</strong>
                                            <span class="badge {{ $w->status == 'completed' ? 'badge-success' : 'badge-danger' }} ms-2">
                                                {{ $w->status == 'completed' ? 'تم التحويل' : 'مرفوض' }}
                                            </span>
                                        </div>
                                        <a href="{{ route('admin.instructor-earnings.show-withdrawal', $w->withdrawal_id) }}" class="btn btn-sm btn-outline-info">تفاصيل</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Notifications Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-warning mb-3">
                    <div class="card-header bg-warning text-white d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-bell me-2"></i> إشعارات النظام</span>
                        <div>
                            <a href="{{ route('admin.notifications.test-create') }}" class="btn btn-sm btn-danger me-2">
                                <i class="fas fa-plus me-1"></i> إنشاء إشعار تجريبي
                            </a>
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-light">
                                <i class="fas fa-external-link-alt me-1"></i> عرض كل الإشعارات
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div class="notification-stat text-center p-3 bg-light rounded">
                                    <div class="display-4 text-warning mb-2">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                    @php
                                        $totalNotifications = \App\Models\AdminNotification::count();
                                    @endphp
                                    <h4>{{ $totalNotifications }}</h4>
                                    <p class="text-muted mb-0">إجمالي الإشعارات</p>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div class="notification-stat text-center p-3 bg-light rounded">
                                    <div class="display-4 text-danger mb-2">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    @php
                                        $unreadNotifications = \App\Models\AdminNotification::where('is_read', false)->count();
                                    @endphp
                                    <h4>{{ $unreadNotifications }}</h4>
                                    <p class="text-muted mb-0">غير مقروءة</p>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div class="notification-stat text-center p-3 bg-light rounded">
                                    <div class="display-4 text-primary mb-2">
                                        <i class="fas fa-flag"></i>
                                    </div>
                                    @php
                                        $flaggedContent = \App\Models\AdminNotification::where('type', 'flagged_content')->count();
                                    @endphp
                                    <h4>{{ $flaggedContent }}</h4>
                                    <p class="text-muted mb-0">محتوى محظور</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex flex-column h-100 justify-content-center align-items-center">
                                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-warning btn-lg mb-3 w-100">
                                        <i class="fas fa-external-link-alt me-2"></i> فتح مركز الإشعارات
                                    </a>
                                    <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-envelope me-2"></i> الإشعارات الجديدة
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Latest 3 Notifications -->
                        @php
                            $latestNotifications = \App\Models\AdminNotification::with('user')
                                ->orderBy('created_at', 'desc')
                                ->take(3)
                                ->get();
                        @endphp
                        @if($latestNotifications->count() > 0)
                            <hr>
                            <h6 class="mb-3">أحدث الإشعارات</h6>
                            <div class="list-group">
                                @foreach($latestNotifications as $notification)
                                <a href="{{ route('admin.notifications.show', $notification->id) }}" class="list-group-item list-group-item-action {{ !$notification->is_read ? 'bg-light font-weight-bold' : '' }}">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            @if($notification->type == 'flagged_content')
                                                <span class="badge badge-warning me-2">محتوى محظور</span>
                                            @else
                                                <span class="badge badge-secondary me-2">{{ $notification->type }}</span>
                                            @endif
                                            {{ Str::limit($notification->content, 80) }}
                                        </div>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        @endif

                        <!-- Important Notifications -->
                        @if(isset($importantNotifications) && $importantNotifications->count() > 0)
                            <hr>
                            <h6 class="mb-3">
                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                إشعارات مهمة
                            </h6>
                            <div class="list-group">
                                @foreach($importantNotifications as $notification)
                                <a href="{{ route('admin.notifications.show', $notification->id) }}" class="list-group-item list-group-item-action bg-danger text-white">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-warning text-dark me-2">
                                                @if($notification->type == 'flagged_content')
                                                    محتوى محظور
                                                @elseif($notification->type == 'system_alert')
                                                    تنبيه نظام
                                                @else
                                                    {{ $notification->type }}
                                                @endif
                                            </span>
                                            {{ Str::limit($notification->content, 80) }}
                                        </div>
                                        <small class="text-white">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Welcome Area -->
        <div class="welcome-area fade-in">
            <div class="welcome-shape"></div>
            <div class="welcome-shape-2"></div>
            <h2>مرحباً بك، {{ Auth::user()->name }}!</h2>
            <p>مرحباً بك في لوحة تحكم المسؤول. يمكنك إدارة المستخدمين والدورات والتصنيفات وتتبع أداء المنصة من هنا.</p>
            <a href="{{ route('admin.settings') }}" class="btn">إعدادات المنصة</a>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4 fade-in" style="animation-delay: 0.1s">
                <div class="stats-card">
                    <div class="stats-icon stats-primary">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                    <div class="stats-number">{{ $stats['total_users'] }}</div>
                    <div class="stats-label">Total Users</div>
                    <a href="{{ url('/admin/users') }}" class="view-details">
                        View Details <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 fade-in" style="animation-delay: 0.2s">
                <div class="stats-card">
                    <div class="stats-icon stats-success">
                        <i class="fas fa-graduation-cap fa-2x text-white"></i>
                    </div>
                    <div class="stats-number">{{ $stats['total_courses'] }}</div>
                    <div class="stats-label">Total Courses</div>
                    <a href="{{ route('admin.courses') }}" class="view-details">
                        View Details <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 fade-in" style="animation-delay: 0.3s">
                <div class="stats-card">
                    <div class="stats-icon stats-warning">
                        <i class="fas fa-tags fa-2x text-white"></i>
                    </div>
                    <div class="stats-number">{{ $stats['total_categories'] }}</div>
                    <div class="stats-label">Categories</div>
                    <a href="{{ route('admin.categories') }}" class="view-details">
                        View Details <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 fade-in" style="animation-delay: 0.4s">
                <div class="stats-card">
                    <div class="stats-icon stats-danger">
                        <i class="fas fa-clock fa-2x text-white"></i>
                    </div>
                    <div class="stats-number">{{ $stats['pending_approvals'] }}</div>
                    <div class="stats-label">Course Approvals</div>
                    <a href="{{ route('admin.course.approvals') }}" class="view-details">
                        View Details <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Revenue Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4 fade-in" style="animation-delay: 0.5s">
                <div class="stats-card">
                    <div class="stats-icon" style="background-color: #3498db;">
                        <i class="fas fa-dollar-sign fa-2x text-white"></i>
                    </div>
                    <div class="stats-number">{{ number_format($stats['total_revenue'], 2) }}</div>
                    <div class="stats-label">Total Revenue</div>
                    <a href="{{ route('admin.reports') }}?type=revenue" class="view-details">
                        View Reports <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 fade-in" style="animation-delay: 0.6s">
                <div class="stats-card">
                    <div class="stats-icon" style="background-color: #9b59b6;">
                        <i class="fas fa-shopping-cart fa-2x text-white"></i>
                    </div>
                    <div class="stats-number">{{ $stats['payment_count'] }}</div>
                    <div class="stats-label">Total Transactions</div>
                    <a href="{{ route('admin.reports') }}?type=revenue" class="view-details">
                        View Transactions <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 fade-in" style="animation-delay: 0.7s">
                <div class="stats-card">
                    <div class="stats-icon" style="background-color: #2ecc71;">
                        <i class="fas fa-credit-card fa-2x text-white"></i>
                    </div>
                    <div class="stats-number">{{ isset($revenueByMethod[0]) ? number_format($revenueByMethod[0]->total_amount, 2) : '0.00' }}</div>
                    <div class="stats-label">{{ isset($revenueByMethod[0]) ? ucfirst($revenueByMethod[0]->payment_method) . ' Revenue' : 'No Payments' }}</div>
                    <a href="{{ route('admin.reports') }}?type=revenue" class="view-details">
                        Payment Methods <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 fade-in" style="animation-delay: 0.8s">
                <div class="stats-card">
                    <div class="stats-icon" style="background-color: #e74c3c;">
                        <i class="fas fa-chart-line fa-2x text-white"></i>
                    </div>
                    <div class="stats-number">
                        @php
                            $avgTransactionValue = $stats['payment_count'] > 0 
                                ? number_format($stats['total_revenue'] / $stats['payment_count'], 2) 
                                : '0.00';
                        @endphp
                        {{ $avgTransactionValue }}
                    </div>
                    <div class="stats-label">Avg. Transaction</div>
                    <a href="{{ route('admin.reports') }}?type=revenue" class="view-details">
                        View Details <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-xl-6 mb-4 fade-in" style="animation-delay: 0.5s">
                <div class="chart-card">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-2"></i>
                        User Roles Distribution
                    </div>
                    <div class="card-body">
                        <canvas id="userRolesChart" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 mb-4 fade-in" style="animation-delay: 0.6s">
                <div class="chart-card">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-2"></i>
                        Courses by Category
                    </div>
                    <div class="card-body">
                        <canvas id="coursesByCategoryChart" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tables Section -->
        <div class="row">
            <div class="col-xl-6 mb-4 fade-in" style="animation-delay: 0.7s">
                <div class="table-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-users me-2"></i>
                            Latest Users
                        </div>
                        <a href="{{ url('/admin/users') }}" class="btn btn-primary btn-sm btn-icon">
                            <i class="fas fa-eye"></i> View All
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latest_users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->is_admin ? 'danger' : ($user->is_instructor ? 'warning' : 'primary') }}">
                                                {{ $user->is_admin ? 'Admin' : ($user->is_instructor ? 'Instructor' : 'Student') }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-6 mb-4 fade-in" style="animation-delay: 0.8s">
                <div class="table-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-graduation-cap me-2"></i>
                            Latest Courses
                        </div>
                        <a href="{{ route('admin.courses') }}" class="btn btn-primary btn-sm btn-icon">
                            <i class="fas fa-eye"></i> View All
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Instructor</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latest_courses as $course)
                                    <tr>
                                        <td>{{ $course->id }}</td>
                                        <td>{{ $course->title }}</td>
                                        <td>{{ $course->instructor->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $course->is_published ? 'success' : ($course->is_approved ? 'warning' : 'secondary') }}">
                                                {{ $course->is_published ? 'Published' : ($course->is_approved ? 'Approved' : 'Pending') }}
                                            </span>
                                        </td>
                                        <td>{{ $course->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Transactions Table -->
        <div class="row">
            <div class="col-xl-12 mb-4 fade-in" style="animation-delay: 1.0s">
                <div class="table-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-money-bill-wave me-2"></i>
                            Recent Transactions
                        </div>
                        <a href="{{ route('admin.reports') }}?type=revenue" class="btn btn-primary btn-sm btn-icon">
                            <i class="fas fa-eye"></i> View All
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->transaction_id }}</td>
                                        <td>
                                            @php
                                                $user = DB::table('users')->where('user_id', $transaction->user_id)->first();
                                            @endphp
                                            {{ $user ? $user->name : 'User #' . $transaction->user_id }}
                                        </td>
                                        <td>{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst($transaction->payment_method) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td>{{ ucfirst($transaction->transaction_type) }}</td>
                                        <td>{{ isset($transaction->created_at) ? \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No transactions found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Action Cards -->
        <h5 class="mb-4 mt-2 fade-in" style="animation-delay: 0.9s">Quick Actions</h5>
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="quick-action-card">
                    <div class="quick-action-icon" style="background: var(--gradient-1)">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h5 class="quick-action-title">Add User</h5>
                    <p class="quick-action-text">Create a new user account</p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-icon">
                        <i class="fas fa-plus"></i> Create User
                    </a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="quick-action-card">
                    <div class="quick-action-icon" style="background: var(--gradient-2)">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h5 class="quick-action-title">Add Course</h5>
                    <p class="quick-action-text">Create a new course</p>
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-success btn-icon">
                        <i class="fas fa-plus"></i> Create Course
                    </a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="quick-action-card">
                    <div class="quick-action-icon" style="background: var(--gradient-3)">
                        <i class="fas fa-folder-plus"></i>
                    </div>
                    <h5 class="quick-action-title">Add Category</h5>
                    <p class="quick-action-text">Create a new category</p>
                    <a href="{{ route('admin.categories') }}?action=new" class="btn btn-warning btn-icon">
                        <i class="fas fa-plus"></i> Create Category
                    </a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="quick-action-card">
                    <div class="quick-action-icon" style="background: var(--gradient-4)">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h5 class="quick-action-title">System Settings</h5>
                    <p class="quick-action-text">Manage platform settings</p>
                    <a href="{{ route('admin.settings') }}" class="btn btn-dark btn-icon" style="background-color: var(--primary-color); color: var(--secondary-color);">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="quick-action-card">
                    <div class="quick-action-icon" style="background: var(--gradient-3)">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <h5 class="quick-action-title">Website Appearance</h5>
                    <p class="quick-action-text">Manage website content & design</p>
                    <a href="{{ route('admin.website-appearance') }}" class="btn btn-warning btn-icon">
                        <i class="fas fa-desktop"></i> Customize
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set up charts with the new color theme
        const userRolesChart = new Chart(
            document.getElementById('userRolesChart'),
            {
                type: 'pie',
                data: {
                    labels: ['Students', 'Instructors', 'Admins'],
                    datasets: [{
                        data: [
                            {{ $stats['students_count'] }}, 
                            {{ $stats['instructors_count'] }}, 
                            {{ $stats['admins_count'] }}
                        ],
                        backgroundColor: ['#003366', '#FFD700', '#E74C3C'],
                        borderColor: '#FFFFFF',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            }
        );
        
        const coursesByCategoryChart = new Chart(
            document.getElementById('coursesByCategoryChart'),
            {
                type: 'bar',
                data: {
                    labels: {!! json_encode($course_categories->pluck('name')) !!},
                    datasets: [{
                        label: 'Courses',
                        data: {!! json_encode($course_categories->pluck('courses_count')) !!},
                        backgroundColor: '#003366',
                        borderColor: '#003366',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            precision: 0
                        }
                    }
                }
            }
        );
    });
    </script>
@endsection