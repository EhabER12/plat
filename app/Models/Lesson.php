<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lessons';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'lesson_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'video_url',
        'attachments',
        'order',
        'duration_minutes',
        'is_free_preview',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_free_preview' => 'boolean',
        'duration_minutes' => 'integer',
        'order' => 'integer',
        'attachments' => 'array',
    ];

    /**
     * Get the course that the lesson belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the progress records for this lesson.
     */
    public function progress()
    {
        return $this->hasMany(LessonProgress::class, 'lesson_id', 'lesson_id');
    }

    /**
     * Get the quizzes for this lesson.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'lesson_id', 'lesson_id');
    }

    /**
     * Get the comments for this lesson.
     */
    public function comments()
    {
        return $this->hasMany(LessonComment::class, 'lesson_id', 'lesson_id');
    }
} 