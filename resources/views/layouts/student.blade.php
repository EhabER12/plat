<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'منصة تعليمية - لوحة الطالب')</title>
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
    @include('layouts.partials.footer')

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
    </script>

    @yield('scripts')
</body>
</html>
