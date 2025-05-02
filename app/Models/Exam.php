<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'exam_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'duration_minutes',
        'passing_percentage',
        'is_active',
        'is_published',
        'created_by',
        'start_date',
        'end_date',
        'questions_data',
        'max_attempts'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration_minutes' => 'integer',
        'passing_percentage' => 'float',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'questions_data' => 'array',
        'max_attempts' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the course this exam belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the user who created the exam.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the attempts made on this exam.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'exam_id', 'exam_id');
    }

    /**
     * Check if the exam is available.
     */
    public function isAvailable()
    {
        $now = now();
        return $this->is_active &&
            $this->is_published &&
            ($this->start_date === null || $now->gte($this->start_date)) &&
            ($this->end_date === null || $now->lte($this->end_date));
    }

    /**
     * Get the number of questions in the exam.
     */
    public function getQuestionsCountAttribute()
    {
        return count($this->questions_data ?? []);
    }

    /**
     * Get the total possible score for the exam.
     */
    public function getTotalPossibleScoreAttribute()
    {
        return collect($this->questions_data ?? [])->sum('points');
    }

    /**
     * Get the passing score for the exam.
     */
    public function getPassingScoreAttribute()
    {
        return round($this->total_possible_score * ($this->passing_percentage / 100), 1);
    }

    /**
     * Check if a student can attempt the exam.
     */
    public function canBeAttemptedBy($userId)
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $attemptsCount = $this->attempts()
            ->where('user_id', $userId)
            ->count();

        return $this->max_attempts === null || $attemptsCount < $this->max_attempts;
    }
}
