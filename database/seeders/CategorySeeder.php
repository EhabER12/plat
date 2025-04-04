<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'تطوير الويب',
                'description' => 'دورات متعلقة بتقنيات وأطر عمل تطوير الويب',
            ],
            [
                'name' => 'تطوير تطبيقات الجوال',
                'description' => 'دورات في تطوير تطبيقات الجوال لمختلف المنصات',
            ],
            [
                'name' => 'التصميم الجرافيكي',
                'description' => 'دورات في التصميم الجرافيكي وواجهات المستخدم',
            ],
            [
                'name' => 'الذكاء الاصطناعي',
                'description' => 'دورات في الذكاء الاصطناعي وتعلم الآلة',
            ],
            [
                'name' => 'قواعد البيانات',
                'description' => 'دورات في إدارة وتطوير قواعد البيانات',
            ],
            [
                'name' => 'الأمن السيبراني',
                'description' => 'دورات في الأمن السيبراني وحماية المعلومات',
            ],
            [
                'name' => 'التسويق الرقمي',
                'description' => 'دورات في التسويق الرقمي ووسائل التواصل الاجتماعي',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'description' => $category['description'],
                'category_id' => DB::table('categories')->max('category_id') + 1 ?? 1,
            ]);
        }

        // إضافة فئات فرعية
        $subcategories = [
            [
                'parent_name' => 'تطوير الويب',
                'name' => 'HTML & CSS',
                'description' => 'دورات متعلقة بتعلم HTML و CSS',
            ],
            [
                'parent_name' => 'تطوير الويب',
                'name' => 'JavaScript',
                'description' => 'دورات متعلقة بتعلم لغة JavaScript',
            ],
            [
                'parent_name' => 'تطوير الويب',
                'name' => 'PHP',
                'description' => 'دورات متعلقة بتعلم لغة PHP',
            ],
            [
                'parent_name' => 'تطوير تطبيقات الجوال',
                'name' => 'Android',
                'description' => 'دورات تطوير تطبيقات Android',
            ],
            [
                'parent_name' => 'تطوير تطبيقات الجوال',
                'name' => 'iOS',
                'description' => 'دورات تطوير تطبيقات iOS',
            ],
            [
                'parent_name' => 'التصميم الجرافيكي',
                'name' => 'Photoshop',
                'description' => 'دورات في استخدام برنامج Photoshop',
            ],
            [
                'parent_name' => 'التصميم الجرافيكي',
                'name' => 'Illustrator',
                'description' => 'دورات في استخدام برنامج Illustrator',
            ],
        ];

        foreach ($subcategories as $subcategory) {
            $parentId = DB::table('categories')
                ->where('name', $subcategory['parent_name'])
                ->value('category_id');

            if ($parentId) {
                DB::table('categories')->insert([
                    'name' => $subcategory['name'],
                    'description' => $subcategory['description'],
                    'parent_id' => $parentId,
                    'category_id' => DB::table('categories')->max('category_id') + 1 ?? 1,
                ]);
            }
        }
    }
}
