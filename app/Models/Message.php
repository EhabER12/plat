<?php

namespace App\Models;

use App\Services\ContentFilterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;

class Message extends Model
{
    use HasFactory;

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
        'chat_id',
        'user_id',
        'content',
        'attachment_url',
        'attachment_type',
        'is_edited',
        'read_by',
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
        'is_edited' => 'boolean',
        'read_by' => 'array',
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
     * Get the chat this message belongs to.
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'chat_id');
    }

    /**
     * Get the user who sent the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Mark the message as read by a specific user.
     */
    public function markAsReadBy($userId)
    {
        $readBy = $this->read_by ?? [];
        if (!in_array($userId, $readBy)) {
            $readBy[] = $userId;
            $this->update(['read_by' => $readBy]);
        }
        return $this;
    }

    /**
     * Check if the message has been read by a specific user.
     */
    public function isReadBy($userId)
    {
        return in_array($userId, $this->read_by ?? []);
    }

    /**
     * Check if the message has an attachment.
     */
    public function hasAttachment()
    {
        return !empty($this->attachment_url);
    }
}
