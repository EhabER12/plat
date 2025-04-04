<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'payment_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'course_id',
        'amount',
        'payment_method',
        'payment_date',
        'status',
        'transaction_id',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the student who made the payment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Get the course that was paid for.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the coupons applied to this payment.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(PaymentCoupon::class, 'payment_id', 'payment_id');
    }

    /**
     * Get the financial ledger entries for this payment.
     */
    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(FinancialLedger::class, 'payment_id', 'payment_id');
    }

    /**
     * Calculate the total discount amount.
     */
    public function getTotalDiscountAttribute()
    {
        return $this->coupons()->sum('discount_amount');
    }

    /**
     * Calculate the net amount after discounts.
     */
    public function getNetAmountAttribute()
    {
        return $this->amount - $this->total_discount;
    }
}
