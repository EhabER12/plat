@extends('layouts.instructor')

@section('title', 'Instructor Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<style>
    .stat-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .stat-card .card-body {
        padding: 1.5rem;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #6c757d;
        font-size: 14px;
    }

    .course-card {
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .activity-item {
        border-radius: 8px;
        margin-bottom: 10px;
        padding: 15px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .activity-item:hover {
        background-color: #f1f3f5;
    }

    .dashboard-section {
        margin-bottom: 30px;
    }

    .dashboard-section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }

    .dashboard-section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background: #20b7b7;
    }

    .review-item {
        padding: 15px;
        border-radius: 8px;
        background-color: #f8f9fa;
        margin-bottom: 10px;
    }

    .star-rating {
        color: #ffc107;
    }

    .approval-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .approval-badge.approved {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .approval-badge.pending {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .approval-badge.rejected {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Welcome Message -->
    <div class="mb-4">
        <h3>Welcome, {{ Auth::user()->name }}!</h3>
        <p class="text-muted">Here's a summary of your courses and recent activity.</p>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-icon bg-primary-subtle text-primary">
                                <i class="fas fa-book"></i>
                            </div>
                            <h4 class="stat-value">{{ $totalCourses }}</h4>
                            <p class="stat-label mb-0">Total Courses</p>
                        </div>
                        <div class="align-self-end">
                            <div class="small mb-2">
                                <span class="text-success">{{ $approvedCourses }}</span> Approved
                            </div>
                            <div class="small">
                                <span class="text-warning">{{ $pendingCourses }}</span> Pending
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="stat-value">{{ $totalStudents }}</h4>
                    <p class="stat-label mb-0">Total Students</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h4 class="stat-value">${{ number_format($totalRevenue, 2) }}</h4>
                    <p class="stat-label mb-0">Total Revenue</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4 class="stat-value">
                        {{ isset($courses) && $courses->sum('ratings_count') > 0 ? number_format($courses->sum('ratings_count') / $courses->count(), 1) : '0.0' }}
                    </h4>
                    <p class="stat-label mb-0">Avg. Rating</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-xl-8 mb-4">
            <!-- Revenue Chart -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Revenue Overview</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active">6 Months</button>
                        <button type="button" class="btn btn-outline-secondary">YTD</button>
                        <button type="button" class="btn btn-outline-secondary">All Time</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>

            <!-- My Courses -->
            <div class="dashboard-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="dashboard-section-title">My Courses</h5>
                    <a href="{{ route('instructor.courses') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>

                @if(count($courses) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Course</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courses as $course)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-book text-primary"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $course->title }}</h6>
                                                    <span class="badge bg-secondary">{{ $course->videos_count }} videos</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $course->students_count }}</td>
                                        <td>
                                            @if($course->status === 'published')
                                                <span class="approval-badge approved">Published</span>
                                            @elseif($course->status === 'pending')
                                                <span class="approval-badge pending">Pending</span>
                                            @else
                                                <span class="approval-badge rejected">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('instructor.courses.manage', $course->id) }}">Manage</a></li>
                                                    <li><a class="dropdown-item" href="{{ route('instructor.courses.edit', $course->id) }}">Edit</a></li>
                                                    <li><a class="dropdown-item" href="{{ route('course.detail', $course->id) }}" target="_blank">Preview</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 bg-light rounded">
                        <div class="mb-3">
                            <i class="fas fa-book-open fa-3x text-muted"></i>
                        </div>
                        <h5>You haven't created any courses yet</h5>
                        <p class="text-muted">Start sharing your knowledge by creating your first course.</p>
                        <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus-circle me-2"></i> Create Course
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-xl-4">
            <!-- Recent Activity -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Enrollments</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($recentEnrollments) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentEnrollments as $enrollment)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">New enrollment</h6>
                                            <p class="mb-0 small">
                                                <span class="fw-semibold">{{ $enrollment->student->name }}</span> enrolled in
                                                <span class="fw-semibold">{{ $enrollment->course->title }}</span>
                                            </p>
                                        </div>
                                        <small class="text-muted">{{ $enrollment->enrolled_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-user-graduate fa-3x text-muted"></i>
                            </div>
                            <p class="mb-0">No enrollments yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Reviews -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Reviews</h5>
                </div>
                <div class="card-body">
                    @if(isset($recentRatings) && count($recentRatings) > 0)
                        @foreach($recentRatings as $rating)
                            <div class="review-item">
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $rating->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($rating->created_at)->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 small">{{ $rating->comment ?? 'No comment provided.' }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">For: <strong>{{ $rating->course_title }}</strong></small>
                                    <small class="text-muted">By: {{ $rating->is_anonymous ? 'Anonymous' : $rating->user_name }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-star fa-3x text-muted"></i>
                            </div>
                            <p class="mb-0">No reviews yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');

        // Parse the JSON safely with fallbacks
        let chartLabels = [];
        let chartData = [];

        try {
            chartLabels = JSON.parse('{!! $chartLabels ?? "[]" !!}');
        } catch (e) {
            console.error('Error parsing chart labels');
            chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        }

        try {
            chartData = JSON.parse('{!! $chartData ?? "[]" !!}');
        } catch (e) {
            console.error('Error parsing chart data');
            chartData = [500, 800, 1200, 1600, 1800, 2000];
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Revenue ($)',
                    data: chartData,
                    backgroundColor: 'rgba(32, 183, 183, 0.2)',
                    borderColor: '#20b7b7',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#20b7b7',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `$${context.raw.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection