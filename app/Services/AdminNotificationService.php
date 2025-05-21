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
     * @param string $title عنوان الإشعار
     * @param string $message محتوى الإشعار
     * @param Model|null $related العنصر المرتبط (إختياري)
     * @param array $data بيانات إضافية (إختياري)
     * @param string $severity مستوى الأهمية ('low', 'medium', 'high')
     * @return AdminNotification
     */
    public function create(
        string $type,
        string $title,
        string $message,
        ?Model $related = null,
        array $data = [],
        string $severity = 'medium'
    ): AdminNotification {
        return AdminNotification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_id' => $related ? $related->getKey() : null,
            'related_type' => $related ? get_class($related) : null,
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
     * @param int $severityLevel مستوى الخطورة (رقم)
     * @return AdminNotification
     */
    public function createFlaggedContentNotification(
        User $user,
        Model $message,
        array $bannedWords,
        int $severityLevel = 1
    ): AdminNotification {
        // تحديد نوع المستخدم (طالب أم مدرس)
        $userType = $user->hasRole('instructor') ? 'مدرس' : 'طالب';
        
        // إنشاء عنوان الإشعار
        $title = "محتوى محظور من {$userType}";
        
        // إنشاء محتوى الإشعار
        $messageText = "قام {$userType} ({$user->name}) باستخدام كلمات محظورة في رسالة.";
        
        // بيانات إضافية
        $data = [
            'banned_words' => $bannedWords,
            'original_message' => $message->content ?? '',
            'user_roles' => $user->getUserRoles(),
            'user_id' => $user->user_id,
            'user_name' => $user->name
        ];
        
        // تحويل مستوى الخطورة الرقمي إلى نص
        $severity = 'low';
        if ($severityLevel >= 3) {
            $severity = 'high';
        } elseif ($severityLevel >= 2) {
            $severity = 'medium';
        }
        
        // إرسال إشعار بالبريد الإلكتروني للمشرف إذا كان مستوى الخطورة عالي
        if ($severity === 'high') {
            $this->sendEmailNotification($user, $message, $bannedWords, $severity);
        }
        
        // إنشاء الإشعار
        return $this->create(
            'flagged_content',
            $title,
            $messageText,
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
     * @param string $severity مستوى الخطورة
     * @return void
     */
    protected function sendEmailNotification(User $user, Model $message, array $bannedWords, string $severity): void
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
        return AdminNotification::unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * الحصول على الإشعارات المهمة غير المقروءة.
     *
     * @param string $minSeverity الحد الأدنى لمستوى الأهمية ('low', 'medium', 'high')
     * @param int $limit عدد الإشعارات للاسترجاع
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getImportantUnreadNotifications($minSeverity = 'medium', $limit = 10)
    {
        return AdminNotification::unread()
            ->where('severity', $minSeverity)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * إنشاء إشعار نظام للتحذير من أي شيء مهم.
     *
     * @param string $title عنوان التحذير
     * @param string $message رسالة التحذير
     * @param string $severity مستوى الخطورة ('low', 'medium', 'high')
     * @param array $data بيانات إضافية اختيارية
     * @return AdminNotification
     */
    public function createSystemAlert(string $title, string $message, string $severity = 'medium', array $data = []): AdminNotification
    {
        return $this->create(
            'system_alert',
            $title,
            $message,
            null,
            $data,
            $severity
        );
    }
} 