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
        'position',
        'hls_path',
        'hls_url',
        'is_hls_enabled',
        'encryption_key',
        'hls_segments_path'
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
        'is_hls_enabled' => 'boolean',
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
        // إعطاء الأولوية لـ HLS إذا كان متاحًا
        if ($this->is_hls_enabled && $this->hls_url) {
            return $this->hls_url;
        }
        
        if ($this->video_url) {
            // إذا كان لدينا رابط خارجي، استخدمه مباشرة
            return $this->video_url;
        }

        // نحتاج للتأكد من أن لدينا معرف للفيديو والكورس
        if (!$this->video_id || !$this->course_id) {
            return null;
        }

        // دائمًا استخدم نظام التدفق الداخلي بدلاً من محاولة الوصول المباشر للملفات
        return route('video.token', ['courseId' => $this->course_id, 'videoId' => $this->video_id]);
    }

    /**
     * التحقق مما إذا كان الفيديو يستخدم HLS
     */
    public function getIsHlsAttribute()
    {
        return $this->is_hls_enabled && !empty($this->hls_path);
    }

    /**
     * الحصول على رابط HLS للفيديو
     */
    public function getHlsUrlAttribute()
    {
        // إذا كان لدينا رابط مخزن، استخدمه
        if (!empty($this->attributes['hls_url'])) {
            return $this->attributes['hls_url'];
        }
        
        // إذا كان لدينا مسار ملف
        if (!empty($this->attributes['hls_path'])) {
            return asset('storage/' . $this->attributes['hls_path']);
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
            // تحقق ما إذا كان يجب استخدام تخزين S3 أو محلي
            if (config('filesystems.default') === 'local' || empty(env('AWS_ACCESS_KEY_ID'))) {
                // استخدام مسار الصورة المصغرة المحلي
                return asset('storage/' . $this->thumbnail_url);
            }
            // استخدام رابط S3 - تجنب استخدام url() مباشرة
            $s3Bucket = config('filesystems.disks.s3.bucket');
            $s3Region = config('filesystems.disks.s3.region', 'us-east-1');
            return "https://{$s3Bucket}.s3.{$s3Region}.amazonaws.com/{$this->thumbnail_url}";
        }

        return asset($this->thumbnail_url);
    }
}
