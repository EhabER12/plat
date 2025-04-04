<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'transaction_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'transaction_type',
        'reference_id',
        'reference_type',
        'gateway_transaction_id',
        'gateway_response',
        'description',
        'metadata',
        'ip_address'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Transaction status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_CANCELED = 'canceled';

    /**
     * Transaction type constants.
     */
    const TYPE_PAYMENT = 'payment';
    const TYPE_REFUND = 'refund';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_TRANSFER = 'transfer';

    /**
     * Get the user associated with the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the related model for this transaction.
     */
    public function reference(): MorphTo
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    /**
     * Check if the transaction is completed.
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the transaction has failed.
     */
    public function hasFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if the transaction is pending.
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the transaction has been refunded.
     */
    public function isRefunded()
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Mark the transaction as completed.
     */
    public function markAsCompleted($gatewayTransactionId = null, $gatewayResponse = null)
    {
        $data = ['status' => self::STATUS_COMPLETED];
        
        if ($gatewayTransactionId) {
            $data['gateway_transaction_id'] = $gatewayTransactionId;
        }
        
        if ($gatewayResponse) {
            $data['gateway_response'] = $gatewayResponse;
        }
        
        $this->update($data);
        return $this;
    }

    /**
     * Mark the transaction as failed.
     */
    public function markAsFailed($gatewayResponse = null)
    {
        $data = ['status' => self::STATUS_FAILED];
        
        if ($gatewayResponse) {
            $data['gateway_response'] = $gatewayResponse;
        }
        
        $this->update($data);
        return $this;
    }

    /**
     * Mark the transaction as refunded.
     */
    public function markAsRefunded($gatewayResponse = null)
    {
        $data = ['status' => self::STATUS_REFUNDED];
        
        if ($gatewayResponse) {
            $data['gateway_response'] = $gatewayResponse;
        }
        
        $this->update($data);
        return $this;
    }

    /**
     * Get the formatted amount with currency.
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . strtoupper($this->currency);
    }

    /**
     * Scope a query to only include transactions of a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include transactions of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope a query to only include successful transactions.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include transactions for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
