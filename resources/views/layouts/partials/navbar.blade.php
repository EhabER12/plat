@php
    $navbarClass = Request::is('/') ? 'navbar-transparent' : '';
@endphp

<nav class="navbar navbar-expand-lg fixed-top {{ $navbarClass }}" style="background-color: #0a2e4d !important; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="TOTO" class="logo-img" width="40" height="40">
            <span class="brand-text" style="color: white; font-weight: 700;">TOTO</span>
        </a>
        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon-custom">
                <span class="toggler-line"></span>
                <span class="toggler-line"></span>
                <span class="toggler-line"></span>
            </span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav {{ app()->getLocale() == 'ar' ? 'me-auto' : 'ms-auto' }}">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" style="color: white !important; font-weight: 600;"
                        href="{{ url('/') }}">
                        <i class="fas fa-home nav-icon"></i>
                        <span class="nav-text">{{ __('Home') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('courses*') ? 'active' : '' }}" style="color: white !important; font-weight: 600;"
                        href="{{ route('courses.index') }}">
                        <i class="fas fa-graduation-cap nav-icon"></i>
                        <span class="nav-text">{{ __('Courses') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('books*') ? 'active' : '' }}" style="color: white !important; font-weight: 600;"
                        href="{{ route('books.index') }}">
                        <i class="fas fa-book nav-icon"></i>
                        <span class="nav-text">{{ __('Books') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('instructors*') ? 'active' : '' }}" style="color: white !important; font-weight: 600;"
                        href="{{ route('instructors.index') }}">
                        <i class="fas fa-chalkboard-teacher nav-icon"></i>
                        <span class="nav-text">{{ __('Instructors') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('about') ? 'active' : '' }}" style="color: white !important; font-weight: 600;"
                        href="{{ url('/about') }}">
                        <i class="fas fa-info-circle nav-icon"></i>
                        <span class="nav-text">{{ __('About') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('contact') ? 'active' : '' }}" style="color: white !important; font-weight: 600;"
                        href="{{ url('/contact') }}">
                        <i class="fas fa-envelope nav-icon"></i>
                        <span class="nav-text">{{ __('Contact') }}</span>
                    </a>
                </li>

                <li class="nav-item dropdown d-lg-none d-block">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false" style="color: white !important; font-weight: 600;">
                        <i class="fas fa-language nav-icon"></i>
                        <span class="nav-text">{{ app()->getLocale() == 'en' ? 'English' : 'العربية' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown" style="background-color: #0a2e4d;">
                        <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}" style="color: white !important;"><i class="fas fa-flag me-2"></i> English</a></li>
                        <li><a class="dropdown-item" href="{{ route('language.switch', 'ar') }}" style="color: white !important;"><i class="fas fa-flag me-2"></i> العربية</a></li>
                    </ul>
                </li>

                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('login') ? 'active' : '' }}" style="color: white !important; font-weight: 600;"
                            href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt nav-icon"></i>
                            <span class="nav-text">{{ __('Login') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('register') ? 'active' : '' }}" style="color: white !important; font-weight: 600;"
                            href="{{ route('register') }}">
                            <i class="fas fa-user-plus nav-icon"></i>
                            <span class="nav-text">{{ __('Register') }}</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false" style="color: white !important; font-weight: 600;">
                            <div class="user-avatar-container">
                                @php
                                    $userRoles = Auth::user()->getUserRoles();
                                    $primaryRole = !empty($userRoles) ? $userRoles[0] : 'student';
                                    $roleIconClass = '';
                                    $roleBgColor = '';

                                    // تحديد الأيقونة واللون حسب نوع المستخدم
                                    switch($primaryRole) {
                                        case 'admin':
                                            $roleIconClass = 'fa-user-shield';
                                            $roleBgColor = 'var(--admin-color, #E74C3C)';
                                            break;
                                        case 'instructor':
                                            $roleIconClass = 'fa-chalkboard-teacher';
                                            $roleBgColor = 'var(--instructor-color, #3498DB)';
                                            break;
                                        case 'parent':
                                            $roleIconClass = 'fa-user-friends';
                                            $roleBgColor = 'var(--parent-color, #9B59B6)';
                                            break;
                                        case 'student':
                                        default:
                                            $roleIconClass = 'fa-user-graduate';
                                            $roleBgColor = 'var(--student-color, #2ECC71)';
                                            break;
                                    }
                                @endphp

                                @if(Auth::user()->profile_image)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" class="user-avatar" alt="{{ Auth::user()->name }}">
                                @else
                                    <div class="user-avatar-placeholder" style="background-color: {{ $roleBgColor }};">
                                        <i class="fas {{ $roleIconClass }}"></i>
                                    </div>
                                @endif
                                <div class="user-role-indicator" style="background-color: {{ $roleBgColor }};" title="{{ ucfirst($primaryRole) }}">
                                    <i class="fas {{ $roleIconClass }}"></i>
                                </div>
                            </div>
                            <span class="user-name">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" style="background-color: #0a2e4d;">
                            @if(Auth::user()->hasRole('admin'))
                                <li>
                                    <a class="dropdown-item" style="color: white !important;" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i> {{ __('Admin Dashboard') }}
                                    </a>
                                </li>
                            @elseif(Auth::user()->hasRole('instructor'))
                                <li>
                                    <a class="dropdown-item" style="color: white !important;" href="{{ route('instructor.dashboard') }}">
                                        <i class="fas fa-chalkboard me-2"></i> {{ __('Instructor Dashboard') }}
                                    </a>
                                </li>
                            @elseif(Auth::user()->hasRole('parent'))
                                <li>
                                    <a class="dropdown-item" style="color: white !important;" href="{{ route('parent.dashboard') }}">
                                        <i class="fas fa-user-friends me-2"></i> لوحة تحكم ولي الأمر
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="dropdown-item" style="color: white !important;" href="{{ route('student.dashboard') }}">
                                        <i class="fas fa-home me-2"></i> {{ __('My Dashboard') }}
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasRole('admin'))
                                <li>
                                    <a class="dropdown-item" style="color: white !important;" href="{{ route('admin.settings') }}">
                                        <i class="fas fa-user-cog me-2"></i> {{ __('Profile') }}
                                    </a>
                                </li>
                            @elseif(Auth::user()->hasRole('instructor'))
                                <li>
                                    <a class="dropdown-item" style="color: white !important;" href="{{ route('instructor.profile.index') }}">
                                        <i class="fas fa-user-tie me-2"></i> {{ __('Profile') }}
                                    </a>
                                </li>
                            @elseif(Auth::user()->hasRole('parent'))
                                <li>
                                    <a class="dropdown-item" style="color: white !important;" href="{{ route('parent.profile') }}">
                                        <i class="fas fa-user-friends me-2"></i> الملف الشخصي
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="dropdown-item" style="color: white !important;" href="{{ route('student.profile') }}">
                                        <i class="fas fa-user me-2"></i> {{ __('Profile') }}
                                    </a>
                                </li>
                            @endif
                            <li>
                                <hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.2);">
                            </li>
                            <li>
                                <a class="dropdown-item" style="color: white !important;" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>

            <!-- Language Switcher - Visible only on larger screens -->
            <div class="language-switcher d-none d-lg-block ms-3">
                <a href="{{ route('language.switch', app()->getLocale() == 'en' ? 'ar' : 'en') }}"
                    class="btn btn-sm {{ app()->getLocale() == 'ar' ? 'btn-outline-primary' : 'btn-outline-secondary' }}" style="color: white !important; border-color: white !important;">
                    <i class="fas fa-language me-1"></i>
                    {{ app()->getLocale() == 'en' ? 'العربية' : 'English' }}
                </a>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Navbar styles */
    .navbar-transparent {
        transition: all 0.4s ease;
        background-color: #0a2e4d !important;
    }

    .navbar-transparent.navbar-scrolled {
        background-color: #0a2e4d !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .navbar {
        padding: 15px 0;
        z-index: 1030 !important;
    }

    .navbar-scrolled {
        padding: 10px 0;
    }

    .navbar .logo-img {
        transition: all 0.3s ease;
    }

    .navbar .brand-text {
        font-weight: 700 !important;
        font-size: 1.5rem;
        margin-left: 0.5rem;
        transition: all 0.3s ease;
        color: white !important;
    }

    .navbar-toggler {
        border: 2px solid rgba(255,255,255,0.5) !important;
        color: white !important;
        font-size: 1.2rem;
        padding: 0.4rem 0.8rem;
        border-radius: 5px;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .navbar-toggler:focus {
        box-shadow: none;
        outline: none;
    }

    .nav-link {
        font-weight: 600 !important;
        padding: 0.5rem 1rem !important;
        margin: 0 0.2rem;
        border-radius: 5px;
        transition: all 0.3s ease;
        color: white !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        font-size: 1.05rem;
        display: flex;
        align-items: center;
    }

    .nav-link .nav-icon {
        margin-right: 8px;
        font-size: 1.1rem;
        transition: transform 0.3s ease;
        width: 20px;
        text-align: center;
    }

    html[dir="rtl"] .nav-link .nav-icon {
        margin-right: 0;
        margin-left: 8px;
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
        color: #FFD700 !important;
    }

    .nav-link:hover .nav-icon {
        transform: scale(1.2);
        color: #FFD700 !important;
    }

    .nav-link.active {
        background-color: rgba(255, 255, 255, 0.15);
        font-weight: 700 !important;
        color: #FFD700 !important;
    }

    .nav-link.active .nav-icon {
        color: #FFD700 !important;
    }

    .dropdown-menu {
        border: none !important;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        padding: 0.8rem 0;
        margin-top: 0.5rem;
        background-color: #0a2e4d !important;
    }

    /* User Avatar and Role Styles */
    :root {
        --admin-color: #E74C3C;
        --instructor-color: #3498DB;
        --student-color: #2ECC71;
        --parent-color: #9B59B6;
    }

    .user-avatar-container {
        position: relative;
        display: inline-flex;
        align-items: center;
        margin-right: 8px;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .user-avatar-placeholder {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        border: 2px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .user-role-indicator {
        position: absolute;
        bottom: -3px;
        right: -3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 9px;
        border: 1px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .user-name {
        margin-left: 5px;
        font-weight: 600;
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .nav-link:hover .user-role-indicator {
        transform: scale(1.2);
    }

    .dropdown-item {
        padding: 0.6rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s;
        color: white !important;
    }

    .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.15);
        transform: translateX(5px);
        color: #FFD700 !important;
    }

    .language-switcher .btn {
        border-radius: 20px;
        padding: 0.4rem 1rem;
        font-weight: 500;
        transition: all 0.3s;
        color: white !important;
        border-color: white !important;
    }

    .language-switcher .btn:hover {
        transform: translateY(-2px);
        background-color: rgba(255, 255, 255, 0.15);
        color: #FFD700 !important;
    }

    .dropdown-divider {
        border-color: rgba(255,255,255,0.2);
    }

    /* Responsive Styles */
    @media (max-width: 991.98px) {
        .navbar {
            padding: 10px 0 !important;
        }

        .navbar-brand {
            font-size: 1.3rem;
        }

        .navbar-collapse {
            background-color: #0a2e4d !important;
            border-radius: 0 0 15px 15px;
            margin-top: 0.5rem;
            padding: 1.2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            max-height: 80vh;
            overflow-y: auto;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            width: 100%;
        }

        .navbar-nav .nav-item {
            margin-bottom: 0.8rem;
            width: 100%;
        }

        .navbar-nav .nav-link {
            padding: 0.7rem 1rem !important;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .navbar-nav .nav-link .nav-icon {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        html[dir="rtl"] .navbar-nav .nav-link .nav-icon {
            margin-right: 0;
            margin-left: 10px;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255,255,255,0.3);
        }

        .navbar-nav .dropdown-toggle::after {
            margin-left: auto;
        }

        .navbar-nav .dropdown-menu {
            background-color: rgba(0, 0, 0, 0.2) !important;
            border: none;
            box-shadow: none;
            padding: 0.5rem;
            margin: 0.5rem 0;
            border-radius: 8px;
            position: static !important;
            float: none;
            width: 100%;
            transform: none !important;
        }

        .navbar-nav .dropdown-item {
            color: white !important;
            border-radius: 5px;
            padding: 0.8rem 1.5rem;
            white-space: normal;
        }

        .navbar-nav .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #FFD700 !important;
        }

        /* User Avatar Responsive Styles */
        .user-avatar-container {
            margin-right: 10px;
        }

        .user-name {
            max-width: none;
            flex: 1;
        }

        .dropdown-toggle.nav-link {
            display: flex;
            align-items: center;
        }

        /* Custom Toggler Button */
        .navbar-toggler {
            border: none !important;
            padding: 0.5rem;
            background-color: transparent;
            position: relative;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }

        .navbar-toggler-icon-custom {
            position: relative;
            width: 24px;
            height: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .toggler-line {
            display: block;
            width: 100%;
            height: 2px;
            background-color: white;
            border-radius: 2px;
            transition: all 0.3s;
            transform-origin: center;
        }

        .navbar-toggler.collapsed .toggler-line:nth-child(1) {
            transform: translateY(0) rotate(0);
        }

        .navbar-toggler.collapsed .toggler-line:nth-child(2) {
            opacity: 1;
        }

        .navbar-toggler.collapsed .toggler-line:nth-child(3) {
            transform: translateY(0) rotate(0);
        }

        .navbar-toggler:not(.collapsed) .toggler-line:nth-child(1) {
            transform: translateY(8px) rotate(45deg);
        }

        .navbar-toggler:not(.collapsed) .toggler-line:nth-child(2) {
            opacity: 0;
        }

        .navbar-toggler:not(.collapsed) .toggler-line:nth-child(3) {
            transform: translateY(-8px) rotate(-45deg);
        }
    }

    /* Small Mobile Devices */
    @media (max-width: 575.98px) {
        .navbar-brand .brand-text {
            font-size: 1.2rem;
        }

        .navbar .logo-img {
            width: 32px;
            height: 32px;
        }

        .navbar-toggler {
            padding: 0.3rem 0.6rem;
            font-size: 1rem;
            margin-right: 0;
        }

        .navbar-collapse {
            padding: 1rem 0.8rem;
            margin-top: 0.5rem;
            left: 5%;
            right: 5%;
            width: 90%;
            border-radius: 10px;
        }

        .navbar-nav .nav-link {
            font-size: 0.95rem;
            padding: 0.6rem 0.8rem !important;
            margin-bottom: 0.5rem;
        }

        .navbar-nav .nav-link .nav-icon {
            font-size: 1rem;
        }

        .navbar-nav .nav-link .nav-text {
            margin-left: 8px;
        }

        html[dir="rtl"] .navbar-nav .nav-link .nav-text {
            margin-left: 0;
            margin-right: 8px;
        }

        .dropdown-item {
            font-size: 0.9rem;
            padding: 0.7rem 1rem;
        }

        .user-avatar, .user-avatar-placeholder {
            width: 28px;
            height: 28px;
        }

        .user-role-indicator {
            width: 16px;
            height: 16px;
            font-size: 8px;
        }

        /* Improve spacing for mobile menu items */
        .navbar-nav {
            padding: 0.5rem 0;
        }

        /* Better animation for mobile menu */
        .navbar-collapse.collapsing {
            height: 0;
            overflow: hidden;
            transition: height 0.35s ease;
        }

        .navbar-collapse.show {
            animation: slideDown 0.35s ease forwards;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    }

    /* RTL Fixes */
    html[dir="rtl"] .navbar .brand-text {
        margin-right: 0.5rem;
        margin-left: 0;
    }

    html[dir="rtl"] .navbar-nav .nav-item {
        text-align: right;
    }

    html[dir="rtl"] .dropdown-menu {
        text-align: right;
    }

    html[dir="rtl"] .navbar-toggler {
        margin-right: auto;
        margin-left: 0;
    }

    html[dir="rtl"] .dropdown-item:hover {
        transform: translateX(-5px);
    }

    /* RTL fixes for user avatar */
    html[dir="rtl"] .user-avatar-container {
        margin-right: 0;
        margin-left: 8px;
    }

    html[dir="rtl"] .user-role-indicator {
        right: auto;
        left: -3px;
    }

    html[dir="rtl"] .user-name {
        margin-left: 0;
        margin-right: 5px;
    }

    html[dir="rtl"] .dropdown-item i {
        margin-right: 0;
        margin-left: 8px;
    }

    /* RTL Responsive Fixes */
    @media (max-width: 991.98px) {
        html[dir="rtl"] .navbar-nav .dropdown-toggle::after {
            margin-left: 0;
            margin-right: auto;
        }

        html[dir="rtl"] .user-avatar-container {
            margin-left: 10px;
            margin-right: 0;
        }

        html[dir="rtl"] .dropdown-item {
            text-align: right;
        }

        html[dir="rtl"] .navbar-toggler {
            margin-left: 0;
            margin-right: auto;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navbar = document.querySelector('.navbar-transparent');

        // Make sure all nav links are visible and properly styled
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.style.color = 'white';
            link.style.fontWeight = '600';
            link.style.visibility = 'visible';
            link.style.opacity = '1';
        });

        // Add hover effects to user avatar
        const userAvatarContainer = document.querySelector('.user-avatar-container');
        if (userAvatarContainer) {
            const roleIndicator = userAvatarContainer.querySelector('.user-role-indicator');

            userAvatarContainer.addEventListener('mouseenter', function() {
                if (roleIndicator) {
                    roleIndicator.style.transform = 'scale(1.2)';
                }
            });

            userAvatarContainer.addEventListener('mouseleave', function() {
                if (roleIndicator) {
                    roleIndicator.style.transform = 'scale(1)';
                }
            });
        }

        // Add tooltips to role indicators
        const roleIndicators = document.querySelectorAll('.user-role-indicator');
        roleIndicators.forEach(indicator => {
            const role = indicator.getAttribute('title');
            if (role) {
                indicator.setAttribute('data-bs-toggle', 'tooltip');
                indicator.setAttribute('data-bs-placement', 'bottom');

                // Initialize Bootstrap tooltips if Bootstrap is available
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    new bootstrap.Tooltip(indicator);
                }
            }
        });

        // Navbar scroll effect
        if (navbar) {
            window.addEventListener('scroll', function () {
                if (window.scrollY > 50) {
                    navbar.classList.add('navbar-scrolled');
                } else {
                    navbar.classList.remove('navbar-scrolled');
                }
            });

            // Check initial scroll position
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            }
        }

        // Add animation to dropdown menu items
        const dropdownItems = document.querySelectorAll('.dropdown-menu .dropdown-item');
        dropdownItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(10px)';
            item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            item.style.transitionDelay = `${index * 0.05}s`;
        });

        // Show dropdown items with animation when dropdown is shown
        const dropdownMenus = document.querySelectorAll('.dropdown-menu');
        dropdownMenus.forEach(menu => {
            const observer = new MutationObserver(mutations => {
                mutations.forEach(mutation => {
                    if (mutation.attributeName === 'class') {
                        const isShown = menu.classList.contains('show');
                        const items = menu.querySelectorAll('.dropdown-item');

                        if (isShown) {
                            items.forEach((item, index) => {
                                setTimeout(() => {
                                    item.style.opacity = '1';
                                    item.style.transform = 'translateY(0)';
                                }, index * 50);
                            });
                        } else {
                            items.forEach(item => {
                                item.style.opacity = '0';
                                item.style.transform = 'translateY(10px)';
                            });
                        }
                    }
                });
            });

            observer.observe(menu, { attributes: true });
        });

        // Responsive behavior improvements
        let isMobile = window.innerWidth < 992;
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');

        // Improve toggler animation
        if (navbarToggler) {
            navbarToggler.addEventListener('click', function() {
                // Add a small delay to allow the collapsed class to be toggled
                setTimeout(() => {
                    const isCollapsed = navbarToggler.classList.contains('collapsed');
                    const togglerLines = navbarToggler.querySelectorAll('.toggler-line');

                    if (togglerLines.length) {
                        if (!isCollapsed) {
                            // Menu is open
                            togglerLines[0].style.transform = 'translateY(8px) rotate(45deg)';
                            togglerLines[1].style.opacity = '0';
                            togglerLines[2].style.transform = 'translateY(-8px) rotate(-45deg)';
                        } else {
                            // Menu is closed
                            togglerLines[0].style.transform = 'translateY(0) rotate(0)';
                            togglerLines[1].style.opacity = '1';
                            togglerLines[2].style.transform = 'translateY(0) rotate(0)';
                        }
                    }
                }, 10);
            });
        }

        // Close navbar when clicking outside
        document.addEventListener('click', function(event) {
            const isNavbarOpen = navbarCollapse && navbarCollapse.classList.contains('show');
            const clickedInsideNavbar = event.target.closest('.navbar') !== null;
            const clickedToggler = event.target.closest('.navbar-toggler') !== null;

            if (isNavbarOpen && !clickedInsideNavbar && !clickedToggler && isMobile) {
                navbarToggler.click();
            }
        });

        // Close navbar when clicking on a nav link (on mobile)
        const mobileNavLinks = document.querySelectorAll('.navbar-nav .nav-link:not(.dropdown-toggle)');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (isMobile && navbarCollapse && navbarCollapse.classList.contains('show')) {
                    navbarToggler.click();
                }
            });
        });

        // Add icons animation on hover for mobile
        const navIcons = document.querySelectorAll('.nav-icon');
        navIcons.forEach(icon => {
            const parentLink = icon.closest('.nav-link');
            if (parentLink) {
                parentLink.addEventListener('mouseenter', function() {
                    icon.style.transform = 'scale(1.2)';
                    icon.style.transition = 'transform 0.3s ease';
                });

                parentLink.addEventListener('mouseleave', function() {
                    icon.style.transform = 'scale(1)';
                });
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const wasDesktop = !isMobile;
            const newIsMobile = window.innerWidth < 992;

            // Update isMobile state
            isMobile = newIsMobile;

            // If switching from mobile to desktop view, ensure dropdown positioning is reset
            if (wasDesktop && !newIsMobile) {
                dropdownMenus.forEach(menu => {
                    menu.style.transform = '';
                    menu.style.position = '';
                });
            }

            // Reset navbar collapse on resize to desktop
            if (newIsMobile === false && navbarCollapse && navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
                if (navbarToggler && !navbarToggler.classList.contains('collapsed')) {
                    navbarToggler.classList.add('collapsed');
                    navbarToggler.setAttribute('aria-expanded', 'false');
                }
            }
        });
    });
</script>

<!-- Flash Messages -->
