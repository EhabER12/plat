@php
    $navbarClass = Request::is('/') ? 'navbar-transparent' : '';
@endphp

<nav class="navbar navbar-expand-lg fixed-top {{ $navbarClass }}" style="background-color: #0a2e4d !important; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="TOTO" class="logo-img" width="40" height="40">
            <span class="brand-text" style="color: white;"></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars" style="color: white;"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav {{ app()->getLocale() == 'ar' ? 'me-auto' : 'ms-auto' }}">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" style="color: white;"
                        href="{{ url('/') }}">{{ __('Home') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('courses*') ? 'active' : '' }}" style="color: white;"
                        href="{{ route('courses.index') }}">{{ __('Courses') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('instructors*') ? 'active' : '' }}" style="color: white;"
                        href="{{ route('instructors.index') }}">{{ __('Instructors') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('about') ? 'active' : '' }}" style="color: white;"
                        href="{{ url('/about') }}">{{ __('About') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('contact') ? 'active' : '' }}" style="color: white;"
                        href="{{ url('/contact') }}">{{ __('Contact') }}</a>
                </li>

                <li class="nav-item dropdown d-lg-none d-block">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">
                        {{ app()->getLocale() == 'en' ? 'English' : 'العربية' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown" style="background-color: #0a2e4d;">
                        <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}" style="color: white;">English</a></li>
                        <li><a class="dropdown-item" href="{{ route('language.switch', 'ar') }}" style="color: white;">العربية</a></li>
                    </ul>
                </li>

                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('login') ? 'active' : '' }}" style="color: white;"
                            href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('register') ? 'active' : '' }}" style="color: white;"
                            href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">
                            <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/default-avatar.png') }}"
                                class="rounded-circle me-1" width="24" height="24" alt="{{ Auth::user()->name }}">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" style="background-color: #0a2e4d;">
                            @if(Auth::user()->hasRole('admin'))
                                <li><a class="dropdown-item" style="color: white;"
                                        href="{{ route('admin.dashboard') }}">{{ __('Admin Dashboard') }}</a></li>
                            @elseif(Auth::user()->hasRole('instructor'))
                                <li><a class="dropdown-item" style="color: white;"
                                        href="{{ route('instructor.dashboard') }}">{{ __('Instructor Dashboard') }}</a></li>
                            @else
                                <li><a class="dropdown-item" style="color: white;"
                                        href="{{ route('student.dashboard') }}">{{ __('My Dashboard') }}</a></li>
                            @endif
                            @if(Auth::user()->hasRole('admin'))
                                <li><a class="dropdown-item" style="color: white;" href="{{ route('admin.settings') }}">{{ __('Profile') }}</a></li>
                            @elseif(Auth::user()->hasRole('instructor'))
                                <li><a class="dropdown-item" style="color: white;"
                                        href="{{ route('instructor.profile.index') }}">{{ __('Profile') }}</a></li>
                            @else
                                <li><a class="dropdown-item" style="color: white;" href="{{ route('student.profile') }}">{{ __('Profile') }}</a></li>
                            @endif
                            <li>
                                <hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.2);">
                            </li>
                            <li>
                                <a class="dropdown-item" style="color: white;" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
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
                    class="btn btn-sm {{ app()->getLocale() == 'ar' ? 'btn-outline-primary' : 'btn-outline-secondary' }}" style="color: white; border-color: white;">
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
        background-color: #0a2e4d;
    }

    .navbar-transparent.navbar-scrolled {
        background-color: #0a2e4d !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .navbar {
        padding: 15px 0;
    }
    
    .navbar-scrolled {
        padding: 10px 0;
    }

    .navbar .logo-img {
        transition: all 0.3s ease;
    }

    .navbar .brand-text {
        font-weight: 700;
        font-size: 1.5rem;
        margin-left: 0.5rem;
        transition: all 0.3s ease;
        color: white;
    }

    .navbar-toggler {
        border: none;
        color: white;
        font-size: 1.2rem;
        padding: 0.4rem 0.8rem;
        border-radius: 5px;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .navbar-toggler:focus {
        box-shadow: none;
    }
    
    .nav-link {
        font-weight: 500;
        padding: 0.5rem 1rem !important;
        margin: 0 0.2rem;
        border-radius: 5px;
        transition: all 0.3s ease;
        color: white !important;
    }
    
    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }
    
    .nav-link.active {
        background-color: rgba(255, 255, 255, 0.15);
        font-weight: 600;
    }
    
    .dropdown-menu {
        border: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        padding: 0.8rem 0;
        margin-top: 0.5rem;
        background-color: #0a2e4d !important;
    }
    
    .dropdown-item {
        padding: 0.6rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s;
        color: white !important;
    }
    
    .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
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
        background-color: rgba(255, 255, 255, 0.1);
    }

    .dropdown-divider {
        border-color: rgba(255, 255, 255, 0.2);
    }

    @media (max-width: 991.98px) {
        .navbar-collapse {
            background-color: #0a2e4d;
            border-radius: 0 0 15px 15px;
            margin-top: 0.5rem;
            padding: 1.2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .navbar-nav .nav-item {
            margin-bottom: 0.5rem;
        }

        .navbar-nav .nav-link {
            padding: 0.6rem 1rem;
            border-radius: 8px;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .navbar-nav .dropdown-menu {
            background-color: rgba(0, 0, 0, 0.1) !important;
            border: none;
            box-shadow: none;
            padding: 0.5rem;
            margin: 0.5rem 0;
            border-radius: 8px;
        }

        .navbar-nav .dropdown-item {
            color: white !important;
            border-radius: 5px;
        }

        .navbar-nav .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navbar = document.querySelector('.navbar-transparent');

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
    });
</script>

<!-- Flash Messages -->
<div class="container mt-5 pt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>