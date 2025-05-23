<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * إنشاء إشعار جديد للمستخدم.
     *
     * @param int $userId معرف المستخدم
     * @param string $title عنوان الإشعار
     * @param string $message محتوى الإشعار
     * @param string|null $type نوع الإشعار (اختياري)
     * @param string|null $relatedEntity نوع الكيان المرتبط (اختياري)
     * @param int|null $entityId معرف الكيان المرتبط (اختياري)
     * @return Notification
     */
    public function create(
        int $userId,
        string $title,
        string $message,
        ?string $type = null,
        ?string $relatedEntity = null,
        ?int $entityId = null
    ): Notification {
        try {
            return Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'related_entity' => $relatedEntity,
                'entity_id' => $entityId,
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('فشل إنشاء إشعار: ' . $e->getMessage(), [
                'user_id' => $userId,
                'title' => $title,
                'exception' => $e,
            ]);
            
            throw $e;
        }
    }

    /**
     * إنشاء إشعار للطالب عند الحصول على شارة.
     *
     * @param User $student الطالب
     * @param \App\Models\Badge $badge الشارة
     * @return Notification
     */
    public function createBadgeNotification(User $student, \App\Models\Badge $badge): Notification
    {
        return $this->create(
            $student->user_id,
            'تهانينا! لقد حصلت على شارة جديدة',
            'مبروك! لقد حصلت على شارة "' . $badge->name . '". ' . $badge->description,
            'badge',
            'badges',
            $badge->id
        );
    }

    /**
     * إنشاء إشعار للطالب عند الحصول على إنجاز.
     *
     * @param User $student الطالب
     * @param \App\Models\Achievement $achievement الإنجاز
     * @return Notification
     */
    public function createAchievementNotification(User $student, \App\Models\Achievement $achievement): Notification
    {
        return $this->create(
            $student->user_id,
            'تهانينا! لقد حققت إنجازاً جديداً',
            'مبروك! لقد حققت إنجاز "' . $achievement->name . '". ' . $achievement->description,
            'achievement',
            'achievements',
            $achievement->id
        );
    }

    /**
     * إنشاء إشعار للطالب عند اجتياز اختبار.
     *
     * @param User $student الطالب
     * @param \App\Models\QuizAttempt $attempt محاولة الاختبار
     * @return Notification
     */
    public function createQuizPassedNotification(User $student, \App\Models\QuizAttempt $attempt): Notification
    {
        $quiz = $attempt->quiz;
        $score = $attempt->score_percentage;
        
        $message = "لقد اجتزت اختبار \"{$quiz->title}\" بنجاح بنسبة {$score}%.";
        
        if ($score >= 90) {
            $message .= " أداء ممتاز! استمر في العمل الجيد.";
        } elseif ($score >= 80) {
            $message .= " أداء جيد جداً! واصل التقدم.";
        } elseif ($score >= 70) {
            $message .= " أداء جيد. يمكنك تحسين أدائك أكثر.";
        } else {
            $message .= " لقد نجحت، ولكن يمكنك تحسين أدائك بمزيد من الدراسة.";
        }
        
        return $this->create(
            $student->user_id,
            'تهانينا! لقد اجتزت الاختبار',
            $message,
            'quiz_passed',
            'quizzes',
            $quiz->id
        );
    }

    /**
     * إنشاء إشعار للطالب عند الرسوب في اختبار.
     *
     * @param User $student الطالب
     * @param \App\Models\QuizAttempt $attempt محاولة الاختبار
     * @return Notification
     */
    public function createQuizFailedNotification(User $student, \App\Models\QuizAttempt $attempt): Notification
    {
        $quiz = $attempt->quiz;
        $score = $attempt->score_percentage;
        $passingScore = $quiz->passing_percentage;
        
        $message = "للأسف، لم تجتز اختبار \"{$quiz->title}\". حصلت على {$score}% والحد الأدنى للنجاح هو {$passingScore}%.";
        $message .= " لا تيأس! يمكنك المحاولة مرة أخرى بعد مراجعة المواد الدراسية.";
        
        return $this->create(
            $student->user_id,
            'نتيجة الاختبار',
            $message,
            'quiz_failed',
            'quizzes',
            $quiz->id
        );
    }

    /**
     * إنشاء إشعار للطالب عند إضافة محتوى جديد للدورة.
     *
     * @param User $student الطالب
     * @param \App\Models\Course $course الدورة
     * @param string $contentType نوع المحتوى (فيديو، مادة، اختبار)
     * @param string $contentTitle عنوان المحتوى
     * @return Notification
     */
    public function createNewContentNotification(User $student, \App\Models\Course $course, string $contentType, string $contentTitle): Notification
    {
        $contentTypeArabic = $contentType == 'video' ? 'فيديو' : ($contentType == 'material' ? 'مادة تعليمية' : 'اختبار');
        
        return $this->create(
            $student->user_id,
            'محتوى جديد في الدورة',
            "تم إضافة {$contentTypeArabic} جديد بعنوان \"{$contentTitle}\" إلى دورة \"{$course->title}\". تحقق منه الآن!",
            'new_content',
            'courses',
            $course->course_id
        );
    }
}
