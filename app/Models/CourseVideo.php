<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class CourseVideo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_videos';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'video_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'section_id',
        'title',
        'description',
        'video_url',
        'video_path',
        'thumbnail_url',
        'duration_seconds',
        'sequence_order',
        'is_free_preview',
        'storage_disk',
        'is_encrypted',
        'position'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration_seconds' => 'integer',
        'sequence_order' => 'integer',
        'position' => 'integer',
        'is_free_preview' => 'boolean',
        'is_encrypted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the course that the video belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the section that the video belongs to.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id', 'section_id');
    }

    /**
     * Get the formatted duration of the video.
     */
    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Get student progress records for this video.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(StudentProgress::class, 'content_id', 'video_id')
                    ->where('content_type', 'video');
    }

    /**
     * الحصول على رابط الفيديو الكامل بناءً على مكان التخزين
     */
    public function getVideoFullUrlAttribute()
    {
        if ($this->video_url) {
            return $this->video_url; // رابط خارجي
        }

        if ($this->video_path) {
            if ($this->storage_disk === 's3') {
                // استخدام رابط S3 - تجنب استخدام url() مباشرة
                $s3Bucket = config('filesystems.disks.s3.bucket');
                $s3Region = config('filesystems.disks.s3.region', 'us-east-1');
                return "https://{$s3Bucket}.s3.{$s3Region}.amazonaws.com/{$this->video_path}";
            } else {
                // استخدام المسار المحلي
                return asset($this->video_path);
            }
        }

        return null;
    }

    /**
     * الحصول على رابط الصورة المصغرة الكامل
     */
    public function getThumbnailFullUrlAttribute()
    {
        if (!$this->thumbnail_url) {
            return asset('images/default-video-thumbnail.jpg');
        }

        if (strpos($this->thumbnail_url, 'http') === 0) {
            return $this->thumbnail_url;
        }

        if ($this->storage_disk === 's3') {
            // استخدام رابط S3 - تجنب استخدام url() مباشرة
            $s3Bucket = config('filesystems.disks.s3.bucket');
            $s3Region = config('filesystems.disks.s3.region', 'us-east-1');
            return "https://{$s3Bucket}.s3.{$s3Region}.amazonaws.com/{$this->thumbnail_url}";
        }

        return asset($this->thumbnail_url);
    }
}
