<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'submission_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assignment_id',
        'student_id',
        'submission_text',
        'attachment_url',
        'submitted_at',
        'status',
        'score',
        'feedback',
        'graded_by',
        'graded_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'submitted_at' => 'datetime',
        'score' => 'float',
        'graded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the assignment this submission is for.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id', 'assignment_id');
    }

    /**
     * Get the student who submitted the assignment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Get the instructor who graded the submission.
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by', 'user_id');
    }

    /**
     * Check if the submission is late.
     */
    public function isLate()
    {
        if (!$this->assignment->due_date) {
            return false;
        }
        
        return $this->submitted_at->gt($this->assignment->due_date);
    }

    /**
     * Check if the submission has been graded.
     */
    public function isGraded()
    {
        return $this->status === 'graded' && $this->graded_at !== null;
    }

    /**
     * Get the percentage score of the submission.
     */
    public function getScorePercentageAttribute()
    {
        if ($this->assignment->total_points === 0) {
            return 0;
        }
        
        return round(($this->score / $this->assignment->total_points) * 100, 1);
    }

    /**
     * Grade the submission.
     */
    public function grade($score, $feedback, $graderId)
    {
        $this->update([
            'score' => $score,
            'feedback' => $feedback,
            'graded_by' => $graderId,
            'graded_at' => now(),
            'status' => 'graded'
        ]);
        return $this;
    }

    /**
     * Scope a query to only include submissions pending grading.
     */
    public function scopePendingGrading($query)
    {
        return $query->where('status', 'submitted')
                    ->whereNull('graded_at');
    }

    /**
     * Scope a query to only include graded submissions.
     */
    public function scopeGraded($query)
    {
        return $query->where('status', 'graded')
                    ->whereNotNull('graded_at');
    }
}
