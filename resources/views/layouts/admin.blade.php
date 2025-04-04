<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Dashboard</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    
    <!-- Additional CSS -->
    @yield('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="border-end bg-dark text-white" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom bg-primary text-white py-4 px-3">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-white">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <span class="fs-5 fw-bold">Admin Panel</span>
                </a>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="fas fa-users me-2"></i> Users
                </a>
                <a href="{{ route('admin.instructor.verifications') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.instructor.verification*') ? 'active' : '' }}">
                    <i class="fas fa-user-check me-2"></i> Instructor Verifications
                </a>
                <a href="{{ route('admin.courses') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.courses') ? 'active' : '' }}">
                    <i class="fas fa-book me-2"></i> Courses
                </a>
                <a href="{{ route('admin.course.approvals') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.course.approvals') ? 'active' : '' }}">
                    <i class="fas fa-check-circle me-2"></i> Course Approvals
                </a>
                <a href="{{ route('admin.categories') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                    <i class="fas fa-folder me-2"></i> Categories
                </a>
                <a href="{{ route('admin.reports') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                    <i class="fas fa-chart-line me-2"></i> Reports
                </a>
                <a href="{{ route('admin.settings') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog me-2"></i> Settings
                </a>
            </div>
        </div>
        <!-- Page Content Wrapper -->
        <div id="page-content-wrapper">
            <!-- Top navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-sm btn-outline-primary" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 ms-3">@yield('page-title', 'Dashboard')</h5>
                    <div class="ms-auto d-flex align-items-center">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle text-dark" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ url('/') }}" target="_blank"><i class="fas fa-home me-2"></i> Visit Website</a></li>
                                <li><hr class="dropdown-divider" /></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Main content container -->
            <div class="container-fluid p-4">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <footer class="bg-light py-3 border-top">
                <div class="container-fluid text-center">
                    <p class="text-muted mb-0">&copy; {{ date('Y') }} Learning Platform. All rights reserved.</p>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (for some components) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom Admin Scripts -->
    <script>
        // Toggle sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.getElementById('wrapper').classList.toggle('toggled');
                });
            }
        });
    </script>
    
    <!-- Page-specific scripts -->
    @yield('scripts')
</body>
</html> 