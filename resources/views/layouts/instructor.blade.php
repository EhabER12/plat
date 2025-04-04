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
    <style>
        /* Custom Instructor Dashboard Styles */
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }

        .sidebar-sticky {
            position: sticky;
            top: 20px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 10px 20px;
            margin-bottom: 5px;
            border-radius: 5px;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: #20b7b7;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .instructor-header {
            background-color: #20b7b7;
            color: white;
            padding: 16px 0;
        }

        .content-wrapper {
            padding: 20px;
        }

        .card-stats {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card-stats:hover {
            transform: translateY(-5px);
        }

        .card-stats .icon {
            font-size: 48px;
            margin-bottom: 15px;
            color: #20b7b7;
        }

        .card-stats .count {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .card-stats .label {
            font-size: 14px;
            color: #6c757d;
        }

        .activity-item {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .activity-item:last-child {
            border-bottom: none;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 bg-dark sidebar">
                <div class="text-center mb-4">
                    <h4 class="text-white">E-Learning</h4>
                    <p class="text-white-50">Instructor Portal</p>
                </div>
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('instructor.dashboard') }}" class="nav-link {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.courses') }}" class="nav-link {{ request()->routeIs('instructor.courses*') ? 'active' : '' }}">
                                <i class="fas fa-book"></i> My Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.courses.create') }}" class="nav-link {{ request()->routeIs('instructor.courses.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle"></i> Create Course
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('instructor.reports') }}" class="nav-link {{ request()->routeIs('instructor.reports') ? 'active' : '' }}">
                                <i class="fas fa-chart-bar"></i> Reports & Analytics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-comments"></i> Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
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
            <div class="col-md-9 col-lg-10 ms-sm-auto px-0">
                <!-- Header -->
                <div class="instructor-header">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center">
                            <h1 class="h4 mb-0">@yield('page-title', 'Dashboard')</h1>
                            <div class="d-flex align-items-center">
                                <div class="dropdown">
                                    <a href="#" class="text-white dropdown-toggle text-decoration-none" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" width="32" height="32" class="rounded-circle me-2">
                                        <span>{{ Auth::user()->name }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                                        <li><a class="dropdown-item" href="#">Profile</a></li>
                                        <li><a class="dropdown-item" href="#">Settings</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item">Logout</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
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

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts')
</body>
</html>