@extends('layouts.app')

@section('title', 'منصة تعليمية - منصة التعلم عبر الإنترنت')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container-fluid px-lg-5">
            <div class="row align-items-center min-vh-80">
                <div class="col-lg-6 order-lg-1 order-2">
                    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                        <div class="hero-badge animate-pulse">
                            <span class="badge-icon"><i class="fas fa-graduation-cap"></i></span>
                            <span class="badge-text">منصة تعليمية متكاملة</span>
                        </div>
                        <h1 class="hero-title animate-typing">طور مهاراتك مع <span class="text-highlight">أفضل المدربين</span> في مكان واحد</h1>
                        <p class="hero-description animate-fade-in">
                            انضم إلى مجتمعنا التعليمي واكتسب المهارات التي تحتاجها للنجاح في عالم اليوم. دورات عالية الجودة بأسعار مناسبة.
                        </p>
                        <div class="hero-buttons animate-slide-up">
                            <a href="/register" class="btn btn-primary btn-lg animated-button">ابدأ التعلم الآن</a>
                            <a href="#how-it-works" class="btn btn-outline-light btn-lg ms-3 animated-button-outline">
                                <i class="fas fa-play-circle me-2"></i> كيف يعمل
                            </a>
                        </div>
                        <div class="hero-stats mt-4 animate-slide-up" data-aos-delay="300">
                            <div class="row">
                                <div class="col-4">
                                    <div class="hero-stat-item animate-counter" data-count="15000">
                                        <h4>15K+</h4>
                                        <p>طالب</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="hero-stat-item animate-counter" data-count="200">
                                        <h4>200+</h4>
                                        <p>دورة</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="hero-stat-item animate-counter" data-count="50">
                                        <h4>50+</h4>
                                        <p>مدرب</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-2 order-1 mb-5 mb-lg-0">
                    <div class="hero-image-container" data-aos="fade-left" data-aos-duration="1200">
                        <div class="hero-image-wrapper">
                            <img src="https://img.freepik.com/free-photo/students-knowing-right-answer_23-2149000158.jpg" alt="طلاب يتعلمون" class="hero-image img-fluid rounded-4 shadow-lg">
                            <div class="image-overlay"></div>
                            <div class="image-shape-1"></div>
                            <div class="image-shape-2"></div>
                            <div class="light-rays"></div>
                        </div>
                        <div class="floating-card card-stats animate-float">
                            <div class="card-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="card-content">
                                <h5 class="counter" data-count="95">0</h5>
                                <p>معدل الرضا</p>
                            </div>
                        </div>
                        <div class="floating-card card-message animate-float" data-float-delay="300">
                            <div class="message-avatar">
                                <img src="https://img.freepik.com/free-photo/young-student-wearing-hijab-using-laptop_23-2149066117.jpg" alt="طالب" class="avatar-img">
                            </div>
                            <div class="message-content">
                                <p>"الدورات ساعدتني في تطوير مهاراتي!"</p>
                            </div>
                        </div>
                        <div class="floating-notification animate-float" data-float-delay="600">
                            <div class="notification-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="notification-content">
                                <p>دورة جديدة متاحة الآن!</p>
                            </div>
                        </div>
                        <div class="floating-achievement animate-float" data-float-delay="900">
                            <div class="achievement-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="achievement-content">
                                <p>أكملت 3 دورات!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-shape-divider">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#ffffff" fill-opacity="1" d="M0,96L48,122.7C96,149,192,203,288,197.3C384,192,480,128,576,117.3C672,107,768,149,864,165.3C960,181,1056,171,1152,154.7C1248,139,1344,117,1392,106.7L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
        </div>
        <div class="hero-particles" id="particles-js"></div>
        <div class="moving-squares"></div>
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
    <section id="about" style="margin-top: 50px; margin-bottom: 50px; padding: 50px 20px; direction: rtl; text-align: center; background: linear-gradient(to bottom, #FAFAFA, #F5F5F5);">
        <div class="container">
            <h2 style="font-size: 32px; color: var(--primary-color); margin-bottom: 20px; text-align: center; position: relative; display: inline-block; font-weight: 700;">
                {{ $aboutSettings['about_title'] ?? 'ما هو TOTO' }}
                <div style="width: 70px; height: 4px; background-color: var(--secondary-color); margin: 15px auto 0;"></div>
            </h2>
            
            <p style="font-size: 18px; line-height: 1.8; color: var(--text-dark); text-align: center; max-width: 900px; margin: 0 auto 50px;">
                {{ $aboutSettings['about_description'] ?? 'هي منصة تعليمية متكاملة تتيح للمعلمين إنشاء فصول دراسية عبر الإنترنت حيث يمكنهم تخزين المواد التعليمية عبر الإنترنت، وإدارة الواجبات والاختبارات ومتابعة مواعيد التسليم TOTO وتقييم النتائج وتزويد الطلاب بالملاحظات، كل ذلك في مكان واحد' }}
            </p>
            
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 40px;">
                <!-- كارد المدرسين -->
                <div style="flex-basis: 320px; text-align: center; background-color: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.4s ease; position: relative; border: 1px solid var(--border-color);" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.08)';">
                    <div style="position: relative; overflow: hidden; height: 200px;">
                        @if(isset($aboutSettings['instructor_image']))
                            <img src="{{ asset('storage/' . $aboutSettings['instructor_image']) }}" alt="للمدرسين" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1.0)';">
                        @else
                            <img src="{{ asset('images/instructor.jpg') }}" alt="للمدرسين" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1.0)';">
                        @endif
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, rgba(0, 51, 102, 0.7), transparent); opacity: 0.8; transition: opacity 0.4s ease;"></div>
                    </div>
                    <div style="padding: 25px 20px;">
                        <h3 style="font-size: 36px; font-weight: 800; margin: 0 0 15px; color: var(--primary-color); position: relative; display: inline-block;">{{ $aboutSettings['instructor_title'] ?? 'للمدرسين' }}</h3>
                        <a href="/register?role=instructor" style="display: inline-block; padding: 14px 30px; background: var(--primary-color); color: var(--secondary-color); border: none; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 18px; transition: all 0.3s ease; position: relative; overflow: hidden; z-index: 1; margin-top: 10px;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(0, 51, 102, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                            {{ $aboutSettings['instructor_button_text'] ?? 'أنشئ فصلاً دراسياً الآن' }}
                        </a>
                    </div>
                </div>
                
                <!-- كارد الطلاب -->
                <div style="flex-basis: 320px; text-align: center; background-color: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: all 0.4s ease; position: relative; border: 1px solid var(--border-color);" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.08)';">
                    <div style="position: relative; overflow: hidden; height: 200px;">
                        @if(isset($aboutSettings['student_image']))
                            <img src="{{ asset('storage/' . $aboutSettings['student_image']) }}" alt="للطلاب" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1.0)';">
                        @else
                            <img src="{{ asset('images/student.jpg') }}" alt="للطلاب" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1.0)';">
                        @endif
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, rgba(0, 51, 102, 0.7), transparent); opacity: 0.8; transition: opacity 0.4s ease;"></div>
                    </div>
                    <div style="padding: 25px 20px; display: flex; flex-direction: column; align-items: center;">
                        <h3 style="font-size: 36px; font-weight: 800; margin: 0 0 15px; color: var(--primary-color); position: relative; display: inline-block;">{{ $aboutSettings['student_title'] ?? 'للطلاب' }}</h3>
                        <a href="/login" style="display: inline-block; padding: 14px 30px; background: var(--secondary-color); color: var(--primary-color); border: none; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 18px; transition: all 0.3s ease; position: relative; overflow: hidden; z-index: 1; margin-top: 10px; min-width: 180px;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(255, 215, 0, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                            {{ $aboutSettings['student_button_text'] ?? 'أدخل رمز الوصول' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الفيديو التعريفي -->
    <section style="padding: 70px 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); direction: rtl; overflow: hidden; margin-bottom: 50px;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 30px;">
            <!-- الجانب النصي -->
            <div style="flex: 1; min-width: 300px; padding: 20px;">
                <div style="position: relative; display: inline-block; margin-bottom: 15px;">
                    <div style="width: 50px; height: 50px; background-color: var(--secondary-color); border-radius: 50%; position: absolute; top: -10px; right: -15px; z-index: -1; opacity: 0.2;"></div>
                    <h2 style="font-size: 32px; font-weight: 700; color: var(--primary-color); margin-bottom: 20px; position: relative;">
                        {{ $videoSettings['video_title'] ?? 'كل ما يمكنك فعله في الفصل الدراسي التقليدي' }}
                        <span style="display: block; color: var(--secondary-color); margin-top: 5px; font-weight: 700;">{{ $videoSettings['video_subtitle'] ?? 'يمكنك فعله مع TOTO' }}</span>
                        <div style="width: 70px; height: 4px; background-color: var(--secondary-color); margin: 15px 0 0;"></div>
                    </h2>
                </div>
                
                <p style="font-size: 18px; line-height: 1.8; color: var(--text-dark); margin-bottom: 25px;">
                    {{ $videoSettings['video_description'] ?? 'تساعد منصة TOTO التعليمية المدارس التقليدية والإلكترونية على إدارة الجداول الدراسية، وتسجيل الحضور، وإدارة المدفوعات، والفصول الافتراضية، كل ذلك في نظام آمن قائم على الحوسبة السحابية.' }}
                </p>
                
                <a href="{{ $videoSettings['video_button_url'] ?? '#features' }}" style="display: inline-block; padding: 12px 25px; background: var(--primary-color); color: var(--secondary-color); border: none; border-radius: 50px; text-decoration: none; font-weight: 500; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(0, 51, 102, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0, 51, 102, 0.3)';">
                    {{ $videoSettings['video_button_text'] ?? 'اكتشف المزيد' }}
                </a>
            </div>
            
            <!-- جانب الفيديو -->
            <div style="flex: 1; min-width: 300px; padding: 20px; position: relative;">
                <div style="position: relative; border-radius: 16px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.15); aspect-ratio: 16/9; border: 3px solid var(--primary-color);">
                    <!-- إطار الفيديو من يوتيوب (يمكنك استبدال رابط الفيديو) -->
                    <iframe width="100%" height="100%" src="{{ $videoSettings['video_embed_url'] ?? 'https://www.youtube.com/embed/dQw4w9WgXcQ' }}" title="فيديو تعريفي عن منصة TOTO" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border: none;"></iframe>
                    
                    <!-- زخارف حول الفيديو -->
                    <div style="position: absolute; width: 80px; height: 80px; background-color: var(--secondary-color); border-radius: 50%; top: -20px; left: -20px; z-index: -1; opacity: 0.3;"></div>
                    <div style="position: absolute; width: 120px; height: 120px; background-color: var(--primary-color); border-radius: 50%; bottom: -40px; right: -30px; z-index: -1; opacity: 0.3;"></div>
                </div>
                
                <!-- زر التشغيل (للزخرفة فقط) -->
                <div style="position: absolute; width: 80px; height: 80px; background-color: var(--primary-color); border-radius: 50%; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; justify-content: center; align-items: center; pointer-events: none; opacity: 0.9;">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="var(--secondary-color)">
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

            <!-- قسم تصفية الفئات (أفقي مع مؤشرات ملونة) -->
            <div class="filter-container" data-aos="fade-up">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-category="all">
                        <i class="fas fa-layer-group"></i> جميع الفئات
                    </button>

                    @foreach($categories ?? [] as $category)
                        <button class="filter-tab" data-category="{{ $category->name }}">
                            <i class="fas fa-circle" style="color: {{ '#' . substr(md5($category->name), 0, 6) }};"></i>
                            {{ $category->name }}
                            <span class="filter-count">{{ $category->courses_count ?? 0 }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="filter-search">
                    <div class="search-box">
                        <input type="text" id="courseSearch" placeholder="ابحث عن دورة...">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="filter-toggles">
                        <button class="view-toggle grid-view active"><i class="fas fa-th"></i></button>
                        <button class="view-toggle list-view"><i class="fas fa-list"></i></button>
                    </div>
                </div>
            </div>

            <!-- عرض الدورات -->
            <div class="courses-grid mt-5">
                @forelse($courses ?? [] as $course)
                    <div class="course-card" data-category="{{ $course->category->name ?? '' }}">
                        <div class="course-image">
                            <div class="course-overlay"></div>
                            @if($course->thumbnail && file_exists(public_path($course->thumbnail)))
                                <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}">
                            @else
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                    <p>بدون صورة</p>
                                </div>
                            @endif
                            <div class="course-badge">
                                @php
                                    $avgRating = number_format($course->rating ?? 0, 1);
                                    $ratingsCount = $course->reviews_count ?? 0;
                                @endphp
                                <div class="course-rating-wrapper">
                                    <span class="course-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($avgRating))
                                                <i class="fas fa-star"></i>
                                            @elseif($i - 0.5 <= $avgRating)
                                                <i class="fas fa-star-half-alt"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                        <span class="rating-text">{{ $avgRating }} ({{ $ratingsCount }})</span>
                                    </span>
                                </div>
                                <span class="course-category-badge" style="background: linear-gradient(135deg, {{ '#' . substr(md5($course->category->name ?? 'عام'), 0, 6) }}, {{ '#' . substr(md5($course->category->name ?? 'عام'), 6, 6) }});">
                                    {{ $course->category->name ?? 'عام' }}
                                </span>
                            </div>
                        </div>
                        <div class="course-content">
                            <h3 class="course-title">{{ $course->title }}</h3>
                            <div class="course-meta">
                                <div class="course-instructor">
                                    <i class="fas fa-user-tie"></i>
                                    <span>{{ $course->instructor->name ?? 'مدرس غير معروف' }}</span>
                                </div>
                                <div class="course-details">
                                    <div class="course-stat">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $course->duration ?? 0 }} ساعة</span>
                                    </div>
                                    <div class="course-stat">
                                        <i class="fas fa-signal"></i>
                                        <span>{{ $course->level ?? 'مبتدئ' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="course-footer">
                                <div class="course-price">${{ number_format($course->price ?? 0, 2) }}</div>
                                <a href="{{ url('/courses/' . $course->id) }}" class="course-btn">
                                    <span>عرض التفاصيل</span>
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="no-courses">
                        <div class="no-courses-content">
                            <i class="fas fa-info-circle"></i>
                            <h3>لا توجد دورات متاحة حالياً</h3>
                            <p>يرجى التحقق لاحقاً، سيتم إضافة دورات جديدة قريباً.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="/courses" class="btn-view-all">
                    <span>عرض جميع الدورات</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
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
                        <img src="https://media.istockphoto.com/id/1358014313/photo/group-of-elementary-students-having-computer-class-with-their-teacher-in-the-classroom.jpg?s=612x612&w=0&k=20&c=3xsykmHXFa9ejL_sP2Xxiow7zdtmKvg15UxXFfgR98Q=" alt="كيفية عمل المنصة" class="img-fluid rounded-4 shadow-lg">
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

    <!-- Partners Section -->
    @if(isset($partnersSettings['partners_list']) && is_array($partnersSettings['partners_list']) && count($partnersSettings['partners_list']) > 0)
    <section class="partners-section py-5" style="background-color: #FAFAFA; padding: 50px 0; text-align: center;">
        <div class="container">
            <h2 style="font-size: 32px; color: var(--primary-color); margin-bottom: 10px; font-weight: 700;">
                {{ $partnersSettings['partners_title'] ?? 'شركاؤنا المميزون' }}
            </h2>
            <p style="color: var(--text-dark); margin-bottom: 30px; max-width: 800px; margin-left: auto; margin-right: auto;">
                {{ $partnersSettings['partners_subtitle'] ?? 'نتعاون مع أفضل المؤسسات والشركات لتقديم تجربة تعليمية متميزة' }}
            </p>
            <div style="width: 70px; height: 4px; background-color: var(--secondary-color); margin: 0 auto 40px;"></div>
            
            <div style="display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 40px; margin-top: 30px;">
                @foreach($partnersSettings['partners_list'] as $partner)
                    @if(isset($partner['logo']))
                        <div style="transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-10px)';" onmouseout="this.style.transform='translateY(0)';">
                            @if(isset($partner['url']) && !empty($partner['url']))
                                <a href="{{ $partner['url'] }}" target="_blank" rel="noopener noreferrer" style="display: block;">
                                    <img src="{{ asset('storage/' . $partner['logo']) }}" alt="{{ $partner['name'] }}" style="max-height: 80px; max-width: 160px; filter: grayscale(100%); opacity: 0.7; transition: all 0.3s ease;" onmouseover="this.style.filter='grayscale(0)'; this.style.opacity='1';" onmouseout="this.style.filter='grayscale(100%)'; this.style.opacity='0.7';">
                                </a>
                            @else
                                <img src="{{ asset('storage/' . $partner['logo']) }}" alt="{{ $partner['name'] }}" style="max-height: 80px; max-width: 160px; filter: grayscale(100%); opacity: 0.7; transition: all 0.3s ease;" onmouseover="this.style.filter='grayscale(0)'; this.style.opacity='1';" onmouseout="this.style.filter='grayscale(100%)'; this.style.opacity='0.7';">
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection

@section('styles')
<style>
    /* تنسيقات إصلاح شريط التنقل */
    .navbar {
        background-color: rgba(0, 51, 102, 0.95) !important;
        backdrop-filter: blur(10px) !important;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3) !important;
        z-index: 1000 !important;
    }
    
    .navbar-brand {
        color: white !important;
    }
    
    .navbar-nav .nav-link {
        color: white !important;
        font-weight: 600 !important;
        padding: 0.8rem 1rem !important;
        font-size: 1.1rem !important;
        text-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
    }
    
    .navbar-nav .nav-link:hover {
        color: var(--secondary-color) !important;
        transform: translateY(-2px);
    }
    
    .navbar-nav .nav-item.active .nav-link {
        color: var(--secondary-color) !important;
    }
    
    .navbar-toggler {
        border: 2px solid rgba(255,255,255,0.5) !important;
        padding: 0.5rem !important;
        background-color: rgba(0,51,102,0.8) !important;
    }
    
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
    }
    
    .dropdown-menu {
        background-color: rgba(0, 51, 102, 0.95) !important;
        border: none !important;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2) !important;
    }
    
    .dropdown-item {
        color: white !important;
        font-weight: 500 !important;
    }
    
    .dropdown-item:hover {
        background-color: rgba(255, 215, 0, 0.2) !important;
        color: var(--secondary-color) !important;
    }
    
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background-color: rgba(0, 51, 102, 0.98) !important;
            padding: 20px !important;
            border-radius: 10px !important;
            margin-top: 10px !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2) !important;
        }
        
        .navbar-nav .nav-link {
            padding: 1rem !important;
            border-bottom: 1px solid rgba(255,255,255,0.1) !important;
        }
        
        .navbar-nav .nav-item:last-child .nav-link {
            border-bottom: none !important;
        }
    }
    
    /* Nav Bar Fixes */
    .navbar {
        background-color: rgba(0, 51, 102, 0.95) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }
    
    .navbar-nav .nav-link {
        color: #fff !important;
        font-weight: 600;
        padding: 0.8rem 1rem;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .navbar-nav .nav-link:hover {
        color: var(--secondary-color) !important;
        transform: translateY(-2px);
    }
    
    .navbar-nav .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        background-color: var(--secondary-color);
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        transition: width 0.3s ease;
    }
    
    .navbar-nav .nav-link:hover::after {
        width: 70%;
    }
    
    .navbar-nav .nav-item.active .nav-link {
        color: var(--secondary-color) !important;
    }
    
    .navbar-nav .nav-item.active .nav-link::after {
        width: 70%;
    }
    
    .navbar-brand img {
        height: 45px;
        transition: all 0.3s ease;
    }
    
    .navbar-toggler {
        border: none;
        padding: 0.5rem;
    }
    
    .navbar-toggler:focus {
        box-shadow: none;
        outline: none;
    }
    
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
    
    /* Enhanced Hero Section Styles */
    .hero-section {
        position: relative;
        padding: 120px 0 100px;
        background: linear-gradient(135deg, #0a2e4d 0%, #1a4e7d 100%);
        color: white;
        overflow: hidden;
        min-height: 100vh;
    }
    
    .hero-particles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
    }
    
    .hero-shape-divider {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        z-index: 1;
        line-height: 0;
    }
    
    .hero-content {
        position: relative;
        z-index: 10;
    }
    
    .moving-squares {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        background: linear-gradient(45deg, rgba(255, 204, 0, 0.3), transparent);
        transition: all 0.4s ease;
        z-index: -1;
    }
    
    .animated-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    
    .animated-button:hover::before {
        left: 0;
    }
    
    .animated-button-outline {
        border: 2px solid rgba(255, 255, 255, 0.3);
        background: transparent;
        transition: all 0.4s ease;
    }
    
    .animated-button-outline:hover {
        border-color: var(--secondary-color);
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .hero-image-container {
        position: relative;
        padding: 10px;
        z-index: 10;
    }

    .hero-image-wrapper {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        transform: perspective(1000px) rotateY(-5deg);
        transition: all 0.5s ease;
        border: 4px solid rgba(255, 255, 255, 0.1);
    }

    .hero-image-wrapper:before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #FFD700, #0a2e4d, #FFD700, #0a2e4d);
        background-size: 400%;
        z-index: -1;
        filter: blur(10px);
        opacity: 0.6;
        animation: glowingBorder 10s linear infinite;
    }

    @keyframes glowingBorder {
        0% { background-position: 0 0; }
        50% { background-position: 400% 0; }
        100% { background-position: 0 0; }
    }

    .hero-image-wrapper:hover {
        transform: perspective(1000px) rotateY(0deg) scale(1.02);
        border-color: rgba(255, 215, 0, 0.3);
    }

    .hero-image {
        width: 100%;
        border-radius: 12px;
        transition: all 0.7s ease;
        filter: contrast(1.1) brightness(1.05);
        transform: scale(1.01);
    }

    .hero-image-wrapper:hover .hero-image {
        transform: scale(1.08);
        filter: contrast(1.15) brightness(1.1);
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(10, 46, 77, 0.3) 0%, rgba(0, 0, 0, 0.6) 100%);
        z-index: 1;
        transition: opacity 0.3s ease;
    }

    .hero-image-wrapper:hover .image-overlay {
        opacity: 0.4;
    }

    .image-shape-1, .image-shape-2 {
        position: absolute;
        border-radius: 50%;
        z-index: -1;
        filter: blur(20px);
    }

    .image-shape-1 {
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.4) 0%, rgba(255, 215, 0, 0) 70%);
        bottom: -40px;
        right: -50px;
        animation: pulse 6s infinite alternate ease-in-out;
    }

    .image-shape-2 {
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(0, 180, 255, 0.3) 0%, rgba(0, 180, 255, 0) 70%);
        top: -40px;
        left: -50px;
        animation: pulse 7s infinite alternate-reverse ease-in-out;
    }

    .light-rays {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(ellipse at center, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
        opacity: 0;
        transition: opacity 0.5s;
        z-index: 2;
        pointer-events: none;
        mix-blend-mode: overlay;
        animation: fadeInOut 5s infinite alternate;
    }

    .hero-image-wrapper:hover .light-rays {
        opacity: 0.8;
    }

    .floating-card {
        position: absolute;
        background: rgba(255, 255, 255, 0.97);
        border-radius: 14px;
        padding: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        display: flex;
        align-items: center;
        z-index: 20;
        transform: translateY(30px);
        opacity: 0;
        animation: floatIn 0.6s forwards ease-out;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .card-stats {
        bottom: 30px;
        right: -30px;
        animation-delay: 0.5s;
    }

    .card-message {
        top: 20%;
        left: -40px;
        animation-delay: 0.8s;
    }

    .floating-notification {
        position: absolute;
        background: rgba(255, 255, 255, 0.97);
        border-radius: 14px;
        padding: 12px 18px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        display: flex;
        align-items: center;
        z-index: 20;
        top: 60%;
        right: -25px;
        transform: translateY(30px);
        opacity: 0;
        animation: floatIn 0.6s forwards ease-out;
        animation-delay: 1.1s;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .floating-achievement {
        position: absolute;
        background: rgba(255, 255, 255, 0.97);
        border-radius: 14px;
        padding: 12px 18px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        display: flex;
        align-items: center;
        z-index: 20;
        bottom: 45%;
        left: -30px;
        transform: translateY(30px);
        opacity: 0;
        animation: floatIn 0.6s forwards ease-out;
        animation-delay: 1.4s;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    @keyframes floatIn {
        0% {
            opacity: 0;
            transform: translateY(30px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-icon, .notification-icon, .achievement-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #0a2e4d 0%, #1a4e7d 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFD700;
        font-size: 1.1rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }

    .card-content, .notification-content, .achievement-content {
        padding-left: 12px;
        color: #0a2e4d;
    }

    .card-content h5 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        color: #0a2e4d;
        background: linear-gradient(to right, #0a2e4d, #1a4e7d);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .card-content p, .notification-content p, .achievement-content p {
        font-size: 0.85rem;
        margin: 3px 0 0;
        font-weight: 500;
        opacity: 0.7;
    }

    .message-avatar {
        width: 38px;
        height: 38px;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        border: 2px solid #FFD700;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .message-avatar:hover .avatar-img {
        transform: scale(1.1);
    }

    .message-content {
        padding-left: 12px;
        color: #0a2e4d;
    }

    .message-content p {
        font-size: 0.85rem;
        margin: 0;
        font-weight: 500;
        font-style: italic;
    }

    /* تنسيقات الأقسام الإضافية */
    .stats-section {
        background-color: #fff;
        padding: 60px 0;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .stats-item {
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
        padding: 20px;
        border-radius: 10px;
    }

    .stats-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        background-color: rgba(255, 255, 255, 0.8);
    }

    .stats-number {
        font-size: 3rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 10px;
        position: relative;
        display: inline-block;
    }

    .stats-label {
        font-size: 1.1rem;
        color: #444;
        margin: 0;
    }

    /* قسم الخصائص */
    .features-section {
        padding: 80px 0;
        background-color: #f8f9fa;
    }

    .section-header {
        margin-bottom: 60px;
        text-align: center;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
        position: relative;
        display: inline-block;
    }

    .section-description {
        font-size: 1.2rem;
        color: #555;
        max-width: 800px;
        margin: 0 auto;
    }

    .text-highlight {
        color: var(--secondary-color);
        position: relative;
    }

    .text-highlight::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 30%;
        background-color: rgba(255, 215, 0, 0.2);
        z-index: -1;
        border-radius: 3px;
    }

    .feature-card {
        background-color: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    }

    .feature-icon-wrapper {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        font-size: 1.8rem;
        color: white;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .blue {
        background: linear-gradient(135deg, #1976d2, #0a2e4d);
    }

    .green {
        background: linear-gradient(135deg, #2ecc71, #1cac4f);
    }

    .purple {
        background: linear-gradient(135deg, #9c27b0, #7b1fa2);
    }

    .orange {
        background: linear-gradient(135deg, #ff9800, #f57c00);
    }

    .red {
        background: linear-gradient(135deg, #f44336, #d32f2f);
    }

    .teal {
        background: linear-gradient(135deg, #009688, #00796b);
    }

    .feature-title {
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--primary-color);
    }

    .feature-description {
        color: #666;
        line-height: 1.6;
    }

    /* قسم الدورات */
    .courses-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #f5f7fa 0%, #f8f9fa 100%);
    }

    .filter-container {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 30px;
        padding: 15px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .filter-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .filter-tab {
        padding: 8px 15px;
        border: 1px solid #eee;
        border-radius: 30px;
        background: white;
        color: #555;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .filter-tab.active, .filter-tab:hover {
        background-color: var(--primary-color);
        color: var(--secondary-color);
        border-color: var(--primary-color);
    }

    .filter-count {
        background: rgba(0, 0, 0, 0.1);
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-left: 5px;
    }

    .filter-search {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .search-box {
        position: relative;
    }

    .search-box input {
        padding: 10px 15px 10px 40px;
        border: 1px solid #eee;
        border-radius: 30px;
        width: 250px;
        font-size: 0.9rem;
    }

    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
    }

    .filter-toggles {
        display: flex;
        gap: 5px;
    }

    .view-toggle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid #eee;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .view-toggle.active, .view-toggle:hover {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }

    .course-card {
        background-color: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        height: 100%;
    }

    .course-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
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

    .course-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
    }

    .no-image {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        background-color: #edf2f7;
        color: #a0aec0;
    }

    .course-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .course-rating-wrapper {
        background-color: rgba(0, 0, 0, 0.7);
        border-radius: 20px;
        padding: 5px 10px;
        display: flex;
        align-items: center;
        color: white;
    }

    .course-rating {
        display: flex;
        align-items: center;
        font-size: 0.8rem;
    }

    .course-rating i {
        color: #ffc107;
        margin-right: 2px;
    }

    .rating-text {
        margin-left: 5px;
    }

    .course-category-badge {
        background-color: var(--primary-color);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .course-content {
        padding: 20px;
    }

    .course-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--primary-color);
        height: 2.6em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .course-meta {
        margin-bottom: 15px;
    }

    .course-instructor {
        display: flex;
        align-items: center;
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }

    .course-instructor i {
        margin-right: 5px;
        color: var(--primary-color);
    }

    .course-details {
        display: flex;
        justify-content: space-between;
    }

    .course-stat {
        display: flex;
        align-items: center;
        color: #666;
        font-size: 0.85rem;
    }

    .course-stat i {
        margin-right: 5px;
        color: var(--primary-color);
    }

    .course-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .course-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .course-btn {
        display: flex;
        align-items: center;
        padding: 8px 15px;
        background-color: var(--primary-color);
        color: white;
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .course-btn i {
        margin-left: 5px;
    }

    .course-btn:hover {
        background-color: var(--primary-color);
        color: var(--secondary-color);
        transform: translateY(-3px);
    }

    .no-courses {
        grid-column: 1 / -1;
        background-color: white;
        border-radius: 15px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .no-courses-content {
        max-width: 500px;
        margin: 0 auto;
    }

    .no-courses i {
        font-size: 3rem;
        color: #cbd5e0;
        margin-bottom: 20px;
    }

    .no-courses h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: var(--primary-color);
    }

    .no-courses p {
        color: #718096;
    }

    .btn-view-all {
        display: inline-flex;
        align-items: center;
        padding: 12px 30px;
        background-color: var(--primary-color);
        color: white;
        border-radius: 30px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-view-all i {
        margin-left: 8px;
    }

    .btn-view-all:hover {
        background-color: #004080;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        color: var(--secondary-color);
    }

    /* قسم آراء الطلاب */
    .testimonials-section {
        padding: 80px 0;
        background-color: white;
    }

    .testimonial-card {
        background-color: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .testimonial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    }

    .testimonial-card::before {
        content: '\201C';
        position: absolute;
        top: 20px;
        left: 20px;
        font-size: 5rem;
        color: rgba(0, 0, 0, 0.05);
        line-height: 1;
    }

    .testimonial-rating {
        margin-bottom: 15px;
    }

    .testimonial-rating i {
        color: #ffc107;
        margin-right: 2px;
    }

    .testimonial-text {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #555;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .testimonial-author {
        display: flex;
        align-items: center;
    }

    .testimonial-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary-color);
    }

    /* قسم كيف يعمل */
    .how-it-works-section {
        padding: 80px 0;
        background-color: #f8f9fa;
    }

    .steps-container {
        position: relative;
        padding: 40px 0;
    }

    .step-line {
        position: absolute;
        top: 70px;
        left: 10%;
        right: 10%;
        height: 3px;
        background-color: var(--primary-color);
        z-index: 1;
    }

    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .step-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        color: white;
        font-size: 1.8rem;
        position: relative;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .step-number {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: var(--secondary-color);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 700;
    }

    .step-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--primary-color);
    }

    .step-description {
        color: #666;
        line-height: 1.6;
    }

    .how-it-works-content {
        padding: 20px;
    }

    .how-it-works-content h3 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 20px;
    }

    .how-it-works-content p {
        color: #555;
        line-height: 1.7;
        margin-bottom: 20px;
    }

    .check-list {
        list-style: none;
        padding: 0;
        margin: 0 0 20px;
    }

    .check-list li {
        margin-bottom: 10px;
        display: flex;
        align-items: flex-start;
        color: #555;
    }

    .check-list li i {
        color: var(--primary-color);
        margin-right: 10px;
        margin-top: 5px;
    }

    .how-it-works-image {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    /* قسم خطط الأسعار */
    .pricing-section {
        padding: 80px 0;
        background-color: white;
    }

    .pricing-card {
        background-color: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .pricing-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .pricing-card.popular {
        border: 2px solid var(--primary-color);
        transform: scale(1.05);
        z-index: 2;
    }

    .pricing-card.popular:hover {
        transform: translateY(-15px) scale(1.05);
    }

    .popular-badge {
        position: absolute;
        top: 20px;
        right: -35px;
        background-color: var(--secondary-color);
        color: var(--primary-color);
        padding: 5px 40px;
        font-size: 0.8rem;
        font-weight: 600;
        transform: rotate(45deg);
    }

    .pricing-header {
        background-color: var(--primary-color);
        color: white;
        padding: 30px;
        text-align: center;
    }

    .pricing-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .pricing-price {
        position: relative;
        display: inline-block;
    }

    .currency {
        position: absolute;
        top: 0;
        left: -15px;
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--secondary-color);
    }

    .amount {
        font-size: 3.5rem;
        font-weight: 700;
        color: var(--secondary-color);
        line-height: 1;
    }

    .period {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.7);
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
        display: flex;
        align-items: center;
        color: #555;
        font-size: 1rem;
        border-bottom: 1px solid #eee;
    }

    .pricing-features li:last-child {
        border-bottom: none;
    }

    .pricing-features li i {
        margin-right: 10px;
        font-size: 1.1rem;
    }

    .pricing-features li i.fa-check {
        color: var(--primary-color);
    }

    .pricing-features li i.fa-times {
        color: #e74c3c;
    }

    .pricing-features li.disabled {
        color: #aaa;
    }

    .pricing-footer {
        padding: 0 30px 30px;
        text-align: center;
    }

    /* قسم CTA */
    .cta-section {
        padding: 80px 0;
        background-color: var(--primary-color);
        color: white;
    }

    .cta-container {
        padding: 50px;
        border-radius: 15px;
        background: linear-gradient(135deg, rgba(0, 51, 102, 0.8) 0%, rgba(0, 31, 82, 0.9) 100%);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
    }

    .cta-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml;utf8,<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path d="M0 0 L50 50 L0 100 Z" fill="rgba(255,255,255,0.03)"/></svg>') repeat;
        opacity: 0.2;
    }

    .cta-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .text-highlight-white {
        color: var(--secondary-color);
        position: relative;
    }

    .text-highlight-white::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 30%;
        background-color: rgba(255, 255, 255, 0.1);
        z-index: -1;
        border-radius: 3px;
    }

    .cta-description {
        font-size: 1.2rem;
        margin-bottom: 30px;
        opacity: 0.9;
        max-width: 700px;
    }

    .cta-features {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
    }

    .cta-feature {
        display: flex;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.1);
        padding: 10px 20px;
        border-radius: 30px;
    }

    .cta-feature i {
        margin-right: 10px;
        color: var(--secondary-color);
    }

    .animated-pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }

    /* قسم الشركاء */
    .partners-section {
        padding: 60px 0;
        background-color: #fff;
    }

    /* تنسيقات متكررة */
    .min-vh-80 {
        min-height: 80vh;
    }

    .btn-outline-light {
        color: white;
        border-color: white;
    }

    .btn-outline-light:hover {
        background-color: white;
        color: var(--primary-color);
    }

    /* تنسيقات متجاوبة */
    @media (max-width: 991.98px) {
        .feature-card, .testimonial-card, .pricing-card {
            margin-bottom: 30px;
        }
        
        .step-line {
            display: none;
        }
        
        .step-item {
            margin-bottom: 30px;
        }
        
        .pricing-card.popular {
            transform: none;
        }
        
        .pricing-card.popular:hover {
            transform: translateY(-15px);
        }
        
        .cta-container {
            padding: 30px;
        }
        
        .cta-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 767.98px) {
        .section-title {
            font-size: 2rem;
        }
        
        .hero-title {
            font-size: 2rem;
        }
        
        .filter-container {
            flex-direction: column;
            align-items: stretch;
            gap: 15px;
        }
        
        .filter-tabs, .filter-search {
            width: 100%;
            justify-content: center;
        }
        
        .search-box input {
            width: 100%;
        }
        
        .cta-features {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>
@endsection

@section('scripts')
@parent
<!-- Particle.js for background effect -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize particles background
        if (typeof particlesJS !== 'undefined') {
            particlesJS("particles-js", {
                "particles": {
                    "number": {
                        "value": 80,
                        "density": { "enable": true, "value_area": 800 }
                    },
                    "color": { "value": "#ffffff" },
                    "shape": {
                        "type": "circle",
                        "stroke": { "width": 0, "color": "#000000" },
                        "polygon": { "nb_sides": 5 }
                    },
                    "opacity": {
                        "value": 0.3,
                        "random": true,
                        "anim": { "enable": false }
                    },
                    "size": {
                        "value": 3,
                        "random": true,
                        "anim": { "enable": false }
                    },
                    "line_linked": {
                        "enable": true,
                        "distance": 150,
                        "color": "#ffffff",
                        "opacity": 0.1,
                        "width": 1
                    },
                    "move": {
                        "enable": true,
                        "speed": 2,
                        "direction": "none",
                        "random": true,
                        "straight": false,
                        "out_mode": "out",
                        "bounce": false,
                    }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": {
                        "onhover": { "enable": true, "mode": "grab" },
                        "onclick": { "enable": true, "mode": "push" },
                        "resize": true
                    },
                    "modes": {
                        "grab": { "distance": 140, "line_linked": { "opacity": 0.5 } },
                        "push": { "particles_nb": 4 }
                    }
                },
                "retina_detect": true
            });
        }

        // Animate counters
        const counterElements = document.querySelectorAll('.counter');
        
        counterElements.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-count'));
            const duration = 2000; // 2 seconds
            const increment = target / (duration / 30); // Update every 30ms
            let current = 0;
            
            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.ceil(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            
            // Start animation after 3 seconds
            setTimeout(() => {
                updateCounter();
            }, 3000);
        });
        
        // Add float delay to elements
        const floatElements = document.querySelectorAll('.animate-float');
        floatElements.forEach(el => {
            const delay = el.getAttribute('data-float-delay') || 0;
            el.style.setProperty('--float-delay', `${delay}ms`);
        });
        
        // Add 3D hover effect to hero image
        const heroImage = document.querySelector('.hero-image-wrapper');
        heroImage.addEventListener('mousemove', (e) => {
            const rect = heroImage.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const moveX = (x - centerX) / 20;
            const moveY = (y - centerY) / 20;
            
            heroImage.style.transform = `perspective(1000px) rotateY(${moveX}deg) rotateX(${-moveY}deg) scale(1.02)`;
            
            // Make light effect follow cursor
            const lightRays = heroImage.querySelector('.light-rays');
            if (lightRays) {
                lightRays.style.background = `radial-gradient(circle at ${x}px ${y}px, rgba(255, 255, 255, 0.4) 0%, rgba(255, 255, 255, 0) 60%)`;
                lightRays.style.opacity = '0.8';
            }
        });
        
        heroImage.addEventListener('mouseleave', () => {
            heroImage.style.transform = 'perspective(1000px) rotateY(-5deg) rotateX(0) scale(1)';
            
            const lightRays = heroImage.querySelector('.light-rays');
            if (lightRays) {
                lightRays.style.background = 'radial-gradient(ellipse at center, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 70%)';
                lightRays.style.opacity = '0';
            }
        });
        
        // Rest of your existing JavaScript code
        // ... existing code ...
    });
</script>
@endsection