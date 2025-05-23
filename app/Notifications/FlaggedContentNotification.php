<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FlaggedContentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * المستخدم الذي استخدم الكلمات المحظورة
     * 
     * @var User
     */
    protected $user;

    /**
     * الرسالة التي تحتوي على الكلمات المحظورة
     * 
     * @var Model
     */
    protected $message;

    /**
     * قائمة الكلمات المحظورة التي تم اكتشافها
     * 
     * @var array
     */
    protected $bannedWords;

    /**
     * مستوى خطورة المحتوى
     * 
     * @var int
     */
    protected $severity;

    /**
     * إنشاء نسخة جديدة من الإشعار.
     * 
     * @param User $user
     * @param Model $message
     * @param array $bannedWords
     * @param int $severity
     */
    public function __construct(User $user, Model $message, array $bannedWords, int $severity)
    {
        $this->user = $user;
        $this->message = $message;
        $this->bannedWords = $bannedWords;
        $this->severity = $severity;
    }

    /**
     * الحصول على قنوات توصيل الإشعار.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * الحصول على تمثيل البريد الإلكتروني للإشعار.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $userType = $this->user->hasRole('instructor') ? 'مدرس' : 'طالب';
        
        // تحديد حالة الخطورة لاستخدامها في موضوع البريد
        $severityText = 'عادي';
        if ($this->severity >= 4) {
            $severityText = 'خطير جداً';
        } elseif ($this->severity >= 3) {
            $severityText = 'خطير';
        } elseif ($this->severity >= 2) {
            $severityText = 'متوسط';
        }
        
        // تجميع الكلمات المحظورة كنص
        $bannedWordsText = implode('، ', $this->bannedWords);
        
        $adminUrl = url('/admin/notifications');
        
        return (new MailMessage)
            ->subject("تنبيه! اكتشاف محتوى محظور [{$severityText}]")
            ->greeting('مرحباً!')
            ->line("تم اكتشاف محتوى محظور في نظام المراسلة من قبل {$userType} ({$this->user->name}).")
            ->line("مستوى الخطورة: {$severityText}")
            ->line("الكلمات المحظورة: {$bannedWordsText}")
            ->line("المحتوى الأصلي: {$this->message->content}")
            ->action('عرض التفاصيل في لوحة التحكم', $adminUrl)
            ->line('شكراً لاستخدامك نظام المراقبة الآلي!');
    }

    /**
     * الحصول على تمثيل المصفوفة للإشعار.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->user_id,
            'message_id' => $this->message->id,
            'banned_words' => $this->bannedWords,
            'severity' => $this->severity,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'user_roles' => $this->user->getUserRoles(),
            'original_content' => $this->message->content,
        ];
    }
}
