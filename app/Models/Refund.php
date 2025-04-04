<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'refund_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',
        'user_id',
        'course_id',
        'amount',
        'status',
        'reason',
        'requested_at',
        'processed_at',
        'processed_by',
        'admin_notes',
        'refund_transaction_id',
        'gateway_response'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'gateway_response' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Refund status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

    /**
     * Get the payment associated with this refund.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Get the user who requested the refund.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the course associated with this refund.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the admin who processed the refund.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }

    /**
     * Get the transaction for this refund.
     */
    public function transaction()
    {
        return Transaction::where('transaction_type', Transaction::TYPE_REFUND)
                         ->where('reference_id', $this->refund_id)
                         ->where('reference_type', 'refund')
                         ->first();
    }

    /**
     * Check if the refund is pending.
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the refund is approved.
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the refund is rejected.
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if the refund is completed.
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Approve the refund.
     */
    public function approve($adminId, $notes = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'processed_by' => $adminId,
            'processed_at' => now(),
            'admin_notes' => $notes ?? $this->admin_notes
        ]);
        
        return $this;
    }

    /**
     * Reject the refund.
     */
    public function reject($adminId, $notes)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'processed_by' => $adminId,
            'processed_at' => now(),
            'admin_notes' => $notes
        ]);
        
        return $this;
    }

    /**
     * Mark the refund as completed.
     */
    public function complete($transactionId = null, $gatewayResponse = null)
    {
        $data = [
            'status' => self::STATUS_COMPLETED,
            'processed_at' => now()
        ];
        
        if ($transactionId) {
            $data['refund_transaction_id'] = $transactionId;
        }
        
        if ($gatewayResponse) {
            $data['gateway_response'] = $gatewayResponse;
        }
        
        $this->update($data);
        
        // Unenroll user from course
        Enrollment::where('user_id', $this->user_id)
                 ->where('course_id', $this->course_id)
                 ->delete();
        
        return $this;
    }

    /**
     * Get the processing time in days.
     */
    public function getProcessingTimeAttribute()
    {
        if (!$this->processed_at || !$this->requested_at) {
            return null;
        }
        
        return $this->requested_at->diffInDays($this->processed_at);
    }

    /**
     * Scope a query to only include pending refunds.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved refunds.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include rejected refunds.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to only include completed refunds.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to order by newest first.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('requested_at', 'desc');
    }
}
