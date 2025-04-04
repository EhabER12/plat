<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ForumTopic extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'topic_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'user_id',
        'title',
        'slug',
        'content',
        'status',
        'is_pinned',
        'is_locked',
        'last_post_at',
        'last_post_user_id',
        'views_count'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'last_post_at' => 'datetime',
        'views_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($topic) {
            if (empty($topic->slug)) {
                $topic->slug = Str::slug($topic->title);
            }
        });
    }

    /**
     * Get the course this topic belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the user who created the topic.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the user who made the last post in this topic.
     */
    public function lastPostUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_post_user_id', 'user_id');
    }

    /**
     * Get the posts in this topic.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'topic_id', 'topic_id');
    }

    /**
     * Get the number of posts in this topic.
     */
    public function getPostsCountAttribute()
    {
        return $this->posts()->count();
    }

    /**
     * Check if the topic is locked.
     */
    public function isLocked()
    {
        return $this->is_locked;
    }

    /**
     * Check if the topic is pinned.
     */
    public function isPinned()
    {
        return $this->is_pinned;
    }

    /**
     * Increment the view count.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
        return $this;
    }

    /**
     * Lock the topic.
     */
    public function lock()
    {
        $this->update(['is_locked' => true]);
        return $this;
    }

    /**
     * Unlock the topic.
     */
    public function unlock()
    {
        $this->update(['is_locked' => false]);
        return $this;
    }

    /**
     * Pin the topic.
     */
    public function pin()
    {
        $this->update(['is_pinned' => true]);
        return $this;
    }

    /**
     * Unpin the topic.
     */
    public function unpin()
    {
        $this->update(['is_pinned' => false]);
        return $this;
    }

    /**
     * Update last post information.
     */
    public function updateLastPost($postId, $userId)
    {
        $this->update([
            'last_post_at' => now(),
            'last_post_user_id' => $userId
        ]);
        return $this;
    }

    /**
     * Scope a query to only include active topics.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include pinned topics first.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('is_pinned', 'desc')
                    ->orderBy('last_post_at', 'desc');
    }
}
