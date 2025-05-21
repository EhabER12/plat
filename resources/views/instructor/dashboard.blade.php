@extends('layouts.instructor')

@section('title', 'Instructor Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<!-- استيراد خط تجوال العربي -->
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    /* تطبيق الخط على الصفحة بالكامل */
    body, button, input, select, textarea {
        font-family: 'Tajawal', 'Helvetica Neue', Arial, sans-serif !important;
    }

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

    /* تحسين تصميم قسم الرسم البياني للإيرادات */
    .chart-card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
    }

    .chart-card .card-header {
        background-color: #ffffff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem;
    }

    .chart-card .card-body {
        padding: 1.5rem;
        position: relative;
        height: 350px;
        background-color: #fbfbfb;
    }

    .chart-card canvas {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .chart-card h5 {
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
    }

    .chart-card h5 i {
        color: #20b7b7;
    }

    @media (max-width: 768px) {
        .chart-card .card-body {
            height: 280px;
        }
    }

    /* تحسينات تحليل أداء الطلاب */
    .student-analytics-item {
        transition: all 0.3s ease;
        border-radius: 8px;
        border: 1px solid #f0f0f0;
        margin-bottom: 4px;
    }
    
    .student-analytics-item:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .circular-chart {
        width: 100%;
        height: 100%;
    }
    
    .percentage {
        fill: #555;
        font-family: sans-serif;
        font-size: 5px;
        text-anchor: middle;
        font-weight: bold;
    }
    
    .circle-bg {
        fill: none;
        stroke-width: 2.8;
    }
    
    .circle {
        fill: none;
        stroke-width: 2.8;
        stroke-linecap: round;
    }
    
    .performance-label {
        color: #6c757d;
    }
    
    .border-success {
        border-color: rgba(40, 167, 69, 0.3) !important;
    }
    
    .border-info {
        border-color: rgba(23, 162, 184, 0.3) !important;
    }
    
    .bg-success-subtle {
        background-color: rgba(40, 167, 69, 0.1);
    }
    
    .bg-info-subtle {
        background-color: rgba(23, 162, 184, 0.1);
    }
    
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Welcome Message -->
    <div class="welcome-area fade-in">
        <div class="welcome-shape"></div>
        <div class="welcome-shape-2"></div>
        <h3>مرحباً، {{ Auth::user()->name }}!</h3>
        <p>هذه هي لوحة تحكم المدرب الخاصة بك. يمكنك إدارة دوراتك ومتابعة إحصائياتك من هنا.</p>
        <a href="{{ route('instructor.courses.create') }}" class="btn">
            <i class="fas fa-plus-circle me-2"></i>إنشاء دورة جديدة
        </a>
    </div>

    <!-- Course Stats Section -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Courses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCourses }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Published Courses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $publishedCourses }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Courses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingCourses }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Books Stats Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h4 mb-3">Books Statistics</h2>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Books</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBooks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Published Books</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $publishedBooks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Draft Books</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $draftBooks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Books Section -->
    @if(count($recentBooks) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Books</h6>
                    <a href="{{ route('instructor.books.index') }}" class="btn btn-sm btn-primary">View All Books</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Cover</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBooks as $book)
                                <tr>
                                    <td>
                                        <img src="{{ $book->getCoverImageUrlAttribute() }}" alt="{{ $book->title }}" width="40" height="60" class="img-thumbnail">
                                    </td>
                                    <td>{{ $book->title }}</td>
                                    <td>${{ number_format($book->price, 2) }}</td>
                                    <td>
                                        @if($book->is_published)
                                        <span class="badge badge-success">Published</span>
                                        @else
                                        <span class="badge badge-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('instructor.books.edit', $book) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
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
    @endif

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-xl-8">
            <!-- Revenue Chart -->
            <div class="card chart-card mb-4 fade-in" style="animation-delay: 0.5s">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> الإيرادات الشهرية</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>

            <!-- Latest Courses -->
            <div class="card table-card mb-4 fade-in" style="animation-delay: 0.6s">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-book-open me-2"></i> دوراتك الأخيرة</h5>
                    <a href="{{ route('instructor.courses') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    @if(count($courses) > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>العنوان</th>
                                        <th>السعر</th>
                                        <th>الطلاب</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses->take(5) as $course)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded p-2 me-3">
                                                        <i class="fas fa-book text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $course->title }}</h6>
                                                        <small>{{ \Carbon\Carbon::parse($course->created_at)->format('d M Y') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>${{ $course->price }}</td>
                                            <td>{{ $course->students_count }}</td>
                                            <td>
                                                @if($course->status === 'published')
                                                    <span class="approval-badge approved">معتمدة</span>
                                                @elseif($course->status === 'pending')
                                                    <span class="approval-badge pending">قيد المراجعة</span>
                                                @else
                                                    <span class="approval-badge rejected">مرفوضة</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        الإجراءات
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="{{ route('instructor.courses.manage', $course->course_id) }}">إدارة</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('instructor.courses.edit', $course->course_id) }}">تعديل</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('course.detail', $course->course_id) }}" target="_blank">معاينة</a></li>
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
                            <h5>ليس لديك أي دورات حتى الآن</h5>
                            <p class="text-muted">ابدأ مشاركة معرفتك من خلال إنشاء دورتك الأولى.</p>
                            <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus-circle me-2"></i> إنشاء دورة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-xl-4">
            <!-- Recent Enrollments -->
            <div class="card mb-4 fade-in" style="animation-delay: 0.7s">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i> آخر التسجيلات</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($recentEnrollments) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentEnrollments as $enrollment)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">تسجيل جديد</h6>
                                            <p class="mb-0 small">
                                                <span class="fw-semibold">{{ $enrollment->student->name }}</span> سجل في
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
                            <p class="mb-0">لا يوجد تسجيلات حتى الآن.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Top Performing Students -->
            <div class="card mb-4 fade-in" style="animation-delay: 0.75s">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i> الطلاب المتميزون</h5>
                    <a href="{{ route('instructor.top-students') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-chart-line me-1"></i> تحليل تفصيلي
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($topPerformingStudents) && count($topPerformingStudents) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topPerformingStudents as $student)
                                <div class="list-group-item py-3 student-analytics-item">
                                    <!-- Student Overview Header -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            @if($student->profile_image)
                                                <img src="{{ $student->profile_image }}" alt="{{ $student->name }}" class="rounded-circle me-3" style="width: 48px; height: 48px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary bg-gradient text-white d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $student->name }}</h6>
                                                <div class="text-muted small">
                                                    <i class="fas fa-book me-1" title="دورات مسجلة"></i> {{ $student->courses_enrolled }}
                                                    <span class="mx-1">•</span>
                                                    <i class="fas fa-clipboard-check me-1" title="امتحانات تم أخذها"></i> {{ $student->exams_taken }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="performance-score">
                                                <div class="performance-label small mb-1">مستوى الأداء</div>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 80px; height: 8px; background-color: rgba(0,0,0,0.05);">
                                                        <div class="progress-bar {{ $student->performance_score >= 80 ? 'bg-success' : ($student->performance_score >= 65 ? 'bg-info' : ($student->performance_score >= 50 ? 'bg-warning' : 'bg-danger')) }}" 
                                                             role="progressbar" style="width: {{ $student->performance_score }}%"></div>
                                                    </div>
                                                    <div class="fw-bold {{ $student->performance_score >= 80 ? 'text-success' : ($student->performance_score >= 65 ? 'text-info' : ($student->performance_score >= 50 ? 'text-warning' : 'text-danger')) }}">
                                                        {{ number_format($student->performance_score, 0) }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Performance Metrics -->
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-4">
                                            <div class="card border-0 bg-light h-100">
                                                <div class="card-body p-2 text-center">
                                                    <div class="small text-muted mb-1">النجاح في الامتحانات</div>
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <div class="circular-progress me-2" style="position: relative; width: 50px; height: 50px;">
                                                            <svg viewBox="0 0 36 36" class="circular-chart">
                                                                <path class="circle-bg" d="M18 2.0845
                                                                    a 15.9155 15.9155 0 0 1 0 31.831
                                                                    a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#eee" stroke-width="3" />
                                                                <path class="circle" d="M18 2.0845
                                                                    a 15.9155 15.9155 0 0 1 0 31.831
                                                                    a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" 
                                                                    stroke="{{ $student->exams_taken > 0 ? ($student->exams_passed / $student->exams_taken >= 0.7 ? '#28a745' : ($student->exams_passed / $student->exams_taken >= 0.5 ? '#17a2b8' : '#ffc107')) : '#6c757d' }}" 
                                                                    stroke-width="3" 
                                                                    stroke-dasharray="{{ $student->exams_taken > 0 ? ($student->exams_passed / $student->exams_taken) * 100 : 0 }}, 100" />
                                                                <text x="18" y="20.5" class="percentage">{{ $student->exams_taken > 0 ? number_format(($student->exams_passed / $student->exams_taken) * 100, 0) : 0 }}%</text>
                                                            </svg>
                                                        </div>
                                                        <div class="stats-text text-start">
                                                            <div class="h5 mb-0">{{ $student->exams_passed }}/{{ $student->exams_taken }}</div>
                                                            <span class="small">{{ $student->exams_taken > 0 ? 'تم اجتيازها' : 'لا امتحانات' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-0 bg-light h-100">
                                                <div class="card-body p-2 text-center">
                                                    <div class="small text-muted mb-1">متوسط الدرجات</div>
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <div class="score-badge me-2 rounded-circle d-flex align-items-center justify-content-center" 
                                                             style="width: 50px; height: 50px; background-color: {{ $student->avg_score >= 80 ? '#28a745' : ($student->avg_score >= 70 ? '#17a2b8' : ($student->avg_score >= 60 ? '#ffc107' : '#dc3545')) }}; color: white; font-weight: bold;">
                                                            {{ number_format($student->avg_score, 0) }}
                                                        </div>
                                                        <div class="stats-text text-start">
                                                            <div class="small">تقدير</div>
                                                            <div class="fw-bold">
                                                                @if($student->avg_score >= 90)
                                                                    ممتاز
                                                                @elseif($student->avg_score >= 80)
                                                                    جيد جداً
                                                                @elseif($student->avg_score >= 70)
                                                                    جيد
                                                                @elseif($student->avg_score >= 60)
                                                                    مقبول
                                                                @else
                                                                    ضعيف
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-0 bg-light h-100">
                                                <div class="card-body p-2 text-center">
                                                    <div class="small text-muted mb-1">النشاط الأخير</div>
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <i class="fas {{ $student->courses_enrolled > 0 && isset($student->course_performance) && $student->course_performance->where('progress', '>', 10)->count() > 0 ? 'fa-user-check text-success' : 'fa-user-clock text-warning' }} me-2" style="font-size: 1.5rem;"></i>
                                                        <div class="stats-text text-start">
                                                            <div class="small">
                                                                @if($student->courses_enrolled > 0 && isset($student->course_performance) && $student->course_performance->where('progress', '>', 10)->count() > 0)
                                                                    نشط
                                                                @else
                                                                    غير نشط
                                                                @endif
                                                            </div>
                                                            <div class="fw-bold small">
                                                                @if($student->courses_enrolled > 0 && isset($student->course_performance) && $student->course_performance->count() > 0)
                                                                    {{ $student->course_performance->where('progress', '>', 50)->count() }} دورات قيد التقدم
                                                                @else
                                                                    لم يبدأ بعد
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Student Detailed View Button -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-light text-dark border me-1">
                                                <i class="fas fa-clock me-1"></i>آخر نشاط: {{ isset($student->course_performance) && $student->course_performance->count() > 0 ? '3 أيام' : '-' }}
                                            </span>
                                            @if($student->exams_passed > 0)
                                                <span class="badge bg-success-subtle text-success border-success me-1">
                                                    <i class="fas fa-check-circle me-1"></i>{{ $student->exams_passed }} اختبار تم اجتيازه
                                                </span>
                                            @endif
                                            @if($student->avg_score > 0)
                                                <span class="badge bg-info-subtle text-info border-info">
                                                    <i class="fas fa-star me-1"></i>متوسط {{ number_format($student->avg_score, 1) }} 
                                                </span>
                                            @endif
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#studentDetails{{ $student->user_id }}" aria-expanded="false">
                                            <i class="fas fa-chart-bar me-1"></i>
                                            تفاصيل الأداء
                                        </button>
                                    </div>
                                    
                                    <!-- Collapsible Detailed Performance Section -->
                                    <div class="collapse mt-3" id="studentDetails{{ $student->user_id }}">
                                        <div class="card card-body shadow-sm border-0">
                                            <h6 class="fw-bold mb-3 border-bottom pb-2">تحليل أداء الطالب بالتفصيل</h6>
                                            
                                            <!-- Course Progress Section -->
                                            <div class="mb-3">
                                                <h6 class="fw-bold small text-uppercase text-muted mb-2">
                                                    <i class="fas fa-book-reader me-1"></i> التقدم في الدورات
                                                </h6>
                                                
                                                @if(isset($student->course_performance) && $student->course_performance->count() > 0)
                                            <div class="table-responsive">
                                                        <table class="table table-sm table-hover mb-0 border">
                                                            <thead class="table-light">
                                                        <tr>
                                                                    <th style="width: 40%;">اسم الدورة</th>
                                                                    <th style="width: 30%;">نسبة التقدم</th>
                                                                    <th style="width: 15%;">تاريخ التسجيل</th>
                                                                    <th style="width: 15%;">الحالة</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($student->course_performance as $course)
                                                            <tr>
                                                                        <td>
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="course-icon rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px;">
                                                                                    <i class="fas {{ $course['completed'] ? 'fa-check-circle text-success' : 'fa-play-circle text-primary' }} small"></i>
                                                                                </div>
                                                                                <span class="text-truncate" style="max-width: 200px;" title="{{ $course['course_title'] }}">
                                                                                    {{ $course['course_title'] }}
                                                                                </span>
                                                                    </div>
                                                                </td>
                                                                        <td>
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                                    <div class="progress-bar bg-{{ $course['progress'] >= 80 ? 'success' : ($course['progress'] >= 50 ? 'info' : 'warning') }}" 
                                                                                         role="progressbar" style="width: {{ $course['progress'] }}%"></div>
                                                                                </div>
                                                                                <span class="small">{{ $course['progress'] }}%</span>
                                                                            </div>
                                                                        </td>
                                                                        <td class="small">{{ isset($course['enrolled_at']) ? \Carbon\Carbon::parse($course['enrolled_at'])->format('Y/m/d') : '-' }}</td>
                                                                <td>
                                                                    @if($course['completed'])
                                                                        <span class="badge bg-success">مكتمل</span>
                                                                            @elseif($course['progress'] > 75)
                                                                                <span class="badge bg-info">متقدم</span>
                                                                            @elseif($course['progress'] > 25)
                                                                                <span class="badge bg-primary">قيد التقدم</span>
                                                                    @else
                                                                                <span class="badge bg-secondary">بدأ حديثاً</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                    </div>
                                                @else
                                                    <div class="alert alert-light border text-center py-2">
                                                        <i class="fas fa-info-circle me-1"></i> لم يبدأ الطالب أي من الدورات بعد
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Recommendations Section -->
                                            <div>
                                                <h6 class="fw-bold small text-uppercase text-muted mb-2">
                                                    <i class="fas fa-lightbulb me-1"></i> توصيات لتحسين الأداء
                                                </h6>
                                                
                                                <div class="alert alert-light border py-2">
                                                    @if($student->performance_score >= 80)
                                                        <i class="fas fa-check-circle text-success me-1"></i> 
                                                        أداء الطالب ممتاز، يمكن تشجيعه على مساعدة زملائه والمشاركة في محتوى متقدم.
                                                    @elseif($student->performance_score >= 60)
                                                        <i class="fas fa-info-circle text-info me-1"></i> 
                                                        أداء الطالب جيد، يحتاج إلى المزيد من التدريب على {{ $student->avg_score < 75 ? 'الاختبارات' : 'المشاركة في المزيد من الدورات' }}.
                                                    @else
                                                        <i class="fas fa-exclamation-circle text-warning me-1"></i> 
                                                        يحتاج الطالب إلى اهتمام إضافي ومتابعة دورية. يُوصى بالتواصل المباشر لمعرفة الصعوبات التي يواجهها.
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="d-flex justify-content-end mt-3">
                                                <button class="btn btn-sm btn-outline-secondary me-2">
                                                    <i class="fas fa-envelope me-1"></i> مراسلة
                                                </button>
                                                <a href="{{ route('instructor.top-students') }}?student={{ $student->user_id }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-chart-line me-1"></i> تقرير مفصل
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-trophy fa-3x text-muted"></i>
                            </div>
                            <p class="mb-0">لا يوجد بيانات أداء للطلاب حتى الآن.</p>
                            <p class="small text-muted">ستظهر البيانات بعد اجتياز الطلاب للامتحانات.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Reviews -->
            <div class="card fade-in" style="animation-delay: 0.8s">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i> آخر التقييمات</h5>
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
                                <p class="mb-1 small">{{ $rating->comment ?? 'لا يوجد تعليق.' }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">عن: <strong>{{ $rating->course_title }}</strong></small>
                                    <small class="text-muted">بواسطة: {{ $rating->is_anonymous ? 'مجهول' : $rating->user_name }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-star fa-3x text-muted"></i>
                            </div>
                            <p class="mb-0">لا يوجد تقييمات حتى الآن.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');

        // تحديد الاتجاه للرسم البياني
        Chart.defaults.font.family = "'Tajawal', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
        Chart.defaults.font.size = 14;
        
        // Parse the JSON safely with fallbacks
        let chartLabels = [];
        let chartData = [];

        try {
            chartLabels = JSON.parse('{!! $chartLabels ?? "[]" !!}');
        } catch (e) {
            console.error('Error parsing chart labels');
            chartLabels = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'];
        }

        try {
            chartData = JSON.parse('{!! $chartData ?? "[]" !!}');
        } catch (e) {
            console.error('Error parsing chart data');
            chartData = [500, 800, 1200, 1600, 1800, 2000];
        }

        const monthlyRevenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'الإيرادات ($)',
                    data: chartData,
                    backgroundColor: 'rgba(32, 183, 183, 0.2)',
                    borderColor: '#20b7b7',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#20b7b7',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleAlign: 'center',
                        bodyAlign: 'center',
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `$${context.raw.toFixed(2)}`;
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            padding: 10,
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            padding: 10
                        }
                    }
                },
                elements: {
                    line: {
                        tension: 0.4
                    }
                }
            }
        });
    });
</script>
@endsection