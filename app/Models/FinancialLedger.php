<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialLedger extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'ledger_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'payment_id',
        'withdrawal_id',
        'amount',
        'type',
        'description',
        'transaction_date',
        'balance_after',
        'reference_id',
        'reference_type'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user associated with this ledger entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the payment associated with this ledger entry.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Get the withdrawal associated with this ledger entry.
     */
    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(Withdrawal::class, 'withdrawal_id', 'withdrawal_id');
    }

    /**
     * Scope a query to only include credit entries.
     */
    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    /**
     * Scope a query to only include debit entries.
     */
    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }

    /**
     * Check if the entry is a credit.
     */
    public function isCredit()
    {
        return $this->type === 'credit';
    }

    /**
     * Check if the entry is a debit.
     */
    public function isDebit()
    {
        return $this->type === 'debit';
    }

    /**
     * Get the formatted amount with sign.
     */
    public function getFormattedAmountAttribute()
    {
        return $this->isCredit() ? '+' . $this->amount : '-' . $this->amount;
    }
}
