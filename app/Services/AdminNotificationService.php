<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AdminNotificationService
{
    /**
     * إنشاء إشعار جديد للمشرفين.
     *
     * @param string $type نوع الإشعار
     * @param string $content محتوى الإشعار
     * @param User|null $user المستخدم المرتبط بالإشعار (إختياري)
     * @param Model|null $related العنصر المرتبط (إختياري)
     * @param array $data بيانات إضافية (إختياري)
     * @param int $severity مستوى الأهمية (1-5)
     * @return AdminNotification
     */
    public function create(
        string $type,
        string $content,
        ?User $user = null,
        ?Model $related = null,
        array $data = [],
        int $severity = 1
    ): AdminNotification {
        return AdminNotification::create([
            'type' => $type,
            'user_id' => $user ? $user->user_id : null,
            'related_id' => $related ? $related->getKey() : null,
            'related_type' => $related ? get_class($related) : null,
            'content' => $content,
            'data' => empty($data) ? null : $data,
            'severity' => $severity,
            'is_read' => false,
        ]);
    }

    /**
     * إنشاء إشعار للكلمات المحظورة.
     *
     * @param User $user المستخدم الذي استخدم الكلمات المحظورة
     * @param Model $message الرسالة التي تحتوي على الكلمات المحظورة
     * @param array $bannedWords الكلمات المحظورة التي تم اكتشافها
     * @param int $severity مستوى الخطورة
     * @return AdminNotification
     */
    public function createFlaggedContentNotification(
        User $user,
        Model $message,
        array $bannedWords,
        int $severity
    ): AdminNotification {
        // تحديد نوع المستخدم (طالب أم مدرس)
        $userType = $user->hasRole('instructor') ? 'مدرس' : 'طالب';
        
        // إنشاء محتوى الإشعار
        $content = "قام {$userType} ({$user->name}) باستخدام كلمات محظورة في رسالة.";
        
        // بيانات إضافية
        $data = [
            'banned_words' => $bannedWords,
            'original_message' => $message->content ?? '',
            'user_roles' => $user->getUserRoles(),
        ];
        
        // إرسال إشعار بالبريد الإلكتروني للمشرف إذا كان مستوى الخطورة 3 أو أعلى
        if ($severity >= 3) {
            $this->sendEmailNotification($user, $message, $bannedWords, $severity);
        }
        
        // إنشاء الإشعار
        return $this->create(
            'flagged_content',
            $content,
            $user,
            $message,
            $data,
            $severity
        );
    }
    
    /**
     * إرسال إشعار بالبريد الإلكتروني للمشرف.
     *
     * @param User $user المستخدم المرسل للمحتوى المحظور
     * @param Model $message رسالة المستخدم
     * @param array $bannedWords الكلمات المحظورة
     * @param int $severity مستوى الخطورة
     * @return void
     */
    protected function sendEmailNotification(User $user, Model $message, array $bannedWords, int $severity): void
    {
        try {
            // البحث عن جميع المستخدمين المشرفين لإرسال البريد إليهم
            $admins = \App\Models\Admin::admins()->get();
            
            if ($admins->isEmpty()) {
                // إذا لم يتم العثور على مشرفين، استخدم نموذج الإدارة الافتراضي
                $admin = new \App\Models\Admin();
                $admin->notify(new \App\Notifications\FlaggedContentNotification(
                    $user,
                    $message,
                    $bannedWords,
                    $severity
                ));
            } else {
                // إرسال إشعار لكل مشرف
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\FlaggedContentNotification(
                        $user,
                        $message,
                        $bannedWords,
                        $severity
                    ));
                }
            }
        } catch (\Exception $e) {
            // تسجيل أي أخطاء أثناء إرسال البريد الإلكتروني
            \Illuminate\Support\Facades\Log::error('فشل إرسال إشعار البريد الإلكتروني: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $user->user_id,
                'message_id' => $message->id,
            ]);
        }
    }
    
    /**
     * الحصول على الإشعارات الجديدة غير المقروءة.
     *
     * @param int $limit عدد الإشعارات للاسترجاع
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnreadNotifications($limit = 50)
    {
        return AdminNotification::with('user')
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * الحصول على الإشعارات المهمة غير المقروءة.
     *
     * @param int $minSeverity الحد الأدنى لمستوى الأهمية (1-5)
     * @param int $limit عدد الإشعارات للاسترجاع
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getImportantUnreadNotifications($minSeverity = 3, $limit = 10)
    {
        return AdminNotification::with('user')
            ->unread()
            ->where('severity', '>=', $minSeverity)
            ->orderBy('severity', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * إنشاء إشعار نظام للتحذير من أي شيء مهم.
     *
     * @param string $message رسالة التحذير
     * @param int $severity مستوى الخطورة من 1 إلى 5
     * @param array $data بيانات إضافية اختيارية
     * @return AdminNotification
     */
    public function createSystemAlert(string $message, int $severity = 3, array $data = []): AdminNotification
    {
        return $this->create(
            'system_alert',
            $message,
            null,
            null,
            $data,
            $severity
        );
    }
} 