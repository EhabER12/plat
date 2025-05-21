<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Badge;
use Carbon\Carbon;

class BadgesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => 'المستكشف',
                'description' => 'أكملت أول اختبار',
                'icon' => 'explore',
                'level' => 1,
                'criteria' => json_encode([
                    'type' => 'quiz_attempts',
                    'count' => 1
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'المثابر',
                'description' => 'أكملت 5 اختبارات',
                'icon' => 'persistence',
                'level' => 1,
                'criteria' => json_encode([
                    'type' => 'quiz_attempts',
                    'count' => 5
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'المتسق',
                'description' => 'اجتزت 3 اختبارات متتالية',
                'icon' => 'streak',
                'level' => 1,
                'criteria' => json_encode([
                    'type' => 'quiz_streak',
                    'count' => 3
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'المتفوق',
                'description' => 'حصلت على درجة 90% أو أعلى',
                'icon' => 'star',
                'level' => 1,
                'criteria' => json_encode([
                    'type' => 'quiz_score',
                    'min_score' => 90
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'الكمال',
                'description' => 'حصلت على الدرجة الكاملة في اختبار',
                'icon' => 'perfect',
                'level' => 1,
                'criteria' => json_encode([
                    'type' => 'quiz_score',
                    'min_score' => 100
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'السريع',
                'description' => 'أكملت اختبار في أقل من نصف الوقت المخصص',
                'icon' => 'bolt',
                'level' => 1,
                'criteria' => json_encode([
                    'type' => 'quiz_time',
                    'max_time_percentage' => 50
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'العبقري',
                'description' => 'حصلت على الدرجة الكاملة في 3 اختبارات متتالية',
                'icon' => 'brain',
                'level' => 2,
                'criteria' => json_encode([
                    'type' => 'quiz_perfect_streak',
                    'count' => 3
                ]),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}
