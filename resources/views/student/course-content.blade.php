@extends('layouts.app')

@section('title', isset($course) ? $course->title . ' - محتوى الكورس' : 'محتوى الكورس')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    /* الألوان الرئيسية */
    :root {
        --primary: #003366;
        --secondary: #FFD700;
        --light-bg: #f8f9fa;
        --primary-gradient: linear-gradient(45deg, #003366, #0066cc);
        --secondary-gradient: linear-gradient(45deg, #FFD700, #FFA500);
    }

    /* صندوق الفيديو */
    .video-container {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%; /* نسبة 16:9 */
        height: 0;
        overflow: hidden;
    }
    
    .video-wrapper iframe,
    .video-wrapper video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    /* عنوان الكورس */
    .course-title {
        color: var(--primary);
        font-weight: 700;
        padding-right: 15px;
        border-right: 5px solid var(--secondary);
        margin-bottom: 20px;
    }
    
    /* قائمة الدروس */
    .lessons-container {
        background-color: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
    }
    
    .lessons-header {
        background: var(--primary-gradient);
        color: white;
        padding: 15px 20px;
        border-radius: 15px 15px 0 0;
        font-weight: 600;
    }
    
    .section-item {
        border-bottom: 1px solid #eee;
    }
    
    .section-header {
        padding: 15px 20px;
        background-color: var(--light-bg);
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .section-header:hover {
        background-color: #e9ecef;
    }
    
    .section-title {
        display: flex;
        align-items: center;
        font-weight: 600;
        color: var(--primary);
    }
    
    .section-icon {
        margin-left: 10px;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-gradient);
        color: white;
        border-radius: 50%;
    }
    
    .section-toggle {
        transition: transform 0.3s;
    }
    
    .section-toggle.collapsed {
        transform: rotate(-90deg);
    }
    
    .section-videos-count {
        background-color: var(--primary);
        color: white;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 0.75rem;
        margin-right: 10px;
    }
    
    /* قائمة الفيديوهات */
    .videos-list {
        list-style-type: none;
        padding: 10px 20px;
        margin: 0;
    }
    
    .video-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        margin: 8px 0;
        background-color: var(--light-bg);
        border-radius: 10px;
        transition: all 0.3s;
        position: relative;
        text-decoration: none;
        color: inherit;
        border-right: 3px solid transparent;
    }
    
    .video-item:hover {
        background-color: #e9ecef;
        transform: translateX(-5px);
        border-right-color: var(--secondary);
    }
    
    .video-item.active {
        background-color: rgba(0, 51, 102, 0.08);
        border-right-color: var(--secondary);
        font-weight: 600;
    }
    
    .video-play-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-left: 12px;
        flex-shrink: 0;
    }
    
    .video-item.active .video-play-icon {
        background: var(--secondary-gradient);
    }
    
    .video-info {
        flex-grow: 1;
    }
    
    .video-title {
        margin-bottom: 5px;
        font-size: 0.95rem;
    }
    
    .video-meta {
        font-size: 0.75rem;
        color: #6c757d;
        display: flex;
        align-items: center;
    }
    
    .video-duration {
        background-color: #e2e6ea;
        padding: 2px 8px;
        border-radius: 20px;
        margin-right: 8px;
    }
    
    .video-completed {
        width: 14px;
        height: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #e2e6ea;
        color: #6c757d;
        font-size: 8px;
    }
    
    .video-completed.done {
        background-color: #28a745;
        color: white;
    }
    
    /* معلومات الدرس */
    .lesson-info-card {
        background-color: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
        margin-top: 20px;
    }
    
    .lesson-info-header {
        background: var(--primary-gradient);
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
    }
    
    .lesson-info-icon {
        margin-left: 10px;
    }
    
    .lesson-info-body {
        padding: 20px;
    }
    
    /* شريط التقدم */
    .progress-container {
        margin-top: 20px;
    }
    
    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    
    .progress-percentage {
        background: var(--primary-gradient);
        color: white;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.8rem;
    }
    
    .progress {
        height: 8px;
        border-radius: 4px;
    }
    
    .progress-bar {
        background: var(--secondary-gradient);
    }
    
    /* أزرار التنقل */
    .navigation-buttons {
        display: flex;
        justify-content: space-between;
        margin: 20px 0;
    }
    
    /* المواد التعليمية */
    .materials-container {
        margin-top: 30px;
    }
    
    .material-item {
        display: flex;
        align-items: center;
        padding: 15px;
        background-color: white;
        border-radius: 10px;
        margin-bottom: 10px;
        transition: all 0.3s;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }
    
    .material-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .material-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        background: linear-gradient(45deg, #f1f5f9, #e2e8f0);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 15px;
        font-size: 20px;
        color: var(--primary);
    }
    
    .material-info {
        flex-grow: 1;
    }
    
    .material-title {
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .material-meta {
        font-size: 0.8rem;
        color: #718096;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- القسم الرئيسي (الفيديو) -->
        <div class="col-lg-8">
            <!-- عنوان الكورس -->
            <h1 class="course-title animate__animated animate__fadeIn">
                {{ $course->title }}
            </h1>
            
            <!-- التنبيهات -->
            @if(session('success'))
                <div class="alert alert-success animate__animated animate__fadeIn">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger animate__animated animate__fadeIn">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                </div>
            @endif
            
            <!-- مشغل الفيديو -->
            <div class="video-container animate__animated animate__fadeIn">
                <div class="video-wrapper">
                    @if(isset($currentVideo))
                        @if($currentVideo->video_url)
                            <iframe src="{{ $currentVideo->video_url }}" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen></iframe>
                        @else
                            <video id="videoPlayer" controls preload="metadata" controlsList="nodownload"
                                   poster="{{ $currentVideo->getThumbnailFullUrlAttribute() }}"
                                   class="video-js"
                                   playsinline>
                                <!-- سيتم تعيين مصدر الفيديو عبر JavaScript -->
                                <p class="vjs-no-js">متصفحك لا يدعم تشغيل الفيديو.</p>
                            </video>
                            <div id="videoLoading" class="d-flex flex-column align-items-center justify-content-center h-100 p-5">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">جاري التحميل...</span>
                                </div>
                                <p class="text-center">جاري تحضير الفيديو...</p>
                            </div>
                            <div id="videoErrorContainer" class="d-none">
                                <div class="d-flex flex-column align-items-center justify-content-center h-100 bg-light p-5">
                                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                    <h5 class="text-center mb-3">خطأ في تحميل الفيديو</h5>
                                    <p class="text-center text-muted mb-3">يرجى التأكد من اتصال الإنترنت وإعادة تحميل الصفحة</p>
                                    <button class="btn btn-primary" onclick="window.location.reload()">
                                        <i class="fas fa-sync-alt me-2"></i> إعادة تحميل
                                    </button>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="d-flex justify-content-center align-items-center h-100 bg-light p-5">
                            <div class="text-center">
                                <i class="fas fa-film fa-3x mb-3 text-muted"></i>
                                <p class="text-muted">يرجى اختيار فيديو من القائمة للمشاهدة</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- أزرار التنقل -->
            @if(isset($currentVideo))
                <div class="navigation-buttons">
                    @if($previousVideo)
                        <a href="{{ route('student.course-content', ['courseId' => $course->course_id, 'videoId' => $previousVideo->video_id]) }}" 
                           class="btn btn-outline-primary rounded-pill">
                            <i class="fas fa-chevron-right me-2"></i> الدرس السابق
                        </a>
                    @else
                        <button class="btn btn-outline-secondary rounded-pill" disabled>
                            <i class="fas fa-chevron-right me-2"></i> لا يوجد درس سابق
                        </button>
                    @endif
                    
                    @if($nextVideo)
                        <a href="{{ route('student.course-content', ['courseId' => $course->course_id, 'videoId' => $nextVideo->video_id]) }}" 
                           class="btn btn-primary rounded-pill">
                            الدرس التالي <i class="fas fa-chevron-left ms-2"></i>
                        </a>
                    @else
                        <button class="btn btn-secondary rounded-pill" disabled>
                            لا يوجد درس تالي <i class="fas fa-chevron-left ms-2"></i>
                        </button>
                    @endif
                </div>
            @endif
            
            <!-- معلومات الدرس -->
            @if(isset($currentVideo))
                <div class="lesson-info-card animate__animated animate__fadeIn">
                    <div class="lesson-info-header">
                        <i class="fas fa-info-circle lesson-info-icon"></i>
                        <h5 class="m-0">معلومات الدرس</h5>
                    </div>
                    <div class="lesson-info-body">
                        <h3>{{ $currentVideo->title }}</h3>
                        <div class="text-muted mb-3">
                            <i class="fas fa-book-open me-2"></i> {{ $currentVideo->section->title }}
                            <span class="mx-2">|</span>
                            <i class="fas fa-clock me-2"></i> {{ $currentVideo->getFormattedDurationAttribute() }}
                        </div>
                        
                        <p>{{ $currentVideo->description ?: 'لا يوجد وصف لهذا الدرس' }}</p>
                        
                        @if(isset($videoProgress))
                            <div class="progress-container">
                                <div class="progress-label">
                                    <span>تقدمك في الدرس</span>
                                    <span class="progress-percentage">{{ number_format($videoProgress->progress_percentage) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $videoProgress->progress_percentage }}%" 
                                         aria-valuenow="{{ $videoProgress->progress_percentage }}" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- المواد التعليمية -->
            @if(isset($currentSection) && count($currentSection->materials) > 0)
                <div class="lesson-info-card mt-4 animate__animated animate__fadeIn">
                    <div class="lesson-info-header">
                        <i class="fas fa-file-alt lesson-info-icon"></i>
                        <h5 class="m-0">المواد التعليمية المرفقة</h5>
                    </div>
                    <div class="lesson-info-body">
                        <div class="row">
                            @foreach($currentSection->materials as $material)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="me-3 text-primary">
                                                @if(in_array($material->file_type, ['pdf', 'doc', 'docx']))
                                                    <i class="fas fa-file-pdf fa-2x"></i>
                                                @elseif(in_array($material->file_type, ['xls', 'xlsx', 'csv']))
                                                    <i class="fas fa-file-excel fa-2x"></i>
                                                @elseif(in_array($material->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                                    <i class="fas fa-file-image fa-2x"></i>
                                                @elseif(in_array($material->file_type, ['zip', 'rar']))
                                                    <i class="fas fa-file-archive fa-2x"></i>
                                                @else
                                                    <i class="fas fa-file fa-2x"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h5 class="card-title mb-1">{{ $material->title }}</h5>
                                                <p class="card-text small text-muted">
                                                    {{ strtoupper($material->file_type) }} • {{ $material->getFormattedFileSizeAttribute() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent border-0 text-end">
                                            <a href="{{ route('courses.materials.download', ['courseId' => $course->course_id, 'materialId' => $material->material_id]) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-download me-1"></i> تحميل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- القسم الجانبي (قائمة الدروس) -->
        <div class="col-lg-4">
            <div class="lessons-container sticky-top animate__animated animate__fadeIn" style="top: 20px;">
                <div class="lessons-header">
                    <i class="fas fa-list-ul me-2"></i> محتويات الكورس
                </div>
                
                <!-- شريط التقدم في الكورس -->
                <div class="p-3 border-bottom bg-light">
                    <div class="progress-label">
                        <span>تقدمك في الكورس</span>
                        <span class="progress-percentage">{{ $courseProgress ?? 0 }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $courseProgress ?? 0 }}%" 
                             aria-valuenow="{{ $courseProgress ?? 0 }}" 
                             aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                <!-- قائمة الأقسام والدروس -->
                <div>
                    @foreach($course->sections as $index => $section)
                        <div class="section-item">
                            <div class="section-header d-flex justify-content-between align-items-center" 
                                 data-bs-toggle="collapse" 
                                 data-bs-target="#section{{ $section->section_id }}" 
                                 aria-expanded="{{ isset($currentSection) && $currentSection->section_id == $section->section_id ? 'true' : 'false' }}">
                                <div class="section-title">
                                    <div class="section-icon">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    {{ $section->title }}
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="section-videos-count">{{ $section->videos->count() }} دروس</span>
                                    <i class="fas fa-chevron-down section-toggle ms-2 {{ isset($currentSection) && $currentSection->section_id == $section->section_id ? '' : 'collapsed' }}"></i>
                                </div>
                            </div>
                            
                            <div id="section{{ $section->section_id }}" 
                                 class="collapse {{ isset($currentSection) && $currentSection->section_id == $section->section_id ? 'show' : '' }}">
                                <ul class="videos-list">
                                    @foreach($section->videos as $video)
                                        <li>
                                            <a href="{{ route('student.course-content', ['courseId' => $course->course_id, 'videoId' => $video->video_id]) }}" 
                                               class="video-item {{ isset($currentVideo) && $currentVideo->video_id == $video->video_id ? 'active' : '' }}">
                                                <div class="video-play-icon">
                                                    <i class="fas fa-play"></i>
                                                </div>
                                                <div class="video-info">
                                                    <div class="video-title">{{ $video->title }}</div>
                                                    <div class="video-meta">
                                                        <span class="video-duration">{{ $video->getFormattedDurationAttribute() }}</span>
                                                        @php
                                                            $isCompleted = isset($completedVideos) && in_array($video->video_id, $completedVideos);
                                                        @endphp
                                                        <div class="video-completed {{ $isCompleted ? 'done' : '' }}">
                                                            @if($isCompleted)
                                                                <i class="fas fa-check"></i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- معلومات الكورس -->
                <div class="p-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted"><i class="fas fa-film me-2"></i> عدد الدروس:</span>
                        <span class="fw-bold">{{ $course->videos->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted"><i class="fas fa-clock me-2"></i> المدة الإجمالية:</span>
                        <span class="fw-bold">{{ $course->duration }} ساعة</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="fas fa-user-tie me-2"></i> المدرس:</span>
                        <span class="fw-bold">{{ $course->instructor->name }}</span>
                    </div>
                    @if($course->certificate_available)
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="text-muted"><i class="fas fa-award me-2"></i> شهادة:</span>
                            <span class="badge bg-success">متاح</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // منع أي طلبات GET للمسار student/save-progress
        document.addEventListener('submit', function(e) {
            const formAction = e.target.action || '';
            if (formAction.includes('/student/save-progress')) {
                e.preventDefault();
                console.warn('تم منع محاولة إرسال نموذج GET لحفظ التقدم. يجب استخدام POST فقط.');
            }
        });
        
        // Intercept fetch or XMLHttpRequest calls that use GET with /student/save-progress
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            if (url && typeof url === 'string' && url.includes('/student/save-progress') && (!options || !options.method || options.method === 'GET')) {
                console.error('تم اكتشاف محاولة لاستخدام fetch مع طريقة GET لـ /student/save-progress - تم منعها.');
                console.trace('Stack trace for debugging:');
                // تعديل الطلب ليكون POST بدلاً من GET
                options = options || {};
                options.method = 'POST';
                options.headers = options.headers || {};
                options.headers['Content-Type'] = 'application/json';
                options.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                options.headers['Accept'] = 'application/json';
            }
            return originalFetch.call(this, url, options);
        };
        
        // إضافة إعداد الفيديو من خلال fetch للحصول على التوكن أولاً
        const setupVideo = async function() {
        const videoPlayer = document.getElementById('videoPlayer');
            const videoLoading = document.getElementById('videoLoading');
            
            if (!videoPlayer) return;
            
            try {
                const videoIdFromUrl = new URLSearchParams(window.location.search).get('videoId');
                const videoId = videoIdFromUrl ? videoIdFromUrl.split(':')[0] : null;
                const courseId = {{ $course->course_id ?? 'null' }};
                
                if (!videoId || !courseId) {
                    throw new Error('معرف الفيديو أو الكورس غير متوفر');
                }
                
                // إظهار شاشة التحميل
                videoLoading.classList.remove('d-none');
                videoPlayer.style.display = 'none';
                
                // الحصول على توكن الفيديو من الخادم
                const tokenUrl = `/video/token/${courseId}/${videoId}`;
                console.log('Fetching token from:', tokenUrl);
                
                const response = await fetch(tokenUrl);
                
                // تسجيل إستجابة الخادم للتشخيص
                console.log('Server response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error fetching token:', errorText);
                    throw new Error(`فشل الحصول على توكن الفيديو: ${response.status} ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('Token data received:', data);
                
                if (!data.token) {
                    throw new Error('لم يتم استلام توكن صالح من الخادم');
                }
                
                // إعداد مصدر الفيديو باستخدام التوكن
                const videoUrl = `/video/stream/${data.token}`;
                console.log('Setting video source to:', videoUrl);
                
                // إنشاء عنصر source جديد وإضافته للفيديو
                const source = document.createElement('source');
                source.src = videoUrl;
                source.type = 'video/mp4';
                
                // إزالة أي عناصر source سابقة
                while (videoPlayer.firstChild) {
                    if (videoPlayer.firstChild.nodeName === 'SOURCE') {
                        videoPlayer.removeChild(videoPlayer.firstChild);
                    } else {
                        break;
                    }
                }
                
                // إضافة المصدر الجديد
                videoPlayer.insertBefore(source, videoPlayer.firstChild);
                
                // تحميل الفيديو من المصدر الجديد
                videoPlayer.load();
                
                // إخفاء شاشة التحميل وإظهار الفيديو
                videoLoading.classList.add('d-none');
                videoPlayer.style.display = 'block';
                
                console.log('Video setup completed successfully');
                
            // استرجاع آخر موضع للفيديو إذا كان موجودًا
            const lastPosition = localStorage.getItem(`video_${videoId}_position`);
            
            if (lastPosition && parseFloat(lastPosition) > 0) {
                videoPlayer.addEventListener('loadedmetadata', function() {
                    if (parseFloat(lastPosition) < videoPlayer.duration * 0.9) {
                        videoPlayer.currentTime = parseFloat(lastPosition);
                    }
                });
            }
            
            // حفظ موضع الفيديو كل 5 ثواني
            setInterval(() => {
                if (videoPlayer.currentTime > 0) {
                    localStorage.setItem(`video_${videoId}_position`, videoPlayer.currentTime);
                }
            }, 5000);
            
            // حفظ الموضع عند إيقاف الفيديو مؤقتًا
            videoPlayer.addEventListener('pause', function() {
                saveVideoProgress(videoPlayer.currentTime, videoPlayer.duration);
            });
            
            // حفظ التقدم عند انتهاء الفيديو
            videoPlayer.addEventListener('ended', function() {
                saveVideoProgress(videoPlayer.duration, videoPlayer.duration, true);
                localStorage.removeItem(`video_${videoId}_position`);
            });
                
            } catch (error) {
                console.error('Error setting up video:', error);
                
                // إخفاء شاشة التحميل وإظهار رسالة الخطأ
                videoLoading.classList.add('d-none');
                
                const errorContainer = document.getElementById('videoErrorContainer');
                errorContainer.classList.remove('d-none');
                
                // عرض رسالة خطأ أكثر تفصيلاً
                const errorMessage = document.createElement('div');
                errorMessage.className = 'alert alert-danger mt-3';
                errorMessage.innerHTML = `
                    <h6>تفاصيل الخطأ:</h6>
                    <p>${error.message || 'خطأ غير معروف أثناء إعداد الفيديو'}</p>
                    <p>يرجى المحاولة مرة أخرى لاحقًا، أو إبلاغ الدعم الفني إذا استمرت المشكلة.</p>
                `;
                
                errorContainer.querySelector('div').appendChild(errorMessage);
            }
        };
        
        // تحريك أيقونة السكشن عند الفتح والإغلاق
        const sectionHeaders = document.querySelectorAll('.section-header');
        sectionHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const icon = this.querySelector('.section-toggle');
                icon.classList.toggle('collapsed');
            });
        });
        
        // تشغيل إعداد الفيديو
        if (document.getElementById('videoPlayer')) {
            setupVideo();
        }
        
        // تتبع مشاهدة الفيديو
        const videoPlayer = document.getElementById('videoPlayer');
        if (videoPlayer) {
            // معالجة خطأ تحميل الفيديو
            videoPlayer.addEventListener('error', function(e) {
                console.error('خطأ في تحميل الفيديو (Video Player Error Event):', e);

                const videoSource = videoPlayer.querySelector('source');
                const videoUrl = videoSource ? videoSource.src : 'N/A';
                console.log('محاولة تحميل مسار الفيديو:', videoUrl);
                if (videoUrl === 'N/A' || videoUrl === window.location.href) {
                    console.warn('لم يتمكن من تحديد مسار الفيديو بشكل صحيح.');
                }

                let detailedErrorMessage = 'حدث خطأ غير معروف أثناء محاولة تحميل الفيديو.';
                let errorCode = 'N/A';
                let errorType = 'غير معروف';

                if (e.target && e.target.error) {
                    const videoError = e.target.error;
                    errorCode = videoError.code;
                    switch (videoError.code) {
                        case 1: // MEDIA_ERR_ABORTED
                            detailedErrorMessage = 'عملية جلب الفيديو تم إلغاؤها من قبل المستخدم أو المتصفح.';
                            errorType = 'MEDIA_ERR_ABORTED';
                            break;
                        case 2: // MEDIA_ERR_NETWORK
                            detailedErrorMessage = 'حدث خطأ في الشبكة أثناء تحميل الفيديو. قد يكون السبب مشكلة في الخادم (مثل خطأ 404 أو 500) أو مشكلة في اتصالك بالإنترنت. يرجى التحقق من وحدة التحكم (Developer Console) في المتصفح (خاصة قسم Network) لمزيد من التفاصيل حول طلب الشبكة الخاص بالفيديو.';
                            errorType = 'MEDIA_ERR_NETWORK';
                            break;
                        case 3: // MEDIA_ERR_DECODE
                            detailedErrorMessage = 'حدث خطأ أثناء فك تشفير الفيديو. قد يكون الملف تالفًا، أو بصيغة غير مدعومة بالكامل، أو أن المتصفح لا يدعم فك تشفير هذا النوع من الفيديو.';
                            errorType = 'MEDIA_ERR_DECODE';
                            break;
                        case 4: // MEDIA_ERR_SRC_NOT_SUPPORTED
                            detailedErrorMessage = 'مصدر الفيديو غير مدعوم أو لا يمكن العثور عليه. تأكد من أن عنوان URL للفيديو صحيح، وأن الخادم يستجيب بشكل صحيح، وأن نوع الفيديو (MIME type) صحيح ومدعوم.';
                            errorType = 'MEDIA_ERR_SRC_NOT_SUPPORTED';
                            break;
                        default:
                            detailedErrorMessage = `حدث خطأ غير معروف في الفيديو (الرمز: ${videoError.code}).`;
                            errorType = `Unknown Error Code: ${videoError.code}`;
                    }
                    console.error('تفاصيل خطأ الفيديو:', {
                        code: videoError.code,
                        message: videoError.message || 'لا توجد رسالة محددة من المتصفح',
                        type: errorType,
                        targetSrc: e.target.currentSrc || videoUrl
                    });
                } else {
                     console.error('كائن خطأ الفيديو (e.target.error) غير موجود. قد يكون الخطأ من نوع آخر غير متعلق مباشرة بعنصر الفيديو.');
                }

                // إخفاء عنصر الفيديو الأصلي
                videoPlayer.style.display = 'none';

                // إزالة أي رسائل خطأ سابقة من هذا النوع لتجنب التكرار
                const parentElement = videoPlayer.parentElement || document.body; // Fallback if parentElement is null
                const existingErrorMessages = parentElement.querySelectorAll('.video-error-message-enhanced');
                existingErrorMessages.forEach(msg => msg.remove());

                // عرض رسالة خطأ محسنة
                const errorDisplayHtml = `
                <div class="alert alert-danger mt-3 video-error-message-enhanced">
                    <h5 class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i> خطأ في تشغيل الفيديو</h5>
                    <p class="mb-1"><strong>الرسالة التقنية:</strong> ${detailedErrorMessage}</p>
                    <p class="mb-1"><strong>مسار الفيديو الذي تم محاولة تحميله:</strong> ${videoUrl !== 'N/A' && videoUrl !== window.location.href ? `<a href="${videoUrl}" target="_blank" rel="noopener noreferrer">${videoUrl}</a>` : 'غير متوفر أو غير صحيح'}</p>
                    <p class="mb-1"><strong>رمز الخطأ (من مشغل الفيديو):</strong> ${errorCode} (${errorType})</p>
                    <p class="mb-2 mt-3"><strong>خطوات مقترحة للتشخيص:</strong></p>
                    <ul class="mb-3">
                        <li>تأكد من أن اتصالك بالإنترنت يعمل بشكل جيد.</li>
                        <li>حاول <a href="javascript:window.location.reload(true);" style="text-decoration: underline;">إعادة تحميل الصفحة بقوة</a> (قد يحل مشاكل التخزين المؤقت).</li>
                        ${videoUrl !== 'N/A' && videoUrl !== window.location.href ? `<li>افتح <a href="${videoUrl}" target="_blank" rel="noopener noreferrer">رابط الفيديو مباشرة</a> في تبويب جديد وتحقق من ظهور أي أخطاء (مثل صفحة خطأ 404 أو 500).</li>` : ''}
                        <li><strong>مهم جداً: افتح "أدوات المطور" في متصفحك (عادةً بالضغط على F12):</strong>
                            <ul>
                                <li>اذهب إلى تبويب "Console": ابحث عن أي رسائل خطأ إضافية باللون الأحمر.</li>
                                <li>اذهب إلى تبويب "Network": ابحث عن طلب تحميل ملف الفيديو (قد يكون باسم الملف أو المسار ${videoUrl}). تحقق من "Status" الخاص به. إذا كان 403، 404، 500، أو أي رمز خطأ آخر، فهذا هو سبب المشكلة الرئيسي.</li>
                            </ul>
                        </li>
                        <li>إذا استمرت المشكلة، قد تكون هناك مشكلة في الخادم (مثل مسار الملف غير صحيح، أذونات الملف، أو خطأ في الكود الذي يخدم الفيديو) أو في ملف الفيديو نفسه. يرجى إبلاغ الدعم الفني بهذه التفاصيل.</li>
                    </ul>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" onclick="window.location.reload(true)">
                            <i class="fas fa-sync-alt me-2"></i> إعادة تحميل الصفحة
                        </button>
                    </div>
                </div>`;

                // Ensure videoPlayer.insertAdjacentHTML is called on a valid element
                if (videoPlayer.parentElement) {
                    videoPlayer.insertAdjacentHTML('afterend', errorDisplayHtml);
                } else {
                    // Fallback if videoPlayer is not in the DOM or has no parent
                    // This scenario should be unlikely if videoPlayer was obtained by getElementById
                    const videoContainer = document.querySelector('.video-wrapper');
                    if (videoContainer) {
                        videoContainer.innerHTML = errorDisplayHtml;
                    } else {
                        // As a last resort, append to body, though this is not ideal for layout
                        document.body.insertAdjacentHTML('beforeend', errorDisplayHtml);
                    }
                    console.warn("videoPlayer.parentElement was null. Error message inserted differently.");
                }
            });
        
        // حفظ تقدم الفيديو
        function saveVideoProgress(currentTime, duration, completed = false) {
            if (!duration) return;
            
                const videoIdFromUrl = new URLSearchParams(window.location.search).get('videoId');
                const videoId = videoIdFromUrl ? videoIdFromUrl.split(':')[0] : null;
            if (!videoId) return;
            
            const percentage = Math.min(Math.round((currentTime / duration) * 100), 100);
            
                console.log('Attempting to save progress:', {
                    video_id: videoId,
                    progress: percentage,
                    current_time: currentTime,
                    completed: completed || percentage >= 90
                });
                
                // استخدام FormData بدلاً من JSON
                const formData = new FormData();
                formData.append('video_id', videoId);
                formData.append('progress', percentage);
                formData.append('current_time', currentTime);
                formData.append('completed', (completed || percentage >= 90) ? '1' : '0');
                
                // الحصول على CSRF Token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // استخدام طريقة بديلة لإرسال البيانات باستخدام XMLHttpRequest للتشخيص
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/student/save-progress', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Accept', 'application/json');
                
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        console.log('Progress saved successfully', xhr.responseText);
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (data.success && (completed || percentage >= 90)) {
                    // تحديث حالة إكمال الفيديو في واجهة المستخدم
                    const videoItem = document.querySelector(`.video-item[href*="videoId=${videoId}"]`);
                    if (videoItem) {
                        const completion = videoItem.querySelector('.video-completed');
                        if (completion) {
                            completion.classList.add('done');
                            completion.innerHTML = '<i class="fas fa-check"></i>';
                        }
                    }
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                    } else {
                        console.error('Error saving progress. Status:', xhr.status);
                        console.error('Response:', xhr.responseText);
                    }
                };
                
                xhr.onerror = function() {
                    console.error('Request failed completely');
                };
                
                // إرسال البيانات
                xhr.send(formData);
            }
            
            // إعداد تتبع الفيديو للحفظ الدوري
            let lastSaveTime = 0;
            const SAVE_INTERVAL = 10000; // كل 10 ثواني
            
            videoPlayer.addEventListener('timeupdate', function() {
                const now = Date.now();
                if (now - lastSaveTime > SAVE_INTERVAL) {
                    lastSaveTime = now;
                    saveVideoProgress(videoPlayer.currentTime, videoPlayer.duration);
                }
            });
        }
    });
</script>
@endsection 