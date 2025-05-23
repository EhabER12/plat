<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DirectMessage;
use App\Models\User;

class AdminMessageNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $admin;

    /**
     * Create a new notification instance.
     */
    public function __construct(DirectMessage $message, User $admin)
    {
        $this->message = $message;
        $this->admin = $admin;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('رسالة جديدة من الإدارة')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('لقد تلقيت رسالة جديدة من الإدارة.')
            ->line('المرسل: ' . $this->admin->name)
            ->line('محتوى الرسالة: ' . \Str::limit($this->message->content, 100))
            ->action('عرض الرسالة', $this->getMessageUrl($notifiable))
            ->line('شكراً لاستخدامك منصتنا التعليمية!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin_message',
            'message_id' => $this->message->message_id,
            'admin_id' => $this->admin->user_id,
            'admin_name' => $this->admin->name,
            'content' => \Str::limit($this->message->content, 100),
            'created_at' => $this->message->created_at->toISOString(),
            'url' => $this->getMessageUrl($notifiable)
        ];
    }

    /**
     * Get the appropriate message URL based on user role.
     */
    protected function getMessageUrl($notifiable): string
    {
        $userRoles = $notifiable->getUserRoles();
        
        if (in_array('instructor', $userRoles)) {
            return route('instructor.messages.show', $this->admin->user_id);
        } elseif (in_array('student', $userRoles)) {
            return route('student.messages.show', $this->admin->user_id);
        } elseif (in_array('parent', $userRoles)) {
            // For parents, we might need to create a parent messages route
            // For now, redirect to dashboard
            return route('parent.dashboard');
        }
        
        // Default fallback
        return url('/');
    }
}
