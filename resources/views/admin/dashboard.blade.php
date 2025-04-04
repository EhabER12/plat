@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Admin Dashboard</h1>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                                <div>Total Users</div>
                            </div>
                            <div>
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{ url('/admin/users') }}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['total_courses'] }}</h3>
                                <div>Total Courses</div>
                            </div>
                            <div>
                                <i class="fas fa-book fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{ url('/admin/courses') }}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['pending_instructor_verifications'] }}</h3>
                                <div>Instructor Verifications</div>
                            </div>
                            <div>
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{ route('admin.instructor.verifications') }}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card bg-danger text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['pending_approvals'] }}</h3>
                                <div>Course Approvals</div>
                            </div>
                            <div>
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{ route('admin.course.approvals') }}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-1"></i>
                        User Roles Distribution
                    </div>
                    <div class="card-body">
                        <canvas id="userRolesChart" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-1"></i>
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
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-users me-1"></i>
                        Latest Users
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
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
                                            <td>{{ $user->created_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ url('/admin/users') }}" class="btn btn-primary btn-sm">View All Users</a>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-book me-1"></i>
                        Latest Courses
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
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
                    <div class="card-footer">
                        <a href="{{ url('/admin/courses') }}" class="btn btn-primary btn-sm">View All Courses</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Actions -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">إجراءات سريعة</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.reset-database') }}" class="btn btn-success">
                                <i class="fas fa-database me-2"></i> إضافة بيانات وهمية للكورسات
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.create-demo-data') }}" class="btn btn-info">
                                <i class="fas fa-plus-circle me-2"></i> إضافة المزيد من الكورسات
                            </a>
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
        type: 'pie',
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
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e'
                ],
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
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
                backgroundColor: '#4e73df',
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endsection 