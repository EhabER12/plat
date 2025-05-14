@php
$footerSettings = \App\Models\WebsiteAppearance::getSection('footer');
$footerLogo = $footerSettings['footer_logo'] ?? null;
$footerDescription = $footerSettings['footer_description'] ?? 'منصة تعليمية متكاملة تهدف إلى تمكين المتعلمين من جميع أنحاء العالم من الوصول إلى تعليم عالي الجودة.';
$footerPhone = $footerSettings['footer_phone'] ?? '(123) 456-7890';
$footerEmail = $footerSettings['footer_email'] ?? 'info@elearning.com';
$footerAddress = $footerSettings['footer_address'] ?? '123 شارع التعليم، مدينة المعرفة';
$footerLinks = $footerSettings['footer_links'] ?? null;
$footerCopyright = $footerSettings['footer_copyright'] ?? 'جميع الحقوق محفوظة.';
$footerSocialLinks = $footerSettings['footer_social_links'] ?? null;

// تحويل الروابط من JSON إلى مصفوفة
$quickLinks = json_decode($footerLinks, true) ?? [
    ['title' => 'الرئيسية', 'url' => '/'],
    ['title' => 'الكورسات', 'url' => '/courses'],
    ['title' => 'من نحن', 'url' => '/about'],
    ['title' => 'اتصل بنا', 'url' => '/contact'],
];

// تحويل روابط التواصل الاجتماعي من JSON إلى مصفوفة
$socialLinks = json_decode($footerSocialLinks, true) ?? [
    ['platform' => 'facebook', 'url' => '#', 'icon' => 'fab fa-facebook-f'],
    ['platform' => 'twitter', 'url' => '#', 'icon' => 'fab fa-twitter'],
    ['platform' => 'instagram', 'url' => '#', 'icon' => 'fab fa-instagram'],
    ['platform' => 'linkedin', 'url' => '#', 'icon' => 'fab fa-linkedin-in'],
];

// الحصول على فئات الكورسات
$courseCategories = \App\Models\Category::take(5)->get();
@endphp

<footer class="bg-dark text-white pt-5 pb-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">منصة تعليمية</h5>
                <p>{{ $footerDescription }}</p>
                <div class="social-icons mt-4">
                    @foreach($socialLinks as $link)
                        <a href="{{ $link['url'] }}" class="text-white me-3" target="_blank"><i class="{{ $link['icon'] }}"></i></a>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">روابط سريعة</h5>
                <ul class="list-unstyled">
                    @foreach($quickLinks as $link)
                        <li class="mb-2"><a href="{{ $link['url'] }}" class="text-white text-decoration-none">{{ $link['title'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">الفئات</h5>
                <ul class="list-unstyled">
                    @forelse($courseCategories as $category)
                        <li class="mb-2"><a href="{{ url('/courses?category=' . $category->id) }}" class="text-white text-decoration-none">{{ $category->name }}</a></li>
                    @empty
                        <li class="mb-2"><a href="{{ url('/courses') }}" class="text-white text-decoration-none">جميع الدورات</a></li>
                    @endforelse
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase mb-4">اتصل بنا</h5>
                <p><i class="fas fa-map-marker-alt me-2"></i> {{ $footerAddress }}</p>
                <p><i class="fas fa-phone me-2"></i> {{ $footerPhone }}</p>
                <p><i class="fas fa-envelope me-2"></i> {{ $footerEmail }}</p>
            </div>
        </div>
    </div>
    <div class="text-center p-3 mt-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © {{ date('Y') }} منصة تعليمية. {{ $footerCopyright }}
    </div>
</footer>

<style>
    .social-icons a {
        display: inline-block;
        width: 36px;
        height: 36px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        text-align: center;
        line-height: 36px;
        margin-right: 8px;
        transition: all 0.3s ease;
    }
    
    .social-icons a:hover {
        background-color: var(--secondary-color);
        color: var(--primary-color) !important;
        transform: translateY(-3px);
    }
    
    footer h5 {
        position: relative;
        padding-bottom: 12px;
        color: var(--secondary-color);
    }
    
    footer h5:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 2px;
        background: var(--secondary-color);
    }
    
    [dir="rtl"] footer h5:after {
        left: auto;
        right: 0;
    }
    
    footer ul li a:hover {
        color: var(--secondary-color) !important;
        padding-left: 5px;
    }
    
    [dir="rtl"] footer ul li a:hover {
        padding-left: 0;
        padding-right: 5px;
    }
    
    @media (max-width: 767px) {
        footer h5 {
            margin-top: 15px;
        }
    }
</style> 