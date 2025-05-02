<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadAttempt extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'download_attempts';

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
        'user_id',
        'video_id',
        'ip_address',
        'user_agent',
        'fingerprint',
        'request_details',
        'attempt_count',
        'is_blocked',
        'blocked_until'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_blocked' => 'boolean',
        'blocked_until' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that made the download attempt.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the video that was attempted to be downloaded.
     */
    public function video()
    {
        return $this->belongsTo(CourseVideo::class, 'video_id', 'video_id');
    }

    /**
     * Check if the user is currently blocked.
     *
     * @return bool
     */
    public function isBlocked()
    {
        return $this->is_blocked && $this->blocked_until && $this->blocked_until->isFuture();
    }

    /**
     * Get the remaining block time in minutes.
     *
     * @return int
     */
    public function getRemainingBlockTime()
    {
        if (!$this->isBlocked()) {
            return 0;
        }

        return now()->diffInMinutes($this->blocked_until);
    }
}
