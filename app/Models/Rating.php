<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'rating_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'student_id',
        'rating_value',
        'review_text',
        'is_published',
        'admin_review_status',
        'admin_review_notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating_value' => 'float',
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the course that was rated.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the student who left the rating.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Scope a query to only include published ratings.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include ratings pending admin review.
     */
    public function scopePendingReview($query)
    {
        return $query->where('admin_review_status', 'pending');
    }

    /**
     * Approve the rating.
     */
    public function approve($notes = null)
    {
        $this->update([
            'admin_review_status' => 'approved',
            'is_published' => true,
            'admin_review_notes' => $notes ?? $this->admin_review_notes
        ]);
        return $this;
    }

    /**
     * Reject the rating.
     */
    public function reject($notes = null)
    {
        $this->update([
            'admin_review_status' => 'rejected',
            'is_published' => false,
            'admin_review_notes' => $notes ?? $this->admin_review_notes
        ]);
        return $this;
    }
}
