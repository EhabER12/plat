<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'code',
        'type',
        'value',
        'description',
        'min_order_value',
        'max_discount_value',
        'usage_limit',
        'usage_count',
        'start_date',
        'end_date',
        'is_active',
        'created_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_discount_value' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
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
     * Get the courses associated with this discount.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'discount_courses', 'discount_id', 'course_id')
                    ->withTimestamps();
    }

    /**
     * Check if the discount applies to all courses.
     */
    public function applies_to_all_courses(): bool
    {
        // Check if this discount applies to all courses in the system
        return $this->courses()->count() == Course::count();
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

        if ($this->type === 'percentage') {
            return $price - ($price * $this->value / 100);
        } else {
            return max(0, $price - $this->value);
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
        
        return $this->applies_to_all_courses() || 
            $this->courses()->where('course_id', $courseId)->exists();
    }
}
