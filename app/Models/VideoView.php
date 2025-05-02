<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoView extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'video_views';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'view_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'video_id',
        'course_id',
        'view_date',
        'view_progress',
        'completed',
        'ip_address',
        'user_agent'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'view_date' => 'datetime',
        'view_progress' => 'float',
        'completed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the view.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the video that was viewed.
     */
    public function video()
    {
        return $this->belongsTo(CourseVideo::class, 'video_id', 'video_id');
    }

    /**
     * Get the course that the video belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }
}
