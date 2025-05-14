@extends('layouts.app')

@section('title', $course->title . ' - محتوى الكورس')

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
    
    /* تصميم المقاطع (السكشن) */
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
        font-size: 1rem;
        padding: 0;
        background-color: transparent;
        margin-bottom: 0;
        border-left: 0;
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
    .lesson-list {
        list-style-type: none;
        padding: 10px 20px;
        margin: 0;
    }

    .lesson-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        margin: 8px 0;
        background-color: var(--light-bg);
        border-radius: 10px;
        transition: all 0.3s;
        position: relative;
        cursor: pointer;
        border-right: 3px solid transparent;
        border-left: none;
    }

    .lesson-item:hover {
        background-color: #e9ecef;
        transform: translateX(-5px);
        border-right-color: var(--secondary);
    }

    .lesson-item.active {
        background-color: rgba(0, 51, 102, 0.08);
        border-right-color: var(--secondary);
        font-weight: 600;
        border-left-color: transparent;
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
    
    .lesson-item.active .video-play-icon {
        background: var(--secondary-gradient);
    }
    
    .lesson-title {
        justify-content: space-between;
        flex-grow: 1;
    }

    .lesson-icon {
        margin-right: 10px;
        color: var(--primary);
    }

    .lesson-duration {
        background-color: #e2e6ea;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.75rem;
    }
    
    .video-completion-indicator {
        width: 14px;
        height: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #e2e6ea;
        color: #6c757d;
        font-size: 8px;
        margin-right: 5px;
    }
    
    .video-completion-indicator.completed {
        background-color: #28a745;
        color: white;
    }
    
    /* أزرار التنقل */
    .video-navigation {
        display: flex;
        justify-content: space-between;
        margin: 20px 0;
    }
    
    .nav-btn {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 25px;
        transition: all 0.3s;
        font-weight: 500;
        border: none;
    }
    
    .nav-btn-primary {
        background: var(--primary-gradient);
        color: white;
    }
    
    .nav-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
    }
    
    .nav-btn-secondary {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        color: #495057;
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
    
    /* معلومات الدرس */
    .video-info-panel {
        background-color: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
        margin-top: 20px;
    }
    
    .video-info-header {
        background: var(--primary-gradient);
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
    }
    
    .video-info-title {
        margin: 0;
        font-weight: 600;
        margin-right: 10px;
    }
    
    .video-info-body {
        padding: 20px;
    }
    
    /* المواد التعليمية */
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
        background-color: var(--light-bg);
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
    
    /* حالات تحميل الفيديو */
    .video-loading,
    .video-error {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(0,0,0,0.7);
        color: white;
        z-index: 10;
        padding: 20px;
        text-align: center;
    }
    
    .plyr--video {
        border-radius: 10px;
        overflow: hidden;
    }
</style>
@endsection

@section('scripts')
<!-- Include Plyr.io for better video player -->
<script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

<!-- Include HLS.js for M3U8 streaming support in browsers that don't support it natively -->
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة المتغيرات
        const courseId = {{ $course->course_id }};
        let currentVideoId = null;
        let currentVideoIndex = 0;
        let currentSectionId = null;
        let currentSectionIndex = 0;
        let player = null;
        let videoStartTime = 0;
        let saveProgressInterval = null;
        let isVideoComplete = false;
        let lastSaveTime = 0;
        const SAVE_INTERVAL = 5000; // حفظ كل 5 ثوانية بحد أدنى
        let hasShownCourseCompletionModal = false; // لتتبع ما إذا كان قد تم عرض مربع التهنئة من قبل

        // العناصر في الصفحة
        const videoContainer = document.getElementById('video-container');
        const videoPlayer = document.getElementById('video-player');
        const videoTitle = document.getElementById('current-video-title');
        const videoDescription = document.getElementById('video-description');
        const prevButton = document.getElementById('prev-video-btn');
        const nextButton = document.getElementById('next-video-btn');
        const progressContainer = document.getElementById('video-progress-container');
        const progressBar = document.getElementById('video-progress-bar');
        const progressPercentage = document.getElementById('progress-percentage');
        const videoLoading = document.getElementById('video-loading');
        const videoError = document.getElementById('video-error');
        const errorMessage = document.getElementById('error-message');
        const lessonItems = document.querySelectorAll('.lesson-item');
        const sectionMaterialsContainer = document.getElementById('section-materials-container');
        const sectionMaterialsList = document.getElementById('section-materials-list');

        // جمع بيانات الفيديوهات
        const videos = [];
        lessonItems.forEach(item => {
            if (item.dataset.videoId) {
                videos.push({
                    id: parseInt(item.dataset.videoId),
                    title: item.dataset.videoTitle,
                    description: item.dataset.videoDescription,
                    sectionId: parseInt(item.dataset.sectionId),
                    videoIndex: parseInt(item.dataset.videoIndex),
                    sectionIndex: parseInt(item.dataset.sectionIndex)
                });
            }
        });

        // تهيئة مشغل الفيديو - استخدام HTML5 مباشرة بدلاً من Plyr.js
        videoPlayer.controls = true;
        videoPlayer.preload = "auto";
        videoPlayer.controlsList = "nodownload";
        videoPlayer.style.width = "100%";
        videoPlayer.style.borderRadius = "10px";
        videoPlayer.style.backgroundColor = "#000";
        
        // نحتفظ بمتغير player للتوافق مع بقية الكود
        player = {
            source: null,
            currentTime: 0,
            duration: 0,
            
            // تحديث قيم التقدم بشكل دوري
            _updateInterval: null,
            
            // محاكاة تعيين المصدر
            set source(config) {
                console.log('تعيين مصدر الفيديو:', config);
                if (config && config.sources && config.sources.length > 0) {
                    const source = config.sources[0];
                    
                    // إيقاف التحديث المستمر
                    if (this._updateInterval) {
                        clearInterval(this._updateInterval);
                    }
                    
                    // إظهار رسالة التحميل
                    videoLoading.style.display = 'flex';
                    videoError.style.display = 'none';
                    
                    // تعيين المصدر
                    videoPlayer.src = source.src;
                    videoPlayer.type = source.type || 'video/mp4';
                    
                    // تعيين العنوان إذا وجد
                    if (config.title) {
                        if (videoTitle) {
                            videoTitle.textContent = config.title;
                        }
                    }
                    
                    // حاول تشغيل الفيديو
                    videoPlayer.load();
                    
                    // إعادة تعيين المتغيرات
                    this.currentTime = 0;
                    this.duration = 0;
                    
                    // أحداث الفيديو
                    videoPlayer.onloadedmetadata = () => {
                        console.log('تم تحميل معلومات الفيديو');
                        // إخفاء رسالة التحميل
                        videoLoading.style.display = 'none';
                        this.duration = videoPlayer.duration;
                        // تحديث قيم التقدم
                        this._updateInterval = setInterval(() => {
                            this.currentTime = videoPlayer.currentTime;
                            if (videoPlayer.duration > 0) {
                                const percentage = Math.min(Math.round((videoPlayer.currentTime / videoPlayer.duration) * 100), 100);
                                updateProgressUI(percentage);
                                
                                // تعليم الفيديو كمكتمل عند الوصول لنسبة 90%
                                if (percentage >= 90 && !isVideoComplete && currentVideoId) {
                                    markVideoAsComplete(currentVideoId);
                                    isVideoComplete = true;
                                }
                            }
                        }, 1000);
                    };
                    
                    videoPlayer.onwaiting = () => {
                        console.log('انتظار تحميل الفيديو...');
                        videoLoading.style.display = 'flex';
                    };
                    
                    videoPlayer.onplaying = () => {
                        console.log('بدأ تشغيل الفيديو');
                        videoLoading.style.display = 'none';
                    };
                    
                    videoPlayer.onended = () => {
                        console.log('انتهاء الفيديو');
                        // حفظ تقدم الفيديو على السيرفر
                        if (currentVideoId) {
                            saveProgress(100, true);
                        }
                        
                        // عرض زر الانتقال للدرس التالي
                        if (!nextButton.disabled) {
                            showNextVideoPrompt();
                        }
                    };
                    
                    videoPlayer.onerror = (e) => {
                        console.error('خطأ في تشغيل الفيديو:', e);
                        console.log('رمز الخطأ:', videoPlayer.error ? videoPlayer.error.code : 'غير متوفر');
                        console.log('رسالة الخطأ:', videoPlayer.error ? videoPlayer.error.message : 'غير متوفرة');
                        
                        videoLoading.style.display = 'none';
                        videoError.style.display = 'flex';
                        errorMessage.textContent = 'حدث خطأ أثناء تحميل الفيديو. يرجى المحاولة مرة أخرى.';
                    };
                    
                    // محاولة البدء من آخر موضع
                    if (videoStartTime > 0 && videoStartTime < videoPlayer.duration * 0.9) {
                        videoPlayer.currentTime = videoStartTime;
                    }
                }
            },
            
            // محاكاة أحداث Plyr
            on: function(event, callback) {
                console.log(`تسجيل حدث ${event} (محاكاة Plyr)`);
                // المحاكاة غير ضرورية الآن لأننا نستخدم أحداث الفيديو الأصلية
            }
        };
        
        // إعداد أحداث النقر لعناصر الدروس
        lessonItems.forEach(item => {
            item.addEventListener('click', function() {
                const videoId = parseInt(this.dataset.videoId);
                const videoIndex = parseInt(this.dataset.videoIndex);
                const sectionId = parseInt(this.dataset.sectionId);
                const sectionIndex = parseInt(this.dataset.sectionIndex);

                if (videoId && videoId !== currentVideoId) {
                    loadVideo(videoId, videoIndex, sectionId, sectionIndex);
                }
            });
        });

        // إعداد أزرار التنقل
        prevButton.addEventListener('click', function() {
            if (currentVideoIndex > 0 || currentSectionIndex > 0) {
                let prevVideo;
                let prevVideoIndex = currentVideoIndex - 1;
                let prevSectionIndex = currentSectionIndex;
                
                if (prevVideoIndex < 0) {
                    // انتقل إلى القسم السابق
                    prevSectionIndex = currentSectionIndex - 1;
                    const prevSectionVideos = videos.filter(v => v.sectionIndex === prevSectionIndex);
                    if (prevSectionVideos.length > 0) {
                        prevVideoIndex = prevSectionVideos.length - 1;
                        prevVideo = prevSectionVideos[prevVideoIndex];
                    }
                } else {
                    // انتقل إلى الفيديو السابق في نفس القسم
                    prevVideo = videos.find(v => 
                        v.sectionIndex === prevSectionIndex && v.videoIndex === prevVideoIndex
                    );
                }
                
                if (prevVideo) {
                    loadVideo(prevVideo.id, prevVideo.videoIndex, prevVideo.sectionId, prevVideo.sectionIndex);
                }
            }
        });
        
        nextButton.addEventListener('click', function() {
            const totalSections = Math.max(...videos.map(v => v.sectionIndex)) + 1;
            const isLastSectionLastVideo = 
                currentSectionIndex === totalSections - 1 && 
                currentVideoIndex === videos.filter(v => v.sectionIndex === currentSectionIndex).length - 1;
                
            if (!isLastSectionLastVideo) {
                let nextVideo;
                let nextVideoIndex = currentVideoIndex + 1;
                let nextSectionIndex = currentSectionIndex;
                const currentSectionVideos = videos.filter(v => v.sectionIndex === currentSectionIndex);
                
                if (nextVideoIndex >= currentSectionVideos.length) {
                    // انتقل إلى القسم التالي
                    nextSectionIndex = currentSectionIndex + 1;
                    const nextSectionVideos = videos.filter(v => v.sectionIndex === nextSectionIndex);
                    if (nextSectionVideos.length > 0) {
                        nextVideoIndex = 0;
                        nextVideo = nextSectionVideos[0];
                    }
                } else {
                    // انتقل إلى الفيديو التالي في نفس القسم
                    nextVideo = videos.find(v => 
                        v.sectionIndex === nextSectionIndex && v.videoIndex === nextVideoIndex
                    );
                }
                
                if (nextVideo) {
                    loadVideo(nextVideo.id, nextVideo.videoIndex, nextVideo.sectionId, nextVideo.sectionIndex);
                }
            }
        });
        
        // تحميل تقدم الطالب في الكورس
        loadCourseProgress(courseId);
        
        // تحميل الفيديو الأول تلقائياً أو الفيديو المحدد في URL
        const urlParams = new URLSearchParams(window.location.search);
        const videoIdFromUrl = urlParams.get('videoId');
        
        if (videoIdFromUrl && videos.some(v => v.id === parseInt(videoIdFromUrl))) {
            const video = videos.find(v => v.id === parseInt(videoIdFromUrl));
            loadVideo(video.id, video.videoIndex, video.sectionId, video.sectionIndex);
        } else if (videos.length > 0) {
            const firstVideo = videos[0];
            loadVideo(firstVideo.id, firstVideo.videoIndex, firstVideo.sectionId, firstVideo.sectionIndex);
        }
        
        // وظيفة حفظ تقدم الفيديو
        function saveProgress(percentage, completed = false) {
            fetch('{{ route('student.save-progress') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    video_id: currentVideoId,
                    progress_percentage: percentage,
                    current_time: videoPlayer.currentTime,
                    completed: completed || percentage >= 90
                })
            })
                .then(response => {
                    if (!response.ok) {
                    throw new Error('Server response was not OK: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                console.log('Progress saved successfully:', data);
                
                if (data.success && (completed || percentage >= 90)) {
                    markVideoAsComplete(currentVideoId);
                    isVideoComplete = true;
                }
                
                // حفظ أيضًا في localStorage كنسخة احتياطية
                localStorage.setItem(`video_${currentVideoId}_position`, videoPlayer.currentTime);
                localStorage.setItem(`video_${currentVideoId}_percentage`, percentage);
                
                // تحديث شريط التقدم في الكورس إذا أمكن
                if (percentage >= 90 && document.querySelector('.progress-percentage')) {
                    loadCourseProgress(courseId);
                }
                
                // التحقق من آخر فيديو في الكورس إذا تم إكماله
                const totalVideos = videos.length;
                const completedVideos = document.querySelectorAll('.video-completion-indicator.completed').length;
                
                // إذا تم إكمال آخر فيديو وعدد الفيديوهات المكتملة هو إجمالي عدد الفيديوهات
                if (completed && completedVideos >= totalVideos) {
                    // إجراء طلب للحصول على تقدم الكورس المحدث للتأكد من الإكمال
                    loadCourseProgress(courseId);
                }
            })
            .catch(error => {
                console.error('Error saving progress to server:', error);
                // حفظ في localStorage على الأقل في حالة فشل الاتصال بالخادم
                localStorage.setItem(`video_${currentVideoId}_position`, videoPlayer.currentTime);
                localStorage.setItem(`video_${currentVideoId}_percentage`, percentage);
            });
        }
        
        // وظيفة تعليم الفيديو كمكتمل في واجهة المستخدم
        function markVideoAsComplete(videoId) {
            const completionIndicator = document.getElementById(`completion-${videoId}`);
            if (completionIndicator) {
                completionIndicator.classList.add('completed');
                completionIndicator.innerHTML = '<i class="fas fa-check"></i>';
            }
        }
        
        // وظيفة لتحميل تقدم الطالب في الكورس
        function loadCourseProgress(courseId) {
            fetch(`{{ url('/student/course') }}/${courseId}/progress`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.completed_videos && data.data.completed_videos.length > 0) {
                        // تعليم الفيديوهات المكتملة في واجهة المستخدم
                        data.data.completed_videos.forEach(videoId => {
                            markVideoAsComplete(videoId);
                        });
                        
                        // تحديث شريط التقدم الكلي إذا وجد
                        if (document.querySelector('.progress-percentage')) {
                            document.querySelector('.progress-percentage').textContent = `${data.data.total_percentage}%`;
                            document.querySelector('.progress-bar').style.width = `${data.data.total_percentage}%`;
                            document.querySelector('.progress-bar').setAttribute('aria-valuenow', data.data.total_percentage);
                            
                            // التحقق من إكمال الكورس بالكامل وإظهار الرسالة
                            checkCourseCompletion(data.data.total_percentage);
                        }
                    }
                })
                .catch(error => console.error('Error loading course progress:', error));
        }
        
        // وظيفة للتحقق من إكمال الكورس وإظهار مربع التهنئة
        function checkCourseCompletion(percentageComplete) {
            // إذا وصل التقدم إلى 100% ولم يتم عرض مربع التهنئة من قبل
            if (percentageComplete >= 100 && !hasShownCourseCompletionModal) {
                // تعليم المربع كمعروض لمنع ظهوره مرة أخرى في نفس الجلسة
                hasShownCourseCompletionModal = true;
                
                // تخزين في localStorage لمنع ظهوره مستقبلاً (اختياري)
                localStorage.setItem(`course_${courseId}_completion_shown`, 'true');
                
                // إظهار مربع التهنئة بعد ثانية واحدة للسماح بتحميل الصفحة بالكامل
                setTimeout(() => {
                    const courseCompletionModal = new bootstrap.Modal(document.getElementById('courseCompletionModal'));
                    courseCompletionModal.show();
                }, 1000);
            }
        }
        
        // وظيفة لتحميل آخر موضع مشاهدة للفيديو من الخادم
        function loadVideoProgress(videoId) {
            return new Promise((resolve, reject) => {
                console.log(`Loading progress for video ${videoId}...`);
                fetch(`{{ url('/student/video') }}/${videoId}/progress`)
                .then(response => {
                    if (!response.ok) {
                            throw new Error('Server response was not OK: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                        console.log('Video progress response:', data);
                        if (data.success) {
                            // استعادة التقدم من الخادم
                            if (data.data.exists) {
                                console.log(`Found server progress: ${data.data.last_position}s, ${data.data.percentage}%, completed: ${data.data.completed}`);
                                videoStartTime = data.data.last_position;
                                isVideoComplete = data.data.completed;
                                
                                if (isVideoComplete) {
                                    markVideoAsComplete(videoId);
                                }
                                
                                resolve(data.data);
                            } else {
                                // لم يتم العثور على تقدم، محاولة استخدام localStorage
                                const localPosition = parseFloat(localStorage.getItem(`video_${videoId}_position`) || 0);
                                const localPercentage = parseInt(localStorage.getItem(`video_${videoId}_percentage`) || 0);
                                console.log(`No server progress, using localStorage: ${localPosition}s, ${localPercentage}%`);
                                
                                // إذا كان هناك تقدم محلي أكبر من الصفر، حفظه على الخادم
                                if (localPosition > 0 || localPercentage > 0) {
                                    console.log('Syncing localStorage progress to server...');
                                    saveProgress(localPercentage || Math.min(Math.round((localPosition / 100) * 100), 100), localPercentage >= 90);
                                }
                                
                                videoStartTime = localPosition;
                                isVideoComplete = localPercentage >= 90;
                                
                                if (isVideoComplete) {
                                    markVideoAsComplete(videoId);
                                }
                                
                                resolve({ exists: false, last_position: localPosition, percentage: localPercentage });
                            }
                        } else {
                            // تم استلام استجابة لكنها غير ناجحة
                            console.warn('Server returned unsuccessful response:', data);
                            const localPosition = parseFloat(localStorage.getItem(`video_${videoId}_position`) || 0);
                            const localPercentage = parseInt(localStorage.getItem(`video_${videoId}_percentage`) || 0);
                            videoStartTime = localPosition;
                            resolve({ exists: false, last_position: localPosition, percentage: localPercentage });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading video progress from server:', error);
                        // استخدام localStorage في حالة الفشل
                        const localPosition = parseFloat(localStorage.getItem(`video_${videoId}_position`) || 0);
                        const localPercentage = parseInt(localStorage.getItem(`video_${videoId}_percentage`) || 0);
                        console.log(`Fallback to localStorage: ${localPosition}s, ${localPercentage}%`);
                        videoStartTime = localPosition;
                        resolve({ exists: false, last_position: localPosition, percentage: localPercentage });
                    });
            });
        }
        
        // وظيفة تحميل الفيديو
        function loadVideo(videoId, videoIndex, sectionId, sectionIndex) {
            console.log(`تحميل الفيديو: ${videoId} (${videoIndex}) من القسم: ${sectionId} (${sectionIndex})`);
            
            // إظهار شاشة التحميل
            videoLoading.style.display = 'flex';
            videoError.style.display = 'none';
            
            // إيقاف حفظ التقدم للفيديو السابق
            if (saveProgressInterval) {
                clearInterval(saveProgressInterval);
            }
            
            // تحديث المتغيرات الحالية
            currentVideoId = videoId;
            currentVideoIndex = videoIndex;
            currentSectionId = sectionId;
            currentSectionIndex = sectionIndex;
            isVideoComplete = false;
            
            // العثور على بيانات الفيديو
            const video = videos.find(v => v.id === videoId);
            if (!video) {
                console.error('لم يتم العثور على بيانات الفيديو', videoId);
                videoLoading.style.display = 'none';
                videoError.style.display = 'flex';
                errorMessage.textContent = 'لم يتم العثور على بيانات الفيديو';
                return;
            }
            
            const currentSection = videos.filter(v => v.sectionIndex === currentSectionIndex);
            const isFirstVideo = currentVideoIndex === 0 && currentSectionIndex === 0;
            const isLastVideo = 
                currentSectionIndex === Math.max(...videos.map(v => v.sectionIndex)) && 
                currentVideoIndex === currentSection.length - 1;
            
            // تحديث واجهة المستخدم
            videoTitle.textContent = video.title;
            videoDescription.innerHTML = `<p>${video.description || 'لا يوجد وصف لهذا الدرس'}</p>`;
            progressContainer.style.display = 'block';
            
            // تعطيل/تفعيل أزرار التنقل
            prevButton.disabled = isFirstVideo;
            nextButton.disabled = isLastVideo;
            
            // تحديث العنصر النشط في القائمة
            lessonItems.forEach(item => {
                if (parseInt(item.dataset.videoId) === videoId) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
            
            // إعادة تعيين متغيرات التقدم
            lastSaveTime = 0;
            
            // تحميل آخر موضع تم الوصول إليه من الخادم
            loadVideoProgress(videoId).then(progress => {
                console.log('Loaded progress:', progress);
                
                // الحصول على توكن الوصول للفيديو
                fetch(`/video/token/${courseId}/${videoId}`)
                        .then(response => {
                            if (!response.ok) {
                            throw new Error(`خطأ في استجابة الخادم: ${response.status} ${response.statusText}`);
                            }
                            return response.json();
                        })
                    .then(data => {
                        // تأكد من وجود توكن في البيانات
                        if (!data.token) {
                            throw new Error('لم يتم العثور على توكن الوصول');
                        }
                        
                        console.log('تم الحصول على توكن الوصول:', data.token);
                        
                        // إعداد رابط الفيديو
                        const videoUrl = `/video/stream/${data.token}`;
                        console.log('رابط الفيديو:', videoUrl);
                        
                        // تنظيف أي أحداث سابقة
                        videoPlayer.onloadedmetadata = null;
                        videoPlayer.onwaiting = null;
                        videoPlayer.onplaying = null;
                        videoPlayer.onended = null;
                        videoPlayer.onerror = null;
                        videoPlayer.ontimeupdate = null;
                        
                        // التحقق من نوع الفيديو (HLS أم لا)
                        if (data.video_info && data.video_info.is_hls_enabled) {
                            console.log('استخدام تدفق HLS للفيديو');
                            
                            // التحقق من دعم المتصفح لـ HLS
                            if (videoPlayer.canPlayType('application/vnd.apple.mpegurl')) {
                                // دعم أصلي في Safari
                                videoPlayer.src = videoUrl;
                            } else if (Hls.isSupported()) {
                                // استخدام مكتبة HLS.js للمتصفحات الأخرى
                                const hls = new Hls();
                                hls.loadSource(videoUrl);
                                hls.attachMedia(videoPlayer);
                                
                                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                                    console.log('تم تحليل manifest بنجاح، بدء التشغيل');
                                    videoLoading.style.display = 'none';
                                    
                                    // استعادة آخر موضع للفيديو
                                    if (videoStartTime > 0 && videoStartTime < videoPlayer.duration * 0.9) {
                                        videoPlayer.currentTime = videoStartTime;
                                    }
                                    
                                    // محاولة التشغيل التلقائي
                                    try {
                                        videoPlayer.play();
                                    } catch (e) {
                                        console.log('لا يمكن التشغيل التلقائي بدون تفاعل المستخدم:', e);
                                    }
                                });
                                
                                        hls.on(Hls.Events.ERROR, function(event, data) {
                                    console.error('خطأ في HLS:', event, data);
                                            if (data.fatal) {
                                                switch(data.type) {
                                                    case Hls.ErrorTypes.NETWORK_ERROR:
                                                console.log('خطأ شبكة، محاولة إعادة التحميل...');
                                                        hls.startLoad();
                                                        break;
                                                    case Hls.ErrorTypes.MEDIA_ERROR:
                                                console.log('خطأ وسائط، محاولة الإصلاح...');
                                                        hls.recoverMediaError();
                                                        break;
                                                    default:
                                                // خطأ غير قابل للإصلاح
                                                videoLoading.style.display = 'none';
                                                videoError.style.display = 'flex';
                                                errorMessage.textContent = 'حدث خطأ أثناء تشغيل الفيديو. يرجى المحاولة مرة أخرى.';
                                                        break;
                                                }
                                            }
                                        });
                            } else {
                                // الرجوع إلى استخدام طريقة العرض العادية في حالة عدم توفر دعم HLS
                                console.log('المتصفح لا يدعم HLS، الرجوع للطريقة العادية');
                                videoPlayer.src = videoUrl;
                            }
                        } else {
                            // استخدام الطريقة العادية لتشغيل الفيديو
                            console.log('استخدام التدفق العادي للفيديو');
                            videoPlayer.src = videoUrl;
                        }
                        
                        // إعداد عنصر الفيديو
                        videoPlayer.controls = true;
                        
                        // تسجيل الأحداث
                        videoPlayer.onloadedmetadata = function() {
                            console.log('تم تحميل معلومات الفيديو:', videoPlayer.duration);
                            videoLoading.style.display = 'none';
                            
                            // استعادة آخر موضع للفيديو
                            if (videoStartTime > 0 && videoStartTime < videoPlayer.duration * 0.9) {
                                videoPlayer.currentTime = videoStartTime;
                            }
                        };
                        
                        videoPlayer.onwaiting = function() {
                            console.log('انتظار تحميل الفيديو...');
                            videoLoading.style.display = 'flex';
                        };
                        
                        videoPlayer.onplaying = function() {
                            console.log('بدأ تشغيل الفيديو');
                            videoLoading.style.display = 'none';
                        };
                        
                        videoPlayer.onended = function() {
                            console.log('انتهاء الفيديو');
                            // حفظ تقدم الفيديو على السيرفر
                            saveProgress(100, true);
                            
                            // عرض زر الانتقال للدرس التالي
                            if (!nextButton.disabled) {
                                showNextVideoPrompt();
                            }
                        };
                        
                        videoPlayer.onerror = function(e) {
                            console.error('خطأ في تشغيل الفيديو:', e);
                            console.log('رمز الخطأ:', videoPlayer.error ? videoPlayer.error.code : 'غير متوفر');
                            console.log('رسالة الخطأ:', videoPlayer.error ? videoPlayer.error.message : 'غير متوفرة');
                            
                            // محاولة تشغيل الفيديو مباشرة كملف mp4 من التخزين العام
                            if (data.video_info && data.video_info.path) {
                                const filename = data.video_info.path.split('/').pop();
                                const directUrl = `/storage/courses/${courseId}/videos/${filename}`;
                                console.log('محاولة تشغيل الفيديو من المسار المباشر:', directUrl);
                                
                                videoPlayer.src = directUrl;
                                videoPlayer.load();
                                } else {
                                videoLoading.style.display = 'none';
                                videoError.style.display = 'flex';
                                errorMessage.textContent = 'حدث خطأ أثناء تحميل الفيديو. يرجى المحاولة مرة أخرى.';
                            }
                        };
                        
                        // تسجيل حدث تحديث الوقت
                        videoPlayer.ontimeupdate = function() {
                            if (videoPlayer.currentTime > 0 && videoPlayer.duration > 0) {
                                const percentage = Math.min(Math.round((videoPlayer.currentTime / videoPlayer.duration) * 100), 100);
                                updateProgressUI(percentage);
                                
                                // حفظ آخر موضع للفيديو محلياً في كل مرة
                                localStorage.setItem(`video_${currentVideoId}_position`, videoPlayer.currentTime);
                                localStorage.setItem(`video_${currentVideoId}_percentage`, percentage);
                                
                                // تعليم الفيديو كمكتمل إذا تم مشاهدة 90% أو أكثر
                                if (percentage >= 90 && !isVideoComplete) {
                                    markVideoAsComplete(currentVideoId);
                                    isVideoComplete = true;
                                    saveProgress(percentage, true);
                                    return; // حفظ فوري عند الإكمال
                                }
                                
                                // حفظ التقدم للخادم كل SAVE_INTERVAL ثوانية (لتقليل الطلبات)
                                const now = Date.now();
                                if (now - lastSaveTime > SAVE_INTERVAL) {
                                    lastSaveTime = now;
                                    saveProgress(percentage, false);
                                    console.log(`Auto-saving progress: ${percentage}%`);
                                }
                            }
                        };
                        
                        // بدء تحميل الفيديو
                        videoPlayer.load();
                        
                        // محاولة التشغيل التلقائي (قد لا تعمل في بعض المتصفحات بدون تفاعل المستخدم)
                        try {
                            const playPromise = videoPlayer.play();
                            if (playPromise !== undefined) {
                                playPromise.catch(error => {
                                    console.log('لا يمكن التشغيل التلقائي بدون تفاعل المستخدم:', error);
                                });
                            }
                        } catch (e) {
                            console.log('خطأ في محاولة التشغيل التلقائي:', e);
                        }
                        
                        // البدء في حفظ التقدم دورياً كاحتياط إضافي (في حالة عدم تنشيط حدث التحديث)
                        if (saveProgressInterval) {
                            clearInterval(saveProgressInterval);
                        }
                        
                        saveProgressInterval = setInterval(() => {
                            if (videoPlayer.currentTime > 0 && videoPlayer.duration > 0) {
                                const percentage = Math.min(Math.round((videoPlayer.currentTime / videoPlayer.duration) * 100), 100);
                                console.log('Interval-based progress save:', percentage + '%');
                                
                                // حفظ محلي أولاً
                                localStorage.setItem(`video_${currentVideoId}_position`, videoPlayer.currentTime);
                                localStorage.setItem(`video_${currentVideoId}_percentage`, percentage);
                                
                                // ثم حفظ على الخادم إذا مر وقت كافي من آخر حفظ
                                const now = Date.now();
                                if (now - lastSaveTime > SAVE_INTERVAL) {
                                    lastSaveTime = now;
                                    saveProgress(percentage, percentage >= 90);
                                }
                            }
                        }, 30000); // حفظ كل 30 ثانية كاحتياط
                        })
                        .catch(error => {
                        console.error('خطأ في تحميل الفيديو:', error);
                        videoLoading.style.display = 'none';
                        videoError.style.display = 'flex';
                        errorMessage.textContent = `فشل في تحميل الفيديو: ${error.message}`;
                    });
            });
            
            // تحميل المواد التعليمية المرتبطة بالقسم
            loadSectionMaterials(currentSectionId);
            
            // تحديث URL الصفحة
            updatePageUrl();
        }
        
        // وظيفة تحديث URL الصفحة
        function updatePageUrl() {
            const url = new URL(window.location.href);
            url.searchParams.set('videoId', currentVideoId);
            history.replaceState(null, '', url);
        }
        
        // وظيفة تحديث واجهة التقدم
        function updateProgressUI(percentage) {
            progressBar.style.width = `${percentage}%`;
            progressBar.setAttribute('aria-valuenow', percentage);
            progressPercentage.textContent = `${percentage}%`;
        }
        
        // وظيفة عرض مربع الانتقال للدرس التالي
        function showNextVideoPrompt() {
            const nextPrompt = document.createElement('div');
            nextPrompt.className = 'next-video-prompt animate__animated animate__fadeInUp';
            nextPrompt.innerHTML = `
                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded shadow-sm">
                    <p class="m-0">أحسنت! هل تريد الانتقال للدرس التالي؟</p>
                    <button id="go-to-next" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i> الدرس التالي
                                            </button>
                                    </div>
                                `;
            
            // إضافة الاستايل
            const style = document.createElement('style');
            style.textContent = `
                .next-video-prompt {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    left: 20px;
                    z-index: 1050;
                    max-width: 500px;
                    margin: 0 auto;
                }
            `;
            
            document.head.appendChild(style);
            document.body.appendChild(nextPrompt);
            
            // إضافة حدث النقر
            document.getElementById('go-to-next').addEventListener('click', function() {
                nextButton.click();
                nextPrompt.remove();
            });
            
            // إزالة بعد 10 ثواني
            setTimeout(() => {
                if (document.contains(nextPrompt)) {
                    nextPrompt.classList.replace('animate__fadeInUp', 'animate__fadeOutDown');
                    setTimeout(() => nextPrompt.remove(), 1000);
                }
            }, 10000);
        }
        
        // وظيفة إعادة محاولة تحميل الفيديو
        window.retryVideo = function() {
            console.log('إعادة محاولة تحميل الفيديو');
            
            // إعادة تعيين حالة الشاشة
            videoError.style.display = 'none';
            videoLoading.style.display = 'flex';
            
            // الحصول على توكن جديد
            fetch(`/video/token/${courseId}/${currentVideoId}`)
            .then(response => {
                if (!response.ok) {
                        throw new Error(`خطأ في استجابة الخادم: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                    if (!data.token) {
                        throw new Error('لم يتم العثور على توكن الوصول');
                    }
                    
                    console.log('تم الحصول على توكن جديد:', data.token);
                    
                    // إضافة طابع زمني لمنع التخزين المؤقت
                    const videoUrl = `/video/stream/${data.token}?t=${new Date().getTime()}`;
                    
                    // تعيين مصدر الفيديو
                    videoPlayer.src = videoUrl;
                    videoPlayer.load();
                    
                    // محاولة التشغيل
                    try {
                        videoPlayer.play();
                    } catch (e) {
                        console.log('لا يمكن بدء التشغيل تلقائيًا');
                    }
                    
                    // في حالة وجود خطأ، جرب المسار المباشر كخطة بديلة
                    videoPlayer.onerror = function() {
                        if (data.video_info && data.video_info.path) {
                            const filename = data.video_info.path.split('/').pop();
                            const directUrl = `/storage/courses/${courseId}/videos/${filename}`;
                            console.log('محاولة استخدام مسار مباشر:', directUrl);
                            
                            videoPlayer.src = directUrl;
                            videoPlayer.load();
                        }
                    };
            })
            .catch(error => {
                    console.error('فشل إعادة تحميل الفيديو:', error);
                    videoLoading.style.display = 'none';
                    videoError.style.display = 'flex';
                    errorMessage.textContent = `خطأ: ${error.message}`;
                });
        };
        
        // وظيفة لتجربة صيغ فيديو مختلفة وطرق وصول متعددة
        function tryMultipleVideoFormats(courseId, filenameBase, videoId) {
            const videoTitle = videos.find(v => v.id === videoId)?.title || 'فيديو';
            const formats = [
                { ext: 'mp4', type: 'video/mp4' },
                { ext: 'webm', type: 'video/webm' },
                { ext: 'ogg', type: 'video/ogg' },
                { ext: 'mov', type: 'video/quicktime' }
            ];
            
            const paths = [
                `/storage/courses/${courseId}/videos/${filenameBase}`,
                `/storage/courses/${courseId}/videos/${filenameBase.toLowerCase()}`,
                `/storage/videos/${filenameBase}`,
                `/uploads/courses/${courseId}/videos/${filenameBase}`,
                `/public/storage/courses/${courseId}/videos/${filenameBase}`
            ];
            
            console.log('محاولة تشغيل الفيديو بصيغ مختلفة...');
            
            // تجربة جميع المسارات والصيغ المحتملة
            for (const path of paths) {
                for (const format of formats) {
                    const fullPath = `${path}.${format.ext}`;
                    console.log(`محاولة: ${fullPath}`);
                    
                    // إنشاء عنصر فيديو للاختبار
                    const testVideo = document.createElement('video');
                    testVideo.style.display = 'none';
                    testVideo.src = fullPath;
                    document.body.appendChild(testVideo);
                    
                    testVideo.onloadedmetadata = function() {
                        console.log(`تم تحميل الفيديو بنجاح: ${fullPath}`);
                        
                        // إذا نجح التحميل، جرب تشغيله في المشغل الرئيسي
                        player.source = {
                            type: 'video',
                            title: videoTitle,
                            sources: [{
                                src: fullPath,
                                type: format.type,
                            }]
                        };
                        
                        // تنظيف
                        document.body.removeChild(testVideo);
                        return; // توقف عن البحث بمجرد العثور على صيغة تعمل
                    };
                    
                    testVideo.onerror = function() {
                        console.log(`فشل تحميل: ${fullPath}`);
                        document.body.removeChild(testVideo);
                    };
                    
                    // تجربة الفيديو لمدة ثانية واحدة
                    setTimeout(() => {
                        if (document.body.contains(testVideo)) {
                            document.body.removeChild(testVideo);
                        }
                    }, 1000);
                }
            }
        }
        
        // تحميل المواد التعليمية
        function loadSectionMaterials(sectionId) {
            fetch(`/course/${courseId}/section/${sectionId}/materials`)
                .then(response => response.json())
                .then(data => {
                    if (data.materials && data.materials.length > 0) {
                        sectionMaterialsContainer.style.display = 'block';
                        sectionMaterialsList.innerHTML = '';
                        
                        data.materials.forEach(material => {
                            let iconClass = 'fa-file';
                            if (['pdf', 'doc', 'docx'].includes(material.file_type)) {
                                iconClass = 'fa-file-pdf';
                            } else if (['xls', 'xlsx', 'csv'].includes(material.file_type)) {
                                iconClass = 'fa-file-excel';
                            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(material.file_type)) {
                                iconClass = 'fa-file-image';
                            } else if (['zip', 'rar'].includes(material.file_type)) {
                                iconClass = 'fa-file-archive';
                            }
                            
                            const materialHtml = `
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="me-3 text-primary">
                                                <i class="fas ${iconClass} fa-2x"></i>
                                            </div>
                                        <div>
                                                <h5 class="card-title mb-1">${material.title}</h5>
                                                <p class="card-text small text-muted">
                                                    ${material.file_type.toUpperCase()} • ${material.formatted_file_size || 'غير معروف'}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent border-0 text-end">
                                            <a href="/courses/materials/download/${courseId}/${material.material_id}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-download me-1"></i> تحميل
                                            </a>
                                        </div>
                                        </div>
                                    </div>
                                `;
                            
                            sectionMaterialsList.innerHTML += materialHtml;
                        });
                    } else {
                        sectionMaterialsContainer.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error loading materials:', error);
                    sectionMaterialsContainer.style.display = 'none';
                });
        }
    });
</script>
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
                    <div id="video-loading" class="video-loading" style="display: none;">
                        <div class="spinner-border text-light" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                </div>
                    </div>
                    <div id="video-error" class="video-error" style="display: none;">
                        <div>
                            <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                            <h5>خطأ في تحميل الفيديو</h5>
                            <p id="error-message">حدث خطأ أثناء محاولة تحميل الفيديو. يرجى المحاولة مرة أخرى.</p>
                            <button class="btn btn-light btn-sm mt-2" onclick="retryVideo()">
                                <i class="fas fa-redo me-1"></i> إعادة المحاولة
                        </button>
                    </div>
                    </div>
                    <video id="video-player" playsinline controls></video>
                </div>

                <!-- معلومات الفيديو الحالي -->
                <div class="px-3 py-2 text-center" id="current-video-info">
                    <h4 id="current-video-title" class="mb-0">اختر فيديو من القائمة للمشاهدة</h4>
                    </div>
                </div>
            
            <!-- أزرار التنقل بين الفيديوهات -->
            <div class="video-navigation">
                <button id="prev-video-btn" class="nav-btn nav-btn-secondary" disabled>
                    <i class="fas fa-chevron-right me-2"></i> الدرس السابق
                </button>
                <button id="next-video-btn" class="nav-btn nav-btn-primary" disabled>
                    الدرس التالي <i class="fas fa-chevron-left ms-2"></i>
                </button>
            </div>

            <!-- تفاصيل الفيديو -->
            <div class="video-info-panel animate__animated animate__fadeIn">
                <div class="video-info-header">
                    <i class="fas fa-info-circle me-2"></i>
                    <h5 class="video-info-title">تفاصيل الدرس</h5>
                            </div>
                <div class="video-info-body">
                    <div id="video-description">
                        <p class="text-muted">يرجى اختيار درس من القائمة لعرض التفاصيل</p>
                                </div>
                    
                    <!-- شريط التقدم -->
                    <div class="progress-container" id="video-progress-container" style="display: none;">
                        <div class="progress-label">
                            <span>تقدمك في الدرس</span>
                            <span class="progress-percentage" id="progress-percentage">0%</span>
                            </div>
                        <div class="progress">
                            <div class="progress-bar" id="video-progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- المواد التعليمية -->
            <div id="section-materials-container" class="mt-4" style="display: none;">
                <div class="video-info-panel animate__animated animate__fadeIn">
                    <div class="video-info-header">
                        <i class="fas fa-file-alt me-2"></i>
                        <h5 class="video-info-title">المواد التعليمية المرفقة</h5>
                        </div>
                    <div class="video-info-body">
                        <div id="section-materials-list" class="row">
                            <!-- هنا سيتم عرض المواد التعليمية ديناميكياً -->
                    </div>
                    </div>
                    </div>
            </div>
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
                        <span class="progress-percentage">{{ isset($progress->total_percentage) ? number_format($progress->total_percentage) : 0 }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ isset($progress->total_percentage) ? $progress->total_percentage : 0 }}%" 
                             aria-valuenow="{{ isset($progress->total_percentage) ? $progress->total_percentage : 0 }}" 
                             aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                            
                <!-- الأقسام والدروس -->
                                                <div>
                    @if($course->sections->count() > 0)
                        @foreach($course->sections as $sectionIndex => $section)
                            <div class="section-item">
                                <div class="section-header d-flex justify-content-between align-items-center" 
                                     data-bs-toggle="collapse" 
                                     data-bs-target="#section{{ $section->section_id }}" 
                                     aria-expanded="{{ $sectionIndex === 0 ? 'true' : 'false' }}">
                                    <div class="section-title">
                                        <div class="section-icon">
                                            <i class="fas fa-layer-group"></i>
                                                </div>
                                        {{ $section->title }}
                                            </div>
                                    <div class="d-flex align-items-center">
                                        <span class="section-videos-count">{{ $section->videos->count() }} دروس</span>
                                        <i class="fas fa-chevron-down section-toggle ms-2 {{ $sectionIndex === 0 ? '' : 'collapsed' }}"></i>
                                </div>
                        </div>
                                
                                <div id="section{{ $section->section_id }}" 
                                     class="collapse {{ $sectionIndex === 0 ? 'show' : '' }}">
                    <ul class="lesson-list">
                                        @foreach($section->videos as $videoIndex => $video)
                                            <li class="lesson-item" 
                                    data-video-id="{{ $video->video_id }}"
                                    data-video-title="{{ $video->title }}"
                                    data-video-description="{{ $video->description }}"
                                                data-section-id="{{ $section->section_id }}"
                                                data-video-index="{{ $videoIndex }}"
                                                data-section-index="{{ $sectionIndex }}">
                                                <div class="video-play-icon">
                                                    <i class="fas fa-play"></i>
                                                </div>
                                    <div class="lesson-title">
                                                    <div>{{ $video->title }}</div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="video-completion-indicator" id="completion-{{ $video->video_id }}">
                                                            {{-- سيتم تحديث حالة الإكمال ديناميكيًا --}}
                                        </div>
                                                        <span class="lesson-duration">{{ gmdate('i:s', $video->duration_seconds) }}</span>
                                                    </div>
                                    </div>
                                </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div class="p-3 text-center">
                            <p class="text-muted">لا توجد دروس متاحة حاليًا</p>
                        </div>
                        @endif
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

<!-- مربع تهنئة إكمال الكورس -->
<div class="modal fade" id="courseCompletionModal" tabindex="-1" aria-labelledby="courseCompletionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="courseCompletionModalLabel">تهانينا! 🎉</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="fas fa-award fa-4x text-warning mb-3"></i>
                    <h4>مبروك! لقد أكملت الكورس بنجاح</h4>
                    <p class="text-muted">{{ $course->title }}</p>
                    <div class="progress mt-3 mb-3">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>
                    </div>
                </div>
                
                @if($course->certificate_available)
                <div class="certificate-container border rounded p-3 mb-3 bg-light">
                    <h5><i class="fas fa-certificate text-warning me-2"></i> شهادة إتمام الكورس</h5>
                    <p class="small mb-3">يمكنك الآن الحصول على شهادة إتمام لهذا الكورس</p>
                    <a href="{{ route('student.certificate.request', $course->course_id) }}" class="btn btn-success">
                        <i class="fas fa-download me-2"></i> تحميل الشهادة
                    </a>
                </div>
                @endif
                
                <div>
                    <p class="mb-4">ماذا تريد أن تفعل الآن؟</p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <a href="{{ route('student.my-courses') }}" class="btn btn-outline-primary">
                            <i class="fas fa-book me-2"></i> العودة للكورسات
                        </a>
                        <a href="{{ route('courses.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-search me-2"></i> استكشاف كورسات جديدة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
