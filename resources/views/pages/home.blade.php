@extends('layouts.app')

@section('title', 'منصة تعليمية - منصة التعلم عبر الإنترنت')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container-fluid px-lg-5">
            <div class="row align-items-center min-vh-80">
                <div class="col-lg-6 order-lg-1 order-2">
                    <div class="hero-content" data-aos="fade-up">
                        <div class="hero-badge">
                            <span class="badge-icon"><i class="fas fa-graduation-cap"></i></span>
                            <span class="badge-text">منصة تعليمية متكاملة</span>
                        </div>
                        <h1 class="hero-title">طور مهاراتك مع <span class="text-highlight">أفضل المدربين</span> في مكان واحد</h1>
                        <p class="hero-description">
                            انضم إلى مجتمعنا التعليمي واكتسب المهارات التي تحتاجها للنجاح في عالم اليوم. دورات عالية الجودة بأسعار مناسبة.
                        </p>
                        <div class="hero-buttons">
                            <a href="/register" class="btn btn-primary btn-lg animated-pulse">ابدأ التعلم الآن</a>
                            <a href="#how-it-works" class="btn btn-outline-light btn-lg ms-3">
                                <i class="fas fa-play-circle me-2"></i> كيف يعمل
                            </a>
                        </div>
                        <div class="hero-stats mt-4">
                            <div class="row">
                                <div class="col-4">
                                    <div class="hero-stat-item">
                                        <h4>15K+</h4>
                                        <p>طالب</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="hero-stat-item">
                                        <h4>200+</h4>
                                        <p>دورة</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="hero-stat-item">
                                        <h4>50+</h4>
                                        <p>مدرب</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-2 order-1 mb-5 mb-lg-0">
                    <div class="hero-image-container" data-aos="fade-left">
                        <img src="https://img.freepik.com/free-photo/students-knowing-right-answer_23-2149000158.jpg" alt="طلاب يتعلمون" class="hero-image img-fluid rounded-4 shadow-lg">
                        <div class="floating-card card-stats">
                            <div class="card-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="card-content">
                                <h5>95%</h5>
                                <p>معدل الرضا</p>
                            </div>
                        </div>
                        <div class="floating-card card-message">
                            <div class="message-avatar">
                                <img src="https://img.freepik.com/free-photo/young-student-wearing-hijab-using-laptop_23-2149066117.jpg" alt="طالب" class="avatar-img">
                            </div>
                            <div class="message-content">
                                <p>"الدورات ساعدتني في تطوير مهاراتي!"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-shape-divider">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#ffffff" fill-opacity="1" d="M0,96L48,122.7C96,149,192,203,288,197.3C384,192,480,128,576,117.3C672,107,768,149,864,165.3C960,181,1056,171,1152,154.7C1248,139,1344,117,1392,106.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5 bg-white" id="stats">
        <div class="container-fluid px-lg-5">
            <div class="row text-center justify-content-center">
                <div class="col-lg-2 col-md-4 col-sm-6 mx-auto">
                    <div class="stats-item mb-4" data-aos="fade-up" data-aos-delay="100">
                        <h2 class="stats-number" style="color: #2389dd; font-size: 3.5rem; font-weight: 700;">+15K</h2>
                        <p class="stats-label" style="font-size: 1.1rem; color: #555;">طالب</p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mx-auto">
                    <div class="stats-item mb-4" data-aos="fade-up" data-aos-delay="200">
                        <h2 class="stats-number" style="color: #2389dd; font-size: 3.5rem; font-weight: 700;">75%</h2>
                        <p class="stats-label" style="font-size: 1.1rem; color: #555;">نسبة النجاح الإجمالية</p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mx-auto">
                    <div class="stats-item mb-4" data-aos="fade-up" data-aos-delay="300">
                        <h2 class="stats-number" style="color: #2389dd; font-size: 3.5rem; font-weight: 700;">35</h2>
                        <p class="stats-label" style="font-size: 1.1rem; color: #555;">سؤال رئيسي</p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mx-auto">
                    <div class="stats-item mb-4" data-aos="fade-up" data-aos-delay="400">
                        <h2 class="stats-number" style="color: #2389dd; font-size: 3.5rem; font-weight: 700;">26</h2>
                        <p class="stats-label" style="font-size: 1.1rem; color: #555;">خبير رئيسي</p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mx-auto">
                    <div class="stats-item mb-4" data-aos="fade-up" data-aos-delay="500">
                        <h2 class="stats-number" style="color: #2389dd; font-size: 3.5rem; font-weight: 700;">16</h2>
                        <p class="stats-label" style="font-size: 1.1rem; color: #555;">سنة من الخبرة</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What is TOTO Section -->
    <section id="about" style="margin-top: 50px; margin-bottom: 50px; padding: 30px 20px; direction: rtl; text-align: center; background: linear-gradient(to bottom, #ffffff, #f8f9fa);">
        <h2 style="font-size: 32px; color: #333; margin-bottom: 20px; text-align: center; position: relative; display: inline-block;">
            ما هو <span style="color: #00c3c9; position: relative;">TOTO
                <svg height="10" width="100%" style="position: absolute; bottom: 0; left: 0;">
                    <line x1="0" y1="5" x2="100%" y2="5" style="stroke:#00c3c9; stroke-width:3;" />
                </svg>
            </span>؟
        </h2>
        
        <p style="font-size: 18px; line-height: 1.8; color: #555; text-align: center; max-width: 900px; margin: 0 auto 50px;">
            هي منصة تعليمية متكاملة تتيح للمعلمين إنشاء فصول دراسية عبر الإنترنت حيث يمكنهم تخزين المواد التعليمية عبر الإنترنت، وإدارة الواجبات والاختبارات ومتابعة مواعيد التسليم TOTO وتقييم النتائج وتزويد الطلاب بالملاحظات، كل ذلك في مكان واحد
        </p>
        
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 40px;">
            <!-- كارد المدرسين -->
            <div style="flex-basis: 320px; text-align: center; background-color: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.4s ease; position: relative;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.08)';">
                <div style="position: relative; overflow: hidden; height: 200px;">
                    <img src="{{ asset('images/instructor.jpg') }}" alt="للمدرسين" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1.0)';">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent); opacity: 0; transition: opacity 0.4s ease;" onmouseover="this.style.opacity='1';" onmouseout="this.style.opacity='0';"></div>
                </div>
                <div style="padding: 25px 20px;">
                    <h3 style="font-size: 28px; font-weight: 600; margin: 0 0 15px; color: #333; position: relative; display: inline-block;">للمدرسين</h3>
                    <a href="/register?role=instructor" style="display: inline-block; padding: 10px 24px; background: linear-gradient(to right, #4361ee, #3a0ca3); color: white; border: none; border-radius: 50px; text-decoration: none; font-weight: 500; font-size: 16px; transition: all 0.3s ease; position: relative; overflow: hidden; z-index: 1;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(67, 97, 238, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        أنشئ فصلاً دراسياً الآن
                    </a>
                </div>
            </div>
            
            <!-- كارد الطلاب -->
            <div style="flex-basis: 320px; text-align: center; background-color: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.4s ease; position: relative;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.08)';">
                <div style="position: relative; overflow: hidden; height: 200px;">
                    <img src="{{ asset('images/student.jpg') }}" alt="للطلاب" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1.0)';">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent); opacity: 0; transition: opacity 0.4s ease;" onmouseover="this.style.opacity='1';" onmouseout="this.style.opacity='0';"></div>
                </div>
                <div style="padding: 25px 20px;">
                    <h3 style="font-size: 28px; font-weight: 600; margin: 0 0 15px; color: #333; position: relative; display: inline-block;">للطلاب</h3>
                    <a href="/login" style="display: inline-block; padding: 10px 24px; background: linear-gradient(to right, #00c3c9, #00b4d8); color: white; border: none; border-radius: 50px; text-decoration: none; font-weight: 500; font-size: 16px; transition: all 0.3s ease; position: relative; overflow: hidden; z-index: 1;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(0, 195, 201, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        أدخل رمز الوصول
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الفيديو التعريفي -->
    <section style="padding: 70px 20px; background: linear-gradient(to left, #f8f9fa, #ffffff); direction: rtl; overflow: hidden; margin-bottom: 50px;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 30px;">
            <!-- الجانب النصي -->
            <div style="flex: 1; min-width: 300px; padding: 20px;">
                <div style="position: relative; display: inline-block; margin-bottom: 15px;">
                    <div style="width: 50px; height: 50px; background-color: #00c3c9; border-radius: 50%; position: absolute; top: -10px; right: -15px; z-index: -1; opacity: 0.2;"></div>
                    <h2 style="font-size: 32px; font-weight: 700; color: #333; margin-bottom: 20px; position: relative;">
                        كل ما يمكنك فعله في الفصل الدراسي التقليدي
                        <span style="display: block; color: #00c3c9; margin-top: 5px; font-weight: 700;">يمكنك فعله مع TOTO</span>
                    </h2>
                </div>
                
                <p style="font-size: 18px; line-height: 1.8; color: #555; margin-bottom: 25px;">
                    تساعد منصة TOTO التعليمية المدارس التقليدية والإلكترونية على إدارة الجداول الدراسية، وتسجيل الحضور، وإدارة المدفوعات، والفصول الافتراضية، كل ذلك في نظام آمن قائم على الحوسبة السحابية.
                </p>
                
                <a href="#features" style="display: inline-block; padding: 12px 25px; background: linear-gradient(to right, #00c3c9, #00b4d8); color: white; border: none; border-radius: 50px; text-decoration: none; font-weight: 500; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0, 195, 201, 0.3);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(0, 195, 201, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0, 195, 201, 0.3)';">
                    اكتشف المزيد
                </a>
            </div>
            
            <!-- جانب الفيديو -->
            <div style="flex: 1; min-width: 300px; padding: 20px; position: relative;">
                <div style="position: relative; border-radius: 16px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1); aspect-ratio: 16/9;">
                    <!-- إطار الفيديو من يوتيوب (يمكنك استبدال رابط الفيديو) -->
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="فيديو تعريفي عن منصة TOTO" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border: none;"></iframe>
                    
                    <!-- زخارف حول الفيديو -->
                    <div style="position: absolute; width: 80px; height: 80px; background-color: #00c3c9; border-radius: 50%; top: -20px; left: -20px; z-index: -1; opacity: 0.1;"></div>
                    <div style="position: absolute; width: 120px; height: 120px; background-color: #4361ee; border-radius: 50%; bottom: -40px; right: -30px; z-index: -1; opacity: 0.1;"></div>
                </div>
                
                <!-- زر التشغيل (للزخرفة فقط) -->
                <div style="position: absolute; width: 80px; height: 80px; background-color: rgba(0, 195, 201, 0.9); border-radius: 50%; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; justify-content: center; align-items: center; pointer-events: none; opacity: 0;">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="#ffffff">
                        <path d="M8 5v14l11-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5" id="features">
        <div class="container-fluid px-lg-5">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">مميزات <span class="text-highlight">المنصة</span></h2>
                <p class="section-description">
                    نقدم لك مجموعة من المميزات الفريدة التي تجعل تجربة التعلم أكثر متعة وفعالية
                </p>
            </div>

            <div class="row g-4" data-aos="fade-up" data-aos-delay="100">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper blue">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3 class="feature-title">مدربون متخصصون</h3>
                        <p class="feature-description">
                            تعلم على يد أفضل الخبراء والمتخصصين في مجالاتهم مع خبرة عملية وأكاديمية متميزة.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper green">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h3 class="feature-title">شهادات معتمدة</h3>
                        <p class="feature-description">
                            احصل على شهادات معتمدة بعد إكمال الدورات التدريبية لتعزيز سيرتك الذاتية وفرصك المهنية.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper purple">
                            <i class="fas fa-infinity"></i>
                        </div>
                        <h3 class="feature-title">وصول مدى الحياة</h3>
                        <p class="feature-description">
                            استمتع بالوصول الدائم إلى جميع الدورات التي تشترك فيها، مع تحديثات مجانية مدى الحياة.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper orange">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="feature-title">تعلم من أي مكان</h3>
                        <p class="feature-description">
                            الوصول إلى الدورات من أي جهاز وفي أي وقت ومكان، مع تطبيق جوال متوافق للتعلم أثناء التنقل.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper red">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3 class="feature-title">مجتمع تفاعلي</h3>
                        <p class="feature-description">
                            انضم إلى مجتمع من المتعلمين للتواصل وتبادل الخبرات والتعاون في المشاريع المشتركة.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper teal">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="feature-title">دعم متميز</h3>
                        <p class="feature-description">
                            فريق دعم متخصص جاهز للمساعدة في أي وقت لضمان تجربة تعليمية سلسة وفعالة.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section class="courses-section py-5 bg-light" id="courses">
        <div class="container-fluid px-lg-5">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">استكشف <span class="text-highlight">الدورات</span></h2>
                <p class="section-description">
                    استعرض مجموعة متنوعة من الدورات عالية الجودة في مختلف المجالات
                </p>
            </div>

            @php
                // في حالة عدم وجود بيانات من قاعدة البيانات، سنستخدم بيانات للعرض
                if (!isset($coursesByCategory)) {
                    $coursesByCategory = [
                        'البرمجة والتطوير' => [
                            (object)[
                                'id' => 1,
                                'title' => 'تطوير واجهات المستخدم',
                                'price' => 99.99,
                                'image' => 'https://img.freepik.com/free-photo/programming-background-with-person-working-with-codes-computer_23-2150010125.jpg',
                                'instructor' => (object)['name' => 'محمد أحمد'],
                                'duration' => 24,
                                'level' => 'متوسط',
                                'rating' => 4.8
                            ],
                            // ... existing code ...
                        ],
                        'التصميم' => [
                            // ... existing code ...
                        ],
                        'الأعمال' => [
                            // ... existing code ...
                        ],
                        'اللغات' => [
                            // ... existing code ...
                        ],
                        'التسويق الرقمي' => [
                            // ... existing code ...
                        ],
                    ];
                }
            @endphp

            <!-- قسم تصفية الفئات (أفقي مع مؤشرات ملونة) -->
            <div class="category-filter-container" data-aos="fade-up">
                <div class="category-filter-scroll">
                    <div class="category-filter-items">
                        <button class="category-filter-item active" data-category="all">
                            <span class="category-indicator" style="background: linear-gradient(135deg, #4361ee, #3a0ca3);"></span>
                            <span class="category-text">جميع الفئات</span>
                        </button>

                        @foreach($categories ?? [] as $category)
                            <button class="category-filter-item" data-category="{{ $category->name }}">
                                <span class="category-indicator" style="background: linear-gradient(135deg, #4361ee, #3a0ca3);"></span>
                                <span class="category-text">{{ $category->name }}</span>
                                <span class="category-count">{{ $category->courses_count ?? 0 }}</span>
                            </button>
                    @endforeach
                            </div>
                        </div>
                    </div>

            <!-- عرض الدورات -->
            <div class="courses-container mt-5">
                <div class="all-categories-section" data-aos="fade-up">
                    <div class="row g-4">
                        @php $courseCount = 0; @endphp
                        @forelse($courses ?? [] as $course)
                            @if($courseCount < 3)
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="course-card-item" data-category="{{ $course->category->name ?? '' }}">
                                        <div class="course-card-image">
                                            <div class="course-card-overlay"></div>
                                            @if($course->thumbnail && file_exists(public_path($course->thumbnail)))
                                                <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}">
                                            @else
                                                <div class="no-image-container">
                                                    <i class="fas fa-image"></i>
                                                    <p>بدون صورة</p>
                                                </div>
                                            @endif
                                            <div class="course-card-rating">
                                                <i class="fas fa-star"></i>
                                                <span>{{ number_format($course->rating ?? 0, 1) }}</span>
                                            </div>
                                            <div class="course-card-category">
                                                {{ $course->category->name ?? 'عام' }}
                                            </div>
                                        </div>
                                        <div class="course-card-body">
                                            <h5 class="course-card-title">{{ $course->title }}</h5>
                                            <div class="course-card-instructor">
                                                <i class="fas fa-user-tie"></i>
                                                <span>{{ $course->instructor->name ?? 'مدرس غير معروف' }}</span>
                                            </div>
                                            <div class="course-card-info">
                                                <div class="info-item">
                                                    <i class="fas fa-clock"></i>
                                                    <span>{{ $course->duration ?? 0 }} ساعة</span>
                                                </div>
                                                <div class="info-item">
                                                    <i class="fas fa-signal"></i>
                                                    <span>{{ $course->level ?? 'مبتدئ' }}</span>
                                                </div>
                                            </div>
                                            <div class="course-card-footer">
                                                <div class="course-card-price">${{ number_format($course->price ?? 0, 2) }}</div>
                                                <a href="{{ url('/courses/' . $course->id) }}" class="course-card-btn"><span>التفاصيل</span></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php $courseCount++; @endphp
                            @endif
                        @empty
                            <div class="col-12 text-center">
                                <div class="alert alert-info py-4">
                                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                                    <h5>لا توجد دورات متاحة حالياً</h5>
                                    <p>يرجى التحقق لاحقاً، سيتم إضافة دورات جديدة قريباً.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="/courses" class="btn btn-outline-primary btn-lg">عرض جميع الدورات <i class="fas fa-arrow-left ms-2"></i></a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section py-5" id="testimonials">
        <div class="container-fluid px-lg-5">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">آراء <span class="text-highlight">الطلاب</span></h2>
                <p class="section-description">
                    استمع إلى ما يقوله طلابنا عن تجربتهم مع منصتنا التعليمية
                </p>
            </div>

            <div class="row" data-aos="fade-up">
                <div class="col-lg-6 mb-4">
                    <div class="testimonial-card h-100">
                        <div class="testimonial-content">
                            <div class="testimonial-rating mb-3">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="testimonial-text">"لقد غيرت هذه المنصة حياتي المهنية بالكامل. الدورات عالية الجودة والمدربون متميزون. استطعت الحصول على وظيفة أحلامي بعد إكمال دورة تطوير الويب الشاملة."</p>
                            <div class="testimonial-author d-flex align-items-center mt-4">
                                <img src="https://img.freepik.com/free-photo/young-bearded-man-with-striped-shirt_273609-5677.jpg" alt="محمد العلي" class="testimonial-avatar">
                                <div class="ms-3">
                                    <h5 class="mb-0">محمد العلي</h5>
                                    <p class="text-muted mb-0">مطور ويب</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="testimonial-card h-100">
                        <div class="testimonial-content">
                            <div class="testimonial-rating mb-3">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="testimonial-text">"المنصة سهلة الاستخدام والمحتوى منظم بشكل رائع. أحب كيف يمكنني التعلم بسرعتي الخاصة والوصول إلى المواد في أي وقت. الدعم الفني ممتاز وسريع الاستجابة."</p>
                            <div class="testimonial-author d-flex align-items-center mt-4">
                                <img src="https://img.freepik.com/free-photo/young-beautiful-woman-pink-warm-sweater-natural-look-smiling-portrait-isolated-long-hair_285396-896.jpg" alt="نورة السعيد" class="testimonial-avatar">
                                <div class="ms-3">
                                    <h5 class="mb-0">نورة السعيد</h5>
                                    <p class="text-muted mb-0">مصممة جرافيك</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="testimonial-card h-100">
                        <div class="testimonial-content">
                            <div class="testimonial-rating mb-3">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <p class="testimonial-text">"كمدرس، أجد هذه المنصة مثالية لتقديم دوراتي. الأدوات التفاعلية والواجهة سهلة الاستخدام تجعل من السهل إنشاء محتوى جذاب للطلاب."</p>
                            <div class="testimonial-author d-flex align-items-center mt-4">
                                <img src="https://img.freepik.com/free-photo/close-up-portrait-young-bearded-man-white-shirt-isolated-white_155003-17821.jpg" alt="عبدالرحمن الشمري" class="testimonial-avatar">
                                <div class="ms-3">
                                    <h5 class="mb-0">عبدالرحمن الشمري</h5>
                                    <p class="text-muted mb-0">مدرس لغة إنجليزية</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="testimonial-card h-100">
                        <div class="testimonial-content">
                            <div class="testimonial-rating mb-3">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="testimonial-text">"أنا معجبة جداً بجودة الدورات وتنوعها. لقد أكملت للتو دورة في تحليل البيانات وأشعر بالثقة الكاملة في تطبيق المهارات الجديدة في عملي."</p>
                            <div class="testimonial-author d-flex align-items-center mt-4">
                                <img src="https://img.freepik.com/free-photo/young-woman-with-round-glasses-yellow-sweater_273609-7091.jpg" alt="سارة القحطاني" class="testimonial-avatar">
                                <div class="ms-3">
                                    <h5 class="mb-0">سارة القحطاني</h5>
                                    <p class="text-muted mb-0">محللة بيانات</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works-section py-5 bg-light" id="how-it-works">
        <div class="container-fluid px-lg-5">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">كيف <span class="text-highlight">تعمل المنصة</span></h2>
                <p class="section-description">
                    عملية بسيطة وسهلة للبدء في رحلتك التعليمية معنا
                </p>
            </div>

            <div class="row" data-aos="fade-up">
                <div class="col-12">
                    <div class="steps-container">
                        <div class="step-line"></div>
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                                <div class="step-item" data-aos="fade-up" data-aos-delay="100">
                                    <div class="step-icon">
                                        <i class="fas fa-user-plus"></i>
                                        <span class="step-number">1</span>
                                    </div>
                                    <h3 class="step-title">التسجيل</h3>
                                    <p class="step-description">أنشئ حسابك مجاناً واملأ ملفك الشخصي للبدء في رحلتك التعليمية.</p>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                                <div class="step-item" data-aos="fade-up" data-aos-delay="200">
                                    <div class="step-icon">
                                        <i class="fas fa-search"></i>
                                        <span class="step-number">2</span>
                                    </div>
                                    <h3 class="step-title">استكشاف</h3>
                                    <p class="step-description">تصفح مجموعتنا الواسعة من الدورات واختر ما يناسب اهتماماتك وأهدافك.</p>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                                <div class="step-item" data-aos="fade-up" data-aos-delay="300">
                                    <div class="step-icon">
                                        <i class="fas fa-credit-card"></i>
                                        <span class="step-number">3</span>
                                    </div>
                                    <h3 class="step-title">التسجيل</h3>
                                    <p class="step-description">ادفع بسهولة باستخدام طرق الدفع المتعددة واحصل على وصول فوري للدورة.</p>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                                <div class="step-item" data-aos="fade-up" data-aos-delay="400">
                                    <div class="step-icon">
                                        <i class="fas fa-laptop"></i>
                                        <span class="step-number">4</span>
                                    </div>
                                    <h3 class="step-title">التعلم</h3>
                                    <p class="step-description">ابدأ التعلم فوراً من أي جهاز وفي أي وقت مع الوصول الدائم للمحتوى.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5 align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="how-it-works-content">
                        <h3 class="mb-4">منصة تعليمية متكاملة للطلاب والمدرسين</h3>
                        <p class="mb-4">
                            توفر منصتنا كل ما تحتاجه لتجربة تعليمية ناجحة. سواء كنت طالباً يبحث عن تطوير مهاراته أو مدرساً يرغب في مشاركة خبراته، فإن منصتنا توفر لك الأدوات والموارد اللازمة للنجاح.
                        </p>
                        <ul class="check-list">
                            <li><i class="fas fa-check-circle"></i> محتوى تفاعلي عالي الجودة</li>
                            <li><i class="fas fa-check-circle"></i> اختبارات وتمارين عملية</li>
                            <li><i class="fas fa-check-circle"></i> شهادات معتمدة بعد إكمال الدورة</li>
                            <li><i class="fas fa-check-circle"></i> دعم فني على مدار الساعة</li>
                        </ul>
                        <a href="/register" class="btn btn-primary mt-3">ابدأ الآن</a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="how-it-works-image">
                        <img src="https://img.freepik.com/free-photo/e-learning-student-home-concept_23-2148688942.jpg" alt="كيفية عمل المنصة" class="img-fluid rounded-4 shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section py-5" id="pricing">
        <div class="container-fluid px-lg-5">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">خطط <span class="text-highlight">الأسعار</span></h2>
                <p class="section-description">
                    اختر الخطة المناسبة لاحتياجاتك التعليمية
                </p>
            </div>

            <div class="row g-4" data-aos="fade-up">
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card h-100">
                        <div class="pricing-header">
                            <h3 class="pricing-title">المجانية</h3>
                            <div class="pricing-price">
                                <span class="currency">$</span>
                                <span class="amount">0</span>
                                <span class="period">/شهرياً</span>
                            </div>
                        </div>
                        <div class="pricing-body">
                            <ul class="pricing-features">
                                <li><i class="fas fa-check"></i> الوصول إلى الدورات المجانية</li>
                                <li><i class="fas fa-check"></i> منتدى مجتمعي أساسي</li>
                                <li><i class="fas fa-check"></i> موارد تعليمية محدودة</li>
                                <li class="disabled"><i class="fas fa-times"></i> شهادات إتمام</li>
                                <li class="disabled"><i class="fas fa-times"></i> دعم متقدم</li>
                                <li class="disabled"><i class="fas fa-times"></i> مشاريع عملية</li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            <a href="/register" class="btn btn-outline-primary btn-block">ابدأ مجاناً</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card h-100 popular">
                        <div class="popular-badge">الأكثر شيوعاً</div>
                        <div class="pricing-header">
                            <h3 class="pricing-title">الشهرية</h3>
                            <div class="pricing-price">
                                <span class="currency">$</span>
                                <span class="amount">19.99</span>
                                <span class="period">/شهرياً</span>
                            </div>
                        </div>
                        <div class="pricing-body">
                            <ul class="pricing-features">
                                <li><i class="fas fa-check"></i> الوصول إلى جميع الدورات الأساسية</li>
                                <li><i class="fas fa-check"></i> منتدى مجتمعي كامل</li>
                                <li><i class="fas fa-check"></i> موارد تعليمية غير محدودة</li>
                                <li><i class="fas fa-check"></i> شهادات إتمام</li>
                                <li><i class="fas fa-check"></i> دعم متقدم</li>
                                <li class="disabled"><i class="fas fa-times"></i> مشاريع عملية</li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            <a href="/register" class="btn btn-primary btn-block">اشترك الآن</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card h-100">
                        <div class="pricing-header">
                            <h3 class="pricing-title">مدى الحياة</h3>
                            <div class="pricing-price">
                                <span class="currency">$</span>
                                <span class="amount">199</span>
                                <span class="period">/لمرة واحدة</span>
                            </div>
                        </div>
                        <div class="pricing-body">
                            <ul class="pricing-features">
                                <li><i class="fas fa-check"></i> الوصول إلى جميع الدورات</li>
                                <li><i class="fas fa-check"></i> منتدى مجتمعي متقدم</li>
                                <li><i class="fas fa-check"></i> موارد تعليمية غير محدودة</li>
                                <li><i class="fas fa-check"></i> شهادات إتمام معتمدة</li>
                                <li><i class="fas fa-check"></i> دعم متقدم على مدار الساعة</li>
                                <li><i class="fas fa-check"></i> مشاريع عملية مع المدربين</li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            <a href="/register" class="btn btn-outline-primary btn-block">اشترك الآن</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5" id="cta">
        <div class="container-fluid px-lg-5">
            <div class="cta-container" data-aos="fade-up">
                <div class="row align-items-center">
                    <div class="col-lg-8 mb-4 mb-lg-0">
                        <div class="cta-content">
                            <h2 class="cta-title">استعد لتطوير <span class="text-highlight-white">مهاراتك اليوم</span></h2>
                            <p class="cta-description">انضم إلى آلاف الطلاب الذين يطورون مهاراتهم ويحققون أهدافهم المهنية مع منصتنا التعليمية.</p>
                            <div class="cta-features">
                                <div class="cta-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>لا حاجة لبطاقة ائتمان</span>
                                </div>
                                <div class="cta-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>ضمان استرداد الأموال لمدة 30 يوماً</span>
                                </div>
                                <div class="cta-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>وصول فوري للدورات</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center text-lg-end">
                        <a href="/register" class="btn btn-light btn-lg animated-pulse">ابدأ التعلم مجاناً</a>
                        <p class="mt-3 text-white-50">أو <a href="/courses" class="text-white text-decoration-underline">استكشف الدورات</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                    <h5 class="text-uppercase mb-4">منصة تعليمية</h5>
                    <p class="mb-4">منصة تعليمية متكاملة توفر أفضل الدورات التدريبية عبر الإنترنت بأسعار مناسبة.</p>
                    <div class="d-flex">
                        <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h5 class="mb-4">روابط سريعة</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">الرئيسية</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">عن المنصة</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">الدورات</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">المدربون</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">المدونة</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h5 class="mb-4">المساعدة</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">مركز المساعدة</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">الأسئلة الشائعة</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">الشروط والأحكام</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">سياسة الخصوصية</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">اتصل بنا</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="mb-4">النشرة الإخبارية</h5>
                    <p class="mb-4">اشترك في نشرتنا الإخبارية للحصول على آخر الأخبار والعروض الخاصة.</p>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="البريد الإلكتروني" aria-label="البريد الإلكتروني">
                        <button class="btn btn-primary" type="button">اشتراك</button>
                    </div>
                    <p class="small"><i class="fas fa-lock me-2"></i> لن نشارك بياناتك مع أي طرف ثالث</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-7 mb-3 mb-md-0">
                    <p class="mb-0">&copy; 2023 TOTO جميع الحقوق محفوظة</p>
                </div>
                <div class="col-md-5 text-md-end">
                    <a href="#" class="text-white text-decoration-none me-3">سياسة الخصوصية</a>
                    <a href="#" class="text-white text-decoration-none">الشروط والأحكام</a>
                </div>
            </div>
        </div>
    </footer>

