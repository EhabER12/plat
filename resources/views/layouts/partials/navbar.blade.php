<!-- Navigation -->
<nav class="navbar navbar-light bg-white shadow-sm fixed-top">
    <div class="container d-flex flex-wrap">
        <a class="navbar-brand me-4" href="/">
            <span class="fw-bold">منصة تعليمية</span>
        </a>
        
        <div class="d-flex flex-grow-1 flex-wrap">
            <ul class="navbar-nav d-flex flex-row flex-wrap me-auto">
                <li class="nav-item me-3">
                    <a class="nav-link {{ request()->is('/') ? 'active fw-bold' : '' }}" href="/">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link {{ request()->is('courses*') ? 'active fw-bold' : '' }}" href="/courses">{{ app()->getLocale() == 'ar' ? 'الدورات' : 'Courses' }}</a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link {{ request()->is('instructors*') ? 'active fw-bold' : '' }}" href="/instructors">{{ app()->getLocale() == 'ar' ? 'المدربون' : 'Instructors' }}</a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link {{ request()->is('about*') ? 'active fw-bold' : '' }}" href="/about">{{ app()->getLocale() == 'ar' ? 'عن المنصة' : 'About' }}</a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link {{ request()->is('contact*') ? 'active fw-bold' : '' }}" href="/contact">{{ app()->getLocale() == 'ar' ? 'اتصل بنا' : 'Contact' }}</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center mt-2 mt-md-0">
                <!-- Language Switcher -->
                <div class="dropdown language-dropdown me-2">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe me-1"></i> {{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                        <li><a class="dropdown-item" href="#">English</a></li>
                        <li><a class="dropdown-item" href="#">العربية</a></li>
                    </ul>
                </div>

                @guest
                    <a href="{{ url('/login') }}" class="btn btn-outline-primary btn-sm px-3 me-2">{{ app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Login' }}</a>
                    <a href="{{ url('/register') }}" class="btn btn-primary btn-sm px-3">{{ app()->getLocale() == 'ar' ? 'إنشاء حساب' : 'Sign Up' }}</a>
                @else
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            @if(Auth::user()->hasRole('admin'))
                                <li><a class="dropdown-item" href="{{ url('/admin') }}"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            @if(Auth::user()->hasRole('instructor'))
                                <li><a class="dropdown-item" href="{{ route('instructor.dashboard') }}"><i class="fas fa-chalkboard-teacher me-2"></i>Instructor Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('student.profile.index') }}"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('student.my-courses') }}"><i class="fas fa-graduation-cap me-2"></i>My Courses</a></li>
                            <li><a class="dropdown-item" href="{{ route('chats.index') }}"><i class="fas fa-comments me-2"></i>Group Chats</a></li>
                            @if(Auth::user()->hasRole('student'))
                            <li><a class="dropdown-item" href="{{ route('student.exams.index') }}"><i class="fas fa-file-alt me-2"></i>My Exams</a></li>
                            <li><a class="dropdown-item" href="{{ route('student.messages.index') }}"><i class="fas fa-comments me-2"></i>Messages</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ url('/logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>

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