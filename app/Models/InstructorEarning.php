<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorEarning extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'earning_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'instructor_id',
        'course_id',
        'payment_id',
        'amount',
        'platform_fee',
        'status',
        'withdrawal_id',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_AVAILABLE = 'available';
    const STATUS_WITHDRAWN = 'withdrawn';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the instructor who earned this amount.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id', 'user_id');
    }

    /**
     * Get the course that generated this earning.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the payment that generated this earning.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Get the withdrawal that includes this earning.
     */
    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(Withdrawal::class, 'withdrawal_id', 'withdrawal_id');
    }

    /**
     * Check if the earning is pending.
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the earning is available for withdrawal.
     */
    public function isAvailable()
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Check if the earning has been withdrawn.
     */
    public function isWithdrawn()
    {
        return $this->status === self::STATUS_WITHDRAWN;
    }

    /**
     * Check if the earning has been cancelled.
     */
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Mark the earning as available for withdrawal.
     */
    public function markAsAvailable()
    {
        if ($this->status === self::STATUS_PENDING) {
            $this->status = self::STATUS_AVAILABLE;
            $this->save();
            return true;
        }
        
        return false;
    }

    /**
     * Mark the earning as withdrawn.
     */
    public function markAsWithdrawn($withdrawalId)
    {
        if ($this->status === self::STATUS_AVAILABLE) {
            $this->status = self::STATUS_WITHDRAWN;
            $this->withdrawal_id = $withdrawalId;
            $this->save();
            return true;
        }
        
        return false;
    }

    /**
     * Mark the earning as cancelled.
     */
    public function markAsCancelled($notes = null)
    {
        if ($this->status === self::STATUS_PENDING || $this->status === self::STATUS_AVAILABLE) {
            $this->status = self::STATUS_CANCELLED;
            
            if ($notes) {
                $this->notes = $notes;
            }
            
            $this->save();
            return true;
        }
        
        return false;
    }

    /**
     * Get the total amount (instructor earnings + platform fee).
     */
    public function getTotalAmountAttribute()
    {
        return $this->amount + $this->platform_fee;
    }

    /**
     * Scope a query to only include earnings for a specific instructor.
     */
    public function scopeForInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Scope a query to only include available earnings.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Scope a query to only include pending earnings.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include withdrawn earnings.
     */
    public function scopeWithdrawn($query)
    {
        return $query->where('status', self::STATUS_WITHDRAWN);
    }

    /**
     * Scope a query to only include earnings from a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
