<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'discount_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'discount_type',  // 'percentage' or 'fixed'
        'discount_value', // percentage or fixed amount
        'applies_to_all_courses',
        'courses',  // JSON array of course IDs
        'start_date',
        'end_date',
        'is_active',
        'created_by', // user_id of admin or instructor who created it
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_value' => 'decimal:2',
        'applies_to_all_courses' => 'boolean',
        'courses' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who created the discount.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Check if the discount is valid.
     */
    public function isValid()
    {
        $now = now();
        return $this->is_active && 
            ($this->start_date === null || $now->gte($this->start_date)) &&
            ($this->end_date === null || $now->lte($this->end_date));
    }

    /**
     * Calculate the discounted price for a given price.
     */
    public function calculateDiscountedPrice($price)
    {
        if (!$this->isValid()) {
            return $price;
        }

        if ($this->discount_type === 'percentage') {
            return $price - ($price * $this->discount_value / 100);
        } else {
            return max(0, $price - $this->discount_value);
        }
    }

    /**
     * Check if the discount applies to a specific course.
     */
    public function appliesTo($courseId)
    {
        if (!$this->isValid()) {
            return false;
        }
        
        return $this->applies_to_all_courses || 
            (is_array($this->courses) && in_array($courseId, $this->courses));
    }
}
