@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('styles')
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
<!-- Student Dashboard CSS -->
<link href="{{ asset('css/student.css') }}" rel="stylesheet">
<style>
    .profile-container {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .profile-cover {
        height: 200px;
        background: linear-gradient(135deg, #4776E6, #8E54E9);
        position: relative;
    }
    
    .profile-header {
        padding: 20px 30px;
        position: relative;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 5px solid #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        position: absolute;
        top: -60px;
        overflow: hidden;
        background: #fff;
    }
    
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .profile-info {
        margin-left: 140px;
        min-height: 80px;
    }
    
    .profile-name {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: #333;
    }
    
    .profile-role {
        font-size: 1rem;
        color: #6c757d;
        margin-bottom: 15px;
    }
    
    .profile-bio {
        color: #6c757d;
        margin-bottom: 20px;
    }
    
    .profile-actions {
        position: absolute;
        top: 30px;
        right: 30px;
        display: flex;
    }
    
    .profile-action-btn {
        margin-left: 10px;
    }
    
    .profile-stats {
        display: flex;
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    
    .stat-item {
        flex: 1 1 120px;
        text-align: center;
        padding: 10px 15px;
        border-right: 1px solid #dee2e6;
        min-width: 120px;
    }
    
    .stat-item:last-child {
        border-right: none;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #3a86ff;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .profile-tabs {
        padding: 0 30px;
        margin-top: 20px;
        overflow-x: auto;
    }
    
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        white-space: nowrap;
        flex-wrap: nowrap;
        min-width: 450px;
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        padding: 15px 20px;
        font-weight: 500;
        white-space: nowrap;
    }
    
    .nav-tabs .nav-link.active {
        color: #3a86ff;
        border-bottom: 3px solid #3a86ff;
        background: transparent;
    }
    
    .tab-content {
        padding: 30px 15px;
    }
    
    .course-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        transition: all 0.3s ease;
    }
    
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .course-header {
        display: flex;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    
    .course-image {
        width: 80px;
        height: 80px;
        min-width: 80px;
        background: #f1f3f5;
        border-radius: 10px;
        overflow: hidden;
        margin-right: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .course-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .course-info {
        flex: 1;
        min-width: 150px;
    }
    
    .course-info h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
        word-break: break-word;
    }
    
    .course-info p {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0;
    }
    
    .course-progress {
        margin-bottom: 15px;
    }
    
    .progress {
        height: 8px;
        border-radius: 4px;
        background-color: #e9ecef;
    }
    
    .progress-bar {
        background-color: #3a86ff;
    }
    
    .course-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .course-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .course-meta-item {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .view-course-btn {
        background-color: #3a86ff;
        color: #fff;
        border-radius: 20px;
        padding: 5px 15px;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.3s ease;
        white-space: nowrap;
    }
    
    .view-course-btn:hover {
        background-color: #2a75f0;
        color: #fff;
    }
    
    .activity-item {
        padding: 15px 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .activity-item:last-child {
        border-bottom: none;
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
