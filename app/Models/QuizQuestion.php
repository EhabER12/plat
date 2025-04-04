<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'question_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'explanation',
        'points',
        'question_order',
        'image_url'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
        'correct_answer' => 'array',
        'points' => 'integer',
        'question_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The question types available.
     */
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_SINGLE_CHOICE = 'single_choice';
    const TYPE_TRUE_FALSE = 'true_false';
    const TYPE_SHORT_ANSWER = 'short_answer';
    const TYPE_ESSAY = 'essay';
    const TYPE_MATCHING = 'matching';

    /**
     * Get the quiz this question belongs to.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }

    /**
     * Get the student answers to this question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'question_id', 'question_id');
    }

    /**
     * Check if an answer is correct.
     */
    public function isCorrectAnswer($answer)
    {
        switch ($this->question_type) {
            case self::TYPE_MULTIPLE_CHOICE:
                if (!is_array($answer)) {
                    return false;
                }
                
                sort($answer);
                $correctAnswer = $this->correct_answer;
                sort($correctAnswer);
                
                return $answer == $correctAnswer;
                
            case self::TYPE_SINGLE_CHOICE:
            case self::TYPE_TRUE_FALSE:
                return $answer == $this->correct_answer;
                
            case self::TYPE_SHORT_ANSWER:
                // For short answers, check if the answer contains the correct answer
                // This is a simple implementation and might need to be more sophisticated
                return stripos($answer, $this->correct_answer) !== false;
                
            case self::TYPE_ESSAY:
                // Essay questions need manual grading
                return null;
                
            case self::TYPE_MATCHING:
                if (!is_array($answer) || !is_array($this->correct_answer)) {
                    return false;
                }
                
                // Check if all pairs match
                foreach ($this->correct_answer as $key => $value) {
                    if (!isset($answer[$key]) || $answer[$key] != $value) {
                        return false;
                    }
                }
                
                return true;
                
            default:
                return false;
        }
    }

    /**
     * Get the question text without HTML tags.
     */
    public function getPlainTextQuestionAttribute()
    {
        return strip_tags($this->question_text);
    }

    /**
     * Get all available question types.
     */
    public static function getQuestionTypes()
    {
        return [
            self::TYPE_MULTIPLE_CHOICE => 'Multiple Choice',
            self::TYPE_SINGLE_CHOICE => 'Single Choice',
            self::TYPE_TRUE_FALSE => 'True/False',
            self::TYPE_SHORT_ANSWER => 'Short Answer',
            self::TYPE_ESSAY => 'Essay',
            self::TYPE_MATCHING => 'Matching'
        ];
    }

    /**
     * Shuffle the options for this question.
     */
    public function getShuffledOptionsAttribute()
    {
        if (!$this->options || !is_array($this->options)) {
            return [];
        }
        
        $options = $this->options;
        shuffle($options);
        
        return $options;
    }
}
