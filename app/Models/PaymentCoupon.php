<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentCoupon extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'payment_coupon_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',
        'coupon_id',
        'discount_amount',
        'coupon_code',
        'discount_percentage'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the payment that this coupon is applied to.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Get the coupon that was used.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'coupon_id');
    }

    /**
     * Calculate the discount amount if it's not already set.
     */
    public function calculateDiscountAmount($originalAmount = null)
    {
        if ($this->discount_amount > 0) {
            return $this->discount_amount;
        }

        if ($originalAmount === null) {
            $originalAmount = $this->payment->amount ?? 0;
        }

        $discountAmount = ($originalAmount * $this->discount_percentage) / 100;
        $this->update(['discount_amount' => $discountAmount]);
        
        return $discountAmount;
    }
}
