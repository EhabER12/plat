<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttempt extends Model
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
        'exam_id',
        'user_id',
        'start_time',
        'end_time',
        'score',
        'answers',
        'status',
        'time_spent_seconds',
        'is_passed'
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
        'answers' => 'array',
        'time_spent_seconds' => 'integer',
        'is_passed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the exam this attempt belongs to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    /**
     * Get the user who made the attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Check if the attempt is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed' && $this->end_time !== null;
    }

    /**
     * Check if the attempt is in progress.
     */
    public function isInProgress()
    {
        return $this->status === 'in_progress' && $this->end_time === null;
    }

    /**
     * Check if the attempt is timed out.
     */
    public function isTimedOut()
    {
        if ($this->status !== 'in_progress' || $this->exam->duration_minutes === null) {
            return false;
        }

        $timeLimit = $this->start_time->addMinutes($this->exam->duration_minutes);
        return now()->gt($timeLimit);
    }

    /**
     * Get the percentage score.
     */
    public function getScorePercentageAttribute()
    {
        if ($this->exam->total_possible_score == 0) {
            return 0;
        }
        
        return round(($this->score / $this->exam->total_possible_score) * 100, 1);
    }

    /**
     * Complete the exam attempt.
     */
    public function complete($answers = null, $submitScore = true)
    {
        $data = [
            'status' => 'completed',
            'end_time' => now()
        ];

        if ($answers !== null) {
            $data['answers'] = $answers;
        }

        if ($submitScore) {
            $score = $this->calculateScore();
            $data['score'] = $score;
            $data['is_passed'] = $score >= $this->exam->passing_score;
        }

        $startTime = $this->start_time ?: now()->subSeconds(1);
        $data['time_spent_seconds'] = now()->diffInSeconds($startTime);

        $this->update($data);
        return $this;
    }

    /**
     * Calculate the score based on answers.
     */
    public function calculateScore()
    {
        if (empty($this->answers)) {
            return 0;
        }

        $score = 0;
        $questions = $this->exam->questions_data ?? [];

        foreach ($questions as $index => $question) {
            $questionId = $question['id'] ?? $index;
            $userAnswer = $this->answers[$questionId] ?? null;

            if ($userAnswer !== null) {
                $correctAnswer = $question['correct_answer'] ?? '';
                $points = $question['points'] ?? 1;

                // For multiple choice
                if (is_array($correctAnswer) && is_array($userAnswer)) {
                    $correctCount = count(array_intersect($userAnswer, $correctAnswer));
                    $incorrectCount = count(array_diff($userAnswer, $correctAnswer));
                    
                    if ($incorrectCount == 0 && $correctCount == count($correctAnswer)) {
                        $score += $points;
                    }
                }
                // For single answer questions
                elseif ($userAnswer == $correctAnswer) {
                    $score += $points;
                }
            }
        }

        return $score;
    }
}
