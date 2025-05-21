<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Achievement;
use Carbon\Carbon;

class AchievementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'عشرة اختبارات',
                'description' => 'أكملت 10 اختبارات',
                'icon' => 'medal',
                'points' => 100,
                'criteria' => json_encode([
                    'type' => 'quiz_attempts',
                    'count' => 10
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'معدل نجاح عالي',
                'description' => 'حققت معدل نجاح 80% أو أعلى في 5 اختبارات على الأقل',
                'icon' => 'trophy',
                'points' => 150,
                'criteria' => json_encode([
                    'type' => 'quiz_pass_rate',
                    'min_rate' => 80,
                    'min_attempts' => 5
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'سلسلة ذهبية',
                'description' => 'اجتزت 5 اختبارات متتالية',
                'icon' => 'fire',
                'points' => 200,
                'criteria' => json_encode([
                    'type' => 'quiz_streak',
                    'count' => 5
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'المتعلم المثالي',
                'description' => 'أكملت 20 اختبارًا بنجاح',
                'icon' => 'graduation-cap',
                'points' => 300,
                'criteria' => json_encode([
                    'type' => 'quiz_attempts',
                    'count' => 20,
                    'passed' => true
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'المتفوق الذهبي',
                'description' => 'حافظت على متوسط درجات 90% أو أعلى في 10 اختبارات متتالية',
                'icon' => 'crown',
                'points' => 500,
                'criteria' => json_encode([
                    'type' => 'quiz_score_streak',
                    'min_score' => 90,
                    'count' => 10
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'المستكشف الشامل',
                'description' => 'أكملت اختبارًا واحدًا على الأقل في كل فئة من فئات الدورات',
                'icon' => 'globe',
                'points' => 250,
                'criteria' => json_encode([
                    'type' => 'quiz_categories',
                    'all_categories' => true
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}
