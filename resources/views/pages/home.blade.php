@extends('layouts.app')

@section('title', 'منصة تعليمية - منصة التعلم عبر الإنترنت')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section" id="home" style="background-color: #ffffff !important; background-image: none !important; background: #ffffff !important;">
        <div class="container px-lg-5">
            <div class="row align-items-center min-vh-80">
                <div class="col-lg-6">
                    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000" style="text-align: right; direction: rtl;">
                        <div class="d-flex align-items-center mb-3 justify-content-end">
                            <span>بوابتك للتعلم مدى الحياة</span>
                            <span class="icon me-2 ms-2"><i class="fas fa-book"></i></span>
                        </div>
                        <h1 class="hero-title mb-4">أطلق إمكاناتك مع <span class="text-highlight">التعلم عبر الإنترنت</span></h1>
                        <p class="hero-description mb-4">
                            اكتشف عالمًا من المعرفة والفرص مع منصتنا التعليمية عبر الإنترنت وابدأ مسارًا مهنيًا جديدًا.
                        </p>
                        <div class="hero-buttons mb-5">
                            <a href="/courses" class="btn btn-primary btn-lg">عرض جميع الدورات <i class="fas fa-arrow-left ms-2"></i></a>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="student-info text-end">
                                <h4 class="mb-1">2000 طالب</h4>
                                <p class="mb-0">انضم إلى فصولنا الافتراضية</p>
                            </div>
                            <div class="student-avatars ms-4 me-0">
                                <img src="assets/img/avatar-1.png" alt="طالب" class="rounded-circle">
                                <img src="assets/img/avatar-2.png" alt="طالب" class="rounded-circle">
                                <div class="avatar-count rounded-circle">+</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image-container position-relative" data-aos="fade-left" data-aos-duration="1200">
                        <img src="assets/img/hero-students.jpg" alt="طلاب يتعلمون" class="img-fluid rounded-4">
                        
                        <div class="rating-badge position-absolute">
                            <div class="d-flex align-items-center">
                                <div class="rating-icon me-2">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="rating-info">
                                    <h4 class="mb-0">4.5</h4>
                                    <p class="mb-0">(2.4 ألف تقييم)</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="course-badge position-absolute">
                            <div class="d-flex align-items-center">
                                <div class="course-icon me-2">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="course-info">
                                    <h4 class="mb-0">+100</h4>
                                    <p class="mb-0">دورة تعليمية</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Educational decorative elements -->
        <div class="hero-decoration hero-decoration-1"></div>
        <div class="hero-decoration hero-decoration-2"></div>
        <div class="hero-decoration hero-decoration-3"></div>
        <div class="hero-decoration hero-decoration-4"></div>
        <div class="hero-decoration hero-decoration-5"></div>
        <div class="hero-decoration-dots hero-decoration-dots-1"></div>
        <div class="hero-decoration-dots hero-decoration-dots-2"></div>
        <div class="hero-decoration-circle hero-decoration-circle-1"></div>
        <div class="hero-decoration-circle hero-decoration-circle-2"></div>
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
                    <!-- إطار الفيديو باستخدام laravel-video-embed -->
                    @php
                        // التأكد من أن الرابط يحتوي على t=4 لبدء التشغيل من المقطع الرابع
                        $videoUrl = $videoSettings['video_embed_url'] ?? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=4';
                        if (strpos($videoUrl, 't=') === false) {
                            $videoUrl .= (strpos($videoUrl, '?') !== false ? '&' : '?') . 't=4';
                        }
                        
                        // إضافة enablejsapi=1 للسماح بالتحكم في الفيديو عبر JavaScript
                        if (strpos($videoUrl, 'youtube.com') !== false && strpos($videoUrl, 'enablejsapi=1') === false) {
                            $videoUrl .= (strpos($videoUrl, '?') !== false ? '&' : '?') . 'enablejsapi=1';
                        }
                        
                        // تطبيق التنسيق المخصص على الفيديو المضمن
                        $embedHtml = \Merujan99\LaravelVideoEmbed\Facades\LaravelVideoEmbed::parse($videoUrl);
                        $embedHtml = str_replace('<iframe', '<iframe style="width:100%; height:100%; border:none; position:absolute; top:0; left:0;"', $embedHtml);
                        
                        // إضافة enablejsapi=1 لعنوان الـ iframe إذا كان يوتيوب
                        if (strpos($embedHtml, 'youtube.com') !== false && strpos($embedHtml, 'enablejsapi=1') === false) {
                            $embedHtml = preg_replace('/(src="[^"]*?)(")/i', '$1&enablejsapi=1&origin=' . urlencode(url('/')) . '$2', $embedHtml);
                        }
                    @endphp
                    <div style="position:relative; width:100%; height:0; padding-bottom:56.25%;">
                        {!! $embedHtml !!}
                    </div>
                    
                    <!-- زخارف حول الفيديو -->
                    <div style="position: absolute; width: 80px; height: 80px; background-color: var(--secondary-color); border-radius: 50%; top: -20px; left: -20px; z-index: -1; opacity: 0.3;"></div>
                    <div style="position: absolute; width: 120px; height: 120px; background-color: var(--primary-color); border-radius: 50%; bottom: -40px; right: -30px; z-index: -1; opacity: 0.3;"></div>
                </div>
                
                <!-- زر التشغيل (للزخرفة فقط) -->
                <div style="position: absolute; width: 80px; height: 80px; background-color: var(--primary-color); border-radius: 50%; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; justify-content: center; align-items: center; pointer-events: none; opacity: 0.9; z-index: 2; transition: opacity 0.5s ease;" class="video-play-btn">
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

            <div class="testimonials-grid-wrapper" data-aos="fade-up">
                <div class="row g-4">
                    <!-- الشهادة الأولى -->
                    <div class="col-lg-6 col-xl-3">
                        <div class="testimonial-grid-card h-100">
                            <div class="testimonial-content">
                                <div class="testimonial-quote-icon">
                                    <i class="fas fa-quote-right"></i>
                                </div>
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

                    <!-- الشهادة الثانية -->
                    <div class="col-lg-6 col-xl-3">
                        <div class="testimonial-grid-card h-100">
                            <div class="testimonial-content">
                                <div class="testimonial-quote-icon">
                                    <i class="fas fa-quote-right"></i>
                                </div>
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

                    <!-- الشهادة الثالثة -->
                    <div class="col-lg-6 col-xl-3">
                        <div class="testimonial-grid-card h-100">
                            <div class="testimonial-content">
                                <div class="testimonial-quote-icon">
                                    <i class="fas fa-quote-right"></i>
                                </div>
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

                    <!-- الشهادة الرابعة -->
                    <div class="col-lg-6 col-xl-3">
                        <div class="testimonial-grid-card h-100">
                            <div class="testimonial-content">
                                <div class="testimonial-quote-icon">
                                    <i class="fas fa-quote-right"></i>
                                </div>
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
                
                <!-- Testimonial background decoration -->
                <div class="testimonial-decoration testimonial-decoration-1"></div>
                <div class="testimonial-decoration testimonial-decoration-2"></div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works-section py-5" id="how-it-works">
        <div class="container-fluid px-lg-5">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">كيف <span class="text-highlight">تعمل المنصة</span></h2>
                <p class="section-description">
                    عملية بسيطة وسهلة للبدء في رحلتك التعليمية معنا
                </p>
            </div>

            <div class="how-it-works-wrapper position-relative" data-aos="fade-up">
                <!-- Animated decorative elements -->
                <div class="hiw-decoration hiw-decoration-1"></div>
                <div class="hiw-decoration hiw-decoration-2"></div>
                <div class="hiw-decoration hiw-decoration-3"></div>
                
                <div class="steps-container position-relative">
                    <div class="step-line"></div>
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                            <div class="step-item" data-aos="zoom-in-up" data-aos-delay="100">
                                <div class="step-icon-wrapper">
                                    <div class="step-icon">
                                        <i class="fas fa-user-plus"></i>
                                        <span class="step-number">1</span>
                                    </div>
                                    <div class="step-pulse"></div>
                                </div>
                                <div class="step-content">
                                    <h3 class="step-title">التسجيل</h3>
                                    <p class="step-description">أنشئ حسابك مجاناً واملأ ملفك الشخصي للبدء في رحلتك التعليمية.</p>
                                    <div class="step-arrow">
                                        <i class="fas fa-long-arrow-alt-left"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                            <div class="step-item" data-aos="zoom-in-up" data-aos-delay="200">
                                <div class="step-icon-wrapper">
                                    <div class="step-icon">
                                        <i class="fas fa-search"></i>
                                        <span class="step-number">2</span>
                                    </div>
                                    <div class="step-pulse"></div>
                                </div>
                                <div class="step-content">
                                    <h3 class="step-title">استكشاف</h3>
                                    <p class="step-description">تصفح مجموعتنا الواسعة من الدورات واختر ما يناسب اهتماماتك وأهدافك.</p>
                                    <div class="step-arrow">
                                        <i class="fas fa-long-arrow-alt-left"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                            <div class="step-item" data-aos="zoom-in-up" data-aos-delay="300">
                                <div class="step-icon-wrapper">
                                    <div class="step-icon">
                                        <i class="fas fa-credit-card"></i>
                                        <span class="step-number">3</span>
                                    </div>
                                    <div class="step-pulse"></div>
                                </div>
                                <div class="step-content">
                                    <h3 class="step-title">التسجيل</h3>
                                    <p class="step-description">ادفع بسهولة باستخدام طرق الدفع المتعددة واحصل على وصول فوري للدورة.</p>
                                    <div class="step-arrow">
                                        <i class="fas fa-long-arrow-alt-left"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                            <div class="step-item" data-aos="zoom-in-up" data-aos-delay="400">
                                <div class="step-icon-wrapper">
                                    <div class="step-icon">
                                        <i class="fas fa-laptop"></i>
                                        <span class="step-number">4</span>
                                    </div>
                                    <div class="step-pulse"></div>
                                </div>
                                <div class="step-content">
                                    <h3 class="step-title">التعلم</h3>
                                    <p class="step-description">ابدأ التعلم فوراً من أي جهاز وفي أي وقت مع الوصول الدائم للمحتوى.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5 align-items-center">
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                    <div class="how-it-works-content">
                        <div class="content-badge">
                            <span class="badge-text">منصة متكاملة</span>
                        </div>
                        <h3 class="mb-4 content-title">منصة تعليمية متكاملة للطلاب والمدرسين</h3>
                        <p class="mb-4 content-description">
                            توفر منصتنا كل ما تحتاجه لتجربة تعليمية ناجحة. سواء كنت طالباً يبحث عن تطوير مهاراته أو مدرساً يرغب في مشاركة خبراته، فإن منصتنا توفر لك الأدوات والموارد اللازمة للنجاح.
                        </p>
                        <ul class="check-list animated-list">
                            <li class="check-item" data-aos="fade-up" data-aos-delay="100">
                                <div class="check-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <span>محتوى تفاعلي عالي الجودة</span>
                            </li>
                            <li class="check-item" data-aos="fade-up" data-aos-delay="200">
                                <div class="check-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <span>اختبارات وتمارين عملية</span>
                            </li>
                            <li class="check-item" data-aos="fade-up" data-aos-delay="300">
                                <div class="check-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <span>شهادات معتمدة بعد إكمال الدورة</span>
                            </li>
                            <li class="check-item" data-aos="fade-up" data-aos-delay="400">
                                <div class="check-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <span>دعم فني على مدار الساعة</span>
                            </li>
                        </ul>
                        <div class="mt-4" data-aos="fade-up" data-aos-delay="500">
                            <a href="/register" class="btn-get-started">
                                <span class="btn-text">ابدأ الآن</span>
                                <span class="btn-icon"><i class="fas fa-arrow-left"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000">
                    <div class="how-it-works-image-wrapper">
                        <div class="image-container">
                            <img src="https://media.istockphoto.com/id/1358014313/photo/group-of-elementary-students-having-computer-class-with-their-teacher-in-the-classroom.jpg?s=612x612&w=0&k=20&c=3xsykmHXFa9ejL_sP2Xxiow7zdtmKvg15UxXFfgR98Q=" alt="كيفية عمل المنصة" class="img-fluid rounded-4 shadow-lg main-image">
                            <div class="image-shape-1"></div>
                            <div class="image-shape-2"></div>
                            <div class="image-overlay"></div>
                        </div>
                        <div class="floating-element floating-element-1" data-aos="fade-up" data-aos-delay="200">
                            <div class="floating-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="floating-text">
                                <div class="counter">15K+</div>
                                <div>طالب مسجل</div>
                            </div>
                        </div>
                        <div class="floating-element floating-element-2" data-aos="fade-up" data-aos-delay="400">
                            <div class="floating-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="floating-text">
                                <div class="counter">50+</div>
                                <div>مدرس متخصص</div>
                            </div>
                        </div>
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
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
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
        background: #ffffff !important;
        background-color: #ffffff !important;
        background-image: none !important;
        color: #333;
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
        background: transparent;
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
        position: relative;
        padding: 80px 0;
        background-color: white;
        overflow: hidden;
    }
    
    .testimonials-carousel-wrapper {
        max-width: 900px;
        margin: 0 auto;
        position: relative;
        padding: 30px 0;
    }
    
    .testimonial-carousel-card {
        background-color: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        margin: 30px auto;
        max-width: 800px;
        position: relative;
        z-index: 1;
        transform: translateY(0);
        transition: all 0.5s ease;
        border: 1px solid rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .testimonial-carousel-card:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }
    
    .testimonial-carousel-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }
    
    .testimonial-quote-icon {
        position: absolute;
        top: 30px;
        right: 40px;
        font-size: 4rem;
        color: var(--secondary-color);
        opacity: 0.15;
        transform: rotate(8deg);
        transition: all 0.5s ease;
        filter: drop-shadow(0 10px 15px rgba(0,0,0,0.05));
    }
    
    .testimonial-carousel-card:hover .testimonial-quote-icon {
        transform: rotate(0deg) scale(1.1);
        opacity: 0.2;
    }
    
    .testimonial-content {
        position: relative;
        z-index: 2;
    }
    
    .testimonial-rating {
        margin-bottom: 20px;
        display: inline-flex;
        background: rgba(0, 51, 102, 0.05);
        padding: 8px 15px;
        border-radius: 30px;
        transform: translateY(0);
        transition: all 0.5s ease;
    }
    
    .testimonial-carousel-card:hover .testimonial-rating {
        transform: translateY(-5px);
        background: rgba(0, 51, 102, 0.08);
    }
    
    .testimonial-rating i {
        color: var(--secondary-color);
        font-size: 1.2rem;
        margin-right: 3px;
        filter: drop-shadow(0 2px 3px rgba(0,0,0,0.1));
        transition: all 0.3s ease;
    }
    
    .testimonial-carousel-card:hover .testimonial-rating i {
        transform: rotate(360deg);
        transition-delay: calc(0.05s * var(--i));
    }
    
    .testimonial-text {
        font-size: 1.2rem;
        line-height: 1.8;
        color: #444;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
        font-style: italic;
        opacity: 0.9;
        transition: all 0.5s ease;
    }
    
    .testimonial-carousel-card:hover .testimonial-text {
        color: #222;
        opacity: 1;
    }
    
    .testimonial-author {
        display: flex;
        align-items: center;
        margin-top: 30px;
        position: relative;
        padding: 15px 20px;
        background: rgba(0, 51, 102, 0.03);
        border-radius: 50px;
        transform: translateX(0);
        transition: all 0.5s ease;
    }
    
    .testimonial-carousel-card:hover .testimonial-author {
        transform: translateX(10px);
        background: rgba(0, 51, 102, 0.06);
    }
    
    .testimonial-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary-color);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.5s ease;
        transform: translateX(0) rotate(0deg);
    }
    
    .testimonial-carousel-card:hover .testimonial-avatar {
        transform: translateX(-5px) scale(1.1);
        border-color: var(--secondary-color);
    }
    
    .testimonial-indicators {
        position: relative;
        margin-top: 30px;
        margin-bottom: 0;
        justify-content: center;
    }
    
    .testimonial-indicators button {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: rgba(0, 51, 102, 0.2);
        border: none;
        margin: 0 5px;
        opacity: 1;
        transition: all 0.3s ease;
    }
    
    .testimonial-indicators button.active {
        width: 30px;
        border-radius: 10px;
        background-color: var(--primary-color);
    }
    
    .testimonial-control {
        background: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        opacity: 1;
        top: 50%;
        transform: translateY(-50%);
        transition: all 0.3s ease;
    }
    
    .testimonial-control:hover {
        background-color: var(--primary-color);
        opacity: 1;
        transform: translateY(-50%) scale(1.1);
    }
    
    .carousel-control-prev {
        left: -25px;
    }
    
    .carousel-control-next {
        right: -25px;
    }
    
    .carousel-control-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        color: var(--primary-color);
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }
    
    .testimonial-control:hover .carousel-control-icon {
        color: var(--secondary-color);
    }
    
    /* Carousel Animation */
    .animate-card {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.8s forwards;
    }
    
    .animate-text {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.8s 0.2s forwards;
    }
    
    .animate-author {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.8s 0.4s forwards;
    }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Carousel Fade Effect */
    .carousel-fade .carousel-item {
        opacity: 0;
        transition: opacity 0.6s ease-in-out;
    }
    
    .carousel-fade .carousel-item.active {
        opacity: 1;
    }
    
    /* Testimonial Decorations */
    .testimonial-decoration {
        position: absolute;
        border-radius: 50%;
        filter: blur(40px);
        z-index: 0;
        opacity: 0.5;
    }
    
    .testimonial-decoration-1 {
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.3) 0%, rgba(255, 215, 0, 0) 70%);
        bottom: -100px;
        right: -100px;
        animation: float 8s infinite alternate ease-in-out;
    }
    
    .testimonial-decoration-2 {
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(0, 51, 102, 0.2) 0%, rgba(0, 51, 102, 0) 70%);
        top: -80px;
        left: -80px;
        animation: float 6s infinite alternate-reverse ease-in-out;
    }
    
    @keyframes float {
        0% {
            transform: translate(0, 0);
        }
        100% {
            transform: translate(20px, -20px);
        }
    }
    
    /* Responsive Styles */
    @media (max-width: 991.98px) {
        .testimonial-carousel-card {
            padding: 30px;
            margin: 20px;
        }
        
        .testimonial-control {
            width: 40px;
            height: 40px;
        }
        
        .carousel-control-prev {
            left: -15px;
        }
        
        .carousel-control-next {
            right: -15px;
        }
    }
    
    @media (max-width: 767.98px) {
        .testimonials-section {
            padding: 60px 0;
        }
        
        .testimonial-carousel-card {
            padding: 25px;
            margin: 15px;
        }
        
        .testimonial-quote-icon {
            top: 20px;
            right: 20px;
            font-size: 3rem;
        }
        
        .testimonial-text {
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .testimonial-author {
            padding: 10px 15px;
        }
        
        .testimonial-avatar {
            width: 50px;
            height: 50px;
        }
    }
    
    @media (max-width: 575.98px) {
        .testimonial-carousel-card {
            padding: 20px;
            margin: 10px;
        }
        
        .testimonial-control {
            width: 35px;
            height: 35px;
        }
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

    /* How It Works Section Styles */
    .how-it-works-section {
        position: relative;
        padding: 100px 0;
        overflow: hidden;
        background: linear-gradient(135deg, #f5f7fa 0%, #f8f9fa 100%);
    }
    
    .how-it-works-wrapper {
        padding: 30px 0;
        z-index: 1;
    }
    
    /* Decorative elements */
    .hiw-decoration {
        position: absolute;
        border-radius: 50%;
        filter: blur(40px);
        z-index: 0;
        opacity: 0.5;
    }
    
    .hiw-decoration-1 {
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.2) 0%, rgba(255, 215, 0, 0) 70%);
        bottom: -100px;
        right: 100px;
        animation: float 8s infinite alternate ease-in-out;
    }
    
    .hiw-decoration-2 {
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(0, 51, 102, 0.15) 0%, rgba(0, 51, 102, 0) 70%);
        top: 50px;
        left: 10%;
        animation: float 6s infinite alternate-reverse ease-in-out;
    }
    
    .hiw-decoration-3 {
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(0, 153, 204, 0.1) 0%, rgba(0, 153, 204, 0) 70%);
        top: 30%;
        right: 15%;
        animation: float 10s infinite alternate ease-in-out;
    }
    
    @keyframes float {
        0% {
            transform: translate(0, 0);
        }
        100% {
            transform: translate(10px, -15px);
        }
    }
    
    /* Steps container and line */
    .steps-container {
        padding: 50px 0;
        margin-bottom: 30px;
    }
    
    .step-line {
        position: absolute;
        top: 100px;
        left: 50px;
        right: 50px;
        height: 4px;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        opacity: 0.4;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 51, 102, 0.1);
        z-index: 1;
    }
    
    /* Step items */
    .step-item {
        position: relative;
        z-index: 2;
        padding: 20px;
        text-align: center;
        transition: all 0.5s ease;
    }
    
    .step-item:hover {
        transform: translateY(-15px);
    }
    
    .step-icon-wrapper {
        position: relative;
        margin: 0 auto 25px;
        width: 95px;
        height: 95px;
    }
    
    .step-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), #1a4e7d);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: white;
        font-size: 1.8rem;
        position: relative;
        z-index: 2;
        box-shadow: 0 10px 30px rgba(0, 51, 102, 0.3);
        transition: all 0.5s ease;
    }
    
    .step-item:hover .step-icon {
        transform: scale(1.1) rotate(10deg);
        background: linear-gradient(135deg, #1a4e7d, var(--primary-color));
        box-shadow: 0 15px 35px rgba(0, 51, 102, 0.4);
    }
    
    .step-pulse {
        position: absolute;
        top: -7.5px;
        left: 0;
        right: 0;
        margin: 0 auto;
        width: 95px;
        height: 95px;
        border-radius: 50%;
        background-color: rgba(0, 51, 102, 0.1);
        z-index: 1;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            opacity: 0.7;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.3;
        }
        100% {
            transform: scale(0.95);
            opacity: 0.7;
        }
    }
    
    .step-number {
        position: absolute;
        top: -10px;
        right: -10px;
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
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        border: 2px solid white;
    }
    
    .step-content {
        position: relative;
        background-color: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        transition: all 0.5s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
        min-height: 160px;
    }
    
    .step-item:hover .step-content {
        background-color: #f8f9fa;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        border-color: rgba(0, 51, 102, 0.1);
    }
    
    .step-title {
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--primary-color);
        position: relative;
        display: inline-block;
    }
    
    .step-title::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        right: 0;
        margin: 0 auto;
        width: 50px;
        height: 3px;
        background-color: var(--secondary-color);
        opacity: 0.8;
        border-radius: 10px;
        transition: all 0.5s ease;
    }
    
    .step-item:hover .step-title::after {
        width: 70px;
    }
    
    .step-description {
        color: #555;
        line-height: 1.6;
        margin-bottom: 0;
        transition: all 0.3s ease;
    }
    
    .step-item:hover .step-description {
        color: #333;
    }
    
    .step-arrow {
        position: absolute;
        bottom: 20px;
        right: -15px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-color);
        color: var(--secondary-color);
        border-radius: 50%;
        font-size: 1.2rem;
        box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
        z-index: 3;
        opacity: 0;
        transform: translateX(-20px);
        transition: all 0.5s ease;
    }
    
    .step-item:hover .step-arrow {
        opacity: 1;
        transform: translateX(0);
    }
    
    /* Content section */
    .how-it-works-content {
        padding: 30px;
        position: relative;
    }
    
    .content-badge {
        display: inline-block;
        background: linear-gradient(135deg, var(--primary-color), #1a4e7d);
        color: white;
        padding: 6px 15px;
        border-radius: 30px;
        margin-bottom: 15px;
        box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
        transform: translateY(0);
        transition: all 0.5s ease;
    }
    
    .how-it-works-content:hover .content-badge {
        transform: translateY(-5px);
    }
    
    .badge-text {
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .content-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 20px;
        position: relative;
    }
    
    .content-description {
        color: #555;
        line-height: 1.8;
        font-size: 1.1rem;
        margin-bottom: 30px;
    }
    
    /* Animated check list */
    .animated-list {
        list-style: none;
        padding: 0;
        margin: 0 0 30px;
    }
    
    .check-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 15px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.5s ease;
        transform: translateX(0);
    }
    
    .check-item:hover {
        transform: translateX(10px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    .check-icon {
        width: 40px;
        height: 40px;
        background-color: rgba(0, 51, 102, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: var(--primary-color);
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }
    
    .check-item:hover .check-icon {
        background-color: var(--primary-color);
        color: var(--secondary-color);
    }
    
    /* Custom button */
    .btn-get-started {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, var(--primary-color), #1a4e7d);
        color: white;
        padding: 14px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 8px 25px rgba(0, 51, 102, 0.3);
        transition: all 0.5s ease;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    
    .btn-get-started:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 0%;
        height: 100%;
        background: linear-gradient(135deg, #1a4e7d, var(--primary-color));
        transition: all 0.5s ease;
        z-index: -1;
    }
    
    .btn-get-started:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 51, 102, 0.4);
        color: var(--secondary-color);
    }
    
    .btn-get-started:hover:before {
        width: 100%;
    }
    
    .btn-icon {
        margin-right: 10px;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }
    
    .btn-get-started:hover .btn-icon {
        transform: translateX(-5px);
    }
    
    /* Image section */
    .how-it-works-image-wrapper {
        position: relative;
        padding: 20px;
    }
    
    .image-container {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        transform: perspective(1000px) rotateY(-5deg);
        transition: all 0.5s ease;
        border: 5px solid rgba(255, 255, 255, 0.2);
    }
    
    .image-container:hover {
        transform: perspective(1000px) rotateY(0deg) scale(1.02);
        border-color: rgba(255, 215, 0, 0.3);
    }
    
    .main-image {
        width: 100%;
        transition: all 0.7s ease;
        filter: contrast(1.1) brightness(1.05);
        transform: scale(1.01);
    }
    
    .image-container:hover .main-image {
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
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }
    
    .image-container:hover .image-overlay {
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
    
    /* Floating elements */
    .floating-element {
        position: absolute;
        background: white;
        border-radius: 15px;
        padding: 15px;
        display: flex;
        align-items: center;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        z-index: 10;
        animation: float 5s infinite alternate ease-in-out;
    }
    
    .floating-element-1 {
        bottom: 30px;
        left: -20px;
        animation-delay: 0.5s;
    }
    
    .floating-element-2 {
        top: 30px;
        right: -20px;
        animation-delay: 1s;
    }
    
    .floating-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-color), #1a4e7d);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-color);
        font-size: 1.2rem;
        margin-right: 10px;
    }
    
    .floating-text {
        color: var(--primary-color);
    }
    
    .floating-text .counter {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
    }
    
    /* Responsive styles */
    @media (max-width: 991.98px) {
        .how-it-works-section {
            padding: 80px 0;
        }
        
        .steps-container {
            padding: 30px 0;
        }
        
        .step-line {
            top: 80px;
            left: 30px;
            right: 30px;
        }
        
        .step-item {
            margin-bottom: 30px;
        }
        
        .content-title {
            font-size: 1.8rem;
        }
        
        .how-it-works-image-wrapper {
            margin-top: 30px;
        }
    }
    
    @media (max-width: 767.98px) {
        .how-it-works-section {
            padding: 60px 0;
        }
        
        .step-line {
            display: none;
        }
        
        .step-item {
            margin-bottom: 40px;
        }
        
        .content-title {
            font-size: 1.6rem;
        }
        
        .floating-element {
            position: relative;
            margin: 20px 0;
            top: auto;
            right: auto;
            bottom: auto;
            left: auto;
        }
        
        .floating-element-1, .floating-element-2 {
            display: inline-flex;
            margin-right: 15px;
        }
    }
    
    /* Rest of existing styles */
    
    /* أنماط شبكة آراء الطلاب الجديدة */
    .testimonials-grid-wrapper {
        position: relative;
        padding: 20px 0;
    }
    
    .testimonial-grid-card {
        background-color: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
        transform: translateY(0);
        transition: all 0.5s ease;
        border: 1px solid rgba(0, 0, 0, 0.08);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .testimonial-grid-card:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }
    
    .testimonial-grid-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }
    
    .testimonial-content {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    .testimonial-text {
        font-size: 1rem;
        line-height: 1.7;
        color: #444;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
        font-style: italic;
        opacity: 0.9;
        flex-grow: 1;
    }
    
    .testimonial-author {
        display: flex;
        align-items: center;
        margin-top: auto;
        padding: 15px;
        background: rgba(0, 51, 102, 0.03);
        border-radius: 50px;
    }
    
    .testimonial-avatar {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary-color);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .testimonial-grid-card:hover .testimonial-avatar {
        border-color: var(--secondary-color);
    }
    
    /* الزخارف */
    .testimonial-decoration {
        position: absolute;
        border-radius: 50%;
        filter: blur(40px);
        z-index: 0;
        opacity: 0.5;
    }
    
    .testimonial-decoration-1 {
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.3) 0%, rgba(255, 215, 0, 0) 70%);
        bottom: -100px;
        right: -100px;
        animation: float 8s infinite alternate ease-in-out;
    }
    
    .testimonial-decoration-2 {
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(0, 51, 102, 0.2) 0%, rgba(0, 51, 102, 0) 70%);
        top: -80px;
        left: -80px;
        animation: float 6s infinite alternate-reverse ease-in-out;
    }
    
    @keyframes float {
        0% {
            transform: translate(0, 0);
        }
        100% {
            transform: translate(20px, -20px);
        }
    }
    
    /* تنسيقات متجاوبة للشبكة */
    @media (max-width: 1199.98px) {
        .testimonial-grid-card {
            padding: 25px;
            margin-bottom: 20px;
        }
    }
    
    @media (max-width: 767.98px) {
        .testimonial-text {
            font-size: 0.9rem;
        }
        
        .testimonial-avatar {
            width: 45px;
            height: 45px;
        }
    }
    
    /* ... بقية الأنماط ... */
    
</style>
@endsection

@section('scripts')
@parent
<!-- Particle.js for background effect -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // قمنا بإزالة كود كاروسل الشهادات لأننا نستخدم شبكة ثابتة الآن
        
        // الكود الآخر المهم يمكن إضافته هنا
    });
</script>
@endsection