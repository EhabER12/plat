<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'answer_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer_data',
        'is_correct',
        'points_earned',
        'answered_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'answer_data' => 'array',
        'is_correct' => 'boolean',
        'points_earned' => 'float',
        'answered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the quiz attempt this answer belongs to.
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id', 'attempt_id');
    }

    /**
     * Get the question this answer is for.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id', 'question_id');
    }

    /**
     * Determine if the answer is correct.
     */
    public function evaluateCorrectness()
    {
        $question = $this->question;
        
        if (!$question) {
            return false;
        }
        
        $isCorrect = $question->isCorrectAnswer($this->answer_data);
        $pointsEarned = $isCorrect ? $question->points : 0;
        
        $this->update([
            'is_correct' => $isCorrect,
            'points_earned' => $pointsEarned
        ]);
        
        return $isCorrect;
    }

    /**
     * Format the answer data for display.
     */
    public function getFormattedAnswerAttribute()
    {
        if (!$this->question) {
            return '';
        }
        
        switch ($this->question->question_type) {
            case QuizQuestion::TYPE_MULTIPLE_CHOICE:
            case QuizQuestion::TYPE_SINGLE_CHOICE:
                if (is_array($this->answer_data)) {
                    return implode(', ', $this->answer_data);
                }
                return (string) $this->answer_data;
                
            case QuizQuestion::TYPE_TRUE_FALSE:
                return $this->answer_data ? 'True' : 'False';
                
            case QuizQuestion::TYPE_SHORT_ANSWER:
            case QuizQuestion::TYPE_ESSAY:
                return (string) $this->answer_data;
                
            case QuizQuestion::TYPE_MATCHING:
                if (is_array($this->answer_data)) {
                    $result = [];
                    foreach ($this->answer_data as $key => $value) {
                        $result[] = "{$key} => {$value}";
                    }
                    return implode(', ', $result);
                }
                return '';
                
            default:
                return '';
        }
    }

    /**
     * Get the correct answer formatted for display.
     */
    public function getFormattedCorrectAnswerAttribute()
    {
        if (!$this->question) {
            return '';
        }
        
        switch ($this->question->question_type) {
            case QuizQuestion::TYPE_MULTIPLE_CHOICE:
            case QuizQuestion::TYPE_SINGLE_CHOICE:
                if (is_array($this->question->correct_answer)) {
                    return implode(', ', $this->question->correct_answer);
                }
                return (string) $this->question->correct_answer;
                
            case QuizQuestion::TYPE_TRUE_FALSE:
                return $this->question->correct_answer ? 'True' : 'False';
                
            case QuizQuestion::TYPE_SHORT_ANSWER:
                return (string) $this->question->correct_answer;
                
            case QuizQuestion::TYPE_ESSAY:
                return 'Essay questions require manual grading';
                
            case QuizQuestion::TYPE_MATCHING:
                if (is_array($this->question->correct_answer)) {
                    $result = [];
                    foreach ($this->question->correct_answer as $key => $value) {
                        $result[] = "{$key} => {$value}";
                    }
                    return implode(', ', $result);
                }
                return '';
                
            default:
                return '';
        }
    }
}
