<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\InstructorProfileController;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\WebsiteAppearance;

class PageController extends Controller
{
    public function home()
    {
        try {
            // تخزين نتائج الصفحة الرئيسية في الكاش لمدة 60 دقيقة
            $cacheKey = 'home_page_data';
            
            // استخدام الكاش إذا كان موجودًا وفي بيئة غير التطوير
            // الغاء تنشيط الكاش مؤقتًا لحل مشكلة عدم تحديث صور واعدادات الموقع
            if (false && \Illuminate\Support\Facades\Cache::has($cacheKey) && !config('app.debug')) {
                $data = \Illuminate\Support\Facades\Cache::get($cacheKey);
                return view('pages.home', $data);
            }
            
            // الحصول على الكورسات مع البيانات الضرورية فقط
            $courses = Course::select(['course_id', 'title', 'description', 'thumbnail', 'price', 'instructor_id', 'category_id', 'created_at'])
                ->with(['instructor:user_id,name', 'category:category_id,name'])
                ->where('approval_status', 'approved')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();
            
            // الحصول على الفئات مع عدد الكورسات
            $categories = \Illuminate\Support\Facades\Cache::remember('categories_with_courses', 15*60, function () {
                return Category::withCount('courses')->take(8)->get();
            });
            
            // الحصول على إعدادات الموقع - تقليل مدة التخزين المؤقت الى 5 دقائق فقط
            // يتم استخدام fresh lookup بدلاً من cache:remember لضمان تحديث البيانات
            $settings = [
                    'heroSettings' => WebsiteAppearance::getSection(WebsiteAppearance::SECTION_HERO),
                    'featuresSettings' => WebsiteAppearance::getSection(WebsiteAppearance::SECTION_FEATURES),
                    'statsSettings' => WebsiteAppearance::getSection('stats'),
                    'aboutSettings' => WebsiteAppearance::getSection('about'),
                    'videoSettings' => WebsiteAppearance::getSection('video'),
                    'navbarBannerSettings' => WebsiteAppearance::getSection(WebsiteAppearance::SECTION_NAVBAR_BANNER),
                    'partnersSettings' => WebsiteAppearance::getSection(WebsiteAppearance::SECTION_PARTNERS)
                ];
            
            $data = [
                'courses' => $courses,
                'categories' => $categories,
                'heroSettings' => $settings['heroSettings'],
                'featuresSettings' => $settings['featuresSettings'],
                'statsSettings' => $settings['statsSettings'],
                'aboutSettings' => $settings['aboutSettings'],
                'videoSettings' => $settings['videoSettings'],
                'navbarBannerSettings' => $settings['navbarBannerSettings'],
                'partnersSettings' => $settings['partnersSettings']
            ];
            
            // تخزين النتائج في ذاكرة التخزين المؤقت - تقليل المدة الى 15 دقيقة
            \Illuminate\Support\Facades\Cache::put($cacheKey, $data, 15*60);
            
            return view('pages.home', $data);
        } catch (\Exception $e) {
            Log::error('Error loading home page: ' . $e->getMessage());

            return view('pages.home', [
                'courses' => collect([]),
                'categories' => collect([]),
                'error' => 'Unable to load courses: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the courses page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function courses(Request $request)
    {
        try {
            // تخزين نتائج الاستعلام في الكاش لمدة 30 دقيقة
            $cacheKey = 'courses_' . md5(json_encode($request->all()));

            // استخدام الكاش إذا كان موجوداً
            if (\Illuminate\Support\Facades\Cache::has($cacheKey) && !config('app.debug')) {
                $data = \Illuminate\Support\Facades\Cache::get($cacheKey);
                return view('pages.courses', $data);
            }

            // الاستعلام مع استخدام eager loading وإضافة where فقط عند الحاجة
            $query = Course::select(['course_id', 'title', 'description', 'price', 'thumbnail', 'instructor_id', 'category_id', 'created_at', 'approval_status'])
                ->with(['instructor:user_id,name', 'category:category_id,name'])
                ->where('approval_status', 'approved');

            // تطبيق الفلترة
            if ($request->has('category') && $request->input('category') != 'all') {
                $query->where('category_id', $request->input('category'));
            }

            if ($request->has('search') && !empty($request->input('search'))) {
                $searchTerm = '%' . $request->input('search') . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm);
                });
            }

            // تطبيق الترتيب
            $sort = $request->input('sort', 'newest');
            switch ($sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            // تقسيم الصفحات
            $courses = $query->paginate(9)->withQueryString();

            // حفظ الفئات في ذاكرة التخزين المؤقت لمدة 60 دقيقة
            $categories = \Illuminate\Support\Facades\Cache::remember('all_categories', 60*60, function () {
                return Category::select(['category_id', 'name'])->get();
            });
            
            $data = [
                'courses' => $courses,
                'categories' => $categories,
                'currentCategory' => $request->input('category'),
                'currentSearch' => $request->input('search'),
                'currentSort' => $request->input('sort', 'newest')
            ];
            
            // تخزين النتائج في ذاكرة التخزين المؤقت
            \Illuminate\Support\Facades\Cache::put($cacheKey, $data, 30*60);

            return view('pages.courses', $data);
        } catch (\Exception $e) {
            Log::error('Error in courses page: ' . $e->getMessage());

            return view('pages.courses', [
                'courses' => collect([]),
                'categories' => collect([]),
                'error' => 'Unable to load courses: ' . $e->getMessage()
            ]);
        }
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request)
    {
        // Validate the form data
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|max:255',
            'message' => 'required',
        ]);

        // Here you would typically save to database or send an email
        // This is just a demo so we'll just redirect with a success message

        return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }

