<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateReferral extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'referral_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'affiliate_id',
        'program_id',
        'referred_user_id',
        'course_id',
        'payment_id',
        'referral_code',
        'ip_address',
        'user_agent',
        'referrer_url',
        'landing_page',
        'status',
        'clicks',
        'amount',
        'commission_rate',
        'commission_amount',
        'conversion_date',
        'cookie_set_date',
        'cookie_expiry',
        'tracking_parameters',
        'device',
        'browser',
        'os',
        'country',
        'city',
        'first_click_at',
        'last_click_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'clicks' => 'integer',
        'amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'conversion_date' => 'datetime',
        'cookie_set_date' => 'datetime',
        'cookie_expiry' => 'datetime',
        'tracking_parameters' => 'array',
        'first_click_at' => 'datetime',
        'last_click_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONVERTED = 'converted';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_EXPIRED = 'expired';

    /**
     * Get the affiliate this referral belongs to.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class, 'affiliate_id', 'affiliate_id');
    }

    /**
     * Get the affiliate program this referral belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(AffiliateProgram::class, 'program_id', 'program_id');
    }

    /**
     * Get the referred user.
     */
    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id', 'user_id');
    }

    /**
     * Get the course that was purchased.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the payment associated with this referral.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Increment the click count.
     *
     * @return $this
     */
    public function incrementClickCount()
    {
        $this->increment('clicks');
        $this->update(['last_click_at' => now()]);
        
        return $this;
    }

    /**
     * Mark the referral as converted.
     *
     * @param float $amount
     * @param int|null $paymentId
     * @param int|null $courseId
     * @param int|null $userId
     * @return $this
     */
    public function markAsConverted($amount, $paymentId = null, $courseId = null, $userId = null)
    {
        $data = [
            'status' => self::STATUS_CONVERTED,
            'amount' => $amount,
            'conversion_date' => now()
        ];
        
        if ($paymentId) {
            $data['payment_id'] = $paymentId;
        }
        
        if ($courseId) {
            $data['course_id'] = $courseId;
        }
        
        if ($userId) {
            $data['referred_user_id'] = $userId;
        }
        
        // Calculate commission
        $commissionRate = $this->affiliate->custom_commission_rate 
            ? $this->affiliate->commission_rate 
            : $this->program->commission_rate;
            
        $commissionType = $this->program->commission_type;
        
        if ($commissionType === AffiliateProgram::TYPE_PERCENTAGE) {
            $commissionAmount = ($amount * $commissionRate) / 100;
        } else {
            $commissionAmount = $commissionRate;
        }
        
        $data['commission_rate'] = $commissionRate;
        $data['commission_amount'] = $commissionAmount;
        
        $this->update($data);
        
        // Update affiliate stats
        $this->affiliate->recordConversion($amount);
        
        return $this;
    }

    /**
     * Mark the referral as completed.
     *
     * @return $this
     */
    public function markAsCompleted()
    {
        if ($this->status !== self::STATUS_CONVERTED) {
            return $this;
        }
        
        $this->update(['status' => self::STATUS_COMPLETED]);
        
        return $this;
    }

    /**
     * Mark the referral as canceled.
     *
     * @param string|null $reason
     * @return $this
     */
    public function markAsCanceled($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELED,
            'notes' => $reason
        ]);
        
        return $this;
    }

    /**
     * Mark the referral as refunded.
     *
     * @return $this
     */
    public function markAsRefunded()
    {
        if (!in_array($this->status, [self::STATUS_CONVERTED, self::STATUS_COMPLETED])) {
            return $this;
        }
        
        $this->update(['status' => self::STATUS_REFUNDED]);
        
        // Update affiliate balance if already added
        if ($this->status === self::STATUS_COMPLETED) {
            $this->affiliate->deductBalance($this->commission_amount);
        }
        
        return $this;
    }

    /**
     * Check if the referral has expired.
     *
     * @return bool
     */
    public function hasExpired()
    {
        if ($this->cookie_expiry) {
            return now()->isAfter($this->cookie_expiry);
        }
        
        return false;
    }

    /**
     * Mark the referral as expired.
     *
     * @return $this
     */
    public function markAsExpired()
    {
        if ($this->status === self::STATUS_PENDING) {
            $this->update(['status' => self::STATUS_EXPIRED]);
        }
        
        return $this;
    }

    /**
     * Check if the referral is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the referral is converted.
     *
     * @return bool
     */
    public function isConverted()
    {
        return $this->status === self::STATUS_CONVERTED;
    }

    /**
     * Check if the referral is completed.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the referral is canceled.
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->status === self::STATUS_CANCELED;
    }

    /**
     * Check if the referral is refunded.
     *
     * @return bool
     */
    public function isRefunded()
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Scope a query to only include pending referrals.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include converted referrals.
     */
    public function scopeConverted($query)
    {
        return $query->where('status', self::STATUS_CONVERTED);
    }

    /**
     * Scope a query to only include completed referrals.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include canceled referrals.
     */
    public function scopeCanceled($query)
    {
        return $query->where('status', self::STATUS_CANCELED);
    }

    /**
     * Scope a query to only include refunded referrals.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', self::STATUS_REFUNDED);
    }

    /**
     * Scope a query to only include expired referrals.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    /**
     * Scope a query to only include successful referrals (converted or completed).
     */
    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', [self::STATUS_CONVERTED, self::STATUS_COMPLETED]);
    }

    /**
     * Scope a query to only include referrals for a specific affiliate.
     */
    public function scopeForAffiliate($query, $affiliateId)
    {
        return $query->where('affiliate_id', $affiliateId);
    }

    /**
     * Scope a query to only include referrals for a specific course.
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to only include referrals from a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
