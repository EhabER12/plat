<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogComment extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'comment_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'is_approved',
        'author_name',
        'author_email',
        'ip_address'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the blog post that owns the comment.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'post_id', 'post_id');
    }

    /**
     * Get the user who wrote the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the parent comment.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlogComment::class, 'parent_id', 'comment_id');
    }

    /**
     * Get the child comments.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'parent_id', 'comment_id');
    }

    /**
     * Approve the comment.
     */
    public function approve()
    {
        $this->update(['is_approved' => true]);
        return $this;
    }

    /**
     * Disapprove the comment.
     */
    public function disapprove()
    {
        $this->update(['is_approved' => false]);
        return $this;
    }

    /**
     * Get the author name (either registered user or guest).
     */
    public function getAuthorNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        
        return $this->attributes['author_name'] ?? 'Anonymous';
    }

    /**
     * Check if the comment is from a registered user.
     */
    public function isFromRegisteredUser()
    {
        return $this->user_id !== null;
    }

    /**
     * Check if the comment has replies.
     */
    public function hasReplies()
    {
        return $this->replies()->count() > 0;
    }

    /**
     * Scope a query to only include approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include comments that need moderation.
     */
    public function scopeNeedsModeration($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope a query to only include top-level comments.
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
