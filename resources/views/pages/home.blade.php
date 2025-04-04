@extends('layouts.app')

@section('title', 'TOTO - Online Learning Platform')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container-fluid px-0">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="hero-content" data-aos="fade-right">
                            <div class="hero-badge">
                                <span class="badge-icon"><i class="fas fa-bolt"></i></span>
                                <span class="badge-text">Online is now much easier</span>
                            </div>
                            <h1 class="hero-title">TOTO is a <span class="text-highlight">modern platform</span> that embraces you to learn in one place</h1>
                            <p class="hero-description">
                                Join our community of learners and instructors to enhance your skills and knowledge.
                            </p>
                            <div class="hero-buttons">
                                <a href="/register" class="btn btn-primary btn-lg">Start for Free</a>
                                <a href="#how-it-works" class="btn btn-outline-secondary btn-lg ms-3">
                                    <i class="fas fa-play-circle me-2"></i> Watch how it works
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="hero-image-wrapper" data-aos="fade-left">
                            <img src="https://img.freepik.com/free-photo/young-student-woman-wearing-denim-jacket-holding-colorful-school-books_1258-26583.jpg" alt="Student" class="hero-image">

                            <div class="floating-card card-stats">
                                <div class="card-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="card-content">
                                    <h5>75%</h5>
                                    <p>Course completion rate</p>
                                </div>
                            </div>

                            <div class="floating-card card-message">
                                <div class="message-avatar">
                                    <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="User" class="avatar-img">
                                </div>
                                <div class="message-content">
                                    <p>"Your course helped me get a new job!"</p>
                                    <a href="#testimonials" class="btn-sm btn-light">See More</a>
                                </div>
                            </div>

                            <div class="floating-card card-notification">
                                <div class="notification-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="notification-content">
                                    <p>Assignment deadline in 2 days</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2 class="section-title">Our Success</h2>
                <p class="section-description">
                    TOTO is a trusted platform used by thousands of students and instructors worldwide.
                    Our numbers speak for themselves, ensuring a great experience for all our users.
                </p>
            </div>

            <div class="row stats-row justify-content-center" data-aos="fade-up">
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stat-card text-center">
                        <h3 class="stat-number">15K+</h3>
                        <p class="stat-label">Students</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stat-card text-center">
                        <h3 class="stat-number">75%</h3>
                        <p class="stat-label">Success rate</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stat-card text-center">
                        <h3 class="stat-number">35</h3>
                        <p class="stat-label">Main categories</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stat-card text-center">
                        <h3 class="stat-number">26</h3>
                        <p class="stat-label">Cloud experts</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="stat-card text-center">
                        <h3 class="stat-number">16</h3>
                        <p class="stat-label">Years of experience</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2 class="section-title">All-in-One <span class="text-highlight">Cloud Software</span></h2>
                <p class="section-description">
                    TOTO is a powerful cloud software that combines all the tools you need at the best price.
                    Intended for use in educational settings or office.
                </p>
            </div>

            <div class="row features-row justify-content-center" data-aos="fade-up">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon blue">
                            <i class="fas fa-video"></i>
                        </div>
                        <h4 class="feature-title">Online Video Meetings & Conferences</h4>
                        <p class="feature-description">
                            Hold live video calls with up to 100 participants. Perfect for virtual classrooms and team meetings.
                        </p>
                        <a href="#" class="feature-link">Learn more <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon green">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h4 class="feature-title">Easy Scheduling & Attendance Tracking</h4>
                        <p class="feature-description">
                            Schedule and manage classes easily. Set up recurring sessions and track attendance automatically.
                        </p>
                        <a href="#" class="feature-link">Learn more <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon purple">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h4 class="feature-title">Customer Tracking</h4>
                        <p class="feature-description">
                            A detailed dashboard to track student progress and engagement. Monitor learning outcomes and identify areas for improvement.
                        </p>
                        <a href="#" class="feature-link">Learn more <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What is TOTO Section -->
    <section class="what-is-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2 class="section-title">What is <span class="text-highlight">TOTO</span>?</h2>
                <p class="section-description">
                    TOTO is a platform that allows educators to create online classes, manage digital assignments, grade results and provide students with feedback all in one place.
                </p>
            </div>

            <div class="row audience-row" data-aos="fade-up">
                <div class="col-md-6 mb-4">
                    <div class="audience-card instructor-card">
                        <div class="audience-image">
                            <img src="https://img.freepik.com/free-photo/female-teacher-standing-by-whiteboard_23-2148699777.jpg" alt="For Instructors" class="img-fluid">
                        </div>
                        <div class="audience-content">
                            <h3>FOR INSTRUCTORS</h3>
                            <a href="/register" class="btn btn-outline-primary">Start teaching today</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="audience-card student-card">
                        <div class="audience-image">
                            <img src="https://img.freepik.com/free-photo/group-diverse-students-having-computer-class_53876-46332.jpg" alt="For Students" class="img-fluid">
                        </div>
                        <div class="audience-content">
                            <h3>FOR STUDENTS</h3>
                            <a href="/register" class="btn btn-outline-primary">Start learning today</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Physical Classroom Section -->
    <section class="physical-classroom-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="classroom-content">
                        <div class="classroom-badge">
                            <span>Everything you can do in a physical classroom,</span>
                            <span class="text-highlight">you can do with TOTO</span>
                        </div>
                        <p class="classroom-description">
                            TOTO's school management software was developed specifically for educational institutions. It helps teachers and administrators run their classes and schools more efficiently and without paperwork.
                        </p>
                        <a href="#" class="btn btn-outline-primary">Read more</a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="classroom-image">
                        <img src="https://img.freepik.com/free-photo/teacher-helping-student-computer-class_23-2148888812.jpg" alt="Classroom" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Features Section -->
    <section class="our-features-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2 class="section-title">Our <span class="text-highlight">Features</span></h2>
                <p class="section-description">
                    Only extraordinary features, zero learning complexity, zero effort
                </p>
            </div>

            <div class="row features-detailed-row" data-aos="fade-up">
                <div class="col-lg-6 mb-5">
                    <div class="feature-detailed-card">
                        <div class="feature-icon-wrapper blue">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="feature-detailed-content">
                            <h3 class="feature-detailed-title">TOTO For Teachers And Learners</h3>
                            <p class="feature-detailed-description">
                                TOTO is designed to be intuitive and easy to use for both teachers and students. Teachers can create assignments or quizzes in seconds while students can access all their learning materials in one place.
                            </p>
                            <div class="feature-detailed-image">
                                <img src="https://img.freepik.com/free-photo/woman-teaching-class-through-video-call_23-2148766011.jpg" alt="Teachers and Learners" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-5">
                    <div class="feature-detailed-card">
                        <div class="feature-icon-wrapper purple">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="feature-detailed-content">
                            <h3 class="feature-detailed-title">Assessments, Quizzes, Tests</h3>
                            <p class="feature-detailed-description">
                                Create quizzes for assignments, quizzes, and tests in minutes. Add images, videos, and more to make your assessments engaging and effective for students.
                            </p>
                            <div class="feature-detailed-image">
                                <img src="https://img.freepik.com/free-photo/woman-taking-notes-tablet_23-2148888827.jpg" alt="Assessments" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-5">
                    <div class="feature-detailed-card">
                        <div class="feature-icon-wrapper green">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="feature-detailed-content">
                            <h3 class="feature-detailed-title">Class Management Tools for Educators</h3>
                            <p class="feature-detailed-description">
                                Class schedules, tools to help you run and manage the class such as timers, noise meter, attendance, and more. Everything you need to run your class efficiently.
                            </p>
                            <div class="feature-detailed-image">
                                <img src="https://img.freepik.com/free-photo/teacher-with-students-computer-lab_23-2148888819.jpg" alt="Class Management" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-5">
                    <div class="feature-detailed-card">
                        <div class="feature-icon-wrapper orange">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="feature-detailed-content">
                            <h3 class="feature-detailed-title">One-on-One Discussions</h3>
                            <p class="feature-detailed-description">
                                Teachers can create breakout sessions for 1-on-1 with students or group discussions. Perfect for personalized feedback and collaborative learning.
                            </p>
                            <div class="feature-detailed-image">
                                <img src="https://img.freepik.com/free-photo/teacher-helping-student-with-studies_23-2148888838.jpg" alt="Discussions" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Explore Courses Section -->
    <section class="explore-courses-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2 class="section-title">Explore <span class="text-highlight">Courses</span></h2>
                <p class="section-description">
                    Browse our latest courses and expand your knowledge
                </p>
            </div>

            <div class="course-timeline" data-aos="fade-up">
                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Level Basics</h4>
                        <div class="timeline-courses">
                            @if(isset($featuredCourses) && count($featuredCourses) > 0)
                                @foreach($featuredCourses->take(1) as $course)
                                    <div class="timeline-course-card">
                                        <div class="course-image">
                                            <img src="https://img.freepik.com/free-photo/students-knowing-right-answer_23-2147666977.jpg" alt="Course Image" class="img-fluid">
                                            <div class="course-rating">
                                                <i class="fas fa-star"></i> 4.8
                                            </div>
                                        </div>
                                        <div class="course-details">
                                            <h5>{{ $course->title }}</h5>
                                            <p>{{ Str::limit($course->description, 80) }}</p>
                                            <div class="course-meta">
                                                <span class="price">${{ number_format($course->price, 2) }}</span>
                                                <a href="{{ url('/courses/' . ($course->course_id ?? $course->id ?? '')) }}" class="btn btn-sm btn-primary">Enroll</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> No courses available at this level.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Intermediate Level</h4>
                        <div class="timeline-courses">
                            @if(isset($featuredCourses) && count($featuredCourses) > 0)
                                @foreach($featuredCourses->take(1) as $course)
                                    <div class="timeline-course-card">
                                        <div class="course-image">
                                            <img src="https://img.freepik.com/free-photo/students-knowing-right-answer_23-2147666977.jpg" alt="Course Image" class="img-fluid">
                                            <div class="course-rating">
                                                <i class="fas fa-star"></i> 4.9
                                            </div>
                                        </div>
                                        <div class="course-details">
                                            <h5>Advanced {{ $course->title }}</h5>
                                            <p>Take your skills to the next level with our intermediate course.</p>
                                            <div class="course-meta">
                                                <span class="price">${{ number_format($course->price + 20, 2) }}</span>
                                                <a href="{{ url('/courses/' . ($course->course_id ?? $course->id ?? '')) }}" class="btn btn-sm btn-primary">Enroll</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> No courses available at this level.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Advanced Proficiency</h4>
                        <div class="timeline-courses">
                            @if(isset($featuredCourses) && count($featuredCourses) > 0)
                                @foreach($featuredCourses->take(1) as $course)
                                    <div class="timeline-course-card">
                                        <div class="course-image">
                                            <img src="https://img.freepik.com/free-photo/students-knowing-right-answer_23-2147666977.jpg" alt="Course Image" class="img-fluid">
                                            <div class="course-rating">
                                                <i class="fas fa-star"></i> 5.0
                                            </div>
                                        </div>
                                        <div class="course-details">
                                            <h5>Master {{ $course->title }}</h5>
                                            <p>Become an expert with our comprehensive advanced course.</p>
                                            <div class="course-meta">
                                                <span class="price">${{ number_format($course->price + 50, 2) }}</span>
                                                <a href="{{ url('/courses/' . ($course->course_id ?? $course->id ?? '')) }}" class="btn btn-sm btn-primary">Enroll</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> No courses available at this level.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="/courses" class="btn btn-outline-primary btn-lg">View All Courses <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2 class="section-title">What They <span class="text-highlight">Say?</span></h2>
                <p class="section-description">
                    TOTO has got more than 10K positive ratings from our users around the world.
                    Some of the students who have been using TOTO are sharing their experience.
                </p>
            </div>

            <div class="testimonials-container" data-aos="fade-up">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="testimonial-featured">
                            <div class="testimonial-image">
                                <img src="https://img.freepik.com/free-photo/cheerful-young-african-woman-holding-books-smiling_171337-14037.jpg" alt="Student" class="img-fluid rounded">
                            </div>
                            <div class="testimonial-quote-large">
                                <i class="fas fa-quote-left"></i>
                                <p>"TOTO has completely transformed how I teach my online classes. The interface is intuitive, and my students love the interactive features. I can't imagine teaching without it now!"</p>
                                <div class="testimonial-author">
                                    <h4>Maria Davis</h4>
                                    <p>Science Teacher, Boston</p>
                                    <div class="testimonial-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <span>5.0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="testimonial-cards">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    <div class="testimonial-header">
                                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Student" class="testimonial-avatar">
                                        <div>
                                            <h5>James Wilson</h5>
                                            <p>Math Student</p>
                                        </div>
                                    </div>
                                    <p>"The interactive quizzes and immediate feedback have helped me improve my math skills significantly. Highly recommended!"</p>
                                    <div class="testimonial-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    <div class="testimonial-header">
                                        <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Student" class="testimonial-avatar">
                                        <div>
                                            <h5>Emily Rodriguez</h5>
                                            <p>Language Arts Teacher</p>
                                        </div>
                                    </div>
                                    <p>"TOTO has made managing assignments and grading so much easier. My students are more engaged than ever before."</p>
                                    <div class="testimonial-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    <div class="testimonial-header">
                                        <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Student" class="testimonial-avatar">
                                        <div>
                                            <h5>David Thompson</h5>
                                            <p>School Administrator</p>
                                        </div>
                                    </div>
                                    <p>"Implementing TOTO across our school has improved communication between teachers, students, and parents. The analytics help us track progress effectively."</p>
                                    <div class="testimonial-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <a href="/testimonials" class="btn btn-outline-primary">See all testimonials</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest News Section -->
    <section class="latest-news-section">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2 class="section-title">Latest News and <span class="text-highlight">Resources</span></h2>
                <p class="section-description">
                    Stay updated with the latest educational trends and resources
                </p>
            </div>

            <div class="row news-row" data-aos="fade-up">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="news-card">
                        <div class="news-image">
                            <img src="https://img.freepik.com/free-photo/group-diverse-students-having-computer-class_53876-46332.jpg" alt="News" class="img-fluid">
                            <div class="news-category">Tutorial</div>
                        </div>
                        <div class="news-content">
                            <h4 class="news-title">Class tools for effective online teaching</h4>
                            <p class="news-excerpt">Learn how to use TOTO's classroom tools to create an engaging virtual learning environment.</p>
                            <div class="news-meta">
                                <span><i class="far fa-calendar-alt"></i> June 15, 2023</span>
                                <span><i class="far fa-clock"></i> 5 min read</span>
                            </div>
                            <a href="#" class="btn btn-sm btn-outline-primary">Read More</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="news-card">
                        <div class="news-image">
                            <img src="https://img.freepik.com/free-photo/woman-taking-notes-tablet_23-2148888827.jpg" alt="News" class="img-fluid">
                            <div class="news-category">Update</div>
                        </div>
                        <div class="news-content">
                            <h4 class="news-title">TOTO's mobile interface now with dark mode</h4>
                            <p class="news-excerpt">Our latest update brings dark mode to mobile devices, making it easier on the eyes during evening study sessions.</p>
                            <div class="news-meta">
                                <span><i class="far fa-calendar-alt"></i> May 28, 2023</span>
                                <span><i class="far fa-clock"></i> 3 min read</span>
                            </div>
                            <a href="#" class="btn btn-sm btn-outline-primary">Read More</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="news-card">
                        <div class="news-image">
                            <img src="https://img.freepik.com/free-photo/teacher-helping-student-with-studies_23-2148888838.jpg" alt="News" class="img-fluid">
                            <div class="news-category">Case Study</div>
                        </div>
                        <div class="news-content">
                            <h4 class="news-title">How Brighton College increased student engagement by 45%</h4>
                            <p class="news-excerpt">Discover how this leading institution transformed their remote learning experience with TOTO's collaborative tools.</p>
                            <div class="news-meta">
                                <span><i class="far fa-calendar-alt"></i> April 10, 2023</span>
                                <span><i class="far fa-clock"></i> 8 min read</span>
                            </div>
                            <a href="#" class="btn btn-sm btn-outline-primary">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container-fluid px-4">
            <div class="section-header text-center" data-aos="fade-up">
                <h6 class="section-subtitle">Meet Our Team</h6>
                <h2 class="section-title">Expert <span class="highlight">Instructors</span></h2>
                <p class="section-description">
                    Learn from industry professionals with years of experience in their fields
                </p>
            </div>

            <div class="team-grid">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="team-card">
                            <div class="team-image">
                                <img src="https://img.freepik.com/free-photo/portrait-smiling-young-man-eyewear_171337-4842.jpg" alt="Instructor" class="img-fluid">
                                <div class="team-overlay">
                                    <div class="team-bio">
                                        <p>PhD in Computer Science with 15+ years of industry experience at Google and Microsoft.</p>
                                        <div class="team-skills">
                                            <span>Python</span>
                                            <span>AI</span>
                                            <span>Machine Learning</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="team-content">
                                <h4>Dr. James Wilson</h4>
                                <p class="team-position">Computer Science</p>
                                <div class="team-rating">
                                    <i class="fas fa-star"></i> 4.9 <span>(120 reviews)</span>
                                </div>
                                <div class="social-links">
                                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="social-link"><i class="fab fa-github"></i></a>
                                </div>
                            </div>
                            <div class="team-footer">
                                <div class="team-stats">
                                    <div class="stat">
                                        <span class="stat-value">15</span>
                                        <span class="stat-label">Courses</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-value">10k+</span>
                                        <span class="stat-label">Students</span>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-primary">View Profile</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="team-card">
                            <div class="team-image">
                                <img src="https://img.freepik.com/free-photo/young-female-professor-giving-presentation-class_23-2148522769.jpg" alt="Instructor" class="img-fluid">
                                <div class="team-overlay">
                                    <div class="team-bio">
                                        <p>MBA from Harvard Business School with experience as a senior executive at Fortune 500 companies.</p>
                                        <div class="team-skills">
                                            <span>Leadership</span>
                                            <span>Strategy</span>
                                            <span>Finance</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="team-content">
                                <h4>Prof. Lisa Nguyen</h4>
                                <p class="team-position">Business Management</p>
                                <div class="team-rating">
                                    <i class="fas fa-star"></i> 4.8 <span>(98 reviews)</span>
                                </div>
                                <div class="social-links">
                                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                            <div class="team-footer">
                                <div class="team-stats">
                                    <div class="stat">
                                        <span class="stat-value">12</span>
                                        <span class="stat-label">Courses</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-value">8k+</span>
                                        <span class="stat-label">Students</span>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-primary">View Profile</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="team-card">
                            <div class="team-image">
                                <img src="https://img.freepik.com/free-photo/confident-teacher-explaining-lesson-students_74855-9751.jpg" alt="Instructor" class="img-fluid">
                                <div class="team-overlay">
                                    <div class="team-bio">
                                        <p>Former Marketing Director at Facebook with expertise in digital marketing strategies and analytics.</p>
                                        <div class="team-skills">
                                            <span>SEO</span>
                                            <span>Social Media</span>
                                            <span>Analytics</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="team-content">
                                <h4>David Thompson</h4>
                                <p class="team-position">Digital Marketing</p>
                                <div class="team-rating">
                                    <i class="fas fa-star"></i> 4.7 <span>(85 reviews)</span>
                                </div>
                                <div class="social-links">
                                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                                </div>
                            </div>
                            <div class="team-footer">
                                <div class="team-stats">
                                    <div class="stat">
                                        <span class="stat-value">9</span>
                                        <span class="stat-label">Courses</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-value">6k+</span>
                                        <span class="stat-label">Students</span>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-primary">View Profile</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                        <div class="team-card">
                            <div class="team-image">
                                <img src="https://img.freepik.com/free-photo/female-teacher-standing-by-whiteboard_23-2148699777.jpg" alt="Instructor" class="img-fluid">
                                <div class="team-overlay">
                                    <div class="team-bio">
                                        <p>Award-winning designer with experience at top creative agencies and a passion for teaching design principles.</p>
                                        <div class="team-skills">
                                            <span>UI/UX</span>
                                            <span>Adobe Suite</span>
                                            <span>Branding</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="team-content">
                                <h4>Dr. Sophia Martinez</h4>
                                <p class="team-position">Graphic Design</p>
                                <div class="team-rating">
                                    <i class="fas fa-star"></i> 4.9 <span>(112 reviews)</span>
                                </div>
                                <div class="social-links">
                                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                                    <a href="#" class="social-link"><i class="fab fa-behance"></i></a>
                                    <a href="#" class="social-link"><i class="fab fa-dribbble"></i></a>
                                </div>
                            </div>
                            <div class="team-footer">
                                <div class="team-stats">
                                    <div class="stat">
                                        <span class="stat-value">14</span>
                                        <span class="stat-label">Courses</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-value">9k+</span>
                                        <span class="stat-label">Students</span>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-primary">View Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4" data-aos="fade-up">
                <a href="/about" class="btn btn-outline-primary btn-lg">Meet All Instructors <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="newsletter-container" data-aos="fade-up">
                        <div class="newsletter-header text-center">
                            <h3 class="newsletter-title">Subscribe to our <span class="text-highlight">Newsletter</span></h3>
                            <p class="newsletter-description">Stay up to date with our latest news and updates</p>
                        </div>
                        <form class="newsletter-form">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Enter your email address">
                                <button type="submit" class="btn btn-primary">Subscribe</button>
                            </div>
                        </form>
                        <div class="newsletter-footer text-center">
                            <p class="privacy-note"><i class="fas fa-lock me-1"></i> Your information is secure with us</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-container" data-aos="fade-up">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="cta-content">
                            <h2 class="cta-title">Ready to transform your <span class="text-highlight-white">teaching experience</span>?</h2>
                            <p class="cta-description">Join thousands of educators and students already using TOTO to enhance their learning experience.</p>
                            <div class="cta-buttons">
                                <a href="/register" class="btn btn-light btn-lg">Get Started for Free</a>
                                <a href="/pricing" class="btn btn-outline-light btn-lg ms-3">View Pricing</a>
                            </div>
                            <div class="cta-features">
                                <div class="cta-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>No credit card required</span>
                                </div>
                                <div class="cta-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Free 14-day trial</span>
                                </div>
                                <div class="cta-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Cancel anytime</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="cta-image-wrapper">
                            <img src="https://img.freepik.com/free-photo/group-diverse-students-having-computer-class_53876-46332.jpg" alt="Students in classroom" class="img-fluid rounded">
                            <div class="cta-badge">
                                <div class="badge-content">
                                    <div class="badge-icon">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="badge-text">
                                        <span>Trusted by</span>
                                        <strong>500+ Schools</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                        <div class="footer-widget about-widget">
                            <h3 class="widget-title">TOTO</h3>
                            <p class="widget-desc">A modern platform that embraces you to learn in one place. We provide the best tools for educators and students.</p>
                            <div class="social-links">
                                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                        <div class="footer-widget links-widget">
                            <h3 class="widget-title">Company</h3>
                            <ul class="widget-links">
                                <li><a href="#">About Us</a></li>
                                <li><a href="#">Careers</a></li>
                                <li><a href="#">Blog</a></li>
                                <li><a href="#">Pricing</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                        <div class="footer-widget links-widget">
                            <h3 class="widget-title">Resources</h3>
                            <ul class="widget-links">
                                <li><a href="#">Help Center</a></li>
                                <li><a href="#">Contact Us</a></li>
                                <li><a href="#">Privacy Policy</a></li>
                                <li><a href="#">Terms of Service</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-widget contact-widget">
                            <h3 class="widget-title">Contact Us</h3>
                            <ul class="widget-contact-info">
                                <li><i class="fas fa-map-marker-alt"></i> 123 Education Street, Learning City</li>
                                <li><i class="fas fa-phone-alt"></i> +1 (555) 123-4567</li>
                                <li><i class="fas fa-envelope"></i> info@totolearning.com</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="copyright">© 2023 TOTO Learning Inc. All rights reserved.</p>
                    </div>
                    <div class="col-md-6">
                        <div class="footer-bottom-links text-md-end">
                            <a href="#">Privacy</a>
                            <a href="#">Terms</a>
                            <a href="#">Cookies</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
