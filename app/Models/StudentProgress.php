<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProgress extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'progress_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'course_id',
        'content_type',
        'content_id',
        'progress_percentage',
        'last_position',
        'completed_at',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'progress_percentage' => 'integer',
        'last_position' => 'integer',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the student whose progress is recorded.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Get the course for which progress is recorded.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Check if the content has been completed.
     */
    public function isCompleted()
    {
        return $this->completed_at !== null || $this->progress_percentage >= 100;
    }

    /**
     * Mark the content as completed.
     */
    public function markAsCompleted($notes = null)
    {
        $this->update([
            'progress_percentage' => 100,
            'completed_at' => now(),
            'notes' => $notes ?? $this->notes
        ]);
        return $this;
    }

    /**
     * Update the progress.
     */
    public function updateProgress($percentage, $position = null)
    {
        $data = ['progress_percentage' => $percentage];

        if ($position !== null) {
            $data['last_position'] = $position;
        }

        if ($percentage >= 100 && $this->completed_at === null) {
            $data['completed_at'] = now();
        }

        $this->update($data);
        return $this;
    }

    /**
     * Scope a query to only include completed progress.
     */
    public function scopeCompleted($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('completed_at')
              ->orWhere('progress_percentage', '>=', 100);
        });
    }

    /**
     * Scope a query to only include in-progress items.
     */
    public function scopeInProgress($query)
    {
        return $query->whereNull('completed_at')
                    ->where('progress_percentage', '<', 100)
                    ->where('progress_percentage', '>', 0);
    }

    /**
     * Scope a query to only include not-started items.
     */
    public function scopeNotStarted($query)
    {
        return $query->whereNull('completed_at')
                    ->where('progress_percentage', 0);
    }
}
