<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'quiz_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'time_limit_minutes',
        'passing_score',
        'is_published',
        'created_by',
        'question_count',
        'attempts_allowed',
        'show_answers_after'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'time_limit_minutes' => 'integer',
        'passing_score' => 'integer',
        'is_published' => 'boolean',
        'question_count' => 'integer',
        'attempts_allowed' => 'integer',
        'show_answers_after' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the course this quiz belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the instructor who created the quiz.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the questions for this quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id', 'quiz_id')
                    ->orderBy('question_order');
    }

    /**
     * Get the attempts made by students on this quiz.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id', 'quiz_id');
    }

    /**
     * Check if a student has already taken this quiz.
     */
    public function isAttemptedBy($studentId)
    {
        return $this->attempts()
                    ->where('student_id', $studentId)
                    ->exists();
    }

    /**
     * Check if a student has passed this quiz.
     */
    public function isPassedBy($studentId)
    {
        return $this->attempts()
                    ->where('student_id', $studentId)
                    ->where('score', '>=', $this->passing_score)
                    ->exists();
    }

    /**
     * Get the number of attempts made by a student.
     */
    public function getAttemptsCountForStudent($studentId)
    {
        return $this->attempts()
                    ->where('student_id', $studentId)
                    ->count();
    }

    /**
     * Check if a student can take this quiz again.
     */
    public function canBeAttemptedBy($studentId)
    {
        if (!$this->attempts_allowed) {
            return true; // Unlimited attempts
        }
        
        $attemptsCount = $this->getAttemptsCountForStudent($studentId);
        return $attemptsCount < $this->attempts_allowed;
    }

    /**
     * Get the best score for a student on this quiz.
     */
    public function getBestScoreForStudent($studentId)
    {
        $attempt = $this->attempts()
                        ->where('student_id', $studentId)
                        ->orderBy('score', 'desc')
                        ->first();
        
        return $attempt ? $attempt->score : 0;
    }

    /**
     * Get the average score for all attempts on this quiz.
     */
    public function getAverageScoreAttribute()
    {
        $attempts = $this->attempts;
        
        if ($attempts->isEmpty()) {
            return 0;
        }
        
        return round($attempts->avg('score'), 1);
    }

    /**
     * Scope a query to only include published quizzes.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
