<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
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
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'ip_address',
        'user_agent',
        'status',
        'assigned_to',
        'response',
        'responded_at',
        'responded_by',
        'category',
        'is_spam',
        'user_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_spam' => 'boolean',
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status constants
     */
    const STATUS_NEW = 'new';
    const STATUS_READ = 'read';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESPONDED = 'responded';
    const STATUS_CLOSED = 'closed';

    /**
     * Get the user who sent this message (if registered).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the staff member who is assigned to this message.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to', 'user_id');
    }

    /**
     * Get the staff member who responded to this message.
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by', 'user_id');
    }

    /**
     * Check if the message is new.
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * Check if the message has been read.
     *
     * @return bool
     */
    public function isRead()
    {
        return in_array($this->status, [
            self::STATUS_READ,
            self::STATUS_IN_PROGRESS,
            self::STATUS_RESPONDED,
            self::STATUS_CLOSED
        ]);
    }

    /**
     * Check if the message has been responded to.
     *
     * @return bool
     */
    public function isResponded()
    {
        return in_array($this->status, [
            self::STATUS_RESPONDED,
            self::STATUS_CLOSED
        ]);
    }

    /**
     * Mark the message as read.
     *
     * @return $this
     */
    public function markAsRead()
    {
        if ($this->status === self::STATUS_NEW) {
            $this->update(['status' => self::STATUS_READ]);
        }
        
        return $this;
    }

    /**
     * Mark the message as in progress.
     *
     * @param int|null $staffId
     * @return $this
     */
    public function markAsInProgress($staffId = null)
    {
        $data = ['status' => self::STATUS_IN_PROGRESS];
        
        if ($staffId) {
            $data['assigned_to'] = $staffId;
        }
        
        $this->update($data);
        
        return $this;
    }

    /**
     * Record a response to this message.
     *
     * @param string $response
     * @param int $responderId
     * @param bool $closeMessage
     * @return $this
     */
    public function respond($response, $responderId, $closeMessage = false)
    {
        $status = $closeMessage ? self::STATUS_CLOSED : self::STATUS_RESPONDED;
        
        $this->update([
            'response' => $response,
            'responded_at' => now(),
            'responded_by' => $responderId,
            'status' => $status
        ]);
        
        return $this;
    }

    /**
     * Mark the message as spam.
     *
     * @return $this
     */
    public function markAsSpam()
    {
        $this->update([
            'is_spam' => true,
            'status' => self::STATUS_CLOSED
        ]);
        
        return $this;
    }

    /**
     * Close the message.
     *
     * @return $this
     */
    public function close()
    {
        $this->update(['status' => self::STATUS_CLOSED]);
        
        return $this;
    }

    /**
     * Scope a query to only include new messages.
     */
    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    /**
     * Scope a query to only include read messages.
     */
    public function scopeRead($query)
    {
        return $query->where('status', self::STATUS_READ);
    }

    /**
     * Scope a query to only include in progress messages.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope a query to only include responded messages.
     */
    public function scopeResponded($query)
    {
        return $query->where('status', self::STATUS_RESPONDED);
    }

    /**
     * Scope a query to only include closed messages.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    /**
     * Scope a query to only include non-spam messages.
     */
    public function scopeNotSpam($query)
    {
        return $query->where('is_spam', false);
    }

    /**
     * Scope a query to only include messages in a specific category.
     */
    public function scopeInCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include messages assigned to a specific staff member.
     */
    public function scopeAssignedTo($query, $staffId)
    {
        return $query->where('assigned_to', $staffId);
    }

    /**
     * Scope a query to order by newest first.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get the time elapsed since the message was created.
     *
     * @return string
     */
    public function getElapsedTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the response time in hours.
     *
     * @return float|null
     */
    public function getResponseTimeAttribute()
    {
        if (!$this->responded_at) {
            return null;
        }
        
        return $this->created_at->diffInHours($this->responded_at, false);
    }
}
