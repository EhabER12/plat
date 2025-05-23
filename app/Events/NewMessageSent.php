<?php

namespace App\Events;

use App\Models\DirectMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * الرسالة المرسلة
     *
     * @var \App\Models\DirectMessage
     */
    public $message;

    /**
     * إنشاء حالة حدث جديدة.
     *
     * @param  \App\Models\DirectMessage  $message
     * @return void
     */
    public function __construct(DirectMessage $message)
    {
        $this->message = $message;
    }

    /**
     * الحصول على القنوات التي يجب بث الحدث عليها.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        try {
            return new PrivateChannel('chat.' . $this->message->receiver_id);
        } catch (\Exception $e) {
            Log::error('خطأ في تحديد قناة البث للرسالة', [
                'error' => $e->getMessage(),
                'message_id' => $this->message->message_id
            ]);
            // إرجاع قناة غير موجودة في حالة الخطأ
            return new PrivateChannel('chat.error');
        }
    }
    
    /**
     * الحصول على البيانات المراد بثها.
     *
     * @return array
     */
    public function broadcastWith()
    {
        try {
            return [
                'message' => [
                    'id' => $this->message->message_id,
                    'content' => $this->message->content,
                    'created_at' => $this->message->created_at,
                    'sender_id' => $this->message->sender_id,
                    'receiver_id' => $this->message->receiver_id,
                    'sender' => [
                        'id' => $this->message->sender->user_id,
                        'name' => $this->message->sender->name,
                        'profile_image' => $this->message->sender->profile_image ?? null
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('خطأ في تحضير بيانات البث للرسالة', [
                'error' => $e->getMessage(),
                'message_id' => $this->message->message_id
            ]);
            // إرجاع الحد الأدنى من البيانات في حالة الخطأ
            return [
                'message' => [
                    'id' => $this->message->message_id,
                    'content' => $this->message->content,
                    'sender_id' => $this->message->sender_id,
                    'receiver_id' => $this->message->receiver_id
                ]
            ];
        }
    }
} 