<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Learning Platform</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Admin CSS -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    <!-- Custom Theme -->
    <link href="{{ asset('css/custom-theme.css') }}" rel="stylesheet">

    <!-- Additional CSS -->
    @yield('styles')
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <div class="border-end bg-primary text-white" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom py-4 px-3">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-white">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <span class="fs-5 fw-bold">Admin Panel</span>
                </a>
            </div>
            <div class="list-group list-group-flush sidebar-nav">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>

                <!-- Notifications -->
                <a href="{{ route('admin.notifications.index') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
                    <i class="fas fa-bell me-2"></i> Notifications
                    @php
                    $unreadCount = \App\Models\AdminNotification::where('is_read', false)->count();
                    @endphp
                    @if($unreadCount > 0)
                    <span class="badge bg-danger rounded-pill ms-2">{{ $unreadCount }}</span>
                    @endif
                </a>

                <!-- Messages -->
                <a href="{{ route('admin.messages.index') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.messages*') ? 'active' : '' }}">
                    <i class="fas fa-envelope me-2"></i> Messages
                    @php
                    $adminId = Auth::user()->user_id;
                    $unreadMessagesCount = \App\Models\DirectMessage::where('receiver_id', $adminId)->where('is_read', false)->count();
                    @endphp
                    @if($unreadMessagesCount > 0)
                    <span class="badge bg-danger rounded-pill ms-2">{{ $unreadMessagesCount }}</span>
                    @endif
                </a>

                <!-- User Management Dropdown -->
                <div class="nav-item dropdown">
                    <a href="#" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 dropdown-toggle {{ request()->routeIs('admin.users*') || request()->routeIs('admin.instructor.verification*') || request()->routeIs('admin.parent-verifications*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#userManagementSubmenu" aria-expanded="{{ request()->routeIs('admin.users*') || request()->routeIs('admin.instructor.verification*') || request()->routeIs('admin.parent-verifications*') ? 'true' : 'false' }}">
                        <i class="fas fa-users me-2"></i> User Management
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.users*') || request()->routeIs('admin.instructor.verification*') || request()->routeIs('admin.parent-verifications*') ? 'show' : '' }}" id="userManagementSubmenu">
                        <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action py-2 ps-5 bg-transparent text-white border-0 {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                            <i class="fas fa-user-friends me-2"></i> All Users
                        </a>
                        <a href="{{ route('admin.instructor.verifications') }}" class="list-group-item list-group-item-action py-2 ps-5 bg-transparent text-white border-0 {{ request()->routeIs('admin.instructor.verification*') ? 'active' : '' }}">
                            <i class="fas fa-user-check me-2"></i> Instructor Verifications
                        </a>
                        <a href="{{ route('admin.parent-verifications.index') }}" class="list-group-item list-group-item-action py-2 ps-5 bg-transparent text-white border-0 {{ request()->routeIs('admin.parent-verifications*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog me-2"></i> Parent Verifications
                        </a>
                    </div>
                </div>

                <!-- Course Management Dropdown -->
                <div class="nav-item dropdown">
                    <a href="#" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 dropdown-toggle {{ request()->routeIs('admin.courses') || request()->routeIs('admin.course.approvals') || request()->routeIs('admin.categories') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#courseManagementSubmenu" aria-expanded="{{ request()->routeIs('admin.courses') || request()->routeIs('admin.course.approvals') || request()->routeIs('admin.categories') ? 'true' : 'false' }}">
                        <i class="fas fa-book me-2"></i> Course Management
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.courses') || request()->routeIs('admin.course.approvals') || request()->routeIs('admin.categories') ? 'show' : '' }}" id="courseManagementSubmenu">
                        <a href="{{ route('admin.courses') }}" class="list-group-item list-group-item-action py-2 ps-5 bg-transparent text-white border-0 {{ request()->routeIs('admin.courses') ? 'active' : '' }}">
                            <i class="fas fa-book-open me-2"></i> All Courses
                        </a>
                        <a href="{{ route('admin.course.approvals') }}" class="list-group-item list-group-item-action py-2 ps-5 bg-transparent text-white border-0 {{ request()->routeIs('admin.course.approvals') ? 'active' : '' }}">
                            <i class="fas fa-check-circle me-2"></i> Course Approvals
                            @if(isset($pendingApprovalCount) && $pendingApprovalCount > 0)
                                <span class="badge bg-danger ms-2">{{ $pendingApprovalCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.categories') }}" class="list-group-item list-group-item-action py-2 ps-5 bg-transparent text-white border-0 {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                            <i class="fas fa-folder me-2"></i> Categories
                        </a>
                    </div>
                </div>

                <!-- Marketing Dropdown -->
                <div class="nav-item dropdown">
                    <a href="#" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 dropdown-toggle {{ request()->routeIs('admin.coupons*') || request()->routeIs('admin.discounts*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#marketingSubmenu" aria-expanded="{{ request()->routeIs('admin.coupons*') || request()->routeIs('admin.discounts*') ? 'true' : 'false' }}">
                        <i class="fas fa-bullhorn me-2"></i> Marketing
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.coupons*') || request()->routeIs('admin.discounts*') ? 'show' : '' }}" id="marketingSubmenu">
                        <a href="{{ route('admin.coupons.index') }}" class="list-group-item list-group-item-action py-2 ps-5 bg-transparent text-white border-0 {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}">
                            <i class="fas fa-tags me-2"></i> Coupons
                        </a>
                        <a href="{{ route('admin.discounts.index') }}" class="list-group-item list-group-item-action py-2 ps-5 bg-transparent text-white border-0 {{ request()->routeIs('admin.discounts*') ? 'active' : '' }}">
                            <i class="fas fa-percent me-2"></i> Discounts
                        </a>
                    </div>
                </div>

                <!-- Website Appearance -->
                <a href="{{ route('admin.website-appearance') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.website-appearance*') ? 'active' : '' }}">
                    <i class="fas fa-paint-brush me-2"></i> Website Appearance
                </a>

                <!-- Reports -->
                <a href="{{ route('admin.reports') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                    <i class="fas fa-chart-line me-2"></i> Reports
                </a>

                <!-- Settings -->
                <a href="{{ route('admin.settings') }}" class="list-group-item list-group-item-action p-3 bg-transparent text-white border-0 {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog me-2"></i> Settings
                </a>
            </div>
        </div>

        <!-- Page Content Wrapper -->
        <div id="page-content-wrapper">
            <!-- Top navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-sm btn-primary rounded-circle d-flex align-items-center justify-content-center" id="sidebarToggle" style="width: 38px; height: 38px;">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 ms-3 fw-bold text-primary">@yield('page-title', 'Dashboard')</h5>

                    <div class="ms-auto d-flex align-items-center">
                        <!-- Search Form -->
                        <form class="d-none d-md-flex me-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="search" class="form-control form-control-sm bg-light border-0" placeholder="Search..." aria-label="Search">
                            </div>
                        </form>

                        <!-- Notifications Dropdown -->
                        <div class="dropdown me-3">
                            <a class="btn btn-light rounded-circle position-relative d-flex align-items-center justify-content-center" href="#" role="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 38px; height: 38px;">
                                <i class="fas fa-bell"></i>
                                @php
                                $unreadCount = \App\Models\AdminNotification::where('is_read', false)->count();
                                @endphp
                                @if($unreadCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    <span class="visually-hidden">unread notifications</span>
                                </span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 overflow-hidden p-0" aria-labelledby="notificationsDropdown" style="width: 320px;">
                                <div class="p-3 bg-primary text-white">
                                    <h6 class="mb-0 d-flex justify-content-between align-items-center">
                                        <span>Notifications</span>
                                        <a href="{{ route('admin.notifications.index') }}" class="text-white text-decoration-none small">View All</a>
                                    </h6>
                                </div>
                                <div class="p-2">
                                    @php
                                    $latestNotifications = \App\Models\AdminNotification::latest()->take(5)->get();
                                    @endphp
                                    @if($latestNotifications->count() > 0)
                                        @foreach($latestNotifications as $notification)
                                        <a href="{{ route('admin.notifications.show', $notification->id) }}" class="dropdown-item px-3 py-2 rounded-3 mb-1 {{ $notification->is_read ? '' : 'bg-light' }}">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-{{ $notification->icon ?? 'bell' }} text-{{ $notification->severity == 'high' ? 'danger' : ($notification->severity == 'medium' ? 'warning' : 'info') }}"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-0 fw-semibold">{{ Str::limit($notification->title, 30) }}</h6>
                                                    <p class="mb-0 small text-muted">{{ Str::limit($notification->message, 60) }}</p>
                                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </a>
                                        @endforeach
                                    @else
                                        <p class="text-center text-muted my-3">No notifications</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <a class="d-flex align-items-center text-decoration-none dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px; border-radius: 50%;">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="d-none d-md-block">
                                    <div class="fw-semibold text-dark small">{{ Auth::user()->name }}</div>
                                    <div class="text-muted small">Administrator</div>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="fas fa-user-circle me-2 text-primary"></i> My Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="fas fa-cog me-2 text-primary"></i> Settings</a></li>
                                <li><a class="dropdown-item" href="{{ url('/') }}" target="_blank"><i class="fas fa-home me-2 text-primary"></i> Visit Website</a></li>
                                <li><hr class="dropdown-divider" /></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2 text-primary"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid py-4">
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

            <!-- Footer -->
            <footer class="bg-white py-3 border-top mt-auto">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center text-md-start">
                            <p class="text-muted mb-0">&copy; {{ date('Y') }} Learning Platform. All rights reserved.</p>
                        </div>
                        <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                            <a href="#" class="text-decoration-none text-muted me-3"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-decoration-none text-muted me-3"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-decoration-none text-muted me-3"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-decoration-none text-muted"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (for some components) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Admin Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Create sidebar overlay element
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);

            // Toggle sidebar
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    document.getElementById('wrapper').classList.toggle('toggled');

                    // Add/remove overlay for mobile devices
                    if (window.innerWidth < 992) {
                        document.body.classList.toggle('sidebar-open');
                    }
                });
            }

            // Close sidebar when clicking on overlay
            overlay.addEventListener('click', function() {
                document.getElementById('wrapper').classList.add('toggled');
                document.body.classList.remove('sidebar-open');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 992 &&
                    document.body.classList.contains('sidebar-open') &&
                    !document.getElementById('sidebar-wrapper').contains(event.target) &&
                    !document.getElementById('sidebarToggle').contains(event.target)) {
                    document.getElementById('wrapper').classList.add('toggled');
                    document.body.classList.remove('sidebar-open');
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    document.body.classList.remove('sidebar-open');
                }
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Add active class to parent dropdown if child is active
            const activeDropdownItems = document.querySelectorAll('.sidebar .collapse .list-group-item.active');
            activeDropdownItems.forEach(function(item) {
                const parentCollapse = item.closest('.collapse');
                if (parentCollapse) {
                    const parentToggle = document.querySelector(`[data-bs-target="#${parentCollapse.id}"]`);
                    if (parentToggle) {
                        parentToggle.classList.add('active');
                        parentCollapse.classList.add('show');
                    }
                }
            });

            // Smooth animation for dropdowns
            const dropdownToggles = document.querySelectorAll('.sidebar .dropdown-toggle');
            dropdownToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const target = this.getAttribute('data-bs-target');
                    const targetElement = document.querySelector(target);

                    // Toggle aria-expanded attribute
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !isExpanded);

                    // Toggle the collapse
                    if (targetElement) {
                        targetElement.classList.toggle('show');
                    }
                });
            });

            // Make tables responsive on mobile
            const tables = document.querySelectorAll('table:not(.table-responsive):not(.table-responsive-horizontal):not(.table-responsive-card):not(.table-responsive-stack)');
            tables.forEach(function(table) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            });

            // Add data-label attributes to responsive card tables
            const cardTables = document.querySelectorAll('.table-responsive-card');
            cardTables.forEach(function(table) {
                const headerCells = table.querySelectorAll('thead th');
                const headerTexts = Array.from(headerCells).map(cell => cell.textContent.trim());

                const bodyRows = table.querySelectorAll('tbody tr');
                bodyRows.forEach(function(row) {
                    const cells = row.querySelectorAll('td');
                    cells.forEach(function(cell, index) {
                        if (headerTexts[index]) {
                            cell.setAttribute('data-label', headerTexts[index]);
                        }
                    });
                });
            });

            // Add data-label attributes to responsive stack tables
            const stackTables = document.querySelectorAll('.table-responsive-stack');
            stackTables.forEach(function(table) {
                const headerCells = table.querySelectorAll('thead th');
                const headerTexts = Array.from(headerCells).map(cell => cell.textContent.trim());

                const bodyRows = table.querySelectorAll('tbody tr');
                bodyRows.forEach(function(row) {
                    const cells = row.querySelectorAll('td');
                    cells.forEach(function(cell, index) {
                        if (headerTexts[index]) {
                            cell.setAttribute('data-label', headerTexts[index]);
                        }
                    });
                });
            });

            // Fix input groups on mobile
            if (window.innerWidth <= 576) {
                const inputGroups = document.querySelectorAll('.input-group');
                inputGroups.forEach(function(group) {
                    group.classList.add('input-group-mobile');
                });
            }
        });
    </script>

    <!-- Page-specific scripts -->
    @yield('scripts')
</body>
</html>