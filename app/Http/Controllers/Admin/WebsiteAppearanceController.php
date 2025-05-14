<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteAppearance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WebsiteAppearanceController extends Controller
{
    /**
     * Display the website appearance settings page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get hero section settings
        $heroSettings = WebsiteAppearance::getSection(WebsiteAppearance::SECTION_HERO);
        
        // Get features section settings
        $featuresSettings = WebsiteAppearance::getSection(WebsiteAppearance::SECTION_FEATURES);
        
        // Get stats section settings
        $statsSettings = WebsiteAppearance::getSection('stats');
        
        // Get navbar banner settings
        $navbarBannerSettings = WebsiteAppearance::getSection(WebsiteAppearance::SECTION_NAVBAR_BANNER);
        
        // Get about section settings
        $aboutSettings = WebsiteAppearance::getSection('about');
        
        // Get video section settings
        $videoSettings = WebsiteAppearance::getSection('video');
        
        // Get partners section settings
        $partnersSettings = WebsiteAppearance::getSection(WebsiteAppearance::SECTION_PARTNERS);
        
        return view('admin.website-appearance', compact(
            'heroSettings',
            'featuresSettings',
            'statsSettings',
            'navbarBannerSettings',
            'aboutSettings',
            'videoSettings',
            'partnersSettings'
        ));
    }

    /**
     * Clear all caches related to website appearance
     *
     * @return void
     */
    private function clearAllCaches()
    {
        // Clear website appearance cache
        WebsiteAppearance::clearCache();
        
        // Clear homepage data cache
        \Illuminate\Support\Facades\Cache::forget('home_page_data');
        
        // Clear other related caches
        \Illuminate\Support\Facades\Cache::forget('website_settings');
        
        // Clear all Artisan caches as well
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
    }

    /**
     * Update hero section settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateHero(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'required|string|max:500',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hero_bg_color' => 'nullable|string|max:20',
            'button_text' => 'nullable|string|max:50',
            'button_url' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Save hero title
        WebsiteAppearance::setSetting(
            'hero_title', 
            $request->hero_title, 
            WebsiteAppearance::SECTION_HERO
        );
        
        // Save hero subtitle
        WebsiteAppearance::setSetting(
            'hero_subtitle', 
            $request->hero_subtitle, 
            WebsiteAppearance::SECTION_HERO
        );
        
        // Save background color
        if ($request->has('hero_bg_color')) {
            WebsiteAppearance::setSetting(
                'hero_bg_color', 
                $request->hero_bg_color, 
                WebsiteAppearance::SECTION_HERO, 
                WebsiteAppearance::TYPE_COLOR
            );
        }
        
        // Save button text
        if ($request->has('button_text')) {
            WebsiteAppearance::setSetting(
                'button_text', 
                $request->button_text, 
                WebsiteAppearance::SECTION_HERO
            );
        }
        
        // Save button URL
        if ($request->has('button_url')) {
            WebsiteAppearance::setSetting(
                'button_url', 
                $request->button_url, 
                WebsiteAppearance::SECTION_HERO
            );
        }
        
        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            // Delete old image if exists
            $oldImage = WebsiteAppearance::getSetting('hero_image', WebsiteAppearance::SECTION_HERO);
            if ($oldImage && Storage::exists('public/' . $oldImage)) {
                Storage::delete('public/' . $oldImage);
            }
            
            // Store new image
            $imagePath = $request->file('hero_image')->store('website/hero', 'public');
            
            // Save image path
            WebsiteAppearance::setSetting(
                'hero_image', 
                $imagePath, 
                WebsiteAppearance::SECTION_HERO, 
                WebsiteAppearance::TYPE_IMAGE
            );
        }
        
        // Clear all caches to reflect changes immediately
        $this->clearAllCaches();

        return redirect()->route('admin.website-appearance')
            ->with('success', 'Hero section settings updated successfully.');
    }

    /**
     * Update stats section settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'students_count' => 'required|integer|min:0',
            'students_text' => 'required|string|max:50',
            'courses_count' => 'required|integer|min:0',
            'courses_text' => 'required|string|max:50',
            'instructors_count' => 'required|integer|min:0',
            'instructors_text' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Save students stats
        WebsiteAppearance::setSetting(
            'students_count', 
            $request->students_count, 
            'stats',
            WebsiteAppearance::TYPE_NUMBER
        );
        
        WebsiteAppearance::setSetting(
            'students_text', 
            $request->students_text, 
            'stats'
        );
        
        // Save courses stats
        WebsiteAppearance::setSetting(
            'courses_count', 
            $request->courses_count, 
            'stats',
            WebsiteAppearance::TYPE_NUMBER
        );
        
        WebsiteAppearance::setSetting(
            'courses_text', 
            $request->courses_text, 
            'stats'
        );
        
        // Save instructors stats
        WebsiteAppearance::setSetting(
            'instructors_count', 
            $request->instructors_count, 
            'stats',
            WebsiteAppearance::TYPE_NUMBER
        );
        
        WebsiteAppearance::setSetting(
            'instructors_text', 
            $request->instructors_text, 
            'stats'
        );
        
        // Clear cache to reflect changes immediately
        WebsiteAppearance::clearCache();

        return redirect()->route('admin.website-appearance')
            ->with('success', 'Stats section settings updated successfully.');
    }

    /**
     * Update features section settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFeatures(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'features_title' => 'required|string|max:255',
            'features_subtitle' => 'required|string|max:500',
            'features' => 'nullable|array',
            'features.*.title' => 'required|string|max:100',
            'features.*.description' => 'required|string|max:300',
            'features.*.icon' => 'required|string|max:50',
            'features.*.color' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Save features title
        WebsiteAppearance::setSetting(
            'features_title', 
            $request->features_title, 
            WebsiteAppearance::SECTION_FEATURES
        );
        
        // Save features subtitle
        WebsiteAppearance::setSetting(
            'features_subtitle', 
            $request->features_subtitle, 
            WebsiteAppearance::SECTION_FEATURES
        );
        
        // Save features list
        if ($request->has('features')) {
            WebsiteAppearance::setSetting(
                'features_list', 
                $request->features, 
                WebsiteAppearance::SECTION_FEATURES,
                WebsiteAppearance::TYPE_JSON
            );
        }
        
        // Clear cache to reflect changes immediately
        WebsiteAppearance::clearCache();

        return redirect()->route('admin.website-appearance')
            ->with('success', 'Features section settings updated successfully.');
    }
    
    /**
     * Update navbar banner section settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateNavbarBanner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banner_title' => 'required|string|max:255',
            'banner_subtitle' => 'nullable|string|max:500',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_bg_color' => 'nullable|string|max:20',
            'students_count' => 'nullable|integer|min:0',
            'courses_count' => 'nullable|integer|min:0',
            'instructors_count' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Save title and subtitle
        WebsiteAppearance::setSetting(
            'banner_title', 
            $request->banner_title, 
            WebsiteAppearance::SECTION_NAVBAR_BANNER
        );
        
        WebsiteAppearance::setSetting(
            'banner_subtitle', 
            $request->banner_subtitle, 
            WebsiteAppearance::SECTION_NAVBAR_BANNER
        );
        
        // Save statistics
        if ($request->has('students_count')) {
            WebsiteAppearance::setSetting(
                'students_count', 
                $request->students_count, 
                WebsiteAppearance::SECTION_NAVBAR_BANNER,
                WebsiteAppearance::TYPE_NUMBER
            );
        }
        
        if ($request->has('courses_count')) {
            WebsiteAppearance::setSetting(
                'courses_count', 
                $request->courses_count, 
                WebsiteAppearance::SECTION_NAVBAR_BANNER,
                WebsiteAppearance::TYPE_NUMBER
            );
        }
        
        if ($request->has('instructors_count')) {
            WebsiteAppearance::setSetting(
                'instructors_count', 
                $request->instructors_count, 
                WebsiteAppearance::SECTION_NAVBAR_BANNER,
                WebsiteAppearance::TYPE_NUMBER
            );
        }
        
        // Save background color
        if ($request->has('banner_bg_color')) {
            WebsiteAppearance::setSetting(
                'banner_bg_color', 
                $request->banner_bg_color, 
                WebsiteAppearance::SECTION_NAVBAR_BANNER, 
                WebsiteAppearance::TYPE_COLOR
            );
        }
        
        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            // Delete old image if exists
            $oldImage = WebsiteAppearance::getSetting('banner_image', WebsiteAppearance::SECTION_NAVBAR_BANNER);
            if ($oldImage && Storage::exists('public/' . $oldImage)) {
                Storage::delete('public/' . $oldImage);
            }
            
            // Store new image
            $imagePath = $request->file('banner_image')->store('website/banner', 'public');
            
            // Save image path
            WebsiteAppearance::setSetting(
                'banner_image', 
                $imagePath, 
                WebsiteAppearance::SECTION_NAVBAR_BANNER, 
                WebsiteAppearance::TYPE_IMAGE
            );
        }
        
        // Clear cache to reflect changes immediately
        WebsiteAppearance::clearCache();

        return redirect()->route('admin.website-appearance')
            ->with('success', 'Navbar banner settings updated successfully.');
    }

    /**
     * Update about section settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAbout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'about_title' => 'required|string|max:255',
            'about_description' => 'required|string|max:1000',
            'instructor_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'instructor_title' => 'required|string|max:100',
            'instructor_button_text' => 'required|string|max:50',
            'student_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'student_title' => 'required|string|max:100',
            'student_button_text' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Save about title
        WebsiteAppearance::setSetting(
            'about_title', 
            $request->about_title, 
            'about'
        );
        
        // Save about description
        WebsiteAppearance::setSetting(
            'about_description', 
            $request->about_description, 
            'about'
        );
        
        // Save instructor data
        WebsiteAppearance::setSetting(
            'instructor_title', 
            $request->instructor_title, 
            'about'
        );
        
        WebsiteAppearance::setSetting(
            'instructor_button_text', 
            $request->instructor_button_text, 
            'about'
        );
        
        // Save student data
        WebsiteAppearance::setSetting(
            'student_title', 
            $request->student_title, 
            'about'
        );
        
        WebsiteAppearance::setSetting(
            'student_button_text', 
            $request->student_button_text, 
            'about'
        );
        
        // Handle instructor image upload
        if ($request->hasFile('instructor_image')) {
            // Delete old image if exists
            $oldImage = WebsiteAppearance::getSetting('instructor_image', 'about');
            if ($oldImage && Storage::exists('public/' . $oldImage)) {
                Storage::delete('public/' . $oldImage);
            }
            
            // Store new image
            $imagePath = $request->file('instructor_image')->store('website/about', 'public');
            
            // Save image path
            WebsiteAppearance::setSetting(
                'instructor_image', 
                $imagePath, 
                'about', 
                WebsiteAppearance::TYPE_IMAGE
            );
        }
        
        // Handle student image upload
        if ($request->hasFile('student_image')) {
            // Delete old image if exists
            $oldImage = WebsiteAppearance::getSetting('student_image', 'about');
            if ($oldImage && Storage::exists('public/' . $oldImage)) {
                Storage::delete('public/' . $oldImage);
            }
            
            // Store new image
            $imagePath = $request->file('student_image')->store('website/about', 'public');
            
            // Save image path
            WebsiteAppearance::setSetting(
                'student_image', 
                $imagePath, 
                'about', 
                WebsiteAppearance::TYPE_IMAGE
            );
        }
        
        // Clear cache to reflect changes immediately
        WebsiteAppearance::clearCache();

        return redirect()->route('admin.website-appearance')
            ->with('success', 'About section settings updated successfully.');
    }
    
    /**
     * Update video section settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_title' => 'required|string|max:255',
            'video_subtitle' => 'required|string|max:255',
            'video_description' => 'required|string|max:1000',
            'video_button_text' => 'required|string|max:50',
            'video_button_url' => 'required|string|max:255',
            'video_embed_url' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Save video section data
        WebsiteAppearance::setSetting('video_title', $request->video_title, 'video');
        WebsiteAppearance::setSetting('video_subtitle', $request->video_subtitle, 'video');
        WebsiteAppearance::setSetting('video_description', $request->video_description, 'video');
        WebsiteAppearance::setSetting('video_button_text', $request->video_button_text, 'video');
        WebsiteAppearance::setSetting('video_button_url', $request->video_button_url, 'video');
        WebsiteAppearance::setSetting('video_embed_url', $request->video_embed_url, 'video');
        
        // Clear cache to reflect changes immediately
        WebsiteAppearance::clearCache();

        return redirect()->route('admin.website-appearance')
            ->with('success', 'Video section settings updated successfully.');
    }
    
    /**
     * Update partners section settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePartners(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'partners_title' => 'required|string|max:255',
            'partners_subtitle' => 'required|string|max:500',
            'partners' => 'nullable|array',
            'partners.*.name' => 'required|string|max:100',
            'partners.*.logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'partners.*.url' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Save partners section title and subtitle
        WebsiteAppearance::setSetting(
            'partners_title', 
            $request->partners_title, 
            WebsiteAppearance::SECTION_PARTNERS
        );
        
        WebsiteAppearance::setSetting(
            'partners_subtitle', 
            $request->partners_subtitle, 
            WebsiteAppearance::SECTION_PARTNERS
        );
        
        // Handle existing partners
        $existingPartners = WebsiteAppearance::getSetting('partners_list', WebsiteAppearance::SECTION_PARTNERS, []);
        $updatedPartners = [];
        
        // Process submitted partners
        if ($request->has('partners')) {
            foreach ($request->partners as $index => $partnerData) {
                $partner = [
                    'name' => $partnerData['name'],
                    'url' => $partnerData['url'] ?? '',
                ];
                
                // Keep existing logo if no new one is uploaded
                if (isset($existingPartners[$index]) && isset($existingPartners[$index]['logo']) && 
                    !isset($request->file('partners')[$index]['logo'])) {
                    $partner['logo'] = $existingPartners[$index]['logo'];
                }
                
                // Handle logo upload if present
                if (isset($request->file('partners')[$index]['logo'])) {
                    $logoFile = $request->file('partners')[$index]['logo'];
                    
                    // Delete old logo if exists
                    if (isset($existingPartners[$index]['logo']) && 
                        Storage::exists('public/' . $existingPartners[$index]['logo'])) {
                        Storage::delete('public/' . $existingPartners[$index]['logo']);
                    }
                    
                    // Store new logo
                    $logoPath = $logoFile->store('website/partners', 'public');
                    $partner['logo'] = $logoPath;
                }
                
                $updatedPartners[] = $partner;
            }
        }
        
        // Save updated partners list
        WebsiteAppearance::setSetting(
            'partners_list', 
            $updatedPartners, 
            WebsiteAppearance::SECTION_PARTNERS,
            WebsiteAppearance::TYPE_JSON
        );
        
        // Clear cache to reflect changes immediately
        WebsiteAppearance::clearCache();

        return redirect()->route('admin.website-appearance')
            ->with('success', 'Partners section settings updated successfully.');
    }

    /**
     * Update footer section settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFooter(Request $request)
    {
        // التحقق من البيانات
        $validator = Validator::make($request->all(), [
            'footer_description' => 'nullable|string|max:500',
            'footer_phone' => 'nullable|string|max:20',
            'footer_email' => 'nullable|email|max:100',
            'footer_address' => 'nullable|string|max:255',
            'footer_copyright' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // حفظ وصف الفوتر
        WebsiteAppearance::setSetting(
            'footer_description',
            $request->footer_description,
            WebsiteAppearance::SECTION_FOOTER
        );
        
        // حفظ معلومات الاتصال
        WebsiteAppearance::setSetting(
            'footer_phone',
            $request->footer_phone,
            WebsiteAppearance::SECTION_FOOTER
        );
        
        WebsiteAppearance::setSetting(
            'footer_email',
            $request->footer_email,
            WebsiteAppearance::SECTION_FOOTER
        );
        
        WebsiteAppearance::setSetting(
            'footer_address',
            $request->footer_address,
            WebsiteAppearance::SECTION_FOOTER
        );
        
        // حفظ حقوق النشر
        WebsiteAppearance::setSetting(
            'footer_copyright',
            $request->footer_copyright,
            WebsiteAppearance::SECTION_FOOTER
        );
        
        // معالجة الروابط السريعة
        $quickLinks = [];
        if ($request->has('quick_links') && is_array($request->quick_links)) {
            foreach ($request->quick_links as $link) {
                if (!empty($link['title']) && !empty($link['url'])) {
                    $quickLinks[] = [
                        'title' => $link['title'],
                        'url' => $link['url']
                    ];
                }
            }
        }
        
        WebsiteAppearance::setSetting(
            'footer_links',
            json_encode($quickLinks),
            WebsiteAppearance::SECTION_FOOTER,
            WebsiteAppearance::TYPE_JSON
        );
        
        // معالجة روابط التواصل الاجتماعي
        $socialLinks = [];
        if ($request->has('social_links') && is_array($request->social_links)) {
            foreach ($request->social_links as $link) {
                if (!empty($link['platform']) && !empty($link['url']) && !empty($link['icon'])) {
                    $socialLinks[] = [
                        'platform' => $link['platform'],
                        'url' => $link['url'],
                        'icon' => $link['icon']
                    ];
                }
            }
        }
        
        WebsiteAppearance::setSetting(
            'footer_social_links',
            json_encode($socialLinks),
            WebsiteAppearance::SECTION_FOOTER,
            WebsiteAppearance::TYPE_JSON
        );
        
        // مسح الكاش لعرض التغييرات بشكل فوري
        $this->clearAllCaches();

        return redirect()->route('admin.website-appearance')
            ->with('success', 'تم تحديث إعدادات الفوتر بنجاح.');
    }

    /**
     * Clear all website appearance cache
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearCache()
    {
        try {
            $this->clearAllCaches();
            return redirect()->back()->with('success', 'تم مسح ذاكرة التخزين المؤقت بنجاح. يجب أن تظهر التغييرات الآن على الصفحة الرئيسية.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء مسح ذاكرة التخزين المؤقت: ' . $e->getMessage());
        }
    }
} 