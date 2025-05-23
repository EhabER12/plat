<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use App\Services\ContentFilterService;

class DirectMessage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'messages';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'message_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'sender_id',
        'receiver_id',
        'content',
        'is_read',
        'read_at',
        'course_id',
        'chat_id',
        'contains_flagged_content',
        'flagged_severity',
        'is_filtered',
        'original_content'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'chat_id' => 0,
        'is_read' => false,
        'contains_flagged_content' => false,
        'flagged_severity' => 0,
        'is_filtered' => false
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'contains_flagged_content' => 'boolean',
        'flagged_severity' => 'integer',
        'is_filtered' => 'boolean'
    ];

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id', 'user_id');
    }

    /**
     * Get the course associated with the message.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Mark the message as read.
     */
    public function markAsRead()
    {
        try {
            if (!$this->is_read) {
                $this->is_read = true;
                $this->read_at = now();
                $this->save();
            }
            return $this;
        } catch (\Exception $e) {
            Log::error('خطأ في تحديث حالة القراءة للرسالة', [
                'error' => $e->getMessage(),
                'message_id' => $this->message_id
            ]);
            return $this;
        }
    }

    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include messages for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
        });
    }

    /**
     * Scope a query to only include messages between two users.
     */
    public function scopeBetweenUsers($query, $userId1, $userId2)
    {
        return $query->where(function($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId1)
              ->where('receiver_id', $userId2);
        })->orWhere(function($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId2)
              ->where('receiver_id', $userId1);
        });
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        try {
            static::creating(function ($message) {
                try {
                    if (!empty($message->content)) {
                        $message->filterContent();
                    }
                } catch (\Exception $e) {
                    // Log error but allow message to be created
                    Log::error('Error in DirectMessage creating event', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            });

            static::updating(function ($message) {
                try {
                    if ($message->isDirty('content')) {
                        $message->filterContent();
                    }
                } catch (\Exception $e) {
                    // Log error but allow message to be updated
                    Log::error('Error in DirectMessage updating event', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'message_id' => $message->message_id
                    ]);
                }
            });
        } catch (\Exception $e) {
            // Log error in booted method
            Log::error('Error in DirectMessage booted method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Filter the message content for banned words.
     *
     * @return void
     */
    protected function filterContent()
    {
        try {
            $filterService = app(ContentFilterService::class);
            $result = $filterService->filterContent($this->content);

            if ($result['has_banned_content'] ?? false) {
                // Save original content before filtering
                $this->original_content = $this->content;

                // Update with filtered content
                $this->contains_flagged_content = true;
                $this->flagged_severity = $result['highest_severity'] ?? 1;
                $this->content = $result['filtered_content'] ?? $this->content;
                $this->is_filtered = true;

                // Log the filtering
                Log::warning('Message content filtered', [
                    'message_id' => $this->message_id ?? 'new',
                    'sender_id' => $this->sender_id,
                    'receiver_id' => $this->receiver_id,
                    'found_words' => $result['found_words'] ?? [],
                    'severity' => $result['highest_severity'] ?? 1
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't prevent message from being saved
            Log::error('Error filtering message content in DirectMessage model', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'message_id' => $this->message_id ?? 'new',
                'sender_id' => $this->sender_id ?? 'unknown'
            ]);

            // Ensure message can still be saved
            $this->contains_flagged_content = false;
            $this->flagged_severity = 0;
            $this->is_filtered = false;
        }
    }
}
