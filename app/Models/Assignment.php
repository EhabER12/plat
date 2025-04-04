<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'assignment_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'instructions',
        'due_date',
        'total_points',
        'attachment_url',
        'created_by',
        'is_published'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'total_points' => 'integer',
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the course this assignment belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the instructor who created the assignment.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the submissions for this assignment.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'assignment_id', 'assignment_id');
    }

    /**
     * Check if the assignment is overdue.
     */
    public function isOverdue()
    {
        return $this->due_date && now()->gt($this->due_date);
    }

    /**
     * Check if the assignment is upcoming.
     */
    public function isUpcoming()
    {
        return now()->lt($this->due_date);
    }

    /**
     * Get the percentage of students who have submitted the assignment.
     */
    public function getSubmissionRateAttribute()
    {
        $enrolledStudentsCount = $this->course->enrollments()->count();
        
        if ($enrolledStudentsCount === 0) {
            return 0;
        }
        
        $submissionsCount = $this->submissions()->distinct('student_id')->count('student_id');
        return round(($submissionsCount / $enrolledStudentsCount) * 100, 1);
    }

    /**
     * Get the average score for this assignment.
     */
    public function getAverageScoreAttribute()
    {
        $submissions = $this->submissions()->whereNotNull('score')->get();
        
        if ($submissions->isEmpty()) {
            return 0;
        }
        
        return round($submissions->avg('score'), 1);
    }

    /**
     * Scope a query to only include published assignments.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include assignments for a specific course.
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }
}
