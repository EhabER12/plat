<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumPost extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'post_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'topic_id',
        'user_id',
        'content',
        'is_solution',
        'parent_id',
        'ip_address',
        'edited_at',
        'edited_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_solution' => 'boolean',
        'edited_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the topic this post belongs to.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id', 'topic_id');
    }

    /**
     * Get the user who created the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the user who edited the post.
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by', 'user_id');
    }

    /**
     * Get the parent post if this is a reply.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'parent_id', 'post_id');
    }

    /**
     * Get the replies to this post.
     */
    public function replies()
    {
        return $this->hasMany(ForumPost::class, 'parent_id', 'post_id');
    }

    /**
     * Check if the post has been edited.
     */
    public function isEdited()
    {
        return $this->edited_at !== null;
    }

    /**
     * Check if the post is marked as a solution.
     */
    public function isSolution()
    {
        return $this->is_solution;
    }

    /**
     * Mark this post as a solution.
     */
    public function markAsSolution()
    {
        $this->update(['is_solution' => true]);
        return $this;
    }

    /**
     * Unmark this post as a solution.
     */
    public function unmarkAsSolution()
    {
        $this->update(['is_solution' => false]);
        return $this;
    }

    /**
     * Edit the post content.
     */
    public function edit($newContent, $editorId)
    {
        $this->update([
            'content' => $newContent,
            'edited_at' => now(),
            'edited_by' => $editorId
        ]);
        
        return $this;
    }

    /**
     * Get the plain text content of the post.
     */
    public function getPlainTextContentAttribute()
    {
        return strip_tags($this->content);
    }

    /**
     * Check if the post is a reply to another post.
     */
    public function isReply()
    {
        return $this->parent_id !== null;
    }

    /**
     * Update the corresponding topic's last post information.
     */
    public function updateTopicLastPost()
    {
        if ($this->topic) {
            $this->topic->updateLastPost($this->post_id, $this->user_id);
        }
        
        return $this;
    }

    /**
     * Scope a query to only include top-level posts (not replies).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the excerpt of the post content.
     */
    public function getExcerptAttribute($length = 100)
    {
        return \Str::limit(strip_tags($this->content), $length);
    }
}
