<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel App')</title>
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Arabic Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <!-- Bootstrap RTL CSS (conditionally loaded) -->
    @if(app()->getLocale() == 'ar')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @endif
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        /* Dark mode styles */
        .dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }

        .dark-mode .navbar:not(.navbar-transparent) {
            background-color: #1e1e1e !important;
            color: #e0e0e0;
        }

        .dark-mode .navbar:not(.navbar-transparent) .nav-link {
            color: #e0e0e0 !important;
        }

        .dark-mode .card, .dark-mode .feature-card, .dark-mode .testimonial {
            background-color: #1e1e1e;
            color: #e0e0e0;
        }

        .dark-mode .text-dark {
            color: #e0e0e0 !important;
        }

        .dark-mode .bg-white, .dark-mode .bg-light {
            background-color: #1e1e1e !important;
        }

        .dark-mode .border {
            border-color: #333 !important;
        }

        /* Fix for navbar visibility */
        .navbar {
            z-index: 1030;
            padding: 0.5rem 1rem;
        }
        
        .navbar-nav {
            margin: 0 !important;
        }
        
        .navbar .nav-link {
            padding: 0.5rem 0.75rem !important;
            font-weight: 500;
        }
        
        .navbar .nav-item {
            margin-bottom: 0 !important;
        }
        
        @media (max-width: 768px) {
            .navbar .container {
                flex-direction: column;
                align-items: flex-start;
                padding: 0.5rem;
            }
            
            .navbar-nav {
                flex-wrap: wrap;
                margin-top: 0.5rem !important;
            }
            
            .navbar .navbar-brand {
                margin-bottom: 0.5rem;
            }
        }

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
            --font-arabic: 'Cairo', 'Tajawal', 'IBM Plex Sans Arabic', sans-serif;
        }

        body {
            padding-top: 60px; /* Add padding to account for fixed navbar */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', var(--font-arabic), sans-serif;
            overflow-x: hidden;
            background-color: #fcfcfc;
            color: #333;
        }

        /* RTL specific adjustments */
        html[dir="rtl"] body {
            font-family: var(--font-arabic), 'Poppins', sans-serif;
            text-align: right;
        }

        html[dir="rtl"] .me-auto {
            margin-right: 0 !important;
            margin-left: auto !important;
        }

        html[dir="rtl"] .ms-auto {
            margin-left: 0 !important;
            margin-right: auto !important;
        }

        html[dir="rtl"] .me-2, html[dir="rtl"] .me-3 {
            margin-right: 0 !important;
            margin-left: 0.5rem !important;
        }

        html[dir="rtl"] .ms-2, html[dir="rtl"] .ms-3 {
            margin-left: 0 !important;
            margin-right: 0.5rem !important;
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
            transition: all 0.4s ease;
        }

        .navbar-transparent .navbar-brand {
            color: #fff;
            font-weight: 700;
            font-size: 1.8rem;
            position: relative;
            z-index: 1001;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-transparent .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            position: relative;
            z-index: 1001;
            font-weight: 500;
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            transition: all 0.3s ease;
        }

        .navbar-transparent .nav-link:hover,
        .navbar-transparent .nav-link.active {
            color: #fff !important;
            transform: translateY(-2px);
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
        .navbar-transparent .btn-outline-light {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.5);
            position: relative;
            z-index: 1001;
            transition: all 0.3s ease;
            border-radius: 30px;
            font-weight: 500;
        }

        .navbar-transparent .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #fff;
            transform: translateY(-2px);
        }

        .navbar-transparent .btn-light {
            background-color: #fff;
            color: var(--primary-color);
            border-color: #fff;
            position: relative;
            z-index: 1001;
            transition: all 0.3s ease;
            border-radius: 30px;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .navbar-transparent .btn-light:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        /* Language dropdown in navbar */
        .navbar-transparent .language-dropdown .btn {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 30px;
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
            transition: all 0.3s ease;
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

        /* Navbar sticky effect */
        .navbar-sticky {
            padding-top: 10px;
            padding-bottom: 10px;
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            animation: slideDown 0.35s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
            }
            to {
                transform: translateY(0);
            }
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

        .navbar-banner {
            padding: 30px 0;
            color: #333;
        }
        
        .banner-content h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .banner-content p {
            font-size: 1.1rem;
            margin-bottom: 0;
        }
        
        .banner-stats {
            text-align: center;
        }
        
        .stat-item {
            padding: 10px;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: #4361ee;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #666;
        }
        
        @media (max-width: 991px) {
            .banner-content {
                text-align: center;
                margin-bottom: 20px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navigation Bar -->
    @include('layouts.partials.navbar')

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <!-- Dark Mode Toggle Button -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <button onclick="toggleDarkMode()" class="btn btn-sm rounded-circle" style="width: 40px; height: 40px; background-color: var(--dark-color); color: var(--light-color);">
            <i class="fas fa-moon"></i>
        </button>
    </div>

    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4">منصة تعليمية</h5>
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
            © {{ date('Y') }} منصة تعليمية. All rights reserved.
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- CountUp.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.8/countUp.min.js"></script>

    <script>
        // Dark mode toggle functionality
        function toggleDarkMode() {
            const body = document.body;
            const isDarkMode = body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
        }

        // Check for saved dark mode preference
        document.addEventListener('DOMContentLoaded', function() {
            const darkModePreference = localStorage.getItem('darkMode');
            if (darkModePreference === 'enabled') {
                document.body.classList.add('dark-mode');
            }
        });

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
    </script>

    @yield('scripts')
</body>
</html>