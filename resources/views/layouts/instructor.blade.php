<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Instructor Dashboard') - E-Learning</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Instructor Dashboard CSS -->
    <link href="{{ asset('css/instructor.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="sidebar-header">
                    <h3>E-Learning</h3>
                    <p>Instructor Portal</p>
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
                            <a href="{{ route('instructor.earnings.index') }}" class="nav-link {{ request()->routeIs('instructor.earnings*') ? 'active' : '' }}">
                                <i class="fas fa-dollar-sign"></i> Earnings
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
                                <div class="dropdown">
                                    <a href="#" class="text-white dropdown-toggle text-decoration-none" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                        @if(Auth::user()->profile_image)
                                            <img src="{{ asset(Auth::user()->profile_image) }}" alt="{{ Auth::user()->name }}" class="profile-img">
                                        @else
                                            <div class="default-profile-icon">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                        <span class="ms-2 d-none d-sm-inline-block">{{ Auth::user()->name }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                                        <li><a class="dropdown-item" href="{{ route('instructor.profile.index') }}"><i class="fas fa-user me-2"></i> My Profile</a></li>
                                        <li><a class="dropdown-item" href="{{ route('instructor.earnings.index') }}"><i class="fas fa-dollar-sign me-2"></i> My Earnings</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
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
    <script>
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
        });
    </script>
    @yield('scripts')
</body>
</html>