<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class MediaLibrary extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'media_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'filename',
        'original_filename',
        'file_path',
        'file_size',
        'mime_type',
        'extension',
        'type',
        'dimensions',
        'title',
        'description',
        'alt_text',
        'is_public',
        'folder',
        'metadata',
        'uploaded_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'dimensions' => 'array',
        'is_public' => 'boolean',
        'metadata' => 'array',
        'uploaded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'url',
        'thumbnail_url'
    ];

    /**
     * Media type constants
     */
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_DOCUMENT = 'document';
    const TYPE_OTHER = 'other';

    /**
     * Get the user who uploaded the media.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the full URL to access the file.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        $disk = $this->is_public ? 'public' : 'local';
        return Storage::disk($disk)->url($this->file_path);
    }

    /**
     * Get the thumbnail URL for images.
     *
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->type !== self::TYPE_IMAGE) {
            return null;
        }

        $thumbnailPath = $this->getThumbnailPath();
        
        if (!$thumbnailPath) {
            return null;
        }
        
        $disk = $this->is_public ? 'public' : 'local';
        return Storage::disk($disk)->url($thumbnailPath);
    }

    /**
     * Get the path to the thumbnail.
     *
     * @param string $size
     * @return string|null
     */
    public function getThumbnailPath($size = 'small')
    {
        if ($this->type !== self::TYPE_IMAGE) {
            return null;
        }
        
        $sizes = [
            'small' => '150x150',
            'medium' => '300x300',
            'large' => '600x600'
        ];
        
        $targetSize = $sizes[$size] ?? $sizes['small'];
        $pathInfo = pathinfo($this->file_path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '_' . $targetSize . '.' . $pathInfo['extension'];
        
        return $thumbnailPath;
    }

    /**
     * Generate thumbnails for an image.
     *
     * @return bool
     */
    public function generateThumbnails()
    {
        if ($this->type !== self::TYPE_IMAGE) {
            return false;
        }
        
        $disk = $this->is_public ? 'public' : 'local';
        $imagePath = Storage::disk($disk)->path($this->file_path);
        
        if (!file_exists($imagePath)) {
            return false;
        }
        
        $sizes = [
            'small' => [150, 150],
            'medium' => [300, 300],
            'large' => [600, 600]
        ];
        
        $pathInfo = pathinfo($this->file_path);
        $thumbnailDir = $pathInfo['dirname'] . '/thumbnails';
        
        // Create thumbnails directory if it doesn't exist
        if (!Storage::disk($disk)->exists($thumbnailDir)) {
            Storage::disk($disk)->makeDirectory($thumbnailDir);
        }
        
        foreach ($sizes as $size => $dimensions) {
            $thumbnailName = $pathInfo['filename'] . '_' . $dimensions[0] . 'x' . $dimensions[1] . '.' . $pathInfo['extension'];
            $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
            $thumbnailFullPath = Storage::disk($disk)->path($thumbnailPath);
            
            try {
                $img = Image::make($imagePath);
                $img->fit($dimensions[0], $dimensions[1], function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($thumbnailFullPath);
            } catch (\Exception $e) {
                // Log error
                \Log::error('Error generating thumbnail: ' . $e->getMessage(), ['media_id' => $this->media_id]);
                continue;
            }
        }
        
        return true;
    }

    /**
     * Create a new media record from an uploaded file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $userId
     * @param array $attributes
     * @param bool $isPublic
     * @param string $folder
     * @return MediaLibrary|null
     */
    public static function createFromUpload($file, $userId, $attributes = [], $isPublic = true, $folder = 'uploads')
    {
        if (!$file->isValid()) {
            return null;
        }
        
        $disk = $isPublic ? 'public' : 'local';
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $originalFilename = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        
        // Generate a unique filename
        $filename = Str::uuid() . '.' . $extension;
        
        // Determine the media type based on mime type
        $type = self::determineType($mimeType);
        
        // Set the storage path
        $filePath = $folder . '/' . date('Y/m/d') . '/' . $filename;
        
        // Store the file
        $path = $file->storeAs(dirname($filePath), $filename, $disk);
        
        if (!$path) {
            return null;
        }
        
        // Get image dimensions if it's an image
        $dimensions = null;
        if ($type === self::TYPE_IMAGE) {
            try {
                $img = Image::make($file);
                $dimensions = [
                    'width' => $img->width(),
                    'height' => $img->height()
                ];
            } catch (\Exception $e) {
                // Unable to get dimensions
            }
        }
        
        // Create the media record
        $media = self::create(array_merge([
            'user_id' => $userId,
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'file_path' => $path,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'type' => $type,
            'dimensions' => $dimensions,
            'is_public' => $isPublic,
            'folder' => $folder,
            'uploaded_at' => now()
        ], $attributes));
        
        // Generate thumbnails for images
        if ($media && $type === self::TYPE_IMAGE) {
            $media->generateThumbnails();
        }
        
        return $media;
    }

    /**
     * Determine the media type based on mime type.
     *
     * @param string $mimeType
     * @return string
     */
    public static function determineType($mimeType)
    {
        if (strpos($mimeType, 'image/') === 0) {
            return self::TYPE_IMAGE;
        }
        
        if (strpos($mimeType, 'video/') === 0) {
            return self::TYPE_VIDEO;
        }
        
        if (strpos($mimeType, 'audio/') === 0) {
            return self::TYPE_AUDIO;
        }
        
        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv'
        ];
        
        if (in_array($mimeType, $documentTypes)) {
            return self::TYPE_DOCUMENT;
        }
        
        return self::TYPE_OTHER;
    }

    /**
     * Delete the media file and thumbnails.
     *
     * @return bool
     */
    public function deleteFile()
    {
        $disk = $this->is_public ? 'public' : 'local';
        
        // Delete the main file
        if (Storage::disk($disk)->exists($this->file_path)) {
            Storage::disk($disk)->delete($this->file_path);
        }
        
        // Delete thumbnails if this is an image
        if ($this->type === self::TYPE_IMAGE) {
            $sizes = ['small', 'medium', 'large'];
            foreach ($sizes as $size) {
                $thumbnailPath = $this->getThumbnailPath($size);
                if ($thumbnailPath && Storage::disk($disk)->exists($thumbnailPath)) {
                    Storage::disk($disk)->delete($thumbnailPath);
                }
            }
        }
        
        return true;
    }

    /**
     * Get the human-readable file size.
     *
     * @return string
     */
    public function getHumanReadableSize()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Scope a query to only include media of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Scope a query to only include media in a specific folder.
     */
    public function scopeInFolder($query, $folder)
    {
        return $query->where('folder', $folder);
    }
    
    /**
     * Scope a query to only include public media.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
    
    /**
     * Scope a query to only include media uploaded by a specific user.
     */
    public function scopeUploadedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    /**
     * Scope a query to order by newest first.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('uploaded_at', 'desc');
    }
}
