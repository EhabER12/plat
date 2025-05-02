<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoAccess extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'video_access';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'access_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'video_id',
        'token',
        'expires_at',
        'last_accessed_at',
        'ip_address',
        'user_agent',
        'fingerprint'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the access token.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the video that the access token is for.
     */
    public function video()
    {
        return $this->belongsTo(CourseVideo::class, 'video_id', 'video_id');
    }
}
