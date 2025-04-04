<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'affiliate_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'program_id',
        'referral_code',
        'commission_rate',
        'custom_commission_rate',
        'status',
        'payment_method',
        'payment_details',
        'balance',
        'total_earnings',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'clicks',
        'conversions',
        'last_activity',
        'website',
        'promotional_methods',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'commission_rate' => 'decimal:2',
        'custom_commission_rate' => 'boolean',
        'balance' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'approved_at' => 'datetime',
        'clicks' => 'integer',
        'conversions' => 'integer',
        'last_activity' => 'datetime',
        'payment_details' => 'array',
        'promotional_methods' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Get the user who is the affiliate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the program this affiliate belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(AffiliateProgram::class, 'program_id', 'program_id');
    }

    /**
     * Get the user who approved this affiliate.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    /**
     * Get the referrals created by this affiliate.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(AffiliateReferral::class, 'affiliate_id', 'affiliate_id');
    }

    /**
     * Get the payouts made to this affiliate.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(AffiliatePayout::class, 'affiliate_id', 'affiliate_id');
    }

    /**
     * Generate a unique referral code.
     *
     * @return string
     */
    public static function generateReferralCode()
    {
        do {
            $code = Str::random(10);
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Get the referral URL.
     *
     * @param int|null $courseId
     * @return string
     */
    public function getReferralUrl($courseId = null)
    {
        $baseUrl = config('app.url') . '/ref/' . $this->referral_code;
        
        if ($courseId) {
            return $baseUrl . '?course=' . $courseId;
        }
        
        return $baseUrl;
    }

    /**
     * Calculate the commission for a given amount.
     *
     * @param float $amount
     * @return float
     */
    public function calculateCommission($amount)
    {
        if ($this->custom_commission_rate) {
            if ($this->program->commission_type === AffiliateProgram::TYPE_PERCENTAGE) {
                return ($amount * $this->commission_rate) / 100;
            }
            
            return $this->commission_rate;
        }
        
        return $this->program->calculateCommission($amount);
    }

    /**
     * Approve the affiliate.
     *
     * @param int $approverId
     * @return $this
     */
    public function approve($approverId)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approverId,
            'approved_at' => now()
        ]);
        
        return $this;
    }

    /**
     * Reject the affiliate.
     *
     * @param int $approverId
     * @param string $reason
     * @return $this
     */
    public function reject($approverId, $reason)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $approverId,
            'approved_at' => now(),
            'rejection_reason' => $reason
        ]);
        
        return $this;
    }

    /**
     * Suspend the affiliate.
     *
     * @param string $reason
     * @return $this
     */
    public function suspend($reason)
    {
        $this->update([
            'status' => self::STATUS_SUSPENDED,
            'rejection_reason' => $reason
        ]);
        
        return $this;
    }

    /**
     * Record a click.
     *
     * @return $this
     */
    public function recordClick()
    {
        $this->increment('clicks');
        $this->update(['last_activity' => now()]);
        
        return $this;
    }

    /**
     * Record a conversion.
     *
     * @param float $amount
     * @return $this
     */
    public function recordConversion($amount)
    {
        $commission = $this->calculateCommission($amount);
        
        $this->increment('conversions');
        $this->increment('balance', $commission);
        $this->increment('total_earnings', $commission);
        $this->update(['last_activity' => now()]);
        
        return $this;
    }

    /**
     * Deduct from balance after payout.
     *
     * @param float $amount
     * @return $this
     */
    public function deductBalance($amount)
    {
        $this->decrement('balance', $amount);
        
        return $this;
    }

    /**
     * Get the conversion rate.
     *
     * @return float
     */
    public function getConversionRate()
    {
        if ($this->clicks == 0) {
            return 0;
        }
        
        return ($this->conversions / $this->clicks) * 100;
    }

    /**
     * Check if the affiliate has enough balance for payout.
     *
     * @return bool
     */
    public function hasMinimumPayout()
    {
        return $this->balance >= $this->program->min_payout;
    }

    /**
     * Check if the affiliate is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the affiliate is approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the affiliate is rejected.
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if the affiliate is suspended.
     *
     * @return bool
     */
    public function isSuspended()
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Scope a query to only include pending affiliates.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved affiliates.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include rejected affiliates.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to only include suspended affiliates.
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    /**
     * Scope a query to only include affiliates with a minimum balance.
     */
    public function scopeWithMinBalance($query, $amount)
    {
        return $query->where('balance', '>=', $amount);
    }

    /**
     * Scope a query to only include active affiliates.
     */
    public function scopeActive($query, $days = 30)
    {
        return $query->where('last_activity', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to order by total earnings.
     */
    public function scopeTopEarners($query)
    {
        return $query->orderBy('total_earnings', 'desc');
    }
}
