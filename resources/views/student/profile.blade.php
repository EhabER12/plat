@extends('layouts.app')

@section('title', 'صفحة الطالب')

@section('styles')
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
<!-- Student Dashboard CSS -->
<link href="{{ asset('css/student.css') }}" rel="stylesheet">
<style>
    /* Additional custom styles for student dashboard */
    .main-content {
        padding: 30px;
    }

    /* Education-themed icons for stats */
    .stat-icon {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 10px;
        opacity: 0.8;
    }

    /* Course card improvements */
    .course-card {
        position: relative;
        overflow: hidden;
    }

    .course-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 50px 50px 0;
        border-color: transparent rgba(26, 75, 132, 0.1) transparent transparent;
        transition: all 0.3s ease;
    }

    .course-card:hover::after {
        border-color: transparent rgba(26, 75, 132, 0.2) transparent transparent;
    }

    /* Improved search bar */
    .search-bar {
        background: white;
        border-radius: 30px;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .search-bar:focus-within {
        box-shadow: 0 5px 15px rgba(26, 75, 132, 0.1);
        border-color: rgba(26, 75, 132, 0.2);
    }

    .search-bar input {
        border: none;
        outline: none;
        background: transparent;
        padding: 5px 10px;
        width: 200px;
        font-family: var(--font-body);
    }

    .search-bar i {
        color: var(--primary-color);
    }

    /* Improved user avatar */
    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        border: 2px solid white;
    }

    /* Improved courses section */
    .courses-section {
        background: white;
        border-radius: var(--radius-md);
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .courses-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--gradient-primary);
    }

    .courses-section h3 {
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 20px;
        position: relative;
    }

    .courses-section .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 20px;
    }

    .courses-section .nav-link {
        color: #6c757d;
        border: none;
        padding: 12px 18px;
        font-weight: 500;
        margin-bottom: -1px;
        transition: all 0.3s ease;
    }

    .courses-section .nav-link.active {
        color: var(--primary-color);
        border-bottom: 3px solid var(--primary-color);
        background: transparent;
    }

    .courses-section .nav-link:hover:not(.active) {
        color: #495057;
        background-color: rgba(0, 0, 0, 0.02);
    }

    /* Improved activity items */
    .activity-item {
        position: relative;
    }

    .activity-time {
        font-size: 0.8rem;
        color: #6c757d;
    }

    /* Improved animations */
    .fade-in {
        animation: fadeIn 0.5s ease-in-out forwards;
        opacity: 0;
    }

    .fade-in:nth-child(2) {
        animation-delay: 0.1s;
    }

    .fade-in:nth-child(3) {
        animation-delay: 0.2s;
    }

    .fade-in:nth-child(4) {
        animation-delay: 0.3s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Improved exam cards */
    .exam-card {
        transition: all 0.3s ease;
        height: 100%;
    }

    .exam-card:hover {
        transform: translateY(-5px);
    }

    .exam-card .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    /* RTL improvements */
    [dir="rtl"] .course-card::after {
        right: auto;
        left: 0;
        border-width: 50px 50px 0 0;
        border-color: rgba(26, 75, 132, 0.1) transparent transparent transparent;
    }

    [dir="rtl"] .course-card:hover::after {
        border-color: rgba(26, 75, 132, 0.2) transparent transparent transparent;
    }

    [dir="rtl"] .courses-section::before {
        right: 0;
    }

    /* Mobile improvements */
    @media (max-width: 767px) {
        .main-content {
            padding: 15px;
        }

        .welcome-area .btn {
            margin-bottom: 10px;
        }

        .search-bar {
            margin-bottom: 15px;
        }

        .user-avatar {
            margin-left: auto;
        }

        .courses-section {
            padding: 20px;
        }

        .courses-section .nav-link {
            padding: 10px 15px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-auto p-0">
            @include('layouts.partials.student-sidebar')
        </div>

        <!-- Main Content -->
        <div class="col">
            <div class="main-content">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <!-- Welcome Area -->
                        <div class="welcome-area fade-in">
                            <div class="welcome-shape"></div>
                            <div class="welcome-shape-2"></div>
                            <h3>مرحباً، {{ Auth::user()->name }}! <i class="fas fa-hand-sparkles ms-2"></i></h3>
                            <p>هذه الصفحة الشخصية الخاصة بك. يمكنك الاطلاع على دوراتك ومتابعة تقدمك التعليمي وإدارة الاختبارات الخاصة بك.</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('courses.index') }}" class="btn">
                                    <i class="fas fa-search me-2"></i>استعراض الدورات
                                </a>
                                <a href="{{ route('student.exams.index') }}" class="btn btn-success">
                                    <i class="fas fa-file-alt me-2"></i>الاختبارات
                                </a>
                                <a href="{{ route('student.my-courses') }}" class="btn">
                                    <i class="fas fa-graduation-cap me-2"></i>دوراتي
                                </a>
                            </div>
                        </div>

                        <!-- Current Course -->
                        @if(isset($currentCourse) && $currentCourse)
                        <div class="current-course fade-in" style="animation-delay: 0.2s">
                            <div class="course-info">
                                <h5>{{ $currentCourse->title }}</h5>
                                <p><i class="fas fa-chalkboard-teacher me-1 text-primary"></i> المدرب: {{ $currentCourse->instructor->name ?? 'غير محدد' }}</p>
                                <div class="mt-2">
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-layer-group me-1"></i> {{ $currentCourse->category->name ?? 'غير مصنف' }}
                                    </span>
                                </div>
                            </div>
                            <div class="progress-circle" data-progress="{{ $currentCourse->progress }}%" style="background: conic-gradient(var(--primary-color) 0% {{ $currentCourse->progress }}%, #f3f3f3 {{ $currentCourse->progress }}% 100%);"></div>
                            <a href="{{ route('student.course-content', $currentCourse->course_id) }}" class="continue-btn">
                                <i class="fas fa-play-circle me-1"></i> متابعة التعلم
                            </a>
                        </div>
                        @else
                        <div class="alert alert-info fade-in" style="animation-delay: 0.2s">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading mb-1">ابدأ رحلة التعلم</h5>
                                    <p>لم تقم بالتسجيل في أي دورة حتى الآن. <a href="{{ route('courses.index') }}" class="alert-link">استعراض الدورات</a> للبدء في رحلة التعلم.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <!-- Search and User Menu -->
                        <div class="d-flex justify-content-between mb-4 fade-in" style="animation-delay: 0.3s">
                            <div class="search-bar">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="بحث...">
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar">
                                    @if(Auth::user()->profile_image)
                                        <img src="{{ asset(Auth::user()->profile_image) }}" alt="{{ Auth::user()->name }}">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" alt="{{ Auth::user()->name }}">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row fade-in" style="animation-delay: 0.4s">
                            <div class="col-6 mb-3">
                                <div class="stat-box">
                                    <div class="stat-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="stat-number">{{ $completedCoursesCount ?? 0 }}</div>
                                    <div class="stat-text">الدورات المكتملة</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="stat-box">
                                    <div class="stat-icon">
                                        <i class="fas fa-book-reader"></i>
                                    </div>
                                    <div class="stat-number">{{ $inProgressCoursesCount ?? 0 }}</div>
                                    <div class="stat-text">دورات قيد التقدم</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box">
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-number">{{ $totalWatchHours ?? 0 }}</div>
                                    <div class="stat-text">ساعات المشاهدة</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box">
                                    <div class="stat-icon">
                                        <i class="fas fa-certificate"></i>
                                    </div>
                                    <div class="stat-number">{{ $certificatesCount ?? 0 }}</div>
                                    <div class="stat-text">الشهادات</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <!-- Courses Section -->
                        <div class="courses-section fade-in" style="animation-delay: 0.5s">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3>دوراتي</h3>
                                <a href="{{ route('student.my-courses') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                            </div>

                            <ul class="nav nav-tabs mb-4" id="coursesTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">جميع الدورات</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="inprogress-tab" data-bs-toggle="tab" data-bs-target="#inprogress" type="button" role="tab" aria-controls="inprogress" aria-selected="false">قيد التقدم</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">المكتملة</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="coursesTabsContent">
                                <!-- All Courses Tab -->
                                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                                    @if(isset($enrolledCourses) && count($enrolledCourses) > 0)
                                        @foreach($enrolledCourses->take(3) as $course)
                                            <div class="course-card">
                                                <div class="d-flex align-items-center">
                                                    <div class="course-image">
                                                        @if($course->thumbnail)
                                                            <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}">
                                                        @else
                                                            <i class="fas fa-book fa-2x text-primary d-flex justify-content-center align-items-center h-100"></i>
                                                        @endif
                                                    </div>
                                                    <div class="course-info">
                                                        <h4>{{ $course->title }}</h4>
                                                        <p>المدرب: {{ $course->instructor->name ?? 'غير محدد' }}</p>
                                                    </div>
                                                </div>
                                                <div class="course-progress">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>التقدم</span>
                                                        <span>{{ $course->progress }}%</span>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ $course->progress }}%" aria-valuenow="{{ $course->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div class="course-footer">
                                                    <div class="course-meta">
                                                        <div class="course-meta-item">
                                                            <i class="fas fa-layer-group me-1"></i> {{ $course->category->name ?? 'غير مصنف' }}
                                                        </div>
                                                        <div class="course-meta-item">
                                                            <i class="fas fa-clock me-1"></i> {{ $course->duration ?? 'غير محدد' }}
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('student.course-content', $course->course_id) }}" class="view-course-btn">عرض الدورة</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-5">
                                            <div class="mb-3">
                                                <i class="fas fa-book-open fa-3x text-muted"></i>
                                            </div>
                                            <h5>لم تقم بالتسجيل في أي دورة حتى الآن</h5>
                                            <p class="text-muted">لم تكتشف بعد أي دورات. ابدأ من خلال استعراض مكتبة الدورات لدينا.</p>
                                            <a href="{{ route('courses.index') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-search me-2"></i> استعراض الدورات
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <!-- In Progress Tab -->
                                <div class="tab-pane fade" id="inprogress" role="tabpanel" aria-labelledby="inprogress-tab">
                                    @if(isset($inProgressCourses) && count($inProgressCourses) > 0)
                                        @foreach($inProgressCourses->take(3) as $course)
                                            <div class="course-card">
                                                <div class="d-flex align-items-center">
                                                    <div class="course-image">
                                                        @if($course->thumbnail)
                                                            <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}">
                                                        @else
                                                            <i class="fas fa-book fa-2x text-primary d-flex justify-content-center align-items-center h-100"></i>
                                                        @endif
                                                    </div>
                                                    <div class="course-info">
                                                        <h4>{{ $course->title }}</h4>
                                                        <p>المدرب: {{ $course->instructor->name ?? 'غير محدد' }}</p>
                                                    </div>
                                                </div>
                                                <div class="course-progress">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>التقدم</span>
                                                        <span>{{ $course->progress }}%</span>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ $course->progress }}%" aria-valuenow="{{ $course->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div class="course-footer">
                                                    <div class="course-meta">
                                                        <div class="course-meta-item">
                                                            <i class="fas fa-layer-group me-1"></i> {{ $course->category->name ?? 'غير مصنف' }}
                                                        </div>
                                                        <div class="course-meta-item">
                                                            <i class="fas fa-clock me-1"></i> {{ $course->duration ?? 'غير محدد' }}
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('student.course-content', $course->course_id) }}" class="view-course-btn">متابعة</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-hourglass-half fa-3x text-muted mb-3"></i>
                                            <p>ليس لديك دورات قيد التقدم حالياً.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Completed Tab -->
                                <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                                    @if(isset($completedCourses) && count($completedCourses) > 0)
                                        @foreach($completedCourses->take(3) as $course)
                                            <div class="course-card">
                                                <div class="d-flex align-items-center">
                                                    <div class="course-image">
                                                        @if($course->thumbnail)
                                                            <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}">
                                                        @else
                                                            <i class="fas fa-book fa-2x text-primary d-flex justify-content-center align-items-center h-100"></i>
                                                        @endif
                                                    </div>
                                                    <div class="course-info">
                                                        <h4>{{ $course->title }}</h4>
                                                        <p>المدرب: {{ $course->instructor->name ?? 'غير محدد' }}</p>
                                                    </div>
                                                </div>
                                                <div class="course-progress">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>التقدم</span>
                                                        <span>100%</span>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div class="course-footer">
                                                    <div class="course-meta">
                                                        <div class="course-meta-item">
                                                            <i class="fas fa-layer-group me-1"></i> {{ $course->category->name ?? 'غير مصنف' }}
                                                        </div>
                                                        <div class="course-meta-item">
                                                            <i class="fas fa-clock me-1"></i> {{ $course->duration ?? 'غير محدد' }}
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('student.course-content', $course->course_id) }}" class="view-course-btn">مراجعة</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                            <p>لم تكمل أي دورة بعد. استمر في التعلم!</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Recent Activity -->
                        <div class="card fade-in" style="animation-delay: 0.6s">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-history me-2"></i> النشاط الأخير</h5>
                            </div>
                            <div class="card-body">
                                @if(isset($recentActivity) && count($recentActivity) > 0)
                                    @foreach($recentActivity as $activity)
                                        <div class="activity-item">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="d-flex">
                                                    <div class="me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(26, 75, 132, 0.1); border-radius: 8px;">
                                                        <i class="fas fa-play-circle text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ $activity->title ?? 'نشاط تعليمي' }}</h6>
                                                        <p class="mb-0 small">{{ $activity->course->title ?? 'غير محدد' }}</p>
                                                    </div>
                                                </div>
                                                <div class="activity-time">
                                                    <span class="badge bg-light text-dark">{{ $activity->updated_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <div class="mb-3">
                                            <i class="fas fa-history fa-3x text-muted"></i>
                                        </div>
                                        <p class="mb-0">لا يوجد نشاط مؤخراً.</p>
                                        <p class="text-muted small mt-2">ابدأ بمشاهدة الدروس لتتبع نشاطك التعليمي</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Upcoming Exams -->
                        <div class="card mt-4 fade-in" style="animation-delay: 0.7s">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> الاختبارات القادمة</h5>
                            </div>
                            <div class="card-body">
                                @if(isset($upcomingExams) && count($upcomingExams) > 0)
                                    @foreach($upcomingExams as $exam)
                                        <div class="activity-item">
                                            <div class="d-flex">
                                                <div class="me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(23, 162, 184, 0.1); border-radius: 8px;">
                                                    <i class="fas fa-file-alt text-info"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $exam->title }}</h6>
                                                    <p class="mb-1 small">{{ $exam->course->title ?? 'غير محدد' }}</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <span class="badge bg-light text-dark">
                                                            <i class="fas fa-calendar-alt me-1"></i> {{ $exam->date ? $exam->date->format('d/m/Y') : 'غير محدد' }}
                                                        </span>
                                                        <a href="{{ route('student.exams.show', $exam->id) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i> عرض
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <div class="mb-3">
                                            <i class="fas fa-file-alt fa-3x text-muted"></i>
                                        </div>
                                        <p class="mb-0">لا توجد اختبارات قادمة.</p>
                                        <p class="text-muted small mt-2">سيتم عرض الاختبارات القادمة هنا عند جدولتها</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exams Section -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="courses-section fade-in" style="animation-delay: 0.6s">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3><i class="fas fa-clipboard-check me-2"></i>الامتحانات المتاحة</h3>
                                <div>
                                    <a href="{{ route('student.exams.index') }}" class="btn btn-sm btn-primary me-2">
                                        <i class="fas fa-file-alt me-1"></i> الامتحانات
                                    </a>
                                    <a href="{{ route('student.quizzes.index') }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-question-circle me-1"></i> الامتحانات القصيرة
                                    </a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card mb-3 exam-card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>الامتحانات</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <i class="fas fa-file-alt fa-3x text-primary opacity-75 mb-3"></i>
                                                <p>الامتحانات الرئيسية للدورات المسجل فيها</p>
                                            </div>
                                            <a href="{{ route('student.exams.index') }}" class="btn btn-primary">
                                                <i class="fas fa-external-link-alt me-1"></i>
                                                عرض الامتحانات
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card mb-3 exam-card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fas fa-question-circle me-2 text-info"></i>الامتحانات القصيرة</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <i class="fas fa-question-circle fa-3x text-info opacity-75 mb-3"></i>
                                                <p>الاختبارات القصيرة والتقييمات الدورية</p>
                                            </div>
                                            <a href="{{ route('student.quizzes.index') }}" class="btn btn-info">
                                                <i class="fas fa-external-link-alt me-1"></i>
                                                عرض الامتحانات القصيرة
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card mb-3 exam-card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>التقييمات والإنجازات</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <i class="fas fa-trophy fa-3x text-warning opacity-75 mb-3"></i>
                                                <p>تقييمات الأداء والإنجازات والشارات</p>
                                            </div>
                                            <a href="{{ route('student.motivation.index') }}" class="btn btn-warning">
                                                <i class="fas fa-external-link-alt me-1"></i>
                                                عرض التقييمات
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-3">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="fas fa-info-circle fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="alert-heading mb-1">معلومات عن الامتحانات والتقييمات</h5>
                                        <p class="mb-0">يمكنك الاطلاع على الامتحانات والاختبارات القصيرة المتاحة في الدورات التي قمت بالتسجيل فيها، ومتابعة تقييمات أدائك والإنجازات التي حققتها من خلال الروابط أعلاه.</p>
                                    </div>
                                </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap tabs
        var triggerTabList = [].slice.call(document.querySelectorAll('#coursesTabs button'))
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl)

            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
            })
        })
    });
</script>
@endsection
