<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\User;
use App\Models\Category;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التأكد من وجود مدرسين وتصنيفات
        $instructorIds = User::whereHas('roles', function($query) {
            $query->where('role', 'instructor');
        })->pluck('user_id')->toArray();
        
        // إذا لم يوجد مدرسين، قم بإنشاء مدرس جديد
        if (empty($instructorIds)) {
            $instructorId = DB::table('users')->insertGetId([
                'name' => 'أحمد المدرس',
                'email' => 'instructor@example.com',
                'password' => bcrypt('password'),
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
        
        $categoryIds = Category::pluck('category_id')->toArray();
        
        // إذا لم يوجد تصنيفات، قم بإنشاء تصنيفات جديدة
        if (empty($categoryIds)) {
            $categoryNames = [
                'البرمجة والتطوير',
                'تطوير المواقع',
                'تطوير التطبيقات',
                'الذكاء الاصطناعي',
                'قواعد البيانات',
                'تطوير الألعاب',
                'التسويق الرقمي',
                'التصميم الجرافيكي',
                'ريادة الأعمال',
                'اللغات البرمجية'
            ];
            
            foreach ($categoryNames as $name) {
                $categoryIds[] = DB::table('categories')->insertGetId([
                    'name' => $name,
                    'description' => 'وصف خاص بتصنيف ' . $name,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        // قائمة الدورات التعليمية
        $courses = [
            [
                'title' => 'أساسيات البرمجة بلغة PHP',
                'description' => 'تعلم أساسيات لغة PHP وكيفية استخدامها في تطوير تطبيقات الويب. تغطي هذه الدورة المفاهيم الأساسية من المتغيرات والمصفوفات إلى التعامل مع قواعد البيانات وإنشاء واجهات برمجة التطبيقات.',
                'price' => 299.99,
                'duration' => 24,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
            ],
            [
                'title' => 'تطوير تطبيقات الويب باستخدام Laravel',
                'description' => 'دورة شاملة في تطوير تطبيقات الويب باستخدام إطار العمل Laravel. ستتعلم كيفية إنشاء وتطوير وتحسين تطبيقات ويب متكاملة وفعالة باستخدام أفضل الممارسات.',
                'price' => 499.99,
                'duration' => 36,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
            ],
            [
                'title' => 'تطوير واجهات المستخدم باستخدام React',
                'description' => 'تعلم كيفية تطوير واجهات مستخدم تفاعلية وديناميكية باستخدام مكتبة React.js. ستتمكن من بناء تطبيقات الصفحة الواحدة وتطوير مكونات قابلة لإعادة الاستخدام.',
                'price' => 399.99,
                'duration' => 30,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
            ],
            [
                'title' => 'تطوير تطبيقات الهاتف باستخدام Flutter',
                'description' => 'تعلم كيفية تطوير تطبيقات الهاتف المحمول لنظامي Android و iOS باستخدام إطار العمل Flutter. ستتمكن من إنشاء تطبيقات جميلة وسريعة بكود واحد لجميع المنصات.',
                'price' => 449.99,
                'duration' => 32,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => false,
                'approval_status' => 'approved',
            ],
            [
                'title' => 'مقدمة في علم البيانات والذكاء الاصطناعي',
                'description' => 'استكشف عالم علم البيانات والذكاء الاصطناعي وتعلم المفاهيم الأساسية والتقنيات المستخدمة في هذا المجال المتنامي. من تحليل البيانات إلى بناء نماذج التعلم الآلي البسيطة.',
                'price' => 599.99,
                'duration' => 40,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'approved',
            ],
            [
                'title' => 'أساسيات التصميم الجرافيكي',
                'description' => 'تعلم أساسيات التصميم الجرافيكي واستخدام برامج مثل Photoshop و Illustrator. ستتمكن من إنشاء تصاميم احترافية للطباعة والويب ووسائل التواصل الاجتماعي.',
                'price' => 299.99,
                'duration' => 25,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => false,
                'approval_status' => 'approved',
            ],
            [
                'title' => 'تطوير الألعاب باستخدام Unity',
                'description' => 'ادخل عالم تطوير الألعاب مع Unity. ستتعلم كيفية إنشاء ألعاب ثنائية وثلاثية الأبعاد وتطبيقات الواقع الافتراضي من الصفر.',
                'price' => 549.99,
                'duration' => 45,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'pending',
            ],
            [
                'title' => 'أساسيات التسويق الرقمي',
                'description' => 'تعلم استراتيجيات وتقنيات التسويق الرقمي الفعالة. من تحسين محركات البحث إلى إدارة حملات وسائل التواصل الاجتماعي وتحليل البيانات لتحسين نتائج التسويق.',
                'price' => 349.99,
                'duration' => 20,
                'level' => 'beginner',
                'language' => 'العربية',
                'featured' => false,
                'approval_status' => 'pending',
            ],
            [
                'title' => 'أمن المعلومات والاختراق الأخلاقي',
                'description' => 'تعرف على أساسيات أمن المعلومات وتقنيات الاختراق الأخلاقي. ستتعلم كيفية حماية الأنظمة والشبكات من التهديدات السيبرانية واكتشاف الثغرات الأمنية.',
                'price' => 649.99,
                'duration' => 35,
                'level' => 'advanced',
                'language' => 'العربية',
                'featured' => true,
                'approval_status' => 'pending',
            ],
            [
                'title' => 'إدارة المشاريع الاحترافية',
                'description' => 'تعلم مهارات إدارة المشاريع الاحترافية ومنهجيات مثل Agile و Scrum. ستكتسب المعرفة والمهارات اللازمة لقيادة المشاريع بنجاح من البداية إلى النهاية.',
                'price' => 399.99,
                'duration' => 22,
                'level' => 'intermediate',
                'language' => 'العربية',
                'featured' => false,
                'approval_status' => 'rejected',
            ],
        ];
        
        // إضافة الدورات إلى قاعدة البيانات
        foreach ($courses as $course) {
            DB::table('courses')->insert([
                'title' => $course['title'],
                'description' => $course['description'],
                'instructor_id' => $instructorIds[array_rand($instructorIds)],
                'category_id' => $categoryIds[array_rand($categoryIds)],
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
        
        $this->command->info('تم إضافة 10 دورات تعليمية بنجاح!');
    }
}
