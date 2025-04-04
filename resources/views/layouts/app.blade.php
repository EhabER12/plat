<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel App')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3bc454;
            --accent-color: #f6c23e;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --gradient-primary: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --shadow-standard: 0 5px 15px rgba(0,0,0,0.08);
            --shadow-elevated: 0 10px 30px rgba(0,0,0,0.15);
            --transition-standard: all 0.3s ease;
            --teal-gradient: linear-gradient(135deg, #41cdcd 0%, #2bc9c9 50%, #20b7b7 100%);
        }
        
        body {
            padding-top: 0; /* Remove top padding to allow hero to start at the top */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            background-color: #fcfcfc;
            color: #333;
        }
        
        main {
            flex: 1;
            position: relative;
        }
        
        footer {
            padding: 30px 0;
            background-color: var(--dark-color);
            color: var(--light-color);
        }
        
        /* Transparent navbar styling */
        .navbar-transparent {
            background-color: transparent !important;
            box-shadow: none !important;
            position: absolute;
            width: 100%;
            z-index: 1000;
            padding-top: 20px;
            padding-bottom: 20px;
        }
        
        .navbar-transparent .navbar-brand {
            color: #fff;
            font-weight: 700;
            position: relative;
            z-index: 1001;
        }
        
        .navbar-transparent .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            position: relative;
            z-index: 1001;
        }
        
        .navbar-transparent .nav-link:hover,
        .navbar-transparent .nav-link.active {
            color: #fff !important;
        }
        
        .navbar-transparent .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.5);
            position: relative;
            z-index: 1001;
        }
        
        .navbar-transparent .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        /* Buttons in transparent navbar */
        .navbar-transparent .btn-outline-primary {
            color: #fff;
            border-color: #fff;
            position: relative;
            z-index: 1001;
        }
        
        .navbar-transparent .btn-primary {
            background-color: #fff;
            color: #20b7b7;
            border-color: #fff;
            position: relative;
            z-index: 1001;
        }
        
        /* For pages other than home, we'll apply a solid navbar background */
        .navbar-solid {
            background-color: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
        }
        
        .navbar {
            box-shadow: var(--shadow-standard);
            transition: var(--transition-standard);
            background: #fff;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-link {
            position: relative;
            margin: 0 5px;
        }
        
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            left: 0;
            bottom: -2px;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover:after, .nav-link.active:after {
            width: 100%;
        }
        
        /* Card styling */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow-standard);
            transition: var(--transition-standard);
            overflow: hidden;
        }
        
        .card:hover {
            box-shadow: var(--shadow-elevated);
            transform: translateY(-5px);
        }
        
        /* Button styling */
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
            transition: var(--transition-standard);
        }
        
        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
            transform: translateY(-2px);
        }
        
        /* Animation classes */
        .zoom-in {
            transition: transform 0.3s ease;
        }
        
        .zoom-in:hover {
            transform: scale(1.05);
        }
        
        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        
        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Jumbotron styling */
        .jumbotron {
            position: relative;
            background: var(--gradient-primary);
            color: white;
            border-radius: 15px;
            padding: 60px 30px;
            margin-bottom: 60px;
            overflow: hidden;
            box-shadow: var(--shadow-elevated);
        }
        
        .jumbotron::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-image: url('https://images.unsplash.com/photo-1501504905252-473c47e087f8?q=80&w=1374&auto=format&fit=crop&ixlib=rb-4.0.3');
            background-size: cover;
            background-position: center;
            opacity: 0.1;
        }
        
        .jumbotron .content {
            position: relative;
            z-index: 2;
        }
        
        /* Category styling */
        .category-card {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient-primary);
            opacity: 0.8;
            z-index: 1;
        }
        
        .category-card .content {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
        }
        
        /* Feature card */
        .feature-card {
            text-align: center;
            padding: 30px 20px;
        }
        
        .feature-card i {
            font-size: 3rem;
            margin-bottom: 20px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .jumbotron {
                padding: 40px 20px;
            }
        }
        
        .hero-section {
            background: linear-gradient(135deg, #4e73df 0%, #3b5bdb 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml;charset=utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"%3E%3Cpath fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,122.7C96,149,192,203,288,197.3C384,192,480,128,576,117.3C672,107,768,149,864,165.3C960,181,1056,171,1152,154.7C1248,139,1344,117,1392,106.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"%3E%3C/path%3E%3C/svg%3E');
            background-size: cover;
            background-position: center;
            opacity: 0.2;
        }
        
        .hero-text {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 300;
            margin-bottom: 2rem;
        }
        
        .feature-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .feature-card::before {
            content: "";
            position: absolute;
            top: -100px;
            left: -100px;
            width: 200px;
            height: 200px;
            background-color: rgba(78, 115, 223, 0.05);
            border-radius: 50%;
            transition: all 0.5s ease;
        }
        
        .feature-card:hover::before {
            transform: scale(3);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
            position: relative;
            z-index: 2;
        }
        
        .course-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .course-card .card-img-top {
            height: 180px;
            object-fit: cover;
        }
        
        .course-price {
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stats-counter {
            text-align: center;
            padding: 30px 0;
            background-color: var(--light-color);
            margin: 40px 0;
        }
        
        .counter-item {
            padding: 20px;
        }
        
        .counter-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .counter-title {
            font-size: 1.1rem;
            color: var(--dark-color);
        }
        
        .testimonial {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin: 20px 0;
            position: relative;
        }
        
        .testimonial::before {
            content: """;
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 5rem;
            color: rgba(78, 115, 223, 0.1);
            font-family: sans-serif;
            line-height: 1;
        }
        
        .testimonial-content {
            padding-left: 20px;
            position: relative;
            z-index: 2;
        }
        
        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { transform: translateX(-50px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .animated-fadeIn {
            animation: fadeIn 1s ease forwards;
        }
        
        .animated-slideIn {
            animation: slideIn 1s ease forwards;
        }
        
        .animated-pulse {
            animation: pulse 2s infinite;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg {{ request()->is('/') ? 'navbar-transparent' : 'navbar-light bg-white shadow-sm fixed-top' }}">
        <div class="container">
            <a class="navbar-brand" href="/">
                <span class="fw-bold">E-Learning</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active fw-bold' : '' }}" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('courses*') ? 'active fw-bold' : '' }}" href="/courses">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('about*') ? 'active fw-bold' : '' }}" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('contact*') ? 'active fw-bold' : '' }}" href="/contact">Contact</a>
                    </li>
                </ul>
                <div class="d-flex gap-2">
                    @guest
                        <a href="{{ url('/login') }}" class="btn btn-outline-primary">Login</a>
                        <a href="{{ url('/register') }}" class="btn btn-primary">Sign Up</a>
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
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>My Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-graduation-cap me-2"></i>My Courses</a></li>
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

    <!-- Main Content -->
    <main class="container py-4">
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
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4">E-Learning</h5>
                    <p>Empowering learners worldwide with high-quality online education and skill development opportunities.</p>
                    <div class="social-icons mt-4">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/" class="text-white text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="/courses" class="text-white text-decoration-none">Courses</a></li>
                        <li class="mb-2"><a href="/about" class="text-white text-decoration-none">About Us</a></li>
                        <li class="mb-2"><a href="/contact" class="text-white text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4">Categories</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Web Development</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Data Science</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Mobile Development</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Digital Marketing</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Graphic Design</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4">Contact</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i> 123 Education St, Learning City</p>
                    <p><i class="fas fa-phone me-2"></i> (123) 456-7890</p>
                    <p><i class="fas fa-envelope me-2"></i> info@elearning.com</p>
                </div>
            </div>
        </div>
        <div class="text-center p-3 mt-3" style="background-color: rgba(0, 0, 0, 0.2);">
            © {{ date('Y') }} E-Learning. All rights reserved.
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- CountUp.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.8/countUp.min.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        
        // Initialize CountUp for stat counters
        document.addEventListener('DOMContentLoaded', function() {
            const counterElements = document.querySelectorAll('.counter-value');
            
            if (counterElements.length > 0) {
                counterElements.forEach(function(el) {
                    const countUpObj = new CountUp(el, el.getAttribute('data-value'), {
                        duration: 2.5,
                        separator: ',',
                        decimal: '.'
                    });
                    
                    // Check if visible in viewport
                    const checkIfVisible = function() {
                        const rect = el.getBoundingClientRect();
                        const windowHeight = window.innerHeight || document.documentElement.clientHeight;
                        
                        if (rect.top <= windowHeight && rect.bottom >= 0) {
                            countUpObj.start();
                            window.removeEventListener('scroll', checkIfVisible);
                        }
                    };
                    
                    // Initial check
                    checkIfVisible();
                    
                    // Listen for scroll
                    window.addEventListener('scroll', checkIfVisible);
                });
            }
        });

        // Add scroll event listener for navbar effects
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-sm', 'navbar-sticky');
            } else {
                navbar.classList.remove('shadow-sm', 'navbar-sticky');
            }
        });

        // Custom JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Check if on the home page
            const isHomePage = window.location.pathname === '/' || window.location.pathname === '';
            const navbar = document.querySelector('.navbar');
            
            if (isHomePage) {
                // Apply immediate transparency
                navbar.classList.add('navbar-transparent');
                navbar.classList.remove('navbar-light', 'bg-white', 'shadow-sm', 'fixed-top');
                
                // Add scroll event listener for navbar effects on home page
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 100) {
                        navbar.classList.remove('navbar-transparent');
                        navbar.classList.add('navbar-light', 'bg-white', 'shadow-sm', 'fixed-top');
                    } else {
                        navbar.classList.add('navbar-transparent');
                        navbar.classList.remove('navbar-light', 'bg-white', 'shadow-sm', 'fixed-top');
                    }
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html> 