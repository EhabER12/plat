<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'plan_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'duration_days',
        'is_active',
        'features',
        'max_courses',
        'max_downloads',
        'has_certificate_access',
        'has_forum_access',
        'trial_days',
        'sort_order',
        'stripe_plan_id',
        'paypal_plan_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'is_active' => 'boolean',
        'features' => 'array',
        'max_courses' => 'integer',
        'max_downloads' => 'integer',
        'has_certificate_access' => 'boolean',
        'has_forum_access' => 'boolean',
        'trial_days' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the subscriptions for this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id', 'plan_id');
    }

    /**
     * Get the formatted price with currency.
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ' . strtoupper($this->currency);
    }

    /**
     * Get the duration in months (approximate).
     */
    public function getDurationInMonthsAttribute()
    {
        return round($this->duration_days / 30);
    }

    /**
     * Get the monthly price (calculated if plan is longer than a month).
     */
    public function getMonthlyPriceAttribute()
    {
        if ($this->duration_days <= 0) {
            return $this->price;
        }
        
        $months = max(1, $this->duration_in_months);
        return round($this->price / $months, 2);
    }

    /**
     * Get the formatted monthly price with currency.
     */
    public function getFormattedMonthlyPriceAttribute()
    {
        return number_format($this->monthly_price, 2) . ' ' . strtoupper($this->currency);
    }

    /**
     * Check if the plan has a trial period.
     */
    public function hasTrial()
    {
        return $this->trial_days > 0;
    }

    /**
     * Get the trial period description.
     */
    public function getTrialDescriptionAttribute()
    {
        if (!$this->hasTrial()) {
            return 'No trial';
        }
        
        return $this->trial_days . ' day' . ($this->trial_days > 1 ? 's' : '') . ' free trial';
    }

    /**
     * Get the duration description (e.g. "1 month", "1 year").
     */
    public function getDurationDescriptionAttribute()
    {
        if ($this->duration_days <= 0) {
            return 'Lifetime';
        }
        
        if ($this->duration_days <= 31) {
            return '1 month';
        }
        
        if ($this->duration_days <= 90) {
            return '3 months';
        }
        
        if ($this->duration_days <= 186) {
            return '6 months';
        }
        
        if ($this->duration_days <= 366) {
            return '1 year';
        }
        
        $years = floor($this->duration_days / 365);
        return $years . ' year' . ($years > 1 ? 's' : '');
    }

    /**
     * Get the active subscribers count.
     */
    public function getActiveSubscribersCountAttribute()
    {
        return $this->subscriptions()
                    ->where('status', Subscription::STATUS_ACTIVE)
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                    })
                    ->count();
    }

    /**
     * Scope a query to only include active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include plans with trials.
     */
    public function scopeWithTrial($query)
    {
        return $query->where('trial_days', '>', 0);
    }

    /**
     * Scope a query to order by price.
     */
    public function scopeOrderByPrice($query, $direction = 'asc')
    {
        return $query->orderBy('price', $direction);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrderBySortOrder($query)
    {
        return $query->orderBy('sort_order');
    }
}
