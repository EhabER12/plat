<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorApplication extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'application_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'status',
        'education',
        'experience',
        'expertise',
        'reason',
        'sample_content_url',
        'resume_url',
        'social_media_links',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'additional_notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'social_media_links' => 'array',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ADDITIONAL_INFO = 'need_info';

    /**
     * Get the user who applied to be an instructor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the admin who reviewed the application.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'user_id');
    }

    /**
     * Check if the application is pending.
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the application has been approved.
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the application has been rejected.
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if the application needs additional information.
     */
    public function needsAdditionalInfo()
    {
        return $this->status === self::STATUS_ADDITIONAL_INFO;
    }

    /**
     * Approve the application.
     */
    public function approve($reviewerId, $notes = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'additional_notes' => $notes
        ]);

        // Update the user role to 'instructor'
        if ($this->user) {
            $this->user->update(['role' => 'instructor']);
        }
        
        return $this;
    }

    /**
     * Reject the application.
     */
    public function reject($reviewerId, $reason, $notes = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
            'additional_notes' => $notes
        ]);
        
        return $this;
    }

    /**
     * Request additional information.
     */
    public function requestInfo($reviewerId, $notes)
    {
        $this->update([
            'status' => self::STATUS_ADDITIONAL_INFO,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'additional_notes' => $notes
        ]);
        
        return $this;
    }

    /**
     * Get the time taken to review the application.
     */
    public function getReviewTimeAttribute()
    {
        if (!$this->reviewed_at) {
            return null;
        }
        
        return $this->created_at->diffInDays($this->reviewed_at);
    }

    /**
     * Scope a query to only include pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved applications.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include rejected applications.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to only include applications needing additional information.
     */
    public function scopeNeedingInfo($query)
    {
        return $query->where('status', self::STATUS_ADDITIONAL_INFO);
    }

    /**
     * Scope a query to order by newest first.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
