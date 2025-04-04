<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliatePayout extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'payout_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'affiliate_id',
        'program_id',
        'amount',
        'fee',
        'tax',
        'net_amount',
        'payment_method',
        'payment_details',
        'status',
        'transaction_id',
        'reference',
        'request_date',
        'processed_date',
        'processed_by',
        'notes',
        'receipt_file',
        'period_start',
        'period_end'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'tax' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'payment_details' => 'array',
        'request_date' => 'datetime',
        'processed_date' => 'datetime',
        'period_start' => 'date',
        'period_end' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELED = 'canceled';

    /**
     * Get the affiliate this payout belongs to.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class, 'affiliate_id', 'affiliate_id');
    }

    /**
     * Get the program this payout belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(AffiliateProgram::class, 'program_id', 'program_id');
    }

    /**
     * Get the admin who processed this payout.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }

    /**
     * Get the transaction record for this payout.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'transaction_id');
    }

    /**
     * Get the referrals included in this payout.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(AffiliateReferral::class, 'payout_id', 'payout_id');
    }

    /**
     * Mark the payout as processing.
     *
     * @param int $adminId
     * @return $this
     */
    public function markAsProcessing($adminId)
    {
        $this->update([
            'status' => self::STATUS_PROCESSING,
            'processed_by' => $adminId
        ]);
        
        return $this;
    }

    /**
     * Mark the payout as completed.
     *
     * @param int $adminId
     * @param string|null $transactionId
     * @param string|null $notes
     * @return $this
     */
    public function markAsCompleted($adminId, $transactionId = null, $notes = null)
    {
        $data = [
            'status' => self::STATUS_COMPLETED,
            'processed_by' => $adminId,
            'processed_date' => now()
        ];
        
        if ($transactionId) {
            $data['transaction_id'] = $transactionId;
        }
        
        if ($notes) {
            $data['notes'] = $notes;
        }
        
        $this->update($data);
        
        // Deduct balance from affiliate
        $this->affiliate->deductBalance($this->amount);
        
        return $this;
    }

    /**
     * Mark the payout as failed.
     *
     * @param int $adminId
     * @param string $reason
     * @return $this
     */
    public function markAsFailed($adminId, $reason)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'processed_by' => $adminId,
            'processed_date' => now(),
            'notes' => $reason
        ]);
        
        return $this;
    }

    /**
     * Mark the payout as canceled.
     *
     * @param int $adminId
     * @param string $reason
     * @return $this
     */
    public function markAsCanceled($adminId, $reason)
    {
        $this->update([
            'status' => self::STATUS_CANCELED,
            'processed_by' => $adminId,
            'processed_date' => now(),
            'notes' => $reason
        ]);
        
        return $this;
    }

    /**
     * Calculate the net amount (amount - fees - tax).
     *
     * @return float
     */
    public function calculateNetAmount()
    {
        return $this->amount - ($this->fee ?? 0) - ($this->tax ?? 0);
    }

    /**
     * Get the receipt URL if available.
     *
     * @return string|null
     */
    public function getReceiptUrl()
    {
        if (!$this->receipt_file) {
            return null;
        }
        
        // Check if the receipt is a media library id
        $media = MediaLibrary::find($this->receipt_file);
        if ($media) {
            return $media->url;
        }
        
        // Assume it's a path relative to the storage
        return asset('storage/' . $this->receipt_file);
    }

    /**
     * Check if the payout is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the payout is processing.
     *
     * @return bool
     */
    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Check if the payout is completed.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the payout has failed.
     *
     * @return bool
     */
    public function hasFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if the payout is canceled.
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->status === self::STATUS_CANCELED;
    }

    /**
     * Scope a query to only include pending payouts.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include processing payouts.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    /**
     * Scope a query to only include completed payouts.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include failed payouts.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope a query to only include canceled payouts.
     */
    public function scopeCanceled($query)
    {
        return $query->where('status', self::STATUS_CANCELED);
    }

    /**
     * Scope a query to only include payouts for a specific affiliate.
     */
    public function scopeForAffiliate($query, $affiliateId)
    {
        return $query->where('affiliate_id', $affiliateId);
    }

    /**
     * Scope a query to only include payouts for a specific program.
     */
    public function scopeForProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    /**
     * Scope a query to only include payouts from a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('request_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include payouts with a minimum amount.
     */
    public function scopeMinAmount($query, $amount)
    {
        return $query->where('amount', '>=', $amount);
    }

    /**
     * Scope a query to order by newest first.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('request_date', 'desc');
    }
}
