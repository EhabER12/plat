<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Learning Platform</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Admin CSS -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .admin-container {
            display: flex;
            flex: 1;
        }
        
        .sidebar {
            width: 250px;
            background-color: #212529;
            color: white;
            min-height: 100vh;
            position: fixed;
            padding-top: 56px;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.75rem 1rem;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .sidebar .nav-link.active {
            color: white;
            border-left-color: #0d6efd;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .content {
            flex: 1;
            margin-left: 250px;
            padding: 76px 20px 20px;
        }

        .navbar {
            position: fixed;
            width: 100%;
            z-index: 100;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin">Admin <span>Dashboard</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="search-bar d-none d-md-block">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control" placeholder="Search...">
                </div>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-2">
                        <button class="dark-mode-toggle" title="Toggle Dark Mode">
                            <i class="fas fa-moon"></i>
                        </button>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a>
                    </li>
                    <li class="nav-item dropdown profile-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <span class="profile-name">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ url('/logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.instructor.verification*') ? 'active' : '' }}" href="{{ route('admin.instructor.verifications') }}">
                            <i class="fas fa-user-check"></i> Instructor Verifications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.courses') ? 'active' : '' }}" href="{{ route('admin.courses') }}">
                            <i class="fas fa-book"></i> Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.course.approvals') ? 'active' : '' }}" href="{{ route('admin.course.approvals') }}">
                            <i class="fas fa-check-circle"></i> Course Approvals
                            @if(isset($pendingApprovalCount) && $pendingApprovalCount > 0)
                                <span class="badge bg-danger ms-2">{{ $pendingApprovalCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}" href="{{ route('admin.categories') }}">
                            <i class="fas fa-folder"></i> Categories
                        </a>
                    </li>
                    <hr class="sidebar-divider">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                            <i class="fas fa-chart-line"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                            <i class="fas fa-cogs"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content">
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.querySelector('.navbar-toggler');
            const sidebar = document.querySelector('.sidebar');
            
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
            
            // Dark mode toggle functionality (placeholder)
            const darkModeToggle = document.querySelector('.dark-mode-toggle');
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    document.body.classList.toggle('dark-mode');
                    
                    // Toggle icon between moon and sun
                    const icon = darkModeToggle.querySelector('i');
                    if (icon.classList.contains('fa-moon')) {
                        icon.classList.replace('fa-moon', 'fa-sun');
                    } else {
                        icon.classList.replace('fa-sun', 'fa-moon');
                    }
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html> 