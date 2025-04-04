<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'attempt_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quiz_id',
        'student_id',
        'score',
        'status',
        'started_at',
        'completed_at',
        'time_spent_seconds',
        'is_passed'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'float',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_spent_seconds' => 'integer',
        'is_passed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The statuses available.
     */
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_TIMED_OUT = 'timed_out';
    const STATUS_ABANDONED = 'abandoned';

    /**
     * Get the quiz this attempt belongs to.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }

    /**
     * Get the student who made the attempt.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Get the answers for this attempt.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id', 'attempt_id');
    }

    /**
     * Check if the attempt is in progress.
     */
    public function isInProgress()
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if the attempt is completed.
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the attempt has timed out.
     */
    public function isTimedOut()
    {
        if ($this->status === self::STATUS_TIMED_OUT) {
            return true;
        }
        
        if ($this->isInProgress() && $this->quiz->time_limit_minutes > 0) {
            $timeLimit = $this->started_at->addMinutes($this->quiz->time_limit_minutes);
            if (now()->gt($timeLimit)) {
                $this->update([
                    'status' => self::STATUS_TIMED_OUT,
                    'completed_at' => now(),
                    'time_spent_seconds' => now()->diffInSeconds($this->started_at)
                ]);
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if the attempt is passed.
     */
    public function isPassed()
    {
        return $this->is_passed;
    }

    /**
     * Get the time remaining for this attempt.
     */
    public function getTimeRemainingAttribute()
    {
        if (!$this->isInProgress() || !$this->quiz->time_limit_minutes) {
            return 0;
        }
        
        $timeLimit = $this->started_at->addMinutes($this->quiz->time_limit_minutes);
        $remaining = $timeLimit->diffInSeconds(now(), false);
        
        return max(0, $remaining);
    }

    /**
     * Get the percentage score.
     */
    public function getScorePercentageAttribute()
    {
        $totalPoints = $this->quiz->questions->sum('points');
        
        if ($totalPoints === 0) {
            return 0;
        }
        
        return round(($this->score / $totalPoints) * 100, 1);
    }

    /**
     * Complete the attempt and calculate the score.
     */
    public function complete()
    {
        if ($this->isCompleted()) {
            return $this;
        }
        
        $score = $this->calculateScore();
        $passingScore = $this->quiz->passing_score;
        
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'time_spent_seconds' => now()->diffInSeconds($this->started_at),
            'score' => $score,
            'is_passed' => $score >= $passingScore
        ]);
        
        return $this;
    }

    /**
     * Calculate the score based on the answers.
     */
    public function calculateScore()
    {
        $score = 0;
        $answers = $this->answers;
        
        foreach ($answers as $answer) {
            $question = $answer->question;
            
            if ($question && $question->isCorrectAnswer($answer->answer_data)) {
                $score += $question->points;
            }
        }
        
        return $score;
    }

    /**
     * Scope a query to only include completed attempts.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include passed attempts.
     */
    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    /**
     * Scope a query to only include attempts by a specific student.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}
