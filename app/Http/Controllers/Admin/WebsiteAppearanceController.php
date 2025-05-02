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
        
        return view('admin.website-appearance', compact(
            'heroSettings',
            'featuresSettings',
            'statsSettings',
            'navbarBannerSettings'
        ));
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

        return redirect()->route('admin.website-appearance')
            ->with('success', 'Navbar banner settings updated successfully.');
    }
} 