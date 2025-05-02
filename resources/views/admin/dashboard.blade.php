@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
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
                        <i class="fas fa-book fa-2x text-white"></i>
                    </div>
                    <div class="stats-number">{{ $stats['total_courses'] }}</div>
                    <div class="stats-label">Total Courses</div>
                    <a href="{{ url('/admin/courses') }}" class="view-details">
                        View Details <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 fade-in" style="animation-delay: 0.3s">
                <div class="stats-card">
                    <div class="stats-icon stats-warning">
                        <i class="fas fa-user-check fa-2x text-white"></i>
                    </div>
                    <div class="stats-number">{{ $stats['pending_instructor_verifications'] }}</div>
                    <div class="stats-label">Instructor Verifications</div>
                    <a href="{{ route('admin.instructor.verifications') }}" class="view-details">
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
                                    @foreach($latestUsers as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if(isset($user->userRoles) && count($user->userRoles) > 0)
                                                    @foreach($user->userRoles as $role)
                                                        <span class="badge bg-primary">{{ $role }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="badge bg-secondary">No role</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
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
                            <i class="fas fa-book me-2"></i>
                            Latest Courses
                        </div>
                        <a href="{{ url('/admin/courses') }}" class="btn btn-primary btn-sm btn-icon">
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
                                        <th>Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latestCourses as $course)
                                        <tr>
                                            <td>{{ $course->id }}</td>
                                            <td>{{ $course->title }}</td>
                                            <td>${{ number_format($course->price, 2) }}</td>
                                            <td>
                                                @if($course->status == 'published')
                                                    <span class="badge bg-success">Published</span>
                                                @elseif($course->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($course->status == 'draft')
                                                    <span class="badge bg-secondary">Draft</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4 fade-in" style="animation-delay: 0.9s">
            <div class="col-12">
                <h4 class="my-4">Quick Actions</h4>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="quick-action-card">
                    <div class="quick-action-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h5 class="quick-action-title">Add New User</h5>
                    <p class="quick-action-text">Create a new user account</p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-icon">
                        <i class="fas fa-plus"></i> Create User
                    </a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="quick-action-card">
                    <div class="quick-action-icon" style="background: var(--gradient-2)">
                        <i class="fas fa-book"></i>
                    </div>
                    <h5 class="quick-action-title">Add New Course</h5>
                    <p class="quick-action-text">Create a new course</p>
                    <a href="{{ route('admin.course.create') }}" class="btn btn-success btn-icon">
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
                    <a href="{{ route('admin.settings') }}" class="btn btn-dark btn-icon">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
            </div>
        </div>

        <!-- Admin Actions -->
        <div class="row fade-in" style="animation-delay: 1s">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header" style="background: var(--gradient-1); color: white;">
                        <h5 class="mb-0">إجراءات سريعة</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.reset-database') }}" class="btn btn-success btn-icon">
                                        <i class="fas fa-database me-2"></i> إضافة بيانات وهمية للكورسات
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.create-demo-data') }}" class="btn btn-primary btn-icon">
                                        <i class="fas fa-plus-circle me-2"></i> إضافة المزيد من الكورسات
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // User Roles Distribution Chart
    const userRolesCtx = document.getElementById('userRolesChart').getContext('2d');
    const userRolesChart = new Chart(userRolesCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($usersByRole as $role => $count)
                    '{{ ucfirst($role) }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($usersByRole as $count)
                        {{ $count }},
                    @endforeach
                ],
                backgroundColor: [
                    '#5D5FEF',
                    '#22B573',
                    '#FF6384',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '70%'
        }
    });

    // Courses by Category Chart
    const coursesByCategoryCtx = document.getElementById('coursesByCategoryChart').getContext('2d');
    const coursesByCategoryChart = new Chart(coursesByCategoryCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($coursesByCategory as $category => $count)
                    '{{ $category }}',
                @endforeach
            ],
            datasets: [{
                label: 'Number of Courses',
                data: [
                    @foreach($coursesByCategory as $count)
                        {{ $count }},
                    @endforeach
                ],
                backgroundColor: '#5D5FEF',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });
</script>
@endsection 