@section('styles')
<style>
    /* Hero Section Styles */
    .hero-section {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: #fff;
        padding: 120px 0 80px;
        position: relative;
        overflow: hidden;
    }

    .min-vh-80 {
        min-height: 80vh;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.15);
        border-radius: 50px;
        padding: 8px 16px;
        margin-bottom: 25px;
    }

    .badge-icon {
        margin-right: 8px;
        color: #ffcc00;
    }

    .hero-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 25px;
        line-height: 1.2;
    }

    .hero-description {
        font-size: 1.1rem;
        margin-bottom: 30px;
        opacity: 0.9;
    }

    .hero-image-container {
        position: relative;
    }

    .hero-image {
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .floating-card {
        position: absolute;
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        padding: 15px;
        display: flex;
        align-items: center;
        z-index: 2;
    }

    .card-stats {
        top: 20px;
        right: -20px;
    }

    .card-message {
        bottom: 30px;
        left: -20px;
        max-width: 280px;
    }

    .avatar-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
    }

    .hero-stat-item {
        text-align: center;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 15px 10px;
    }

    .hero-stat-item h4 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: #ffcc00;
    }

    .hero-shape-divider {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        overflow: hidden;
        line-height: 0;
    }

    /* Features Section Styles */
    .feature-card {
        background-color: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        text-align: center;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .feature-icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 32px;
        color: #ffffff;
    }

    .feature-icon-wrapper.blue { background-color: #4361ee; }
    .feature-icon-wrapper.green { background-color: #3bc454; }
    .feature-icon-wrapper.purple { background-color: #8000FF; }
    .feature-icon-wrapper.orange { background-color: #ff9f43; }
    .feature-icon-wrapper.red { background-color: #ff5e5e; }
    .feature-icon-wrapper.teal { background-color: #41cdcd; }

    .feature-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }

    /* Courses Section Styles */
    .course-card {
        background-color: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .course-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .course-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .course-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .course-card:hover .course-image img {
        transform: scale(1.1);
    }

    .course-category {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: rgba(67, 97, 238, 0.9);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .course-rating {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: rgba(255, 204, 0, 0.9);
        color: #333;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .course-content {
        padding: 20px;
    }

    .course-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 10px;
        line-height: 1.4;
        height: 3em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .course-instructor {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .course-meta {
        display: flex;
        justify-content: space-between;
        color: #666;
        font-size: 0.85rem;
    }

    .course-price {
        font-weight: 700;
        color: #4361ee;
        font-size: 1.1rem;
    }

    /* Testimonials Section Styles */
    .testimonial-card {
        background-color: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .testimonial-rating {
        color: #ffcc00;
        margin-bottom: 15px;
    }

    .testimonial-text {
        font-size: 1rem;
        line-height: 1.6;
        color: #555;
        font-style: italic;
    }

    .testimonial-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* How It Works Section Styles */
    .steps-container {
        position: relative;
        padding: 30px 0;
    }

    .step-line {
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #e0e0e0;
        z-index: 1;
        display: none;
    }

    @media (min-width: 992px) {
        .step-line {
            display: block;
        }
    }

    .step-item {
        text-align: center;
        position: relative;
        z-index: 2;
        padding: 20px;
    }

    .step-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #4361ee;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 30px;
        position: relative;
    }

    .step-number {
        position: absolute;
        top: -10px;
        right: -10px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #ffcc00;
        color: #333;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 700;
    }

    .step-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .check-list {
        list-style: none;
        padding: 0;
        margin: 0 0 20px 0;
    }

    .check-list li {
        padding: 8px 0;
        display: flex;
        align-items: center;
    }

    .check-list li i {
        color: #3bc454;
        margin-right: 10px;
        font-size: 1.1rem;
    }

    /* Pricing Section Styles */
    .pricing-card {
        background-color: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
    }

    .pricing-card.popular {
        border: 2px solid #4361ee;
        transform: scale(1.05);
    }

    .pricing-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .pricing-card.popular:hover {
        transform: translateY(-10px) scale(1.05);
    }

    .popular-badge {
        position: absolute;
        top: 0;
        right: 30px;
        background-color: #4361ee;
        color: white;
        padding: 5px 15px;
        border-radius: 0 0 10px 10px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .pricing-header {
        text-align: center;
        padding: 30px;
        border-bottom: 1px solid #eee;
    }

    .pricing-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .pricing-price {
        font-size: 2.5rem;
        font-weight: 700;
        color: #4361ee;
        margin-bottom: 10px;
    }

    .currency {
        font-size: 1.5rem;
        vertical-align: super;
    }

    .period {
        font-size: 1rem;
        color: #666;
        font-weight: 400;
    }

    .pricing-body {
        padding: 30px;
    }

    .pricing-features {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pricing-features li {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
    }

    .pricing-features li:last-child {
        border-bottom: none;
    }

    .pricing-features li i {
        margin-right: 10px;
        color: #3bc454;
    }

    .pricing-features li.disabled {
        color: #999;
    }

    .pricing-features li.disabled i {
        color: #ff5e5e;
    }

    .pricing-footer {
        padding: 20px 30px 30px;
        text-align: center;
    }

    .btn-block {
        display: block;
        width: 100%;
    }

    /* CTA Section Styles */
    .cta-section {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
    }

    .cta-container {
        padding: 60px 40px;
        border-radius: 20px;
        background-color: rgba(0, 0, 0, 0.1);
    }

    .cta-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
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
        font-size: 1.1rem;
        margin-bottom: 30px;
        opacity: 0.9;
    }

    .cta-features {
        display: flex;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .cta-feature {
        display: flex;
        align-items: center;
        margin-right: 30px;
        margin-bottom: 10px;
    }

    .cta-feature i {
        color: #ffcc00;
        margin-right: 10px;
    }

    /* RTL Specific Styles */
    html[dir="rtl"] .badge-icon {
        margin-right: 0;
        margin-left: 8px;
    }

    html[dir="rtl"] .check-list li i {
        margin-right: 0;
        margin-left: 10px;
    }

    html[dir="rtl"] .pricing-features li i {
        margin-right: 0;
        margin-left: 10px;
    }

    html[dir="rtl"] .cta-feature i {
        margin-right: 0;
        margin-left: 10px;
    }

    html[dir="rtl"] .cta-feature {
        margin-right: 0;
        margin-left: 30px;
    }

    html[dir="rtl"] .avatar-img {
        margin-right: 0;
        margin-left: 10px;
    }

    /* Responsive Adjustments */
    @media (max-width: 991.98px) {
        .hero-title {
            font-size: 2.5rem;
        }

        .cta-title {
            font-size: 2rem;
        }

        .pricing-card.popular {
            transform: scale(1);
        }

        .pricing-card.popular:hover {
            transform: translateY(-10px);
        }
    }

    @media (max-width: 767.98px) {
        .hero-section {
            padding: 80px 0 60px;
        }

        .hero-title {
            font-size: 2rem;
        }

        .cta-container {
            padding: 40px 20px;
        }

        .cta-title {
            font-size: 1.8rem;
        }
    }

    /* Animation Styles */
    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(255, 204, 0, 0.7);
        }
        70% {
            transform: scale(1.05);
            box-shadow: 0 0 0 10px rgba(255, 204, 0, 0);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(255, 204, 0, 0);
        }
    }

    .animated-pulse {
        position: relative;
        overflow: hidden;
    }

    .animated-pulse.pulse {
        animation: pulse 1s ease-in-out;
    }

    /* Stats Section Styles */
    .stats-section {
        padding: 70px 0;
        background: linear-gradient(to right, #f8f9fa, #ffffff, #f8f9fa);
        border-top: 1px solid rgba(0,0,0,0.05);
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .stats-item {
        position: relative;
        padding: 30px 15px;
        transition: all 0.3s ease;
        border-radius: 10px;
    }

    .stats-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        background-color: #fff;
    }

    .stats-number {
        position: relative;
        display: inline-block;
        margin-bottom: 15px;
    }

    .stats-number:after {
        content: '';
        position: absolute;
        width: 40px;
        height: 3px;
        background: #2389dd;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
    }

    .stats-label {
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .stats-item {
            margin-bottom: 30px;
        }
    }

    /* Courses Section Styles */
    .course-categories-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        margin-bottom: 40px;
    }

    .course-category-section {
        flex: 1;
        text-align: center;
        background-color: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        position: relative;
    }

    .course-category-section:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .category-header {
        padding: 25px 20px;
        border-bottom: 1px solid #eee;
    }

    .category-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }

    .see-all-link {
        display: inline-flex;
        align-items: center;
        color: #4361ee;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .see-all-link:hover {
        transform: translateX(5px);
    }

    .see-all-link i {
        margin-left: 5px;
    }

    .courses-slider {
        padding: 20px;
    }

    .course-card {
        margin-bottom: 20px;
    }

    .course-card:last-child {
        margin-bottom: 0;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .course-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .course-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .course-card:hover .course-image img {
        transform: scale(1.1);
    }

    .course-category {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: rgba(67, 97, 238, 0.9);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .course-rating {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: rgba(255, 204, 0, 0.9);
        color: #333;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .course-content {
        padding: 20px;
    }

    .course-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 10px;
        line-height: 1.4;
        height: 3em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .course-instructor {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .course-meta {
        display: flex;
        justify-content: space-between;
        color: #666;
        font-size: 0.85rem;
    }

    .course-price {
        font-weight: 700;
        color: #4361ee;
        font-size: 1.1rem;
    }

    .course-details-btn {
        display: inline-block;
        padding: 10px 24px;
        background: linear-gradient(to right, #00c3c9, #00b4d8);
        color: white;
        border: none;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 500;
        font-size: 16px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        z-index: 1;
        margin-top: 20px;
    }

    .course-details-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 195, 201, 0.4);
    }

    .course-details-btn:active {
        transform: translateY(0);
        box-shadow: none;
    }

    .course-details-btn:focus {
        outline: none;
    }

    /* Category Filter Styles */
    .category-filter-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .category-filter-scroll {
        overflow-x: auto;
        white-space: nowrap;
        padding: 10px;
        border-radius: 10px;
        background-color: #f8f9fa;
    }

    .category-filter-items {
        display: flex;
        gap: 10px;
    }

    .category-filter-item {
        padding: 10px 20px;
        border: 2px solid transparent;
        border-radius: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .category-filter-item.active {
        border-color: #4361ee;
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        color: white;
    }

    .category-filter-item:hover {
        border-color: #00c3c9;
        background: linear-gradient(135deg, #00c3c9, #00b4d8);
        color: white;
    }

    .category-indicator {
        display: inline-block;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .category-text {
        font-weight: 500;
    }

    .category-count {
        font-size: 0.9rem;
        color: #666;
    }

    /* Courses Container Styles */
    .all-categories-section {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }

    .course-category-content {
        display: none;
        flex: 1;
        text-align: center;
        background-color: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        position: relative;
    }

    .course-category-content:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .course-category-content.active {
        display: block;
    }
</style>
@endsection

@section('scripts')
@parent
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تصفية الدورات حسب الفئة
        const filterButtons = document.querySelectorAll('.category-filter-item');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // إزالة الفئة النشطة من جميع الأزرار
                filterButtons.forEach(btn => btn.classList.remove('active'));
                
                // إضافة الفئة النشطة للزر المحدد
                this.classList.add('active');
                
                // الحصول على الفئة المحددة
                const category = this.getAttribute('data-category');
                
                // ترشيح البطاقات حسب الفئة
                const cards = document.querySelectorAll('.course-card-item');
                let visibleCount = 0;
                
                cards.forEach(card => {
                    const parentCol = card.closest('.col-lg-4') || card.closest('.col-md-6');
                    if (!parentCol) return;
                    
                    if ((category === 'all' || card.getAttribute('data-category') === category) && visibleCount < 3) {
                        parentCol.style.display = 'block';
                        visibleCount++;
                    } else {
                        parentCol.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endsection