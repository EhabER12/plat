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
        @php
            $unreadAdminMessages = \App\Models\DirectMessage::where('receiver_id', Auth::id())
                ->whereHas('sender.roles', function($query) {
                    $query->where('role', 'admin');
                })
                ->where('is_read', false)
                ->count();
        @endphp
        @if($unreadAdminMessages > 0)
            <span class="badge bg-warning position-absolute top-0 start-100 translate-middle" style="font-size: 0.6rem;" title="رسائل من الإدارة">A{{ $unreadAdminMessages }}</span>
        @endif
        <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الرسائل' : 'Messages' }}</div>
    </a>

    <a href="{{ route('student.notifications.index') }}" class="sidebar-icon {{ request()->routeIs('student.notifications.*') ? 'active' : '' }}">
        <i class="fas fa-bell"></i>
        @php
            $unreadCount = Auth::user()->notifications()->whereNull('read_at')->count();
        @endphp
        @if($unreadCount > 0)
            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $unreadCount }}</span>
        @endif
        <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الإشعارات' : 'Notifications' }}</div>
    </a>

    <!-- إضافة رابط نظام التقييمات التحفيزية -->
    <a href="{{ route('student.motivation.index') }}" class="sidebar-icon {{ request()->routeIs('student.motivation.*') ? 'active' : '' }}">
        <i class="fas fa-trophy"></i>
        <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'التقييمات والإنجازات' : 'Motivation' }}</div>
    </a>

    <a href="{{ route('student.certificates.index') }}" class="sidebar-icon {{ request()->routeIs('student.certificates.*') ? 'active' : '' }}">
        <i class="fas fa-certificate"></i>
        <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الشهادات' : 'Certificates' }}</div>
    </a>

    <a href="{{ route('student.exams.index') }}" class="sidebar-icon mt-auto mb-4 {{ request()->routeIs('student.exams.*') ? 'active' : '' }}">
        <i class="fas fa-file-alt"></i>
        <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الاختبارات' : 'Exams' }}</div>
    </a>
    <a href="{{ route('student.quizzes.index') }}" class="sidebar-icon mb-2 {{ request()->routeIs('student.quizzes.*') ? 'active' : '' }}">
        <i class="fas fa-question-circle"></i>
        <div class="sidebar-tooltip">{{ app()->getLocale() == 'ar' ? 'الامتحانات القصيرة' : 'Quizzes' }}</div>
    </a>
</div>
