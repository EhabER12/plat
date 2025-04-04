<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'testimonial_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'position',
        'company',
        'avatar',
        'content',
        'rating',
        'course_id',
        'approved_at',
        'approved_by',
        'display_order',
        'is_featured',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'display_order' => 'integer',
        'is_featured' => 'boolean',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the user that gave the testimonial.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the course this testimonial is for.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the user who approved this testimonial.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    /**
     * Check if the testimonial is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the testimonial is approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the testimonial is rejected.
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Approve the testimonial.
     *
     * @param int $approverId
     * @return $this
     */
    public function approve($approverId)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approverId,
            'approved_at' => now()
        ]);

        return $this;
    }

    /**
     * Reject the testimonial.
     *
     * @param int $approverId
     * @return $this
     */
    public function reject($approverId)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $approverId,
            'approved_at' => now()
        ]);

        return $this;
    }

    /**
     * Toggle the featured status of the testimonial.
     *
     * @return $this
     */
    public function toggleFeatured()
    {
        $this->update([
            'is_featured' => !$this->is_featured
        ]);

        return $this;
    }

    /**
     * Get the avatar URL.
     *
     * @return string|null
     */
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return null;
        }

        // Check if the avatar is a URL
        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        // Assume it's a media library id
        $media = MediaLibrary::find($this->avatar);
        if ($media) {
            return $media->url;
        }

        // Assume it's a path relative to the storage
        return asset('storage/' . $this->avatar);
    }

    /**
     * Get a short excerpt of the testimonial content.
     *
     * @param int $length
     * @return string
     */
    public function getExcerpt($length = 100)
    {
        return \Str::limit($this->content, $length);
    }

    /**
     * Scope a query to only include pending testimonials.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved testimonials.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include rejected testimonials.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to only include featured testimonials.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Scope a query to only include testimonials for a specific course.
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to only include testimonials by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include testimonials with a minimum rating.
     */
    public function scopeWithMinRating($query, $rating)
    {
        return $query->where('rating', '>=', $rating);
    }
}
