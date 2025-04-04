<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'announcement_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'created_by',
        'course_id',
        'published_at',
        'expires_at',
        'is_system_wide',
        'is_important',
        'target_roles'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_system_wide' => 'boolean',
        'is_important' => 'boolean',
        'target_roles' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who created the announcement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the course this announcement belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Check if the announcement is currently active.
     */
    public function isActive()
    {
        $now = now();
        return ($this->published_at === null || $now->gte($this->published_at)) &&
               ($this->expires_at === null || $now->lte($this->expires_at));
    }

    /**
     * Check if the announcement is expired.
     */
    public function isExpired()
    {
        return $this->expires_at !== null && now()->gt($this->expires_at);
    }

    /**
     * Check if the announcement is for a specific user role.
     */
    public function isForRole($role)
    {
        return $this->target_roles === null || 
               (is_array($this->target_roles) && in_array($role, $this->target_roles));
    }

    /**
     * Check if the announcement is for a specific user.
     */
    public function isVisibleTo($user)
    {
        // System-wide announcements are visible to all users unless role-restricted
        if ($this->is_system_wide) {
            return $this->isForRole($user->role);
        }
        
        // Course-specific announcements are visible to enrolled students and course instructors
        if ($this->course_id) {
            return ($user->role === 'instructor' && $user->user_id === $this->course->instructor_id) ||
                   ($user->role === 'student' && $user->enrollments()->where('course_id', $this->course_id)->exists());
        }
        
        return false;
    }

    /**
     * Scope a query to only include active announcements.
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where(function($q) use ($now) {
            $q->whereNull('published_at')
              ->orWhere('published_at', '<=', $now);
        })->where(function($q) use ($now) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>=', $now);
        });
    }

    /**
     * Scope a query to only include system-wide announcements.
     */
    public function scopeSystemWide($query)
    {
        return $query->where('is_system_wide', true);
    }

    /**
     * Scope a query to only include important announcements.
     */
    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    /**
     * Scope a query to only include announcements for a specific course.
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to only include announcements for specific roles.
     */
    public function scopeForRole($query, $role)
    {
        return $query->where(function($q) use ($role) {
            $q->whereNull('target_roles')
              ->orWhereJsonContains('target_roles', $role);
        });
    }
}
