<?php

namespace App\Services;

use App\Models\User;
use App\Models\QuizAttempt;
use App\Models\Badge;
use App\Models\StudentAchievement;
use Illuminate\Support\Facades\DB;

class StudentMotivationService
{
    /**
     * Get motivational message based on student's performance
     *
     * @param User $student
     * @return array
     */
    public function getMotivationalContent(User $student)
    {
        $stats = $this->getStudentStats($student);
        
        return [
            'message' => $this->getMotivationalMessage($stats),
            'stats' => $stats,
            'badges' => $this->getStudentBadges($student),
            'achievements' => $this->getStudentAchievements($student),
            'progress' => $this->calculateProgress($stats),
            'suggestion' => $this->getSuggestion($stats),
        ];
    }
    
    /**
     * Get student's quiz statistics
     *
     * @param User $student
     * @return array
     */
    private function getStudentStats(User $student)
    {
        $attempts = QuizAttempt::where('user_id', $student->user_id)
            ->where('status', 'completed')
            ->get();
            
        $totalAttempts = $attempts->count();
        $passedAttempts = $attempts->where('is_passed', true)->count();
        $averageScore = $totalAttempts > 0 ? $attempts->avg('score_percentage') : 0;
        $highestScore = $totalAttempts > 0 ? $attempts->max('score_percentage') : 0;
        $recentScores = QuizAttempt::where('user_id', $student->user_id)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->pluck('score_percentage')
            ->toArray();
            
        // Calculate improvement trend
        $improvementTrend = 0;
        if (count($recentScores) >= 2) {
            $improvementTrend = $recentScores[0] - $recentScores[count($recentScores) - 1];
        }
        
        // Calculate streak (consecutive passed quizzes)
        $streak = 0;
        foreach ($attempts->sortByDesc('created_at') as $attempt) {
            if ($attempt->is_passed) {
                $streak++;
            } else {
                break;
            }
        }
        
        return [
            'total_attempts' => $totalAttempts,
            'passed_attempts' => $passedAttempts,
            'average_score' => round($averageScore, 1),
            'highest_score' => round($highestScore, 1),
            'recent_scores' => $recentScores,
            'improvement_trend' => round($improvementTrend, 1),
            'streak' => $streak,
            'pass_rate' => $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 1) : 0,
        ];
    }
    
    /**
     * Get motivational message based on student's statistics
     *
     * @param array $stats
     * @return string
     */
    private function getMotivationalMessage(array $stats)
    {
        // No attempts yet
        if ($stats['total_attempts'] === 0) {
            return 'مرحباً بك في رحلة التعلم! ابدأ بخوض أول اختبار واكتشف قدراتك.';
        }
        
        // Perfect scores
        if ($stats['average_score'] >= 95) {
            return 'أداء استثنائي! أنت من الطلاب المتميزين. استمر في هذا المستوى الرائع!';
        }
        
        // High scores
        if ($stats['average_score'] >= 85) {
            return 'أداء ممتاز! أنت على الطريق الصحيح نحو التفوق والتميز.';
        }
        
        // Good scores with improvement
        if ($stats['average_score'] >= 75 && $stats['improvement_trend'] > 0) {
            return 'أداء جيد جداً مع تحسن ملحوظ! استمر في التقدم وستصل إلى أهدافك.';
        }
        
        // Good scores
        if ($stats['average_score'] >= 75) {
            return 'أداء جيد! لديك فهم قوي للمواد. واصل الاجتهاد!';
        }
        
        // Average scores with improvement
        if ($stats['average_score'] >= 60 && $stats['improvement_trend'] > 0) {
            return 'أنت تتحسن بشكل ملحوظ! استمر في العمل الجاد وستحقق نتائج أفضل.';
        }
        
        // Average scores
        if ($stats['average_score'] >= 60) {
            return 'أداء جيد، لكن هناك مجال للتحسين. ركز على المراجعة المنتظمة!';
        }
        
        // Below average with improvement
        if ($stats['improvement_trend'] > 0) {
            return 'تحسن ملحوظ في أدائك! استمر في العمل الجاد وستصل إلى مستويات أعلى.';
        }
        
        // Below average
        return 'لا تستسلم! كل محاولة هي فرصة للتعلم والتحسن. حاول مراجعة المواد بشكل أكثر تركيزاً.';
    }
    
    /**
     * Get suggestion based on student's statistics
     *
     * @param array $stats
     * @return string
     */
    private function getSuggestion(array $stats)
    {
        // No attempts
        if ($stats['total_attempts'] === 0) {
            return 'ابدأ بالاختبارات الأساسية لتقييم مستواك الحالي.';
        }
        
        // Perfect student
        if ($stats['average_score'] >= 95) {
            return 'يمكنك مساعدة زملائك أو استكشاف مواد متقدمة لتطوير مهاراتك أكثر.';
        }
        
        // High performer
        if ($stats['average_score'] >= 85) {
            return 'ركز على النقاط الصغيرة التي تفوتك للوصول إلى الكمال.';
        }
        
        // Good with inconsistency
        if ($stats['average_score'] >= 75 && $stats['pass_rate'] < 90) {
            return 'أنت تحقق درجات جيدة، لكن حاول أن تكون أكثر اتساقاً في أدائك.';
        }
        
        // Average performer
        if ($stats['average_score'] >= 60) {
            return 'حاول تخصيص وقت أطول للمراجعة وحل المزيد من التمارين التطبيقية.';
        }
        
        // Struggling
        return 'قد تحتاج إلى مراجعة المفاهيم الأساسية. حاول طلب المساعدة من المعلمين أو مشاهدة فيديوهات تعليمية إضافية.';
    }
    
    /**
     * Calculate overall progress percentage
     *
     * @param array $stats
     * @return int
     */
    private function calculateProgress(array $stats)
    {
        if ($stats['total_attempts'] === 0) {
            return 0;
        }
        
        // Calculate progress based on multiple factors
        $scoreWeight = 0.5; // 50% weight for average score
        $passRateWeight = 0.3; // 30% weight for pass rate
        $streakWeight = 0.2; // 20% weight for current streak
        
        $scoreProgress = min(100, $stats['average_score']);
        $passRateProgress = $stats['pass_rate'];
        $streakProgress = min(100, $stats['streak'] * 20); // 5 in a row = 100%
        
        $totalProgress = ($scoreProgress * $scoreWeight) + 
                         ($passRateProgress * $passRateWeight) + 
                         ($streakProgress * $streakWeight);
                         
        return round($totalProgress);
    }
    
    /**
     * Get student's badges
     *
     * @param User $student
     * @return array
     */
    private function getStudentBadges(User $student)
    {
        // This would normally fetch from a badges table
        // For now, we'll generate them based on stats
        $stats = $this->getStudentStats($student);
        $badges = [];
        
        if ($stats['total_attempts'] >= 1) {
            $badges[] = [
                'name' => 'المستكشف',
                'description' => 'أكملت أول اختبار',
                'icon' => 'explore',
                'level' => 1,
            ];
        }
        
        if ($stats['total_attempts'] >= 5) {
            $badges[] = [
                'name' => 'المثابر',
                'description' => 'أكملت 5 اختبارات',
                'icon' => 'persistence',
                'level' => 1,
            ];
        }
        
        if ($stats['streak'] >= 3) {
            $badges[] = [
                'name' => 'المتسق',
                'description' => 'اجتزت 3 اختبارات متتالية',
                'icon' => 'streak',
                'level' => 1,
            ];
        }
        
        if ($stats['highest_score'] >= 90) {
            $badges[] = [
                'name' => 'المتفوق',
                'description' => 'حصلت على درجة 90% أو أعلى',
                'icon' => 'star',
                'level' => 1,
            ];
        }
        
        if ($stats['highest_score'] === 100) {
            $badges[] = [
                'name' => 'الكمال',
                'description' => 'حصلت على الدرجة الكاملة في اختبار',
                'icon' => 'perfect',
                'level' => 1,
            ];
        }
        
        return $badges;
    }
    
    /**
     * Get student's achievements
     *
     * @param User $student
     * @return array
     */
    private function getStudentAchievements(User $student)
    {
        // This would normally fetch from an achievements table
        // For now, we'll generate them based on stats
        $stats = $this->getStudentStats($student);
        $achievements = [];
        
        if ($stats['total_attempts'] >= 10) {
            $achievements[] = [
                'name' => 'عشرة اختبارات',
                'description' => 'أكملت 10 اختبارات',
                'date_earned' => now()->subDays(rand(1, 30))->format('Y-m-d'),
            ];
        }
        
        if ($stats['pass_rate'] >= 80 && $stats['total_attempts'] >= 5) {
            $achievements[] = [
                'name' => 'معدل نجاح عالي',
                'description' => 'حققت معدل نجاح 80% أو أعلى في 5 اختبارات على الأقل',
                'date_earned' => now()->subDays(rand(1, 20))->format('Y-m-d'),
            ];
        }
        
        if ($stats['streak'] >= 5) {
            $achievements[] = [
                'name' => 'سلسلة ذهبية',
                'description' => 'اجتزت 5 اختبارات متتالية',
                'date_earned' => now()->subDays(rand(1, 15))->format('Y-m-d'),
            ];
        }
        
        return $achievements;
    }
}
