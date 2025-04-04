<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'log_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'template_id',
        'recipient_email',
        'recipient_name',
        'sender_email',
        'sender_name',
        'subject',
        'body',
        'cc',
        'bcc',
        'status',
        'sent_at',
        'error',
        'user_id',
        'related_type',
        'related_id',
        'headers',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cc' => 'array',
        'bcc' => 'array',
        'sent_at' => 'datetime',
        'headers' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Email status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_OPENED = 'opened';
    const STATUS_CLICKED = 'clicked';
    const STATUS_BOUNCED = 'bounced';
    const STATUS_SPAM = 'spam';

    /**
     * Get the template used for this email.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id', 'template_id');
    }

    /**
     * Get the user this email was sent to or from.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the related model for this email.
     */
    public function related()
    {
        if (!$this->related_type || !$this->related_id) {
            return null;
        }

        $relatedClass = '\\App\\Models\\' . $this->related_type;
        
        if (class_exists($relatedClass)) {
            return $relatedClass::find($this->related_id);
        }
        
        return null;
    }

    /**
     * Mark the email as sent.
     */
    public function markAsSent()
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now()
        ]);
        
        return $this;
    }

    /**
     * Mark the email as failed with an error message.
     */
    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error' => $errorMessage
        ]);
        
        return $this;
    }

    /**
     * Mark the email with a new status.
     */
    public function markAs($status)
    {
        $this->update([
            'status' => $status
        ]);
        
        return $this;
    }

    /**
     * Check if the email was sent.
     */
    public function isSent()
    {
        return $this->status === self::STATUS_SENT ||
               $this->status === self::STATUS_DELIVERED ||
               $this->status === self::STATUS_OPENED ||
               $this->status === self::STATUS_CLICKED;
    }

    /**
     * Check if the email failed to send.
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED ||
               $this->status === self::STATUS_BOUNCED ||
               $this->status === self::STATUS_SPAM;
    }

    /**
     * Scope a query to only include sent emails.
     */
    public function scopeSent($query)
    {
        return $query->whereIn('status', [
            self::STATUS_SENT,
            self::STATUS_DELIVERED,
            self::STATUS_OPENED,
            self::STATUS_CLICKED
        ]);
    }

    /**
     * Scope a query to only include failed emails.
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', [
            self::STATUS_FAILED,
            self::STATUS_BOUNCED,
            self::STATUS_SPAM
        ]);
    }

    /**
     * Scope a query to only include pending emails.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include emails for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include emails for a specific template.
     */
    public function scopeUsingTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }

    /**
     * Scope a query to order by newest first.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
