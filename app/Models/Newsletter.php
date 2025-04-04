<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Newsletter extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'subscriber_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'user_id',
        'is_active',
        'subscription_date',
        'unsubscribed_at',
        'verification_token',
        'verified_at',
        'categories',
        'tags',
        'preferences',
        'source',
        'ip_address',
        'user_agent',
        'last_activity'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'subscription_date' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'verified_at' => 'datetime',
        'categories' => 'array',
        'tags' => 'array',
        'preferences' => 'array',
        'last_activity' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user associated with this subscription (if any).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Check if the subscription is verified.
     *
     * @return bool
     */
    public function isVerified()
    {
        return $this->verified_at !== null;
    }

    /**
     * Check if the subscription is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active && $this->unsubscribed_at === null;
    }

    /**
     * Verify the subscription.
     *
     * @return $this
     */
    public function verify()
    {
        $this->update([
            'verified_at' => now(),
            'verification_token' => null,
            'is_active' => true
        ]);
        
        return $this;
    }

    /**
     * Unsubscribe the email.
     *
     * @return $this
     */
    public function unsubscribe()
    {
        $this->update([
            'is_active' => false,
            'unsubscribed_at' => now()
        ]);
        
        return $this;
    }

    /**
     * Resubscribe the email.
     *
     * @return $this
     */
    public function resubscribe()
    {
        $this->update([
            'is_active' => true,
            'unsubscribed_at' => null
        ]);
        
        return $this;
    }

    /**
     * Generate a verification token.
     *
     * @return string
     */
    public function generateVerificationToken()
    {
        $token = md5($this->email . time() . uniqid());
        $this->update(['verification_token' => $token]);
        
        return $token;
    }

    /**
     * Update subscriber preferences.
     *
     * @param array $preferences
     * @return $this
     */
    public function updatePreferences(array $preferences)
    {
        $this->update(['preferences' => $preferences]);
        
        return $this;
    }

    /**
     * Update subscriber categories.
     *
     * @param array $categories
     * @return $this
     */
    public function updateCategories(array $categories)
    {
        $this->update(['categories' => $categories]);
        
        return $this;
    }

    /**
     * Update subscriber tags.
     *
     * @param array $tags
     * @return $this
     */
    public function updateTags(array $tags)
    {
        $this->update(['tags' => $tags]);
        
        return $this;
    }

    /**
     * Add a tag to the subscriber.
     *
     * @param string $tag
     * @return $this
     */
    public function addTag($tag)
    {
        $tags = $this->tags ?? [];
        
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
        
        return $this;
    }

    /**
     * Remove a tag from the subscriber.
     *
     * @param string $tag
     * @return $this
     */
    public function removeTag($tag)
    {
        $tags = $this->tags ?? [];
        
        if (($key = array_search($tag, $tags)) !== false) {
            unset($tags[$key]);
            $this->update(['tags' => array_values($tags)]);
        }
        
        return $this;
    }

    /**
     * Check if a subscriber has a specific tag.
     *
     * @param string $tag
     * @return bool
     */
    public function hasTag($tag)
    {
        return in_array($tag, $this->tags ?? []);
    }

    /**
     * Record activity for the subscriber.
     *
     * @return $this
     */
    public function recordActivity()
    {
        $this->update(['last_activity' => now()]);
        
        return $this;
    }

    /**
     * Scope a query to only include active subscribers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->whereNull('unsubscribed_at');
    }

    /**
     * Scope a query to only include verified subscribers.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Scope a query to only include unverified subscribers.
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    /**
     * Scope a query to only include unsubscribed subscribers.
     */
    public function scopeUnsubscribed($query)
    {
        return $query->where('is_active', false)
                    ->whereNotNull('unsubscribed_at');
    }

    /**
     * Scope a query to only include subscribers with a specific tag.
     */
    public function scopeWithTag($query, $tag)
    {
        return $query->where('tags', 'like', '%"' . $tag . '"%');
    }

    /**
     * Scope a query to only include subscribers in a specific category.
     */
    public function scopeInCategory($query, $category)
    {
        return $query->where('categories', 'like', '%"' . $category . '"%');
    }

    /**
     * Scope a query to only include subscribers from a specific source.
     */
    public function scopeFromSource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope a query to only include recently active subscribers.
     */
    public function scopeRecentlyActive($query, $days = 30)
    {
        return $query->where('last_activity', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include subscribers who haven't been active recently.
     */
    public function scopeInactive($query, $days = 90)
    {
        return $query->where(function($q) use ($days) {
            $q->where('last_activity', '<', now()->subDays($days))
              ->orWhereNull('last_activity');
        });
    }

    /**
     * Create a new subscriber from an email address.
     *
     * @param string $email
     * @param string|null $name
     * @param array $options
     * @return Newsletter
     */
    public static function subscribe($email, $name = null, array $options = [])
    {
        $defaults = [
            'verification_token' => md5($email . time() . uniqid()),
            'subscription_date' => now(),
            'is_active' => !config('newsletter.require_verification', true),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'source' => 'website'
        ];
        
        $data = array_merge($defaults, $options, [
            'email' => $email,
            'name' => $name
        ]);
        
        return self::updateOrCreate(
            ['email' => $email],
            $data
        );
    }
}
