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
    <!-- Custom Theme -->
    <link href="{{ asset('css/custom-theme.css') }}" rel="stylesheet">
    <style>
        /* Dark mode styles */
        .dark-mode {
            background-color: #0d1924;
            color: #e0e0e0;
        }

        .dark-mode .navbar:not(.navbar-transparent) {
            background-color: #002244 !important;
            color: #e0e0e0;
        }

        .dark-mode .navbar:not(.navbar-transparent) .nav-link {
            color: #FFD700 !important;
        }

        .dark-mode .card, .dark-mode .feature-card, .dark-mode .testimonial {
            background-color: #002244;
            color: #e0e0e0;
            border-color: #003366;
        }

        .dark-mode .text-dark {
            color: #e0e0e0 !important;
        }

        .dark-mode .bg-white, .dark-mode .bg-light {
            background-color: #002244 !important;
        }

        .dark-mode .border {
            border-color: #003366 !important;
        }

        /* Fix for navbar visibility */
        .navbar {
            z-index: 1030;
            padding: 0.5rem 1rem;
            background-color: var(--primary-color) !important;
        }
        
        .navbar-nav {
            margin: 0 !important;
        }
        
        .navbar .nav-link {
            padding: 0.5rem 0.75rem !important;
            font-weight: 500;
            color: white !important;
        }
        
        .navbar .nav-link:hover {
            color: var(--secondary-color) !important;
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
            --primary-color: #003366;
            --secondary-color: #FFD700;
            --accent-color: #FFD700;
            --dark-color: #003366;
            --light-color: #FAFAFA;
            --gradient-primary: linear-gradient(135deg, #003366 0%, #002244 100%);
            --shadow-standard: 0 5px 15px rgba(0,0,0,0.08);
            --shadow-elevated: 0 10px 30px rgba(0,0,0,0.15);
            --transition-standard: all 0.3s ease;
            --teal-gradient: linear-gradient(135deg, #003366 0%, #004080 50%, #002244 100%);
            --font-arabic: 'Cairo', 'Tajawal', 'IBM Plex Sans Arabic', sans-serif;
            --success-color: #2ECC71;
            --error-color: #E74C3C;
            --background-color: #FAFAFA;
            --text-color: #1F1F1F;
            --border-color: #003366;
            --text-on-primary: #ffffff;
            --text-on-accent: #003366;
        }

        body {
            padding-top: 60px; /* Add padding to account for fixed navbar */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', var(--font-arabic), sans-serif;
            overflow-x: hidden;
            background-color: var(--background-color);
            color: var(--text-color);
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
            color: var(--secondary-color) !important;
            transform: translateY(-2px);
        }

        .navbar-transparent .navbar-toggler {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        /* Navbar Brand */
        .navbar-brand {
            color: var(--secondary-color) !important;
            font-weight: 700;
        }
        
        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: var(--secondary-color);
        }
        
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: #004080;
            border-color: #004080;
            color: var(--secondary-color);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: var(--primary-color);
        }
        
        .btn-secondary:hover, .btn-secondary:focus, .btn-secondary:active {
            background-color: #E6C200;
            border-color: #E6C200;
            color: var(--primary-color);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-danger {
            background-color: var(--error-color);
            border-color: var(--error-color);
        }
        
        /* Form controls */
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 51, 102, 0.25);
        }
        
        /* Cards */
        .card {
            border-color: var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            transition: var(--transition-standard);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-elevated);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
        }
    </style>
    @yield('styles')
</head>
<body data-user-id="{{ Auth::id() ?? '' }}">
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

    @include('layouts.partials.footer')

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