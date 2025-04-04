<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'cart_item_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'price',
        'discount_price',
        'coupon_code',
        'added_at',
        'session_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'added_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who owns the cart item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the course in the cart.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the applied coupon.
     */
    public function coupon()
    {
        if (!$this->coupon_code) {
            return null;
        }
        
        return Coupon::where('code', $this->coupon_code)->first();
    }

    /**
     * Get the final price after discounts.
     */
    public function getFinalPriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    /**
     * Get the discount amount.
     */
    public function getDiscountAmountAttribute()
    {
        if ($this->discount_price === null || $this->price <= 0) {
            return 0;
        }
        
        return $this->price - $this->discount_price;
    }

    /**
     * Get the discount percentage.
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->discount_price === null || $this->price <= 0) {
            return 0;
        }
        
        return round(($this->discount_amount / $this->price) * 100, 2);
    }

    /**
     * Check if the item has a discount.
     */
    public function hasDiscount()
    {
        return $this->discount_price !== null && $this->discount_price < $this->price;
    }

    /**
     * Apply a coupon to the cart item.
     */
    public function applyCoupon($couponCode)
    {
        $coupon = Coupon::where('code', $couponCode)
                        ->where('is_active', true)
                        ->first();
        
        if (!$coupon || !$coupon->isValid()) {
            return false;
        }
        
        // Check if coupon applies to this course
        if (!empty($coupon->courses_applicable) && 
            !in_array($this->course_id, $coupon->courses_applicable)) {
            return false;
        }
        
        $discountAmount = $coupon->calculateDiscount($this->price);
        $discountedPrice = $this->price - $discountAmount;
        
        $this->update([
            'coupon_code' => $couponCode,
            'discount_price' => max(0, $discountedPrice)
        ]);
        
        // Increment the coupon usage
        $coupon->incrementUsed();
        
        return true;
    }

    /**
     * Remove the applied coupon.
     */
    public function removeCoupon()
    {
        $this->update([
            'coupon_code' => null,
            'discount_price' => null
        ]);
        
        return $this;
    }

    /**
     * Scope a query to only include cart items for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include cart items for a specific session.
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope a query to order by most recently added.
     */
    public function scopeRecentlyAdded($query)
    {
        return $query->orderBy('added_at', 'desc');
    }
}
