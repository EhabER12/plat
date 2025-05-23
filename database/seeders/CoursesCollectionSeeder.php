<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;

class CoursesCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get instructor IDs
        $instructorIds = User::whereHas('roles', function($query) {
            $query->where('role', 'instructor');
        })->pluck('id')->toArray();
        
        // Create a default instructor if none exists
        if (empty($instructorIds)) {
            $instructorId = DB::table('users')->insertGetId([
                'name' => 'مدرس نموذجي',
                'email' => 'default_instructor@example.com',
                'password' => bcrypt('password123'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            DB::table('user_roles')->insert([
                'user_id' => $instructorId,
                'role' => 'instructor',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $instructorIds = [$instructorId];
        }
        
        // Get category IDs
        $categoryIds = Category::pluck('category_id', 'name')->toArray();
        
        // Create default categories if none exist
        if (empty($categoryIds)) {
            $categories = [
                'البرمجة' => 'دورات متخصصة في مختلف لغات البرمجة وتقنياتها',
                'تطوير الويب' => 'دورات في تطوير المواقع الإلكترونية وتطبيقات الويب',
                'علوم البيانات' => 'دورات في تحليل البيانات والذكاء الاصطناعي وعلم البيانات',
                'التسويق الرقمي' => 'دورات في التسويق عبر الإنترنت ووسائل التواصل الاجتماعي',
                'اللغات' => 'دورات لتعلم اللغات المختلفة',
                'التصميم' => 'دورات في التصميم الجرافيكي وتصميم المواقع',
                'الأعمال والإدارة' => 'دورات في إدارة الأعمال والقيادة',
                'الصحة واللياقة' => 'دورات في الصحة العامة واللياقة البدنية',
                'المهارات الشخصية' => 'دورات لتطوير المهارات الشخصية والحياتية',
                'التعليم والتربية' => 'دورات في مجال التعليم وطرق التدريس'
            ];
            
            $categoryIds = [];
            
            foreach ($categories as $name => $description) {
                $categoryId = DB::table('categories')->insertGetId([
                    'name' => $name,
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $categoryIds[$name] = $categoryId;
            }
        }
        
        // Courses collection
        $courses = [
            // دورات البرمجة
            [
                'title' => 'البرمجة بلغة بايثون للمبتدئين',
                'description' => 'دورة شاملة لتعلم أساسيات البرمجة بلغة بايثون من الصفر. تتضمن تطبيقات عملية ومشاريع واقعية.',
                'price' => 249.99,
                'duration' => 30,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'البرمجة',
            ],
            [
                'title' => 'جافا سكريبت المتقدمة وإطار العمل Vue.js',
                'description' => 'تعلم جافا سكريبت المتقدمة وكيفية بناء تطبيقات تفاعلية باستخدام إطار العمل Vue.js.',
                'price' => 349.99,
                'duration' => 35,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'البرمجة',
            ],
            
            // دورات تطوير الويب
            [
                'title' => 'تطوير تطبيقات الويب الكاملة باستخدام MERN Stack',
                'description' => 'تعلم كيفية تطوير تطبيقات ويب كاملة باستخدام MongoDB و Express.js و React و Node.js.',
                'price' => 499.99,
                'duration' => 45,
                'level' => 'advanced',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'تطوير الويب',
            ],
            [
                'title' => 'تصميم واجهات المستخدم التفاعلية',
                'description' => 'تعلم أساسيات وأفضل ممارسات تصميم واجهات المستخدم لتطبيقات الويب والهاتف المحمول.',
                'price' => 299.99,
                'duration' => 25,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => false,
                'approval_status' => 'approved',
                'category' => 'تطوير الويب',
            ],
            
            // دورات علوم البيانات
            [
                'title' => 'مقدمة في علم البيانات والتحليل الإحصائي',
                'description' => 'تعلم المفاهيم الأساسية في علم البيانات وكيفية استخدام الأدوات الإحصائية لتحليل البيانات.',
                'price' => 399.99,
                'duration' => 40,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'علوم البيانات',
            ],
            [
                'title' => 'تعلم الآلة والذكاء الاصطناعي',
                'description' => 'استكشف مجال تعلم الآلة والذكاء الاصطناعي وتعلم كيفية بناء نماذج تنبؤية فعالة.',
                'price' => 599.99,
                'duration' => 50,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'علوم البيانات',
            ],
            
            // دورات التسويق الرقمي
            [
                'title' => 'استراتيجيات السوشيال ميديا للأعمال',
                'description' => 'تعلم كيفية استخدام منصات التواصل الاجتماعي لتعزيز نمو عملك وزيادة المبيعات.',
                'price' => 299.99,
                'duration' => 20,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => false,
                'approval_status' => 'approved',
                'category' => 'التسويق الرقمي',
            ],
            [
                'title' => 'تحسين محركات البحث (SEO) الاحترافي',
                'description' => 'تعلم تقنيات وأدوات تحسين محركات البحث لزيادة ظهور موقعك في نتائج البحث.',
                'price' => 349.99,
                'duration' => 25,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'التسويق الرقمي',
            ],
            
            // دورات اللغات
            [
                'title' => 'اللغة الإنجليزية للمبتدئين',
                'description' => 'دورة شاملة لتعلم أساسيات اللغة الإنجليزية للمبتدئين تماماً.',
                'price' => 199.99,
                'duration' => 60,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'اللغات',
            ],
            [
                'title' => 'اللغة الصينية للمسافرين',
                'description' => 'تعلم العبارات والمفردات الأساسية في اللغة الصينية للسفر والتواصل البسيط.',
                'price' => 149.99,
                'duration' => 15,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => false,
                'approval_status' => 'approved',
                'category' => 'اللغات',
            ],
            
            // دورات التصميم
            [
                'title' => 'أساسيات التصميم الجرافيكي',
                'description' => 'تعلم المبادئ الأساسية للتصميم الجرافيكي واستخدام برامج مثل Photoshop و Illustrator.',
                'price' => 249.99,
                'duration' => 30,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'التصميم',
            ],
            [
                'title' => 'تصميم المواقع الإلكترونية احترافي',
                'description' => 'تعلم كيفية تصميم مواقع إلكترونية جذابة وسهلة الاستخدام باستخدام أحدث التقنيات.',
                'price' => 399.99,
                'duration' => 35,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'التصميم',
            ],
            
            // دورات الأعمال والإدارة
            [
                'title' => 'ريادة الأعمال: من الفكرة إلى المشروع',
                'description' => 'تعلم كيفية تحويل أفكارك إلى مشاريع ناجحة من خلال استراتيجيات ريادة الأعمال الفعالة.',
                'price' => 349.99,
                'duration' => 25,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'الأعمال والإدارة',
            ],
            [
                'title' => 'إدارة الفرق عن بعد',
                'description' => 'تعلم كيفية إدارة الفرق عن بعد بفعالية وتحقيق أقصى قدر من الإنتاجية والتعاون.',
                'price' => 299.99,
                'duration' => 20,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => false,
                'approval_status' => 'approved',
                'category' => 'الأعمال والإدارة',
            ],
            
            // دورات الصحة واللياقة
            [
                'title' => 'اللياقة البدنية المنزلية',
                'description' => 'تمارين لياقة بدنية يمكن ممارستها في المنزل دون الحاجة لمعدات خاصة.',
                'price' => 99.99,
                'duration' => 12,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => false,
                'approval_status' => 'approved',
                'category' => 'الصحة واللياقة',
            ],
            [
                'title' => 'التغذية السليمة ونمط الحياة الصحي',
                'description' => 'تعلم أساسيات التغذية السليمة وكيفية اتباع نمط حياة صحي ومتوازن.',
                'price' => 149.99,
                'duration' => 15,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'الصحة واللياقة',
            ],
            
            // دورات المهارات الشخصية
            [
                'title' => 'إدارة الوقت وزيادة الإنتاجية',
                'description' => 'تعلم تقنيات وأدوات إدارة الوقت لزيادة الإنتاجية وتحقيق التوازن بين العمل والحياة الشخصية.',
                'price' => 129.99,
                'duration' => 10,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'المهارات الشخصية',
            ],
            [
                'title' => 'مهارات التواصل الفعال',
                'description' => 'تطوير مهارات التواصل الفعال للنجاح في الحياة الشخصية والمهنية.',
                'price' => 149.99,
                'duration' => 12,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => false,
                'approval_status' => 'approved',
                'category' => 'المهارات الشخصية',
            ],
            
            // دورات التعليم والتربية
            [
                'title' => 'أساليب التدريس الحديثة',
                'description' => 'تعلم أحدث أساليب وتقنيات التدريس لتقديم تعليم فعال وجذاب للطلاب.',
                'price' => 249.99,
                'duration' => 20,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
                'category' => 'التعليم والتربية',
            ],
            [
                'title' => 'التعلم الإلكتروني: التصميم والتطبيق',
                'description' => 'تعلم كيفية تصميم وتنفيذ برامج التعلم الإلكتروني الفعالة والتفاعلية.',
                'price' => 299.99,
                'duration' => 25,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => false,
                'approval_status' => 'approved',
                'category' => 'التعليم والتربية',
            ],
        ];
        
        // Add courses to database
        foreach ($courses as $course) {
            DB::table('courses')->insert([
                'title' => $course['title'],
                'description' => $course['description'],
                'instructor_id' => $instructorIds[array_rand($instructorIds)],
                'category_id' => $categoryIds[$course['category']] ?? $categoryIds[array_rand($categoryIds)],
                'price' => $course['price'],
                'duration' => $course['duration'],
                'level' => $course['level'],
                'language' => $course['language'],
                'featured' => $course['featured'],
                'approval_status' => $course['approval_status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info('تم إضافة ' . count($courses) . ' دورة تعليمية بنجاح!');
    }
} 