<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'coupon_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'type',
        'value',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_to',
        'is_active',
        'courses_applicable',
        'minimum_order_amount',
        'created_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'is_active' => 'boolean',
        'courses_applicable' => 'array',
        'minimum_order_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the admin who created the coupon.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the payment coupons that use this coupon.
     */
    public function paymentCoupons(): HasMany
    {
        return $this->hasMany(PaymentCoupon::class, 'coupon_id', 'coupon_id');
    }

    /**
     * Check if the coupon is valid.
     */
    public function isValid()
    {
        $now = now();
        return $this->is_active && 
            ($this->valid_from === null || $now->gte($this->valid_from)) &&
            ($this->valid_to === null || $now->lte($this->valid_to)) &&
            ($this->max_uses === null || $this->used_count < $this->max_uses);
    }

    /**
     * Calculate the discount amount for a given subtotal.
     */
    public function calculateDiscount($subtotal)
    {
        if (!$this->isValid() || $subtotal < $this->minimum_order_amount) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return ($subtotal * $this->value) / 100;
        } else {
            return min($this->value, $subtotal);
        }
    }

    /**
     * Increment the used count.
     */
    public function incrementUsed()
    {
        $this->increment('used_count');
        return $this;
    }
}
