<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // We can't truncate due to foreign key constraints
        
        // Main categories
        $mainCategories = [
            [
                'name' => 'Web Development',
                'description' => 'Learn to build and design websites and web applications',
                'parent_category_id' => null,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Mobile Development',
                'description' => 'Build mobile applications for iOS and Android',
                'parent_category_id' => null,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Data Science',
                'description' => 'Learn data analysis, visualization, and machine learning',
                'parent_category_id' => null,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Business',
                'description' => 'Improve your business skills and knowledge',
                'parent_category_id' => null,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Design',
                'description' => 'Graphic design, UI/UX, and more',
                'parent_category_id' => null,
                'created_at' => Carbon::now(),
            ],
        ];
        
        // Insert main categories
        DB::table('categories')->insert($mainCategories);
        
        // Get the IDs of the inserted categories
        $webDevelopmentId = DB::table('categories')->where('name', 'Web Development')->value('category_id');
        $mobileDevelopmentId = DB::table('categories')->where('name', 'Mobile Development')->value('category_id');
        $dataScienceId = DB::table('categories')->where('name', 'Data Science')->value('category_id');
        
        // Subcategories
        $subCategories = [
            // Web Development subcategories
            [
                'name' => 'JavaScript',
                'description' => 'Learn JavaScript programming language',
                'parent_category_id' => $webDevelopmentId,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'PHP',
                'description' => 'Learn PHP programming language',
                'parent_category_id' => $webDevelopmentId,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'React',
                'description' => 'Learn React JavaScript library',
                'parent_category_id' => $webDevelopmentId,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Laravel',
                'description' => 'Learn Laravel PHP framework',
                'parent_category_id' => $webDevelopmentId,
                'created_at' => Carbon::now(),
            ],
            
            // Mobile Development subcategories
            [
                'name' => 'iOS Development',
                'description' => 'Build apps for iOS devices',
                'parent_category_id' => $mobileDevelopmentId,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Android Development',
                'description' => 'Build apps for Android devices',
                'parent_category_id' => $mobileDevelopmentId,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'React Native',
                'description' => 'Build cross-platform mobile apps with React Native',
                'parent_category_id' => $mobileDevelopmentId,
                'created_at' => Carbon::now(),
            ],
            
            // Data Science subcategories
            [
                'name' => 'Machine Learning',
                'description' => 'Learn machine learning algorithms and applications',
                'parent_category_id' => $dataScienceId,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Python for Data Science',
                'description' => 'Learn Python for data analysis and visualization',
                'parent_category_id' => $dataScienceId,
                'created_at' => Carbon::now(),
            ],
        ];
        
        // Insert subcategories
        DB::table('categories')->insert($subCategories);
    }
}
