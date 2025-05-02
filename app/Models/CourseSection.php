<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    use HasFactory;

    protected $primaryKey = 'section_id';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'position',
        'is_published',
    ];

    /**
     * Get the course that owns the section.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the videos for the section.
     */
    public function videos()
    {
        return $this->hasMany(CourseVideo::class, 'section_id', 'section_id')
            ->orderBy('position', 'asc');
    }

    /**
     * Get the materials for the section.
     */
    public function materials()
    {
        return $this->hasMany(CourseMaterial::class, 'section_id', 'section_id')
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get the exams for the section.
     */
    public function exams()
    {
        return $this->hasMany(Exam::class, 'section_id', 'section_id');
    }
} 