/**
 * Video Cache Buster
 * يساعد على منع التخزين المؤقت للفيديوهات في المتصفح
 */
(function() {
    // إضافة معلمة عشوائية لروابط الفيديو
    function addCacheBusterToVideoUrls() {
        const videoElements = document.querySelectorAll('video source');
        videoElements.forEach(source => {
            if (source.src) {
                // تحقق مما إذا كان الرابط يحتوي على معلمات URL
                const hasParams = source.src.indexOf('?') !== -1;
                // إضافة معلمة زمنية عشوائية
                const separator = hasParams ? '&' : '?';
                source.src = source.src + separator + '_cb=' + Date.now();
                
                // تحديث عنصر الفيديو الأب
                const video = source.parentElement;
                if (video && video.tagName === 'VIDEO') {
                    video.load(); // إعادة تحميل الفيديو مع الرابط الجديد
                }
            }
        });
    }

    // استخدام خيار force-refresh لمنع التخزين المؤقت للفيديوهات
    function disableVideoCache() {
        // إضافة أنماط CSS لمنع التخزين المؤقت للفيديوهات
        const style = document.createElement('style');
        style.textContent = `
            video {
                will-change: transform; /* تحسين أداء الفيديو */
            }
        `;
        document.head.appendChild(style);
        
        // إعادة تعيين عناصر الفيديو عند التحميل
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) { // صفحة معروضة من التخزين المؤقت
                const videos = document.querySelectorAll('video');
                videos.forEach(video => {
                    video.currentTime = 0; // إعادة تعيين الوقت
                    video.load(); // إعادة تحميل الفيديو
                });
            }
        });
    }

    // كشف عن روابط تبديل الفيديو
    function interceptVideoTokenRequests() {
        // اعتراض طلبات الحصول على رموز الوصول للفيديو
        const originalFetch = window.fetch;
        window.fetch = function() {
            const url = arguments[0];
            if (typeof url === 'string' && url.includes('/video/token/')) {
                // إضافة معلمة عشوائية لمنع التخزين المؤقت
                const hasParams = url.indexOf('?') !== -1;
                const separator = hasParams ? '&' : '?';
                arguments[0] = url + separator + '_nocache=' + Date.now();
            }
            return originalFetch.apply(this, arguments);
        };
        
        // اعتراض طلبات XMLHttpRequest
        const originalOpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function() {
            if (arguments.length > 1 && typeof arguments[1] === 'string' && arguments[1].includes('/video/token/')) {
                const url = arguments[1];
                const hasParams = url.indexOf('?') !== -1;
                const separator = hasParams ? '&' : '?';
                arguments[1] = url + separator + '_nocache=' + Date.now();
            }
            return originalOpen.apply(this, arguments);
        };
    }
    
    // تنفيذ جميع الوظائف
    function init() {
        addCacheBusterToVideoUrls();
        disableVideoCache();
        interceptVideoTokenRequests();
        
        // تنفيذ الدوال عند تغيير المحتوى (مثل SPA)
        document.addEventListener('DOMContentLoaded', function() {
            addCacheBusterToVideoUrls();
        });
        
        // تنفيذ الدوال كل 10 دقائق للتأكد من تحديث الفيديوهات
        setInterval(addCacheBusterToVideoUrls, 10 * 60 * 1000);
    }
    
    // تنفيذ عند تحميل الصفحة
    init();
})(); 