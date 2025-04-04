<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'enrollments';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'enrollment_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'student_id',
        'enrolled_at',
        'completed_at',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the course for this enrollment.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the student for this enrollment.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Get the progress records for this enrollment.
     */
    // public function lessonProgress()
    // {
    //     return $this->hasMany(LessonProgress::class, 'enrollment_id', 'enrollment_id');
    // }

    /**
     * Get the certificate for this enrollment.
     */
    // public function certificate()
    // {
    //     return $this->hasOne(Certificate::class, 'enrollment_id', 'enrollment_id');
    // }

    /**
     * Check if the enrollment is completed.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->completed_at !== null;
    }

    /**
     * Get enrollment date formatted.
     *
     * @return string
     */
    public function getFormattedEnrolledAtAttribute()
    {
        return $this->enrolled_at ? $this->enrolled_at->format('M d, Y') : 'N/A';
    }

    /**
     * Scope a query to only include active enrollments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include completed enrollments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
