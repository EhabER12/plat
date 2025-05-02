<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

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
        'user_id',  // Added user_id as an alternative to student_id
        'rating_value',
        'rating',   // Added rating as an alternative to rating_value
        'review_text',
        'review',   // Added review as an alternative to review_text
        'comment',  // Added comment as another alternative
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
        'rating' => 'float',
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the student ID attribute.
     *
     * @return mixed
     */
    public function getStudentIdAttribute()
    {
        if ($this->attributes['student_id'] ?? null) {
            return $this->attributes['student_id'];
        }

        return $this->attributes['user_id'] ?? null;
    }

    /**
     * Get the rating value attribute.
     *
     * @return float
     */
    public function getRatingValueAttribute()
    {
        if ($this->attributes['rating_value'] ?? null) {
            return $this->attributes['rating_value'];
        }

        return $this->attributes['rating'] ?? 0;
    }

    /**
     * Get the review text attribute.
     *
     * @return string|null
     */
    public function getReviewTextAttribute()
    {
        if ($this->attributes['review_text'] ?? null) {
            return $this->attributes['review_text'];
        }

        if ($this->attributes['review'] ?? null) {
            return $this->attributes['review'];
        }

        return $this->attributes['comment'] ?? null;
    }

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
        // Check if student_id column exists in the table
        if (Schema::hasColumn('ratings', 'student_id')) {
            return $this->belongsTo(User::class, 'student_id', 'user_id');
        }
        // Fallback to user_id if student_id doesn't exist
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the user who left the rating (alias for student).
     */
    public function user(): BelongsTo
    {
        // Check if user_id column exists in the table
        if (Schema::hasColumn('ratings', 'user_id')) {
            return $this->belongsTo(User::class, 'user_id', 'user_id');
        }
        // Fallback to student_id if user_id doesn't exist
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Scope a query to only include published ratings.
     */
    public function scopePublished($query)
    {
        // Check if is_published column exists
        if (Schema::hasColumn('ratings', 'is_published')) {
            return $query->where('is_published', true);
        }

        return $query;
    }

    /**
     * Scope a query to only include ratings pending admin review.
     */
    public function scopePendingReview($query)
    {
        // Check if admin_review_status column exists
        if (Schema::hasColumn('ratings', 'admin_review_status')) {
            return $query->where('admin_review_status', 'pending');
        }

        return $query;
    }

    /**
     * Approve the rating.
     */
    public function approve($notes = null)
    {
        $data = [];

        // Check if columns exist before updating them
        if (Schema::hasColumn('ratings', 'admin_review_status')) {
            $data['admin_review_status'] = 'approved';
        }

        if (Schema::hasColumn('ratings', 'is_published')) {
            $data['is_published'] = true;
        }

        if (Schema::hasColumn('ratings', 'admin_review_notes')) {
            $data['admin_review_notes'] = $notes ?? $this->admin_review_notes;
        }

        if (!empty($data)) {
            $this->update($data);
        }

        return $this;
    }

    /**
     * Reject the rating.
     */
    public function reject($notes = null)
    {
        $data = [];

        // Check if columns exist before updating them
        if (Schema::hasColumn('ratings', 'admin_review_status')) {
            $data['admin_review_status'] = 'rejected';
        }

        if (Schema::hasColumn('ratings', 'is_published')) {
            $data['is_published'] = false;
        }

        if (Schema::hasColumn('ratings', 'admin_review_notes')) {
            $data['admin_review_notes'] = $notes ?? $this->admin_review_notes;
        }

        if (!empty($data)) {
            $this->update($data);
        }

        return $this;
    }
}
