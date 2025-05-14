<?php

namespace App\Models;

use App\Services\ContentFilterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;

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
        'contains_flagged_content',
        'flagged_severity',
        'is_filtered'
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
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'contains_flagged_content' => false,
        'flagged_severity' => 0,
        'is_filtered' => false
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($message) {
            if (!empty($message->content)) {
                $message->filterContent();
            }
        });

        static::updating(function ($message) {
            if ($message->isDirty('content')) {
                $message->filterContent();
            }
        });
    }

    /**
     * Filter the message content for banned words.
     *
     * @return void
     */
    protected function filterContent()
    {
        $filterService = App::make(ContentFilterService::class);
        $result = $filterService->filterContent($this->content);
        
        if ($result['has_banned_content']) {
            $this->contains_flagged_content = true;
            $this->flagged_severity = $result['highest_severity'];
            $this->content = $result['filtered_content'];
            $this->is_filtered = true;
        }
    }

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
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
        return $this;
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
     * Scope a query to only include flagged messages.
     */
    public function scopeFlagged($query)
    {
        return $query->where('contains_flagged_content', true);
    }

    /**
     * Scope a query to filter by minimum severity level.
     */
    public function scopeMinSeverity($query, $level)
    {
        return $query->where('flagged_severity', '>=', $level);
    }
}
