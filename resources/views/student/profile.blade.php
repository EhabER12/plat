@extends('layouts.app')

@section('title', 'صفحة الطالب')

@section('styles')
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
<!-- Student Dashboard CSS -->
<link href="{{ asset('css/student.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-auto p-0">
            <div class="sidebar">
                <a href="{{ route('student.profile') }}" class="sidebar-icon mb-5" style="background-color: rgba(255, 255, 255, 0.2); width: 50px; height: 50px;">
                    <h2 class="mb-0 fw-bold" style="font-family: 'Tajawal', sans-serif;">مت</h2>
                    <div class="sidebar-tooltip">منصة تعليمية</div>
                </a>
                <a href="{{ route('student.profile') }}" class="sidebar-icon {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Dashboard' }}</div>
                </a>
                <a href="{{ route('student.my-courses') }}" class="sidebar-icon {{ request()->routeIs('student.my-courses') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i>
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'دوراتي' : 'My Courses' }}</div>
                </a>
                <a href="{{ route('student.profile.index') }}" class="sidebar-icon {{ request()->routeIs('student.profile.index') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الملف الشخصي' : 'Profile' }}</div>
                </a>
                <a href="{{ route('student.messages.index') }}" class="sidebar-icon {{ request()->routeIs('student.messages.index') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i>
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الرسائل' : 'Messages' }}</div>
                </a>
                <a href="{{ route('student.profile.edit') }}" class="sidebar-icon {{ request()->routeIs('student.profile.edit') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الإعدادات' : 'Settings' }}</div>
                </a>
                <a href="{{ route('student.exams.index') }}" class="sidebar-icon mt-auto mb-4 {{ request()->routeIs('student.exams.index') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الاختبارات' : 'Exams' }}</div>
                </a>
                <a href="{{ route('student.quizzes.index') }}" class="sidebar-icon mb-2 {{ request()->routeIs('student.quizzes.index') ? 'active' : '' }}">
                    <i class="fas fa-question-circle"></i>
                    <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الامتحانات القصيرة' : 'Quizzes' }}</div>
                </a>
            </div>
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
                            <h3>مرحباً، {{ Auth::user()->name }}!</h3>
                            <p>هذه الصفحة الشخصية الخاصة بك. يمكنك الاطلاع على دوراتك ومتابعة تقدمك.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('courses') }}" class="btn">
                                    <i class="fas fa-search me-2"></i>استعراض الدورات
                                </a>
                                <a href="{{ route('student.exams.index') }}" class="btn btn-success">
                                    <i class="fas fa-file-alt me-2"></i>الاختبارات
                                </a>
                            </div>
                        </div>

                        <!-- Current Course -->
                        @if(isset($currentCourse) && $currentCourse)
                        <div class="current-course fade-in" style="animation-delay: 0.2s">
                            <div class="course-info">
                                <h5>{{ $currentCourse->title }}</h5>
                                <p>المدرب: {{ $currentCourse->instructor->name ?? 'غير محدد' }}</p>
                            </div>
                            <div class="progress-circle" data-progress="{{ $currentCourse->progress }}%" style="background: conic-gradient(var(--accent-color) 0% {{ $currentCourse->progress }}%, #f3f3f3 {{ $currentCourse->progress }}% 100%);"></div>
                            <a href="{{ route('student.course-content', $currentCourse->course_id) }}" class="continue-btn">متابعة التعلم</a>
                        </div>
                        @else
                        <div class="alert alert-info fade-in" style="animation-delay: 0.2s">
                            <p>لم تقم بالتسجيل في أي دورة حتى الآن. <a href="{{ route('courses') }}">استعراض الدورات</a> للبدء في رحلة التعلم.</p>
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
                                    <div class="stat-number">{{ $completedCoursesCount ?? 0 }}</div>
                                    <div class="stat-text">الدورات المكتملة</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="stat-box">
                                    <div class="stat-number">{{ $inProgressCoursesCount ?? 0 }}</div>
                                    <div class="stat-text">دورات قيد التقدم</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box">
                                    <div class="stat-number">{{ $totalWatchHours ?? 0 }}</div>
                                    <div class="stat-text">ساعات المشاهدة</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box">
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
                                            <p class="text-muted">استعرض الدورات المتاحة وابدأ رحلة التعلم!</p>
                                            <a href="{{ route('courses') }}" class="btn btn-primary mt-2">
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
                                                <div>
                                                    <h6 class="mb-1">{{ $activity->title ?? 'نشاط تعليمي' }}</h6>
                                                    <p class="mb-0 small">{{ $activity->course->title ?? 'غير محدد' }}</p>
                                                </div>
                                                <small class="text-muted">{{ $activity->updated_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <div class="mb-3">
                                            <i class="fas fa-history fa-3x text-muted"></i>
                                        </div>
                                        <p class="mb-0">لا يوجد نشاط مؤخراً.</p>
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
                                            <h6 class="mb-1">{{ $exam->title }}</h6>
                                            <p class="mb-1 small">{{ $exam->course->title ?? 'غير محدد' }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i> {{ $exam->date ? $exam->date->format('d/m/Y') : 'غير محدد' }}</small>
                                                <a href="{{ route('student.exams.show', $exam->id) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <div class="mb-3">
                                            <i class="fas fa-file-alt fa-3x text-muted"></i>
                                        </div>
                                        <p class="mb-0">لا توجد اختبارات قادمة.</p>
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
                                <h3>الامتحانات المتاحة</h3>
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
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>الامتحانات</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <p>الامتحانات الرئيسية للدورات المسجل فيها</p>
                                            <a href="{{ route('student.exams.index') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-external-link-alt me-1"></i>
                                                عرض الامتحانات
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>الامتحانات القصيرة</h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <p>الاختبارات القصيرة والتقييمات الدورية</p>
                                            <a href="{{ route('student.quizzes.index') }}" class="btn btn-info mt-2">
                                                <i class="fas fa-external-link-alt me-1"></i>
                                                عرض الامتحانات القصيرة
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                يمكنك الاطلاع على الامتحانات والاختبارات القصيرة المتاحة في الدورات التي قمت بالتسجيل فيها من خلال الروابط أعلاه.
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
