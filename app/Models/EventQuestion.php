<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventQuestion extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'question_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'content',
        'status',
        'is_anonymous',
        'is_featured',
        'upvotes',
        'answered_at',
        'answered_by',
        'answer',
        'moderated_at',
        'moderated_by',
        'moderation_reason',
        'display_order',
        'parent_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_featured' => 'boolean',
        'upvotes' => 'integer',
        'answered_at' => 'datetime',
        'moderated_at' => 'datetime',
        'display_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ANSWERED = 'answered';
    const STATUS_REJECTED = 'rejected';
    const STATUS_HIDDEN = 'hidden';

    /**
     * Get the event this question belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    /**
     * Get the user who asked the question.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the user who answered the question.
     */
    public function answeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by', 'user_id');
    }

    /**
     * Get the user who moderated the question.
     */
    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by', 'user_id');
    }

    /**
     * Get the parent question if this is a reply.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(EventQuestion::class, 'parent_id', 'question_id');
    }

    /**
     * Get the replies to this question.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(EventQuestion::class, 'parent_id', 'question_id');
    }

    /**
     * Get the users who upvoted this question.
     */
    public function upvoters()
    {
        return $this->belongsToMany(User::class, 'event_question_upvotes', 'question_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Approve the question.
     *
     * @param int|null $moderatorId
     * @return $this
     */
    public function approve($moderatorId = null)
    {
        $data = ['status' => self::STATUS_APPROVED];
        
        if ($moderatorId) {
            $data['moderated_by'] = $moderatorId;
            $data['moderated_at'] = now();
        }
        
        $this->update($data);
        
        return $this;
    }

    /**
     * Reject the question.
     *
     * @param int $moderatorId
     * @param string|null $reason
     * @return $this
     */
    public function reject($moderatorId, $reason = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
            'moderation_reason' => $reason
        ]);
        
        return $this;
    }

    /**
     * Hide the question.
     *
     * @param int $moderatorId
     * @param string|null $reason
     * @return $this
     */
    public function hide($moderatorId, $reason = null)
    {
        $this->update([
            'status' => self::STATUS_HIDDEN,
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
            'moderation_reason' => $reason
        ]);
        
        return $this;
    }

    /**
     * Answer the question.
     *
     * @param string $answer
     * @param int $answererId
     * @return $this
     */
    public function answer($answer, $answererId)
    {
        $this->update([
            'answer' => $answer,
            'answered_by' => $answererId,
            'answered_at' => now(),
            'status' => self::STATUS_ANSWERED
        ]);
        
        return $this;
    }

    /**
     * Update the answer to the question.
     *
     * @param string $answer
     * @param int|null $answererId
     * @return $this
     */
    public function updateAnswer($answer, $answererId = null)
    {
        $data = ['answer' => $answer];
        
        if ($answererId) {
            $data['answered_by'] = $answererId;
            $data['answered_at'] = now();
        }
        
        $this->update($data);
        
        return $this;
    }

    /**
     * Remove the answer from the question.
     *
     * @return $this
     */
    public function removeAnswer()
    {
        $status = $this->status === self::STATUS_ANSWERED ? self::STATUS_APPROVED : $this->status;
        
        $this->update([
            'answer' => null,
            'answered_by' => null,
            'answered_at' => null,
            'status' => $status
        ]);
        
        return $this;
    }

    /**
     * Toggle the featured status of the question.
     *
     * @return $this
     */
    public function toggleFeatured()
    {
        $this->update(['is_featured' => !$this->is_featured]);
        
        return $this;
    }

    /**
     * Upvote the question by a user.
     *
     * @param int $userId
     * @return bool
     */
    public function upvote($userId)
    {
        // Check if the user already upvoted
        if ($this->upvoters()->where('user_id', $userId)->exists()) {
            return false;
        }
        
        // Add the upvote
        $this->upvoters()->attach($userId);
        
        // Update the upvote count
        $this->increment('upvotes');
        
        return true;
    }

    /**
     * Remove upvote from a user.
     *
     * @param int $userId
     * @return bool
     */
    public function removeUpvote($userId)
    {
        // Check if the user has upvoted
        if (!$this->upvoters()->where('user_id', $userId)->exists()) {
            return false;
        }
        
        // Remove the upvote
        $this->upvoters()->detach($userId);
        
        // Update the upvote count
        $this->decrement('upvotes');
        
        return true;
    }

    /**
     * Check if a user has upvoted this question.
     *
     * @param int $userId
     * @return bool
     */
    public function isUpvotedBy($userId)
    {
        return $this->upvoters()->where('user_id', $userId)->exists();
    }

    /**
     * Check if the question is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the question is approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the question is answered.
     *
     * @return bool
     */
    public function isAnswered()
    {
        return $this->status === self::STATUS_ANSWERED;
    }

    /**
     * Check if the question is rejected.
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if the question is hidden.
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->status === self::STATUS_HIDDEN;
    }

    /**
     * Check if the question is a reply to another question.
     *
     * @return bool
     */
    public function isReply()
    {
        return $this->parent_id !== null;
    }

    /**
     * Check if the question has replies.
     *
     * @return bool
     */
    public function hasReplies()
    {
        return $this->replies()->count() > 0;
    }

    /**
     * Scope a query to only include pending questions.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved questions.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include answered questions.
     */
    public function scopeAnswered($query)
    {
        return $query->where('status', self::STATUS_ANSWERED);
    }

    /**
     * Scope a query to only include rejected questions.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to only include hidden questions.
     */
    public function scopeHidden($query)
    {
        return $query->where('status', self::STATUS_HIDDEN);
    }

    /**
     * Scope a query to only include featured questions.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include anonymous questions.
     */
    public function scopeAnonymous($query)
    {
        return $query->where('is_anonymous', true);
    }

    /**
     * Scope a query to only include top-level questions (not replies).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to only include questions for a specific event.
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Scope a query to only include questions by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include questions with the most upvotes.
     */
    public function scopePopular($query)
    {
        return $query->orderBy('upvotes', 'desc');
    }

    /**
     * Scope a query to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')
                    ->orderBy('created_at', 'asc');
    }

    /**
     * Scope a query to only include visible questions (approved or answered).
     */
    public function scopeVisible($query)
    {
        return $query->whereIn('status', [self::STATUS_APPROVED, self::STATUS_ANSWERED]);
    }
}
