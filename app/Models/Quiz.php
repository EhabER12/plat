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
    protected $primaryKey = 'id';

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
        'questions_json',
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
        'questions_json' => 'array',
        'max_attempts' => 'integer',
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
     * Get the user who created the quiz.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the attempts made on this quiz.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id', 'id');
    }

    /**
     * Check if the quiz is available.
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
     * Get the number of questions in the quiz.
     */
    public function getQuestionsCountAttribute()
    {
        return count($this->questions_json ?? []);
    }

    /**
     * Get the total possible score for the quiz.
     */
    public function getTotalPossibleScoreAttribute()
    {
        return collect($this->questions_json ?? [])->sum('points');
    }

    /**
     * Get the passing score for the quiz.
     */
    public function getPassingScoreAttribute()
    {
        return round($this->total_possible_score * ($this->passing_percentage / 100), 1);
    }

    /**
     * Check if a student can attempt the quiz.
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

    /**
     * Check if the quiz hasn't started yet.
     */
    public function hasNotStarted()
    {
        return $this->start_date !== null && now()->lt($this->start_date);
    }

    /**
     * Check if the quiz has ended.
     */
    public function hasEnded()
    {
        return $this->end_date !== null && now()->gt($this->end_date);
    }

    /**
     * Check if the quiz is currently active (started but not ended).
     */
    public function isActive()
    {
        $now = now();
        return $this->is_published &&
            ($this->start_date === null || $now->gte($this->start_date)) &&
            ($this->end_date === null || $now->lte($this->end_date));
    }

    /**
     * Get the status badge HTML for the quiz.
     */
    public function getStatusBadgeHtml()
    {
        if (!$this->is_published) {
            return '<span class="badge bg-secondary">مسودة</span>';
        } elseif ($this->hasNotStarted()) {
            return '<span class="badge bg-info">سيبدأ قريباً</span>';
        } elseif ($this->hasEnded()) {
            return '<span class="badge bg-danger">انتهى</span>';
        } elseif ($this->isActive()) {
            return '<span class="badge bg-success">نشط</span>';
        } else {
            return '<span class="badge bg-warning">غير متاح</span>';
        }
    }

    /**
     * Get the remaining time for the quiz as a formatted string.
     */
    public function getTimeStatus()
    {
        if ($this->hasNotStarted()) {
            $now = now();
            $diff = $now->diff($this->start_date);

            if ($diff->days > 0) {
                $hours = $diff->h;
                $minutes = $diff->i;

                $result = "يبدأ بعد " . $diff->days . " " . ($diff->days == 1 ? "يوم" : "أيام");

                if ($hours > 0 || $minutes > 0) {
                    $result .= " و " . $hours . " " . ($hours == 1 ? "ساعة" : "ساعات");

                    if ($minutes > 0) {
                        $result .= " و " . $minutes . " " . ($minutes == 1 ? "دقيقة" : "دقائق");
                    }
                }

                return $result;
            } else if ($diff->h > 0) {
                $hours = $diff->h;
                $minutes = $diff->i;

                $result = "يبدأ بعد " . $hours . " " . ($hours == 1 ? "ساعة" : "ساعات");

                if ($minutes > 0) {
                    $result .= " و " . $minutes . " " . ($minutes == 1 ? "دقيقة" : "دقائق");
                }

                return $result;
            } else {
                $minutes = $diff->i;
                return "يبدأ بعد " . $minutes . " " . ($minutes == 1 ? "دقيقة" : "دقائق");
            }
        } elseif ($this->hasEnded()) {
            return "انتهى";
        } elseif ($this->end_date !== null) {
            $now = now();
            $diff = $now->diff($this->end_date);

            if ($diff->days > 0) {
                $hours = $diff->h;
                $minutes = $diff->i;

                $result = "ينتهي بعد " . $diff->days . " " . ($diff->days == 1 ? "يوم" : "أيام");

                if ($hours > 0 || $minutes > 0) {
                    $result .= " و " . $hours . " " . ($hours == 1 ? "ساعة" : "ساعات");

                    if ($minutes > 0) {
                        $result .= " و " . $minutes . " " . ($minutes == 1 ? "دقيقة" : "دقائق");
                    }
                }

                return $result;
            } else if ($diff->h > 0) {
                $hours = $diff->h;
                $minutes = $diff->i;

                $result = "ينتهي بعد " . $hours . " " . ($hours == 1 ? "ساعة" : "ساعات");

                if ($minutes > 0) {
                    $result .= " و " . $minutes . " " . ($minutes == 1 ? "دقيقة" : "دقائق");
                }

                return $result;
            } else {
                $minutes = $diff->i;
                return "ينتهي بعد " . $minutes . " " . ($minutes == 1 ? "دقيقة" : "دقائق");
            }
        } else {
            return "غير محدد";
        }
    }
}