@endsection

@section('styles')
<style>
    /* Hero Section */
    .hero-section {
        position: relative;
        padding: 100px 0 80px;
        background-color: #4361ee;
        overflow: hidden;
        width: 100%;
        color: #ffffff;
    }

    .hero-content {
        padding-right: 30px;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50px;
        padding: 8px 16px;
        margin-bottom: 25px;
    }

    .badge-icon {
        margin-right: 8px;
        color: #ffcc00;
    }

    .badge-text {
        font-size: 14px;
        font-weight: 500;
        color: #ffffff;
    }

    .hero-title {
        font-size: 42px;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 25px;
        color: #ffffff;
    }

    .text-highlight {
        color: #41cdcd;
        position: relative;
        z-index: 1;
    }

    .text-highlight::after {
        content: '';
        position: absolute;
        bottom: 5px;
        left: 0;
        width: 100%;
        height: 8px;
        background-color: rgba(65, 205, 205, 0.4);
        z-index: -1;
    }

    .hero-description {
        font-size: 18px;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 30px;
        max-width: 600px;
    }

    .hero-buttons .btn {
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .hero-buttons .btn-primary {
        background-color: #ffcc00;
        border-color: #ffcc00;
        color: #333;
    }

    .hero-buttons .btn-primary:hover {
        background-color: #ffd633;
        border-color: #ffd633;
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(255, 204, 0, 0.3);
    }

    .hero-buttons .btn-outline-secondary {
        border: 2px solid rgba(255, 255, 255, 0.5);
        color: #ffffff;
    }

    .hero-buttons .btn-outline-secondary:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(255, 255, 255, 0.1);
    }

    .hero-image-wrapper {
        position: relative;
        padding: 20px;
    }

    .hero-image {
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 2;
    }

    .floating-card {
        position: absolute;
        background-color: #ffffff;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        z-index: 3;
        animation: float 3s ease-in-out infinite;
    }

    .card-stats {
        top: 20px;
        right: 0;
        display: flex;
        align-items: center;
        animation-delay: 0.5s;
    }

    .card-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: rgba(65, 205, 205, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-size: 18px;
        color: #41cdcd;
    }

    .card-content h5 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 0;
        color: #333;
    }

    .card-content p {
        font-size: 12px;
        color: #666;
        margin-bottom: 0;
    }

    .card-message {
        bottom: 80px;
        left: 0;
        display: flex;
        align-items: center;
        max-width: 280px;
        animation-delay: 1s;
    }

    .message-avatar {
        margin-right: 10px;
        flex-shrink: 0;
    }

    .avatar-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .message-content p {
        font-size: 13px;
        color: #333;
        margin-bottom: 5px;
        font-style: italic;
    }

    .btn-sm {
        padding: 2px 8px;
        font-size: 12px;
    }

    .card-notification {
        top: 50%;
        right: -10px;
        display: flex;
        align-items: center;
        animation-delay: 1.5s;
    }

    .notification-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #ff6b6b;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-size: 14px;
        color: #ffffff;
    }

    .notification-content p {
        font-size: 13px;
        color: #333;
        margin-bottom: 0;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
        100% {
            transform: translateY(0px);
        }
    }

    /* Stats Section */
    .stats-section {
        padding: 80px 0;
        background-color: #f9fbfd;
    }

    .stats-row {
        margin-top: 40px;
    }

    .stat-card {
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: #41cdcd;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 14px;
        color: #666;
        margin-bottom: 0;
    }

    /* Features Section */
    .features-section {
        padding: 100px 0 60px;
        background-color: #fff;
    }

    .section-header {
        margin-bottom: 60px;
    }

    .section-title {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #333;
    }

    .section-description {
        font-size: 18px;
        color: #666;
        max-width: 700px;
        margin: 0 auto;
    }

    .features-row {
        margin-top: 30px;
    }

    .feature-card {
        padding: 30px 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .feature-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 28px;
        color: #ffffff;
    }

    .feature-icon.blue {
        background-color: #41cdcd;
    }

    .feature-icon.green {
        background-color: #20C997;
    }

    .feature-icon.purple {
        background-color: #8000FF;
    }

    .feature-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }

    .feature-description {
        font-size: 15px;
        color: #666;
        margin-bottom: 20px;
    }

    .feature-link {
        font-size: 14px;
        font-weight: 600;
        color: #41cdcd;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .feature-link:hover {
        color: #38b6b6;
    }

    /* What is TOTO Section */
    .what-is-section {
        padding: 100px 0;
        background-color: #f9fbfd;
    }

    .audience-row {
        margin-top: 40px;
    }

    .audience-card {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .audience-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .audience-image img {
        width: 100%;
        height: 300px;
        object-fit: cover;
    }

    .audience-content {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 30px 20px;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0));
        color: #ffffff;
        text-align: center;
    }

    .audience-content h3 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .audience-content .btn {
        border: 2px solid #ffffff;
        color: #ffffff;
        padding: 8px 20px;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .audience-content .btn:hover {
        background-color: #ffffff;
        color: #41cdcd;
    }

    /* Physical Classroom Section */
    .physical-classroom-section {
        padding: 100px 0;
        background-color: #ffffff;
    }

    .classroom-content {
        padding-right: 30px;
    }

    .classroom-badge {
        display: inline-block;
        background-color: #f9fbfd;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .classroom-badge span {
        display: block;
        font-size: 16px;
        font-weight: 500;
        color: #333;
        line-height: 1.5;
    }

    .classroom-badge .text-highlight {
        font-weight: 700;
    }

    .classroom-description {
        font-size: 18px;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .classroom-image img {
        border-radius: 10px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    /* Our Features Section */
    .our-features-section {
        padding: 100px 0;
        background-color: #f9fbfd;
    }

    .features-detailed-row {
        margin-top: 50px;
    }

    .feature-detailed-card {
        display: flex;
        background-color: #ffffff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
    }

    .feature-detailed-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .feature-icon-wrapper {
        flex-shrink: 0;
        width: 80px;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 30px;
        color: #ffffff;
        font-size: 30px;
    }

    .feature-icon-wrapper.blue {
        background-color: #41cdcd;
    }

    .feature-icon-wrapper.purple {
        background-color: #8000FF;
    }

    .feature-icon-wrapper.green {
        background-color: #20C997;
    }

    .feature-icon-wrapper.orange {
        background-color: #FF9F43;
    }

    .feature-detailed-content {
        padding: 30px;
        flex-grow: 1;
    }

    .feature-detailed-title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }

    .feature-detailed-description {
        font-size: 16px;
        color: #666;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .feature-detailed-image {
        margin-top: 20px;
    }

    .feature-detailed-image img {
        border-radius: 8px;
        width: 100%;
    }

    /* Explore Courses Section */
    .explore-courses-section {
        padding: 100px 0;
        background-color: #ffffff;
    }

    .course-timeline {
        position: relative;
        margin-top: 50px;
        padding-left: 50px;
    }

    .course-timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 20px;
        height: 100%;
        width: 2px;
        background-color: #e9ecef;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 50px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-icon {
        position: absolute;
        left: -50px;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #41cdcd;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 18px;
        z-index: 2;
    }

    .timeline-content {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .timeline-content h4 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #333;
    }

    .timeline-courses {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .timeline-course-card {
        flex: 1;
        min-width: 280px;
        background-color: #f9fbfd;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .timeline-course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .timeline-course-card .course-image {
        position: relative;
        height: 160px;
    }

    .timeline-course-card .course-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .timeline-course-card .course-rating {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(0, 0, 0, 0.7);
        color: #ffffff;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
    }

    .timeline-course-card .course-details {
        padding: 15px;
    }

    .timeline-course-card h5 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #333;
    }

    .timeline-course-card p {
        font-size: 14px;
        color: #666;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .timeline-course-card .course-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .timeline-course-card .price {
        font-size: 16px;
        font-weight: 700;
        color: #41cdcd;
    }

    /* Testimonials Section */
    .testimonials-section {
        padding: 100px 0;
        background-color: #f9fbfd;
    }

    .testimonials-container {
        margin-top: 50px;
    }

    .testimonial-featured {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .testimonial-image {
        margin-bottom: 30px;
    }

    .testimonial-image img {
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .testimonial-quote-large {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .testimonial-quote-large i {
        font-size: 30px;
        color: #41cdcd;
        margin-bottom: 20px;
    }

    .testimonial-quote-large p {
        font-size: 18px;
        color: #333;
        line-height: 1.6;
        font-style: italic;
        margin-bottom: 20px;
        flex-grow: 1;
    }

    .testimonial-author {
        margin-top: auto;
    }

    .testimonial-author h4 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #333;
    }

    .testimonial-author p {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
        font-style: normal;
    }

    .testimonial-rating {
        color: #ffc107;
    }

    .testimonial-rating span {
        color: #333;
        margin-left: 5px;
    }

    .testimonial-cards {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .testimonial-card {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .testimonial-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .testimonial-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
    }

    .testimonial-header h5 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #333;
    }

    .testimonial-header p {
        font-size: 14px;
        color: #666;
        margin-bottom: 0;
    }

    .testimonial-content p {
        font-size: 15px;
        color: #333;
        line-height: 1.6;
        font-style: italic;
        margin-bottom: 15px;
    }

    /* Latest News Section */
    .latest-news-section {
        padding: 100px 0;
        background-color: #ffffff;
    }

    .news-row {
        margin-top: 40px;
    }

    .news-card {
        background-color: #ffffff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        height: 100%;
        transition: all 0.3s ease;
    }

    .news-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .news-image {
        position: relative;
        height: 200px;
    }

    .news-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .news-category {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: #41cdcd;
        color: #ffffff;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .news-content {
        padding: 25px;
    }

    .news-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
        line-height: 1.4;
    }

    .news-excerpt {
        font-size: 14px;
        color: #666;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .news-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        font-size: 13px;
        color: #888;
    }

    .news-meta span {
        margin-right: 15px;
    }

    .news-meta i {
        margin-right: 5px;
    }

    /* Newsletter Section */
    .newsletter-section {
        padding: 80px 0;
        background-color: #f9fbfd;
    }

    .newsletter-container {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .newsletter-header {
        margin-bottom: 30px;
    }

    .newsletter-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }

    .newsletter-description {
        font-size: 16px;
        color: #666;
    }

    .newsletter-form .input-group {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        overflow: hidden;
    }

    .newsletter-form .form-control {
        border: none;
        padding: 15px 20px;
        height: auto;
        font-size: 16px;
    }

    .newsletter-form .btn {
        padding: 15px 30px;
        background-color: #41cdcd;
        border-color: #41cdcd;
        font-weight: 600;
    }

    .newsletter-form .btn:hover {
        background-color: #38b6b6;
        border-color: #38b6b6;
    }

    .newsletter-footer {
        margin-top: 20px;
    }

    .privacy-note {
        font-size: 14px;
        color: #888;
    }

    /* CTA Section */
    .cta-section {
        padding: 100px 0;
        background: linear-gradient(135deg, #41cdcd 0%, #2bc9c9 50%, #20b7b7 100%);
        color: #ffffff;
    }

    .cta-container {
        position: relative;
        z-index: 2;
    }

    .cta-title {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 20px;
        line-height: 1.3;
    }

    .text-highlight-white {
        position: relative;
        z-index: 1;
    }

    .text-highlight-white::after {
        content: '';
        position: absolute;
        bottom: 5px;
        left: 0;
        width: 100%;
        height: 8px;
        background-color: rgba(255, 255, 255, 0.3);
        z-index: -1;
    }

    .cta-description {
        font-size: 18px;
        margin-bottom: 30px;
        opacity: 0.9;
    }

    .cta-buttons {
        margin-bottom: 30px;
    }

    .cta-features {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .cta-feature {
        display: flex;
        align-items: center;
    }

    .cta-feature i {
        margin-right: 10px;
        color: #ffffff;
    }

    .cta-image-wrapper {
        position: relative;
    }

    .cta-badge {
        position: absolute;
        bottom: -20px;
        right: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .badge-content {
        display: flex;
        align-items: center;
    }

    .badge-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #ffc107;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-size: 18px;
        color: #ffffff;
    }

    .badge-text span {
        display: block;
        font-size: 12px;
        color: #666;
    }

    .badge-text strong {
        font-size: 16px;
        color: #333;
    }

    /* Footer Styles */
    .site-footer {
        background-color: #333;
        color: #ffffff;
    }

    .footer-top {
        padding: 80px 0 50px;
    }

    .widget-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 25px;
        color: #ffffff;
    }

    .widget-desc {
        font-size: 15px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .social-links {
        display: flex;
        gap: 15px;
    }

    .social-link {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background-color: #41cdcd;
        color: #ffffff;
        transform: translateY(-3px);
    }

    .widget-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .widget-links li {
        margin-bottom: 12px;
    }

    .widget-links a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .widget-links a:hover {
        color: #41cdcd;
        padding-left: 5px;
    }

    .widget-contact-info {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .widget-contact-info li {
        display: flex;
        margin-bottom: 15px;
        color: rgba(255, 255, 255, 0.7);
    }

    .widget-contact-info li i {
        margin-right: 10px;
        color: #41cdcd;
    }

    .footer-bottom {
        padding: 20px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .copyright {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0;
    }

    .footer-bottom-links a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        margin-left: 20px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .footer-bottom-links a:hover {
        color: #41cdcd;
    }

    .course-image img {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }

    .course-price {
        position: absolute;
        top: 20px;
        right: 20px;
        background: var(--primary-color);
        color: white;
        padding: 5px 15px;
        border-radius: 30px;
        font-weight: 600;
    }

    .course-category {
        position: absolute;
        bottom: 20px;
        left: 20px;
        background: white;
        color: var(--primary-color);
        padding: 5px 15px;
        border-radius: 30px;
        font-weight: 500;
        font-size: 14px;
    }

    .course-content {
        padding: 25px;
    }

    .course-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }

    .course-description {
        color: #6c757d;
        margin-bottom: 20px;
        font-size: 15px;
    }

    .course-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 14px;
        color: #6c757d;
    }

    .course-meta i {
        color: var(--primary-color);
        margin-right: 5px;
    }

    /* Testimonials Section */
    .testimonials-section {
        padding: 80px 0;
    }

    .testimonial-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: var(--shadow-standard);
        transition: var(--transition-standard);
        height: 100%;
    }

    .testimonial-card:hover {
        box-shadow: var(--shadow-elevated);
    }

    .quote-icon {
        color: var(--primary-color);
        font-size: 24px;
        margin-bottom: 20px;
    }

    .testimonial-text {
        font-size: 16px;
        color: #6c757d;
        margin-bottom: 25px;
        font-style: italic;
    }

    .testimonial-author {
        display: flex;
        align-items: center;
    }

    .author-image {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
    }

    .author-info h5 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }

    .author-info p {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 0;
    }

    /* Team Section */
    .team-section {
        padding: 80px 0;
        background-color: #f9fbfd;
        width: 100%;
    }

    .team-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
        margin-bottom: 20px;
    }

    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .team-image {
        position: relative;
        overflow: hidden;
    }

    .team-image img {
        width: 100%;
        height: 300px;
        object-fit: cover;
    }

    .team-content .social-links {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 15px;
    }

    .social-link {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #41cdcd;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background-color: #41cdcd;
        color: white;
        transform: translateY(-3px);
    }

    .team-content {
        padding: 20px;
        text-align: center;
    }

    .team-content h5 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }

    .team-content p {
        color: var(--primary-color);
        font-weight: 500;
        margin-bottom: 0;
    }

    /* Newsletter Section */
    .newsletter-section {
        padding: 80px 0;
    }

    .newsletter-box {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: var(--shadow-standard);
    }

    .newsletter-content h3 {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }

    .newsletter-content p {
        color: #6c757d;
        margin-bottom: 0;
    }

    .newsletter-form .form-control {
        height: 54px;
        border-radius: 10px 0 0 10px;
        border: 1px solid #ced4da;
        font-size: 16px;
    }

    .newsletter-form .btn {
        border-radius: 0 10px 10px 0;
        padding: 10px 20px;
        font-weight: 500;
    }

    /* CTA Section */
    .cta-section {
        padding: 80px 0;
        background-color: #f9fbfd;
    }

    .cta-box {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        border-radius: 15px;
        padding: 60px 40px;
        color: white;
    }

    .cta-box h2 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .cta-box p {
        font-size: 18px;
        margin-bottom: 30px;
        opacity: 0.9;
    }

    .cta-box .btn {
        background: white;
        color: var(--primary-color);
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: var(--transition-standard);
    }

    .cta-box .btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    /* Responsive Adjustments */
    @media (max-width: 991px) {
        .hero-section {
            height: auto;
            padding-bottom: 100px;
        }

        .hero-section::after {
            width: 100%;
            height: 70%;
            top: unset;
            bottom: 0;
            opacity: 0.2;
        }

        .hero-content {
            padding-right: 0;
            padding-bottom: 300px;
        }

        .section-title {
            font-size: 30px;
        }
    }

    @media (max-width: 767.98px) {
        .hero-section {
            padding: 60px 0;
        }

        .hero-content .title {
            font-size: 30px;
        }

        .services-section,
        .courses-section,
        .testimonials-section,
        .team-section,
        .newsletter-section,
        .cta-section {
            padding: 60px 0;
        }

        .section-title {
            font-size: 28px;
        }

        .newsletter-content {
            margin-bottom: 20px;
            text-align: center;
        }

        .cta-box {
            padding: 40px 20px;
        }

        .cta-box h2 {
            font-size: 28px;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS animation library
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Add visible class to elements with fade-up for manual animation
        setTimeout(function() {
            document.querySelectorAll('.fade-up').forEach(function(element) {
                element.classList.add('visible');
            });
        }, 300);
    });
</script>
@endsection