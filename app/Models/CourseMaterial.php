<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMaterial extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'material_id';

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
        'file_url',
        'file_path',
        'file_type',
        'file_size',
        'sequence_order',
        'is_downloadable'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'sequence_order' => 'integer',
        'is_downloadable' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the course that the material belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    /**
     * Get the section that the material belongs to.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id', 'section_id');
    }

    /**
     * Get the formatted file size.
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } elseif ($bytes < 1073741824) {
            return round($bytes / 1048576, 2) . ' MB';
        } else {
            return round($bytes / 1073741824, 2) . ' GB';
        }
    }

    /**
     * Get the file extension from the URL.
     */
    public function getFileExtensionAttribute()
    {
        return pathinfo($this->file_url, PATHINFO_EXTENSION);
    }

    /**
     * Get student progress records for this material.
     */
    public function progress()
    {
        return $this->hasMany(StudentProgress::class, 'content_id', 'material_id')
                    ->where('content_type', 'material');
    }

    /**
     * Get the file icon based on file type.
     */
    public function getFileIconAttribute()
    {
        $fileType = strtolower($this->file_type ?? 'file');
        $fileIcon = 'fa-file';

        if (in_array($fileType, ['pdf'])) {
            $fileIcon = 'fa-file-pdf text-danger';
        } elseif (in_array($fileType, ['doc', 'docx'])) {
            $fileIcon = 'fa-file-word text-primary';
        } elseif (in_array($fileType, ['xls', 'xlsx'])) {
            $fileIcon = 'fa-file-excel text-success';
        } elseif (in_array($fileType, ['ppt', 'pptx'])) {
            $fileIcon = 'fa-file-powerpoint text-warning';
        } elseif (in_array($fileType, ['zip', 'rar'])) {
            $fileIcon = 'fa-file-archive text-secondary';
        } elseif (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $fileIcon = 'fa-file-image text-info';
        }

        return $fileIcon;
    }

    /**
     * Check if the file exists in storage.
     */
    public function fileExists()
    {
        $fileUrl = $this->attributes['file_url'] ?? null;
        if (!$fileUrl) {
            return false;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->exists($fileUrl);
    }

    /**
     * Get the file download URL.
     */
    public function getDownloadUrlAttribute()
    {
        if ($this->fileExists()) {
            return route('courses.materials.download', [
                'courseId' => $this->course_id,
                'materialId' => $this->material_id
            ]);
        }
        return null;
    }

    /**
     * Get the file public URL.
     */
    public function getFileUrlAttribute($value)
    {
        if ($value && $this->fileExists()) {
            return asset('storage/' . $value);
        }
        return $value;
    }
}
