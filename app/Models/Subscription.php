<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'subscription_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'started_at',
        'expires_at',
        'canceled_at',
        'trial_ends_at',
        'payment_method',
        'renewal_reminder_sent',
        'gateway_subscription_id',
        'gateway_status',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'canceled_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'renewal_reminder_sent' => 'boolean',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Subscription status constants.
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELED = 'canceled';
    const STATUS_EXPIRED = 'expired';
    const STATUS_TRIAL = 'trial';
    const STATUS_PAST_DUE = 'past_due';

    /**
     * Get the user who owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the plan for this subscription.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id', 'plan_id');
    }

    /**
     * Check if the subscription is active.
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Check if the subscription is on trial.
     */
    public function onTrial()
    {
        return $this->status === self::STATUS_TRIAL && 
               $this->trial_ends_at !== null && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if the subscription is canceled.
     */
    public function isCanceled()
    {
        return $this->status === self::STATUS_CANCELED || 
               $this->canceled_at !== null;
    }

    /**
     * Check if the subscription has expired.
     */
    public function hasExpired()
    {
        return $this->status === self::STATUS_EXPIRED || 
               ($this->expires_at !== null && $this->expires_at->isPast());
    }

    /**
     * Cancel the subscription.
     */
    public function cancel()
    {
        $this->update([
            'status' => self::STATUS_CANCELED,
            'canceled_at' => now()
        ]);
        
        return $this;
    }

    /**
     * Renew the subscription.
     */
    public function renew($expiresAt = null)
    {
        if ($expiresAt === null) {
            $expiresAt = $this->plan ? now()->addDays($this->plan->duration_days) : now()->addMonth();
        }
        
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'started_at' => now(),
            'expires_at' => $expiresAt,
            'canceled_at' => null,
            'renewal_reminder_sent' => false
        ]);
        
        return $this;
    }

    /**
     * Get the days remaining in the subscription.
     */
    public function getDaysRemainingAttribute()
    {
        if ($this->hasExpired() || $this->expires_at === null) {
            return 0;
        }
        
        return now()->diffInDays($this->expires_at, false);
    }

    /**
     * Get the days used in the subscription period.
     */
    public function getDaysUsedAttribute()
    {
        if ($this->started_at === null) {
            return 0;
        }
        
        $endDate = $this->hasExpired() ? $this->expires_at : now();
        return $this->started_at->diffInDays($endDate);
    }

    /**
     * Send a renewal reminder for the subscription.
     */
    public function sendRenewalReminder()
    {
        if ($this->renewal_reminder_sent || $this->hasExpired() || $this->isCanceled()) {
            return false;
        }
        
        // Logic to send renewal reminder would go here
        // This could call a notification class to send an email
        
        $this->update(['renewal_reminder_sent' => true]);
        return true;
    }

    /**
     * Scope a query to only include active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope a query to only include trial subscriptions.
     */
    public function scopeOnTrial($query)
    {
        return $query->where('status', self::STATUS_TRIAL)
                    ->where('trial_ends_at', '>', now());
    }

    /**
     * Scope a query to only include canceled subscriptions.
     */
    public function scopeCanceled($query)
    {
        return $query->where('status', self::STATUS_CANCELED)
                    ->orWhereNotNull('canceled_at');
    }

    /**
     * Scope a query to only include expired subscriptions.
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', self::STATUS_EXPIRED)
              ->orWhere(function ($query) {
                  $query->whereNotNull('expires_at')
                        ->where('expires_at', '<=', now());
              });
        });
    }

    /**
     * Scope a query to only include subscriptions needing renewal reminders.
     */
    public function scopeNeedingRenewalReminder($query, $daysBeforeExpiration = 7)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->whereNull('canceled_at')
                    ->where('renewal_reminder_sent', false)
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '>', now())
                    ->where('expires_at', '<=', now()->addDays($daysBeforeExpiration));
    }
}