    /**
     * Display the course detail page.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function courseDetail($courseId)
    {
        try {
            // تسجيلات تصحيح مفصلة
            Log::info('Course detail request for ID: ' . $courseId);
            
            // التحقق من وجود الكورس مباشرة (بدون العلاقات) للتأكد مما إذا كان موجودًا على الإطلاق
            $courseExists = Course::where('course_id', $courseId)->exists();
            
            if (!$courseExists) {
                Log::error('Course ID ' . $courseId . ' does not exist in database');
                return redirect('/courses')->with('error', 'Course not found or unavailable.');
            }
            
            Log::info('Course ID ' . $courseId . ' exists in database, continuing...');
            
            // تحقق من وجود الجداول المطلوبة
            $courseReviewsTableExists = Schema::hasTable('course_reviews');
            $ratingsTableExists = Schema::hasTable('ratings');
            
            Log::info('Tables check: course_reviews=' . ($courseReviewsTableExists ? 'yes' : 'no') . ', ratings=' . ($ratingsTableExists ? 'yes' : 'no'));
            
            // تخزين نتائج صفحة تفاصيل الكورس في الكاش
            $cacheKey = 'course_detail_' . $courseId;
            
            // مسح التخزين المؤقت للكورس الحالي (لتجنب مشاكل الكاش)
            if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                \Illuminate\Support\Facades\Cache::forget($cacheKey);
                Log::info('Cleared cache for course ID: ' . $courseId);
            }
            
            Log::info('Fetching course data for ID: ' . $courseId);
            
            // الحصول على الكورس مع العلاقات اللازمة
            $course = Course::where('course_id', $courseId)
                ->with([
                    'instructor:user_id,name,bio,profile_image',
                    'category:category_id,name',
                    'videos',
                    'materials'
                ])
                ->first();
            
            if (!$course) {
                Log::error('Course not found in database with relations: ' . $courseId);
                throw new \Exception('Course not found with relations: ' . $courseId);
            }
            
            Log::info('Course found: ' . $course->title . ', approval status: ' . $course->approval_status);
                
            // الكورسات ذات الصلة (في نفس الفئة)
            $relatedCourses = Course::where('category_id', $course->category_id)
                ->where('course_id', '!=', $course->course_id)
                ->with('instructor:user_id,name')
                ->select(['course_id', 'title', 'thumbnail', 'price', 'instructor_id'])
                ->take(4)
                ->get();
                
            // تحسين الحصول على التقييمات
            $reviews = collect([]);
            $averageRating = 0;
            $totalRatings = 0;
            $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
            
            // جلب المراجعات فقط إذا كان جدول course_reviews موجودًا
            if ($courseReviewsTableExists) {
                try {
                $reviews = \App\Models\CourseReview::where('course_id', $courseId)
                    ->where('is_approved', true)
                        ->with('user:user_id,name,profile_image')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

            // حساب متوسط التقييم
                $averageRating = \App\Models\CourseReview::where('course_id', $courseId)
                    ->where('is_approved', true)
                    ->avg('rating') ?? 0;

            // حساب عدد التقييمات
                $totalRatings = \App\Models\CourseReview::where('course_id', $courseId)
                    ->where('is_approved', true)
                    ->count();

            // حساب توزيع التقييمات
                $ratingDistribution = \App\Models\CourseReview::where('course_id', $courseId)
                    ->where('is_approved', true)
                    ->select('rating', DB::raw('count(*) as count'))
                    ->groupBy('rating')
                    ->get()
                    ->pluck('count', 'rating')
                    ->toArray();

            // ملء مصفوفة توزيع التقييمات
                foreach ($ratingDistribution as $rating => $count) {
                    if (isset($ratingCounts[$rating])) {
                        $ratingCounts[$rating] = $count;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Error fetching course reviews: ' . $e->getMessage());
                }
            } 
            // فحص جدول ratings إذا لم يكن جدول course_reviews موجودًا
            elseif ($ratingsTableExists) {
                try {
                    $reviews = \App\Models\Rating::where('course_id', $courseId)
                        ->where('is_approved', true)
                        ->with('user:user_id,name,profile_image')
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
    
                    // حساب متوسط التقييم
                    $averageRating = \App\Models\Rating::where('course_id', $courseId)
                        ->where('is_approved', true)
                        ->avg('rating') ?? 0;
    
                    // حساب عدد التقييمات
                    $totalRatings = \App\Models\Rating::where('course_id', $courseId)
                        ->where('is_approved', true)
                        ->count();
    
                    // حساب توزيع التقييمات
                    $ratingDistribution = \App\Models\Rating::where('course_id', $courseId)
                        ->where('is_approved', true)
                        ->select('rating', DB::raw('count(*) as count'))
                        ->groupBy('rating')
                        ->get()
                        ->pluck('count', 'rating')
                        ->toArray();
    
                    // ملء مصفوفة توزيع التقييمات
                    foreach ($ratingDistribution as $rating => $count) {
                        if (isset($ratingCounts[$rating])) {
                            $ratingCounts[$rating] = $count;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Error fetching ratings: ' . $e->getMessage());
                    }
                }
                
            // تنسيق متوسط التقييم
            $averageRating = number_format($averageRating, 1);

            $data = [
                'course' => $course,
                'relatedCourses' => $relatedCourses,
                'averageRating' => $averageRating,
                'totalRatings' => $totalRatings,
                'ratingCounts' => $ratingCounts,
                'reviews' => $reviews,
                'courseReviewsTableExists' => $courseReviewsTableExists,
                'ratingsTableExists' => $ratingsTableExists
            ];
            
            Log::info('Completed gathering course data for ID: ' . $courseId);
            
            // نحن لا نريد تخزين النتائج في ذاكرة التخزين المؤقت في الوقت الحالي أثناء التصحيح
            // \Illuminate\Support\Facades\Cache::put($cacheKey, $data, 60*60);
            
            return view('pages.course-detail', $data);
        } catch (\Exception $e) {
            // تسجيل الخطأ
            Log::error('Error in course detail page: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // إعادة التوجيه إلى صفحة الكورسات في حالة حدوث خطأ
            return redirect('/courses')->with('error', 'Course not found or unavailable: ' . $e->getMessage());
        }
    }
}
