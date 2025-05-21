<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Instructor Dashboard') - منصة تعليمية</title>
    <!-- Bootstrap CSS -->
    @if(app()->getLocale() == 'ar')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Instructor Dashboard CSS -->
    <link href="{{ asset('css/instructor.css') }}" rel="stylesheet">
    <!-- Custom Theme CSS -->
    <link href="{{ asset('css/custom-theme.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body data-user-id="{{ Auth::id() ?? '' }}">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="sidebar-header">
                    <h3>منصة تعليمية</h3>
                    <p>بوابة المدرب</p>
                    <div class="education-icon-container">
                        <i class="fas fa-graduation-cap education-icon"></i>
                    </div>
                </div>
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('instructor.dashboard') }}" class="nav-link {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.courses') }}" class="nav-link {{ request()->routeIs('instructor.courses*') && !request()->routeIs('instructor.courses.create') ? 'active' : '' }}">
                                <i class="fas fa-book"></i> My Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.courses.create') }}" class="nav-link {{ request()->routeIs('instructor.courses.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle"></i> Create Course
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.exams.index') }}" class="nav-link {{ request()->routeIs('instructor.exams*') ? 'active' : '' }}">
                                <i class="fas fa-file-alt"></i> Exams
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.quizzes.index') }}" class="nav-link {{ request()->routeIs('instructor.quizzes*') ? 'active' : '' }}">
                                <i class="fas fa-question-circle"></i> Quizzes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.books.index') }}" class="nav-link {{ request()->routeIs('instructor.books*') ? 'active' : '' }}">
                                <i class="fas fa-book"></i> Books
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.earnings.index') }}" class="nav-link {{ request()->routeIs('instructor.earnings*') ? 'active' : '' }}">
                                <i class="fas fa-dollar-sign"></i> Earnings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.coupons.index') }}" class="nav-link {{ request()->routeIs('instructor.coupons*') ? 'active' : '' }}">
                                <i class="fas fa-tags"></i> Coupons
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.discounts.index') }}" class="nav-link {{ request()->routeIs('instructor.discounts*') ? 'active' : '' }}">
                                <i class="fas fa-percent"></i> Discounts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.reports') }}" class="nav-link {{ request()->routeIs('instructor.reports') ? 'active' : '' }}">
                                <i class="fas fa-chart-bar"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.messages.index') }}" class="nav-link {{ request()->routeIs('instructor.messages*') ? 'active' : '' }}">
                                <i class="fas fa-comments"></i> Messages
                                @php
                                    $unreadMessages = App\Models\DirectMessage::where('receiver_id', Auth::id())
                                        ->where('is_read', false)
                                        ->count();
                                @endphp
                                @if($unreadMessages > 0)
                                    <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.7rem;">{{ $unreadMessages }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('chats.index') }}" class="nav-link {{ request()->routeIs('chats.*') ? 'active' : '' }}">
                                <i class="fas fa-users"></i> Group Chats
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.profile.index') }}" class="nav-link {{ request()->routeIs('instructor.profile.*') ? 'active' : '' }}">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a href="/" class="nav-link">
                                <i class="fas fa-home"></i> Back to Site
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link bg-transparent border-0">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-0 content-main">
                <!-- Header -->
                <div class="instructor-header">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <button class="menu-toggle me-3" id="sidebarToggle">
                                    <i class="fas fa-bars"></i>
                                </button>
                                <h1 class="h4 mb-0">@yield('page-title', 'Dashboard')</h1>
                            </div>
                            <div class="d-flex align-items-center">
                                <!-- Notifications -->
                                <div class="dropdown me-3">
                                    <a href="#" class="text-white position-relative" id="dropdownNotifications" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-bell fs-5"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            3
                                            <span class="visually-hidden">unread notifications</span>
                                        </span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end p-0 overflow-hidden" aria-labelledby="dropdownNotifications" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                        <div class="p-3 bg-primary text-white border-bottom">
                                            <h6 class="mb-0">الإشعارات</h6>
                                        </div>
                                        <div class="list-group list-group-flush">
                                            <a href="#" class="list-group-item list-group-item-action p-3">
                                                <div class="d-flex w-100 align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notification-icon bg-success-subtle text-success">
                                                            <i class="fas fa-user-graduate"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <p class="mb-1 text-dark">طالب جديد مسجل في دورتك "تطوير الويب"</p>
                                                        <small class="text-muted">منذ 5 دقائق</small>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action p-3">
                                                <div class="d-flex w-100 align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notification-icon bg-warning-subtle text-warning">
                                                            <i class="fas fa-star"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <p class="mb-1 text-dark">تقييم جديد (4 نجوم) لدورتك "أساسيات البرمجة"</p>
                                                        <small class="text-muted">منذ ساعة</small>
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="#" class="list-group-item list-group-item-action p-3">
                                                <div class="d-flex w-100 align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notification-icon bg-info-subtle text-info">
                                                            <i class="fas fa-comment"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <p class="mb-1 text-dark">رسالة جديدة من الإدارة</p>
                                                        <small class="text-muted">منذ 3 ساعات</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="p-2 border-top text-center">
                                            <a href="#" class="text-primary small fw-semibold text-decoration-none">عرض كل الإشعارات</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Profile -->
                                <div class="dropdown">
                                    <a href="#" class="text-white dropdown-toggle text-decoration-none d-flex align-items-center" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                        @if(Auth::user()->profile_image)
                                            <img src="{{ Auth::user()->profile_image }}" alt="{{ Auth::user()->name }}" class="profile-img">
                                        @else
                                            <div class="default-profile-icon">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                        <span class="ms-2 d-none d-sm-inline-block">{{ Auth::user()->name }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="dropdownUser">
                                        <li class="dropdown-header text-center">
                                            <div class="d-flex flex-column align-items-center">
                                                @if(Auth::user()->profile_image)
                                                    <img src="{{ Auth::user()->profile_image }}" alt="{{ Auth::user()->name }}" class="rounded-circle mb-2" width="60" height="60">
                                                @else
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                                                        <i class="fas fa-user fa-2x"></i>
                                                    </div>
                                                @endif
                                                <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                                <small class="text-muted">{{ Auth::user()->email }}</small>
                                            </div>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('instructor.profile.index') }}"><i class="fas fa-user me-2 text-primary"></i> الملف الشخصي</a></li>
                                        <li><a class="dropdown-item" href="{{ route('instructor.earnings.index') }}"><i class="fas fa-dollar-sign me-2 text-success"></i> الأرباح</a></li>
                                        <li><a class="dropdown-item" href="{{ route('instructor.courses') }}"><i class="fas fa-book me-2 text-info"></i> دوراتي</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2 text-danger"></i> تسجيل الخروج</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Wrapper -->
                <div class="content-wrapper">
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

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Set up CSRF protection for fetch API
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Configure fetch defaults for all requests
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            // If it's a POST/PUT/DELETE request and no headers were defined for CSRF, add them
            if (options.method && ['POST', 'PUT', 'DELETE'].includes(options.method.toUpperCase())) {
                if (!options.headers) {
                    options.headers = {};
                }

                // Ensure object not Headers instance
                if (!(options.headers instanceof Headers)) {
                    if (!options.headers['X-CSRF-TOKEN'] && !options.headers['x-csrf-token']) {
                        options.headers['X-CSRF-TOKEN'] = csrfToken;
                    }
                    if (!options.headers['X-Requested-With'] && !options.headers['x-requested-with']) {
                        options.headers['X-Requested-With'] = 'XMLHttpRequest';
                    }
                }
            }

            return originalFetch(url, options);
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle functionality for mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle.contains(event.target);

                if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                }
            });

            // Add animation classes to elements
            document.querySelectorAll('.stat-card').forEach((card, index) => {
                card.setAttribute('data-aos', 'fade-up');
                card.setAttribute('data-aos-delay', (index * 100).toString());
            });

            document.querySelectorAll('.chart-card, .table-card').forEach((card, index) => {
                card.setAttribute('data-aos', 'fade-up');
                card.setAttribute('data-aos-delay', ((index + 4) * 100).toString());
            });

            // Refresh AOS after dynamic content is loaded
            document.addEventListener('contentLoaded', function() {
                AOS.refresh();
            });
        });
    </script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- Page specific scripts -->
    @yield('scripts')
</body>
</html>