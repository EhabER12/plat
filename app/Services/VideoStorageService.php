<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VideoStorageService
{
    /**
     * رفع ملف فيديو إلى S3
     *
     * @param UploadedFile $file ملف الفيديو
     * @param int $courseId معرف الكورس
     * @param string $title عنوان الفيديو
     * @return array معلومات الملف المرفوع
     */
    public function uploadVideo(UploadedFile $file, int $courseId, string $title): array
    {
        // Validate the file one more time
        if (!$file->isValid()) {
            throw new \Exception('Invalid uploaded file: ' . $file->getErrorMessage());
        }

        // Check file size - prevent 0 byte or tiny files
        $fileSize = $file->getSize();
        if ($fileSize < 1024 * 1024) { // 1MB minimum 
            throw new \Exception("File size too small: $fileSize bytes. Videos should be at least 1MB.");
        }
        
        // إنشاء اسم فريد للملف
        $fileName = Str::slug($title) . '-' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = 'courses/' . $courseId . '/videos/' . $fileName;
        
        // Create directory if it doesn't exist (S3 may not need this, but it's a good safety check)
        $directory = 'courses/' . $courseId . '/videos';
        if (!Storage::disk('s3')->exists($directory)) {
            Storage::disk('s3')->makeDirectory($directory);
        }
        
        // Log the upload process
        Log::info('Starting S3 upload for video', [
            'original_filename' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'target_path' => $filePath
        ]);
        
        // For force actual file transfer, get file contents explicitly
        $fileContents = file_get_contents($file->getRealPath());
        if ($fileContents === false) {
            throw new \Exception('Failed to read video file contents');
        }
        
        // Check that the file contents match the expected size
        $contentSize = strlen($fileContents);
        if ($contentSize != $fileSize) {
            Log::warning('File content size mismatch', [
                'reported_size' => $fileSize,
                'actual_content_size' => $contentSize
            ]);
            // Continue with the actual content size
        }
        
        // Perform the upload - use putFileAs with custom options
        try {
            // Start timing the upload
            $startTime = microtime(true);
            
            // Use putFileAs with explicit file content
            $success = Storage::disk('s3')->put($filePath, $fileContents);
            
            if (!$success) {
                throw new \Exception('Failed to upload file to S3');
            }
            
            // End timing the upload
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            
            Log::info('S3 upload completed', [
                'duration_seconds' => $duration,
                'upload_speed_mbps' => ($contentSize / 1024 / 1024) / ($duration > 0 ? $duration : 1),
                'file_size_mb' => $contentSize / 1024 / 1024
            ]);
            
            // Verify upload by checking file exists
            if (!Storage::disk('s3')->exists($filePath)) {
                throw new \Exception('File not found in S3 after upload attempt');
            }
            
            // Get the public URL for the file - use a safer method to get URL
            $url = $this->getS3Url($filePath);
            
            // For debugging, check file size on S3 if supported by driver
            try {
                $s3FileSize = Storage::disk('s3')->size($filePath);
                if ($s3FileSize != $contentSize) {
                    Log::warning('S3 stored file size mismatch', [
                        'uploaded_size' => $contentSize,
                        's3_reported_size' => $s3FileSize
                    ]);
                }
            } catch (\Exception $e) {
                Log::info('Could not verify file size on S3', [
                    'error' => $e->getMessage()
                ]);
            }
        
        return [
                'path' => $filePath,
            'url' => $url,
            'disk' => 's3',
                'file_name' => $fileName,
                'file_size' => $contentSize
        ];
        } catch (\Exception $e) {
            Log::error('S3 upload error: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * رفع صورة مصغرة إلى S3
     *
     * @param UploadedFile $file ملف الصورة
     * @param int $courseId معرف الكورس
     * @param string $title عنوان الفيديو
     * @return array معلومات الملف المرفوع
     */
    public function uploadThumbnail(UploadedFile $file, int $courseId, string $title): array
    {
        // إنشاء اسم فريد للملف
        $fileName = Str::slug($title) . '-thumb-' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = 'courses/' . $courseId . '/thumbnails/' . $fileName;
        
        // رفع الملف إلى S3
        $path = $file->storeAs(dirname($filePath), basename($filePath), 's3');
        
        // Get URL using safer method
        $url = $this->getS3Url($path);
        
        return [
            'path' => $path,
            'url' => $url,
            'disk' => 's3',
            'file_name' => $fileName
        ];
    }
    
    /**
     * حذف ملف من S3
     *
     * @param string $path مسار الملف
     * @return bool نجاح العملية
     */
    public function deleteFile(string $path): bool
    {
        return Storage::disk('s3')->delete($path);
    }
    
    /**
     * Get a safe URL for S3 files
     * 
     * @param string $path Path to the file in S3
     * @return string URL to the file
     */
    private function getS3Url(string $path): string
    {
        // Construct URL manually from configuration
        $bucket = config('filesystems.disks.s3.bucket');
        $region = config('filesystems.disks.s3.region');
        $customUrl = config('filesystems.disks.s3.url');
        
        if ($customUrl) {
            return rtrim($customUrl, '/') . '/' . ltrim($path, '/');
        }
        
        return "https://{$bucket}.s3.{$region}.amazonaws.com/{$path}";
    }
}
