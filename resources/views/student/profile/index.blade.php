@extends('layouts.app')

@section('title', 'الملف الشخصي')

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
            </div>
        </div>

        <!-- Main Content -->
        <div class="col profile-content">
            <div class="container py-4">
                <div class="profile-container fade-in">
                    <div class="profile-cover"></div>

                    <div class="profile-header">
                        <div class="profile-avatar">
                            @if($user->profile_image)
                                <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="{{ $user->name }}">
                            @endif
                        </div>

                        <div class="profile-actions">
                            <a href="{{ route('student.profile.edit') }}" class="btn btn-primary profile-action-btn">
                                <i class="fas fa-edit"></i> {{ app()->getLocale() == 'ar' ? 'تعديل الملف الشخصي' : 'Edit Profile' }}
                            </a>
                            <a href="{{ route('student.profile.change-password-form') }}" class="btn btn-outline-secondary profile-action-btn">
                                <i class="fas fa-lock"></i> {{ app()->getLocale() == 'ar' ? 'تغيير كلمة المرور' : 'Change Password' }}
                            </a>
                        </div>

                        <div class="profile-info">
                            <h1 class="profile-name">{{ $user->name }}</h1>
                            <div class="profile-role">{{ app()->getLocale() == 'ar' ? 'طالب' : 'Student' }}</div>

                            <div class="profile-bio">
                                {{ $user->bio ?? (app()->getLocale() == 'ar' ? 'لا توجد نبذة شخصية. أضف نبذة لإخبار الآخرين عن نفسك.' : 'No bio available. Add a bio to tell others about yourself.') }}
                            </div>

                            <div class="profile-stats">
                                <div class="stat-item">
                                    <div class="stat-value">{{ $statistics['totalCoursesEnrolled'] ?? 0 }}</div>
                                    <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'الدورات المسجلة' : 'Courses Enrolled' }}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ $statistics['totalVideosWatched'] ?? 0 }}</div>
                                    <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'مقاطع الفيديو المشاهدة' : 'Videos Watched' }}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ $statistics['totalWatchHours'] ?? 0 }}</div>
                                    <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'ساعات المشاهدة' : 'Hours Watched' }}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ $statistics['totalReviews'] ?? 0 }}</div>
                                    <div class="stat-label">{{ app()->getLocale() == 'ar' ? 'التقييمات المقدمة' : 'Reviews Given' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-tabs">
                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab" aria-controls="courses" aria-selected="true">
                                    <i class="fas fa-book-open me-2"></i> {{ app()->getLocale() == 'ar' ? 'دوراتي' : 'My Courses' }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="false">
                                    <i class="fas fa-chart-line me-2"></i> {{ app()->getLocale() == 'ar' ? 'النشاط' : 'Activity' }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">
                                    <i class="fas fa-star me-2"></i> {{ app()->getLocale() == 'ar' ? 'التقييمات' : 'Reviews' }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                                    <i class="fas fa-address-card me-2"></i> {{ app()->getLocale() == 'ar' ? 'معلومات الاتصال' : 'Contact Info' }}
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="profileTabsContent">
                        <!-- Courses Tab -->
                        <div class="tab-pane fade show active" id="courses" role="tabpanel" aria-labelledby="courses-tab">
                            <h3 class="mb-4">{{ app()->getLocale() == 'ar' ? 'دوراتي' : 'My Courses' }}</h3>

                            @if(count($enrolledCourses) > 0)
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <h4 class="mb-3">{{ app()->getLocale() == 'ar' ? 'قيد التقدم' : 'In Progress' }} ({{ count($inProgressCourses) }})</h4>

                                        @if(count($inProgressCourses) > 0)
                                            @foreach($inProgressCourses as $course)
                                                <div class="course-card">
                                                    <div class="course-header">
                                                        <div class="course-image">
                                                            @if($course->thumbnail)
                                                                <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}">
                                                            @else
                                                                <i class="fas fa-book fa-2x text-primary d-flex justify-content-center align-items-center h-100"></i>
                                                            @endif
                                                        </div>
                                                        <div class="course-info">
                                                            <h4>{{ $course->title }}</h4>
                                                            <p>{{ app()->getLocale() == 'ar' ? 'بواسطة' : 'by' }} {{ $course->instructor->name ?? (app()->getLocale() == 'ar' ? 'مدرب غير معروف' : 'Unknown Instructor') }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="course-progress">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span>{{ app()->getLocale() == 'ar' ? 'التقدم' : 'Progress' }}</span>
                                                            <span>{{ $course->progress }}%</span>
                                                        </div>
                                                        <div class="progress">
                                                            <div class="progress-bar" role="progressbar" style="width: {{ $course->progress }}%" aria-valuenow="{{ $course->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>

                                                    <div class="course-footer">
                                                        <div class="course-meta">
                                                            <div class="course-meta-item">
                                                                <i class="fas fa-layer-group"></i> {{ $course->category->name ?? (app()->getLocale() == 'ar' ? 'غير مصنف' : 'Uncategorized') }}
                                                            </div>
                                                            <div class="course-meta-item">
                                                                <i class="fas fa-clock"></i> {{ $course->duration ?? (app()->getLocale() == 'ar' ? 'غير محدد' : 'N/A') }}
                                                            </div>
                                                        </div>

                                                        <a href="{{ route('student.course-content', $course->course_id) }}" class="view-course-btn">{{ app()->getLocale() == 'ar' ? 'متابعة' : 'Continue' }}</a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center py-4">
                                                <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                                <p>{{ app()->getLocale() == 'ar' ? 'ليس لديك أي دورات قيد التقدم.' : 'You don\'t have any courses in progress.' }}</p>
                                                <a href="{{ route('courses') }}" class="btn btn-primary mt-3">{{ app()->getLocale() == 'ar' ? 'استعراض الدورات' : 'Browse Courses' }}</a>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h4 class="mb-3">{{ app()->getLocale() == 'ar' ? 'مكتملة' : 'Completed' }} ({{ count($completedCourses) }})</h4>

                                        @if(count($completedCourses) > 0)
                                            @foreach($completedCourses as $course)
                                                <div class="course-card">
                                                    <div class="course-header">
                                                        <div class="course-image">
                                                            @if($course->thumbnail)
                                                                <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}">
                                                            @else
                                                                <i class="fas fa-book fa-2x text-primary d-flex justify-content-center align-items-center h-100"></i>
                                                            @endif
                                                        </div>
                                                        <div class="course-info">
                                                            <h4>{{ $course->title }}</h4>
                                                            <p>{{ app()->getLocale() == 'ar' ? 'بواسطة' : 'by' }} {{ $course->instructor->name ?? (app()->getLocale() == 'ar' ? 'مدرب غير معروف' : 'Unknown Instructor') }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="course-progress">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span>{{ app()->getLocale() == 'ar' ? 'مكتملة' : 'Completed' }}</span>
                                                            <span>100%</span>
                                                        </div>
                                                        <div class="progress">
                                                            <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>

                                                    <div class="course-footer">
                                                        <div class="course-meta">
                                                            <div class="course-meta-item">
                                                                <i class="fas fa-layer-group"></i> {{ $course->category->name ?? (app()->getLocale() == 'ar' ? 'غير مصنف' : 'Uncategorized') }}
                                                            </div>
                                                            <div class="course-meta-item">
                                                                <i class="fas fa-clock"></i> {{ $course->duration ?? (app()->getLocale() == 'ar' ? 'غير محدد' : 'N/A') }}
                                                            </div>
                                                        </div>

                                                        <a href="{{ route('student.course-content', $course->course_id) }}" class="view-course-btn">{{ app()->getLocale() == 'ar' ? 'مراجعة' : 'Review' }}</a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center py-4">
                                                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                                <p>{{ app()->getLocale() == 'ar' ? 'لم تكمل أي دورات بعد.' : 'You haven\'t completed any courses yet.' }}</p>
                                                <p>{{ app()->getLocale() == 'ar' ? 'استمر في التعلم للحصول على شهادات الإتمام!' : 'Keep learning to earn your completion certificates!' }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                    <p>{{ app()->getLocale() == 'ar' ? 'أنت غير مسجل في أي دورات حتى الآن.' : 'You are not enrolled in any courses yet.' }}</p>
                                    <a href="{{ route('courses') }}" class="btn btn-primary mt-3">{{ app()->getLocale() == 'ar' ? 'استعراض الدورات' : 'Browse Courses' }}</a>
                                </div>
                            @endif
                        </div>

                        <!-- Activity Tab -->
                        <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                            <h3 class="mb-4">{{ app()->getLocale() == 'ar' ? 'النشاط الأخير' : 'Recent Activity' }}</h3>

                            @if(count($recentActivity) > 0)
                                @foreach($recentActivity as $activity)
                                    <div class="activity-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex">
                                                <div class="me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(0, 86, 179, 0.1); border-radius: 8px;">
                                                    <i class="fas fa-play-circle text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="activity-title fw-medium">
                                                        {{ app()->getLocale() == 'ar' ? 'شاهد' : 'Watched' }} "{{ $activity->video->title ?? (app()->getLocale() == 'ar' ? 'فيديو غير معروف' : 'Unknown Video') }}" {{ app()->getLocale() == 'ar' ? 'في' : 'in' }} {{ $activity->course->title ?? (app()->getLocale() == 'ar' ? 'دورة غير معروفة' : 'Unknown Course') }}
                                                    </div>
                                                    <div class="activity-time small text-muted">
                                                        {{ $activity->updated_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('student.course-content', $activity->course_id ?? 0) }}" class="btn btn-sm btn-outline-primary">{{ app()->getLocale() == 'ar' ? 'عرض' : 'View' }}</a>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                    <p>{{ app()->getLocale() == 'ar' ? 'لا يوجد نشاط حديث للعرض.' : 'No recent activity to show.' }}</p>
                                    <p>{{ app()->getLocale() == 'ar' ? 'ابدأ بمشاهدة مقاطع الفيديو لتتبع تقدمك في التعلم!' : 'Start watching videos to track your learning progress!' }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Reviews Tab -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                            <h3 class="mb-4">{{ app()->getLocale() == 'ar' ? 'تقييماتي' : 'My Reviews' }}</h3>

                            @if(isset($reviews) && count($reviews) > 0)
                                @foreach($reviews as $review)
                                    <div class="activity-item">
                                        <div class="review-header d-flex justify-content-between align-items-center mb-2">
                                            <div class="review-course fw-medium">{{ $review->course->title ?? (app()->getLocale() == 'ar' ? 'دورة غير معروفة' : 'Unknown Course') }}</div>
                                            <div class="review-rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-warning"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="review-content mb-2">
                                            {{ $review->review_text }}
                                        </div>
                                        <div class="review-date small text-muted">
                                            {{ $review->created_at->format('F j, Y') }}
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                    <p>{{ app()->getLocale() == 'ar' ? 'لم تكتب أي تقييمات بعد.' : 'You haven\'t written any reviews yet.' }}</p>
                                    <p>{{ app()->getLocale() == 'ar' ? 'شارك تجربتك من خلال تقييم الدورات التي أخذتها!' : 'Share your experience by reviewing the courses you\'ve taken!' }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Contact Info Tab -->
                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            <h3 class="mb-4">{{ app()->getLocale() == 'ar' ? 'معلومات الاتصال' : 'Contact Information' }}</h3>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="activity-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(0, 86, 179, 0.1); border-radius: 8px;">
                                                <i class="fas fa-envelope text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted">{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email' }}</div>
                                                <div class="fw-medium">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="activity-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(0, 86, 179, 0.1); border-radius: 8px;">
                                                <i class="fas fa-phone text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted">{{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number' }}</div>
                                                <div class="fw-medium">{{ $user->phone ?? (app()->getLocale() == 'ar' ? 'لم يتم إضافة رقم هاتف' : 'No phone number added') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="activity-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(0, 86, 179, 0.1); border-radius: 8px;">
                                                <i class="fas fa-map-marker-alt text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted">{{ app()->getLocale() == 'ar' ? 'العنوان' : 'Address' }}</div>
                                                <div class="fw-medium">{{ $user->address ?? (app()->getLocale() == 'ar' ? 'لم يتم إضافة عنوان' : 'No address added') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('student.profile.edit') }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i> {{ app()->getLocale() == 'ar' ? 'تحديث معلومات الاتصال' : 'Update Contact Info' }}
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
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap tabs
        var triggerTabList = [].slice.call(document.querySelectorAll('#profileTabs button'))
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
