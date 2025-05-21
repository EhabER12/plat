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
        'user_id',
        'start_time',
        'end_time',
        'status',
        'score',
        'score_percentage',
        'is_passed',
        'time_spent_seconds',
        'answers_json',
        'instructor_feedback',
        'correct_answers_count'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'score' => 'float',
        'score_percentage' => 'float',
        'is_passed' => 'boolean',
        'time_spent_seconds' => 'integer',
        'answers_json' => 'array',
        'correct_answers_count' => 'integer'
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
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id');
    }

    /**
     * Get the user who made the attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
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

        // Make sure the quiz relationship is loaded
        if (!$this->relationLoaded('quiz')) {
            $this->load('quiz');
        }

        // Get time limit from the quiz or use a default value if not set
        $timeLimitMinutes = optional($this->quiz)->duration_minutes ?? 0;

        if ($this->isInProgress() && $timeLimitMinutes > 0) {
            $timeLimit = $this->start_time->addMinutes($timeLimitMinutes);
            if (now()->gt($timeLimit)) {
                // Calculate time spent (ensure it's not negative)
                $timeSpent = now()->diffInSeconds($this->start_time);
                if ($timeSpent < 0) {
                    $timeSpent = 0;
                }

                $this->update([
                    'status' => self::STATUS_TIMED_OUT,
                    'end_time' => now(),
                    'time_spent_seconds' => $timeSpent
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
        if ($this->status !== 'in_progress') {
            return 0;
        }

        $now = now();
        if ($now->gt($this->end_time)) {
            return 0;
        }

        return $now->diffInSeconds($this->end_time);
    }

    /**
     * Get the percentage score.
     */
    public function getScorePercentageAttribute()
    {
        // Make sure the quiz relationship is loaded
        if (!$this->relationLoaded('quiz')) {
            $this->load('quiz');
        }

        // Safely access the quiz's total possible score or default to 0 if not available
        $totalPoints = optional($this->quiz)->total_possible_score ?? 0;

        if ($totalPoints === 0) {
            return 0;
        }

        return round(($this->score / $totalPoints) * 100, 1);
    }

    /**
     * Complete the attempt and calculate the score.
     */
    public function complete($answers = null)
    {
        if ($this->isCompleted()) {
            return $this;
        }

        // If answers are provided, update them
        if ($answers !== null) {
            $this->answers_json = $answers;
            $this->save();
        }

        // Calculate the score
        $score = $this->calculateScore();

        // Make sure the quiz relationship is loaded
        if (!$this->relationLoaded('quiz')) {
            $this->load('quiz');
        }

        // Safely get the passing score
        $passingScore = optional($this->quiz)->passing_score ?? 0;

        // Calculate time spent (ensure it's not negative)
        $timeSpent = now()->diffInSeconds($this->start_time);
        if ($timeSpent < 0) {
            $timeSpent = 0;
        }

        // Update the attempt
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'end_time' => now(),
            'time_spent_seconds' => $timeSpent,
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
        $correctAnswersCount = 0;

        // Get the quiz questions
        $questions = $this->quiz->questions_json ?? [];

        // Get the answers from this attempt
        $answers = $this->answers_json ?? [];

        foreach ($questions as $questionIndex => $question) {
            $questionId = $question['id'] ?? $questionIndex;
            $userAnswer = $answers[$questionId] ?? null;
            $isCorrect = false;

            // Check if answer is correct based on question type
            if (isset($question['type'])) {
                switch ($question['type']) {
                    case 'multiple_choice':
                        $correctOptions = collect($question['options'] ?? [])
                            ->where('is_correct', true)
                            ->pluck('id')
                            ->toArray();

                        if (is_array($userAnswer)) {
                            // Multiple answers selected
                            $isCorrect = count(array_diff($correctOptions, $userAnswer)) === 0 &&
                                         count(array_diff($userAnswer, $correctOptions)) === 0;
                        } else {
                            // Single answer selected
                            $isCorrect = in_array($userAnswer, $correctOptions);
                        }
                        break;

                    case 'true_false':
                        $isCorrect = !empty($userAnswer) && $userAnswer == ($question['correct_answer'] ?? null);
                        break;

                    case 'short_answer':
                        $correctAnswer = $question['correct_answer'] ?? '';
                        $isCorrect = !empty($userAnswer) &&
                            strtolower(trim($userAnswer)) == strtolower(trim($correctAnswer));
                        break;
                }
            }

            // Add points if correct
            if ($isCorrect) {
                $points = $question['points'] ?? 1;
                $score += $points;
                $correctAnswersCount++;
            }
        }

        // Update the correct answers count
        $this->correct_answers_count = $correctAnswersCount;
        $this->save();

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
        return $query->where('user_id', $studentId);
    }

    /**
     * Check if the attempt has expired.
     */
    public function hasExpired()
    {
        return $this->status === 'in_progress' && now()->gt($this->end_time);
    }

    /**
     * Get the formatted time spent.
     */
    public function getFormattedTimeSpentAttribute()
    {
        $minutes = floor($this->time_spent_seconds / 60);
        $seconds = $this->time_spent_seconds % 60;

        return "{$minutes} دقيقة {$seconds} ثانية";
    }
}
