<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseVideo;
use App\Services\VideoEncryptionService;
use App\Services\VideoStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class VideoController extends Controller
{
    /**
     * Show the video data for editing.
     *
     * @param  int  $courseId
     * @param  int  $videoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($courseId, $videoId)
    {
        $user = Auth::user();

        // Verify the instructor owns this course
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', $user->user_id)
            ->firstOrFail();

        // Find the video
        $video = CourseVideo::where('video_id', $videoId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        return response()->json([
            'video' => $video
        ]);
    }

    /**
     * Store a newly created video resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $courseId)
    {
        // Start timing the entire process
        $processingStartTime = microtime(true);
        
        // Enhanced logging for debugging upload issues
        Log::debug('================== VIDEO UPLOAD DEBUGGING ==================');
        Log::debug('PHP temp directory details:', [
            'upload_tmp_dir' => ini_get('upload_tmp_dir'),
            'sys_temp_dir' => sys_get_temp_dir(),
            'temp_dir_exists' => file_exists(ini_get('upload_tmp_dir') ?: sys_get_temp_dir()),
            'temp_dir_writable' => is_writable(ini_get('upload_tmp_dir') ?: sys_get_temp_dir()),
            'open_basedir' => ini_get('open_basedir')
        ]);
        
        // Windows-specific handling
        $isWindows = in_array(PHP_OS, ['WIN32', 'WINNT', 'Windows']);
        if ($isWindows) {
            Log::info('Running on Windows system - applying Windows-specific path handling');
        }
        
        // Log the request for debugging
        Log::info('Video upload request received', [
            'course_id' => $courseId,
            'section_id' => $request->input('section_id'),
            'request_has_file' => $request->hasFile('video_file'),
            'request_has_thumbnail' => $request->hasFile('thumbnail'),
            'video_type' => $request->input('video_type'),
            'php_version' => PHP_VERSION,
            'max_file_size' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time')
        ]);

        try {
            $user = Auth::user();

            // Verify the instructor owns this course
            $course = Course::where('course_id', $courseId)
                ->where('instructor_id', $user->user_id)
                ->firstOrFail();

            // Check if course_videos table exists
            if (!Schema::hasTable('course_videos')) {
                throw new \Exception('Video feature is not available at the moment.');
            }

            // Check server environment
            Log::info('Server environment', [
                'storage_path' => storage_path(),
                'public_path' => public_path(),
                'app_path' => app_path(),
                'base_path' => base_path(),
                'storage_app_public_exists' => file_exists(storage_path('app/public')),
                'storage_app_public_writable' => is_writable(storage_path('app/public'))
            ]);

            // Validate request
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video_type' => 'required|in:upload,external',
                'duration_seconds' => 'nullable|integer|min:0',
                'is_free_preview' => 'nullable|boolean',
                'thumbnail' => 'nullable|image|max:5120', // 5MB max
                'section_id' => 'required|exists:course_sections,section_id'
            ];

            // Add conditional validation rules based on video type
            if ($request->video_type === 'upload') {
                $rules['video_file'] = 'required|file|mimes:mp4,webm,mov|max:204800'; // 200MB max
                // No validation for video_url when uploading a file
            } else {
                $rules['video_url'] = 'required|url';
                // No validation for video_file when using external URL
            }

            $validated = $request->validate($rules);

            // Calculate maximum position for the section
            $maxPosition = CourseVideo::where('section_id', $validated['section_id'])->max('position') ?? 0;

            // Create video record
            $video = new CourseVideo();
            $video->course_id = $courseId;
            $video->section_id = $validated['section_id'];
            $video->title = $validated['title'];
            $video->description = $validated['description'] ?? null;
            $video->duration_seconds = $validated['duration_seconds'] ?? 0;
            $video->position = $maxPosition + 1;
            $video->sequence_order = CourseVideo::where('course_id', $courseId)->max('sequence_order') + 1; // For backward compatibility
            $video->is_free_preview = $request->has('is_free_preview');
            $video->is_encrypted = false; // Default value

            // Handle video based on type
            if ($request->video_type === 'external') {
                // Store external video URL
                $video->video_url = $validated['video_url'];
                $video->video_path = null;
                $video->storage_disk = null;
                
                Log::info('External video URL saved', [
                    'title' => $video->title,
                    'video_url' => $video->video_url
                ]);
            } else if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
                if ($request->hasFile('video_file')) {
                    $file = $request->file('video_file');
                    
                    // Fix for SplFileInfo::getSize() issue on Windows
                    try {
                        $fileSize = $file->getSize();
                    } catch (\Exception $e) {
                        Log::warning('Error getting file size using getSize(): ' . $e->getMessage());
                        // Try alternative method to get file size
                        $tempPath = $file->getPathname();
                        if (file_exists($tempPath)) {
                            try {
                                $fileSize = filesize($tempPath);
                                Log::info('Got file size using filesize() instead: ' . $fileSize);
                            } catch (\Exception $e2) {
                                Log::error('Also failed to get size with filesize(): ' . $e2->getMessage());
                                // Use a placeholder size to allow upload to continue
                                $fileSize = $request->header('Content-Length') ?: 1;
                                Log::info('Using Content-Length as fallback: ' . $fileSize);
                            }
                } else {
                            Log::error('Temp file does not exist: ' . $tempPath);
                            throw new \Exception('Uploaded file cannot be accessed. Try uploading again.');
                        }
                    }
                    
                    // Log file information
                    Log::info('Video file received', [
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => $fileSize,
                        'file_extension' => $file->getClientOriginalExtension(),
                        'mime_type' => $file->getMimeType()
                    ]);
                    
                    // Only process if the file has content
                    if ($fileSize > 0) {
                        // Log upload start time
                        $uploadStartTime = microtime(true);
                        $localPath = null;
                        $fullPath = null;
                        $fileStored = false;
                        $fileName = null; // Initialize fileName
                        $directoryPath = null; // Initialize directoryPath

                        try {
                            // Generate a unique filename
                            $fileName = Str::slug($validated['title']) . '-' . time() . '.' . $file->getClientOriginalExtension();
                            // Define the target directory relative to the storage/app/public disk
                            $directoryPath = 'courses/' . $courseId . '/videos';

                            Log::info('Attempting to store video using Storage::putFileAs()', [
                                'disk' => 'public',
                                'directory' => $directoryPath,
                                'filename' => $fileName
                            ]);

                            // ** Primary Method: Use Laravel Storage facade **
                            // This handles directory creation and path normalization automatically.
                            $storedPath = Storage::disk('public')->putFileAs(
                                $directoryPath, // Directory relative to the disk root
                                $file,          // Uploaded file object
                                $fileName       // Desired filename
                            );

                            if ($storedPath) {
                                $localPath = $storedPath; // Path relative to the disk root (e.g., 'courses/1/videos/video-123.mp4')
                                $fullPath = Storage::disk('public')->path($storedPath); // Absolute path on the server
                                $fileStored = true;
                                Log::info('Video stored successfully using Storage::putFileAs()', ['path' => $localPath, 'full_path' => $fullPath]);
                            } else {
                                Log::warning('Storage::putFileAs() returned false or null.');
                            }

                        } catch (\Exception $e) {
                            Log::error('Error storing video with Storage::putFileAs(): ' . $e->getMessage());
                            Log::error($e->getTraceAsString());
                            // Proceed to fallback method if Storage facade fails
                        }
                        
                        // ** Fallback Method: Try move_uploaded_file (If Storage::putFileAs failed) **
                        if (!$fileStored) {
                             Log::warning('Storage::putFileAs() failed. Attempting fallback with move_uploaded_file().');
                             try {
                                $fileName = Str::slug($validated['title']) . '-' . time() . '.' . $file->getClientOriginalExtension();
                                $relativeDirPath = 'courses/' . $courseId . '/videos';
                                $storageDirectory = storage_path('app/public/' . $relativeDirPath);
                                $targetPath = $storageDirectory . DIRECTORY_SEPARATOR . $fileName;

                                // Ensure directory exists (manual creation for fallback)
                                if (!file_exists($storageDirectory)) {
                                    if (!mkdir($storageDirectory, 0777, true)) {
                                        Log::error('Fallback: Failed to create directory: ' . $storageDirectory);
                                        throw new \Exception("Fallback: Failed to create storage directory: $storageDirectory");
                                    }
                                    Log::info('Fallback: Created directory: ' . $storageDirectory);
                                } else if (!is_writable($storageDirectory)) {
                                     Log::error('Fallback: Directory not writable: ' . $storageDirectory);
                                    throw new \Exception("Fallback: Storage directory not writable: $storageDirectory");
                                }
                                
                                Log::info('Attempting fallback move_uploaded_file()', [
                                    'temp_path' => $file->getPathname(),
                                    'target_path' => $targetPath
                                ]);

                                if (move_uploaded_file($file->getPathname(), $targetPath)) {
                                     $localPath = $relativeDirPath . '/' . $fileName;
                                     $fullPath = $targetPath;
                                     $fileStored = true;
                                     Log::info('Video stored successfully using move_uploaded_file() fallback.', ['path' => $localPath, 'full_path' => $fullPath]);
                                } else {
                                     Log::error('Fallback move_uploaded_file() failed.');
                                }
                             } catch (\Exception $e) {
                                 Log::error('Error during move_uploaded_file() fallback: ' . $e->getMessage());
                             }
                        }

                        // If both methods failed, throw an exception
                        if (!$fileStored) {
                            Log::error('All video storage methods failed.');
                            throw new \Exception("Failed to store uploaded video file after multiple attempts.");
                        }

                        // Verify file exists after successful storage report
                        if (!file_exists($fullPath)) {
                             Log::error('CRITICAL: File reported as stored but does not exist!', [
                                 'attempted_full_path' => $fullPath,
                                 'local_path_variable' => $localPath,
                                 'method_used' => $storedPath ? 'Storage::putFileAs' : 'move_uploaded_file'
                             ]);
                            throw new \Exception("Failed to store video file: File verification failed after upload.");
                        }
                        
                        $uploadResult = [
                            'path' => $localPath, // Use the relative path for DB
                            'disk' => 'public',
                            'url' => \Illuminate\Support\Facades\Storage::url('public/' . $localPath)
                        ];

                        Log::info('Video storage process completed successfully', [
                            'path' => $uploadResult['path'],
                            'disk' => $uploadResult['disk'],
                            'final_file_exists' => file_exists($fullPath),
                            'final_file_size' => filesize($fullPath)
                        ]);

                        // Calculate and log upload duration
                        $uploadEndTime = microtime(true);
                        $uploadDuration = $uploadEndTime - $uploadStartTime;

                        Log::info('Upload completed', [
                            'duration' => round($uploadDuration, 2) . ' seconds',
                            'file_size_mb' => round($fileSize / (1024 * 1024), 2),
                            'speed_mbps' => ($uploadDuration > 0) ? round(($fileSize / 1024 / 1024) / $uploadDuration, 2) : 'N/A'
                        ]);

                        // Store video details in database
                        $video->video_path = $uploadResult['path'];
                        $video->storage_disk = $uploadResult['disk'];
                        $video->video_url = null;
                    } else {
                        throw new \Exception('Uploaded file is empty (0 bytes)');
                    }
                } else {
                    throw new \Exception('Video file is missing');
                }
            }

            // Calculate total processing time
            $processingEndTime = microtime(true);
            $totalProcessingTime = $processingEndTime - $processingStartTime;
            Log::info('Total video processing time', [
                'total_seconds' => $totalProcessingTime,
                'video_size' => $request->hasFile('video_file') ? $request->file('video_file')->getSize() : 'N/A (external URL)'
            ]);

            // Store thumbnail if provided
            if ($request->hasFile('thumbnail')) {
                // Check if the thumbnail was uploaded successfully
                if (!$request->file('thumbnail')->isValid()) {
                    throw new \Exception('Thumbnail upload failed: ' . $request->file('thumbnail')->getErrorMessage());
                }

                // تحديد مكان التخزين بناءً على الإعدادات
                $useS3 = Config::get('filesystems.default') === 's3' || Config::get('filesystems.cloud') === 's3';

                if ($useS3) {
                    // استخدام خدمة التخزين السحابي
                    $videoStorageService = new VideoStorageService();

                    // رفع الصورة المصغرة إلى S3
                    $uploadResult = $videoStorageService->uploadThumbnail(
                        $request->file('thumbnail'),
                        $courseId,
                        $validated['title']
                    );

                    // تخزين معلومات الملف
                    $video->thumbnail_url = $uploadResult['path'];

                    // تسجيل نجاح الرفع
                    Log::info('Thumbnail uploaded to S3 successfully', [
                        'path' => $uploadResult['path'],
                        'url' => $uploadResult['url']
                    ]);
                } else {
                    // استخدام التخزين المحلي
                    // Create directory if it doesn't exist
                    $thumbDirectory = storage_path('app/public/courses/' . $courseId . '/thumbnails');
                    if (!file_exists($thumbDirectory)) {
                        if (!mkdir($thumbDirectory, 0755, true)) {
                            throw new \Exception('Failed to create thumbnail directory: ' . $thumbDirectory);
                        }
                    }

                    $thumbName = Str::slug($validated['title']) . '-thumb-' . time() . '.' . $request->file('thumbnail')->getClientOriginalExtension();
                    $thumbPath = 'courses/' . $courseId . '/thumbnails/' . $thumbName;
                    $fullThumbPath = $thumbDirectory . '/' . $thumbName;

                    // Get the uploaded thumbnail file
                    $uploadedThumb = $request->file('thumbnail');

                    // Log the paths for debugging
                    Log::info('Attempting to store thumbnail', [
                        'storage_directory' => $thumbDirectory,
                        'full_path' => $fullThumbPath,
                        'directory_exists' => file_exists($thumbDirectory),
                        'is_writable' => is_writable($thumbDirectory),
                        'file_size' => $uploadedThumb->getSize(),
                        'file_mime' => $uploadedThumb->getMimeType()
                    ]);

                    try {
                        // Move the uploaded thumbnail to the destination
                        if (!$uploadedThumb->move($thumbDirectory, $thumbName)) {
                            throw new \Exception('Failed to move uploaded thumbnail to destination');
                        }

                        // Verify the thumbnail was stored successfully
                        if (!file_exists($fullThumbPath)) {
                            Log::error('Thumbnail not found after storage attempt', [
                                'full_path' => $fullThumbPath
                            ]);
                            throw new \Exception('Failed to store thumbnail - File not found after storage');
                        }

                        // Log success
                        Log::info('Thumbnail stored successfully', [
                            'full_path' => $fullThumbPath,
                            'file_exists' => file_exists($fullThumbPath),
                            'file_size' => filesize($fullThumbPath)
                        ]);

                        $video->thumbnail_url = 'storage/' . $thumbPath;
                    } catch (\Exception $e) {
                        Log::error('Exception during thumbnail storage: ' . $e->getMessage());
                        Log::error($e->getTraceAsString());
                        // Continue without thumbnail
                        Log::warning('Continuing without thumbnail due to storage error');
                    }
                }
            }

            $video->save();

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Video added successfully',
                    'video' => $video,
                    'processing_time' => round($totalProcessingTime, 2) . ' seconds'
                ]);
            }

            return redirect()->back()->with('success', 'Video added successfully');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Video upload error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while uploading the video: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'An error occurred while uploading the video: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified video.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @param  int  $videoId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $courseId, $videoId)
    {
        try {
        $user = Auth::user();

        // Verify the instructor owns this course
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', $user->user_id)
            ->firstOrFail();

        // Find the video
        $video = CourseVideo::where('video_id', $videoId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Validate request
            $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_seconds' => 'required|integer|min:1',
            'is_free_preview' => 'nullable|boolean',
                'thumbnail' => 'nullable|image|max:5120', // 5MB max
                'section_id' => 'required|exists:course_sections,section_id'
            ];

            // Video URL is optional during update
            if ($request->has('video_url')) {
                $rules['video_url'] = 'url';
            }

            $validated = $request->validate($rules);

            // Check if section changed
            $sectionChanged = $video->section_id != $validated['section_id'];
            
            // Update video record
        $video->title = $validated['title'];
            $video->description = $validated['description'] ?? $video->description;
        $video->duration_seconds = $validated['duration_seconds'];
        $video->is_free_preview = $request->has('is_free_preview');

            // If section changed, update position
            if ($sectionChanged) {
                $maxPosition = CourseVideo::where('section_id', $validated['section_id'])->max('position') ?? 0;
                $video->section_id = $validated['section_id'];
                $video->position = $maxPosition + 1;
            }

            // Update video URL if provided
            if ($request->has('video_url')) {
                $video->video_url = $request->video_url;
            }

        // Handle video based on type
        if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
            // تحديد مكان التخزين بناءً على الإعدادات
            $useS3 = Config::get('filesystems.default') === 's3' || Config::get('filesystems.cloud') === 's3';

            // حذف الملف القديم إذا كان موجودًا
            if ($video->video_path) {
                if ($video->storage_disk === 's3') {
                    // حذف من S3
                    $videoStorageService = new VideoStorageService();
                    $videoStorageService->deleteFile($video->video_path);
                } else {
                    // حذف من التخزين المحلي
                    $oldPath = str_replace('storage/', 'public/', $video->video_path);
                    Storage::delete($oldPath);
                }
            }

            if ($useS3) {
                // استخدام خدمة التخزين السحابي
                $videoStorageService = new VideoStorageService();

                // رفع الفيديو إلى S3
                $uploadResult = $videoStorageService->uploadVideo(
                    $request->file('video_file'),
                    $courseId,
                    $validated['title']
                );

                // تخزين معلومات الملف
                $video->video_path = $uploadResult['path'];
                $video->storage_disk = 's3';
                $video->video_url = null; // تعيين الرابط الخارجي كقيمة فارغة
                $video->is_encrypted = false; // لا يتم تشفير الفيديوهات على S3 في هذه المرحلة

                // تسجيل نجاح الرفع
                Log::info('Video uploaded to S3 successfully during update', [
                    'path' => $uploadResult['path'],
                    'url' => $uploadResult['url']
                ]);
            } else {
                // استخدام التخزين المحلي
                // Generate a unique filename
                $fileName = Str::slug($validated['title']) . '-' . time() . '.' . $request->file('video_file')->getClientOriginalExtension();
                $filePath = 'courses/' . $courseId . '/videos/' . $fileName;

                // Get the uploaded file
                $uploadedFile = $request->file('video_file');

                // Create the full path where the file will be stored
                $storageDirectory = storage_path('app/public/' . dirname($filePath));
                $fullPath = $storageDirectory . '/' . basename($filePath);

                // Log the paths for debugging
                Log::info('Attempting to store video file during update', [
                    'storage_directory' => $storageDirectory,
                    'full_path' => $fullPath,
                    'directory_exists' => file_exists($storageDirectory),
                    'is_writable' => is_writable($storageDirectory),
                    'file_size' => $uploadedFile->getSize(),
                    'file_mime' => $uploadedFile->getMimeType()
                ]);

                // Ensure the directory exists
                if (!file_exists($storageDirectory)) {
                    if (!mkdir($storageDirectory, 0755, true)) {
                        throw new \Exception('Failed to create storage directory: ' . $storageDirectory);
                    }
                }

                // Move the uploaded file to the destination
                if (!$uploadedFile->move($storageDirectory, basename($filePath))) {
                    throw new \Exception('Failed to move uploaded file to destination');
                }

                // Verify the file was stored successfully
                if (!file_exists($fullPath)) {
                    Log::error('File not found after storage attempt during update', [
                        'full_path' => $fullPath
                    ]);
                    throw new \Exception('Failed to store video file - File not found after storage');
                }

                $video->video_path = 'storage/' . $filePath;
                $video->video_url = null; // Explicitly set to null for uploaded videos
                $video->storage_disk = 'local'; // تعيين التخزين المحلي
            }
        } elseif ($request->video_type === 'external') {
            // Update external video URL
            $video->video_url = $validated['video_url'];

            // Delete old video file if exists
            if ($video->video_path) {
                $oldPath = str_replace('storage/', 'public/', $video->video_path);
                Storage::delete($oldPath);
                $video->video_path = null;
            }
        }

        // Update thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            // تحديد مكان التخزين بناءً على الإعدادات
            $useS3 = Config::get('filesystems.default') === 's3' || Config::get('filesystems.cloud') === 's3';

            // حذف الصورة المصغرة القديمة إذا كانت موجودة
            if ($video->thumbnail_url) {
                if ($video->storage_disk === 's3') {
                    // حذف من S3
                    $videoStorageService = new VideoStorageService();
                        $result = $videoStorageService->deleteFile($video->thumbnail_url);

                        // تسجيل محاولة الحذف
                        Log::info('Attempting to delete thumbnail file from S3', [
                            'thumbnail_url' => $video->thumbnail_url,
                            'result' => $result
                        ]);
                } else {
                    // حذف من التخزين المحلي
                        // Get the full path to the thumbnail
                        $fullThumbPath = storage_path('app/public/' . str_replace('storage/', '', $video->thumbnail_url));

                        // Log the deletion attempt
                        Log::info('Attempting to delete thumbnail file from local storage', [
                            'thumbnail_url' => $video->thumbnail_url,
                            'full_path' => $fullThumbPath,
                            'file_exists' => file_exists($fullThumbPath)
                        ]);

                        // Delete the file if it exists
                        if (file_exists($fullThumbPath)) {
                            if (is_dir($fullThumbPath)) {
                                // Es un directorio, utiliza rmdir o Storage::deleteDirectory
                                Log::info('Detected directory instead of file, using directory deletion');
                                Storage::deleteDirectory('public/courses/' . $courseId . '/thumbnails');
                            } else {
                                // Es un archivo, usa unlink normalmente
                                unlink($fullThumbPath);
                            }
                        } else {
                            // Fallback to Laravel's Storage facade
                            $thumbPath = str_replace('storage/', 'public/', $video->thumbnail_url);
                            Storage::delete($thumbPath);
                        }
                    }
            }

            if ($useS3) {
                // استخدام خدمة التخزين السحابي
                $videoStorageService = new VideoStorageService();

                // رفع الصورة المصغرة إلى S3
                $uploadResult = $videoStorageService->uploadThumbnail(
                    $request->file('thumbnail'),
                    $courseId,
                    $validated['title']
                );

                // تخزين معلومات الملف
                $video->thumbnail_url = $uploadResult['path'];

                // تسجيل نجاح الرفع
                Log::info('Thumbnail uploaded to S3 successfully during update', [
                    'path' => $uploadResult['path'],
                    'url' => $uploadResult['url']
                ]);
            } else {
                // استخدام التخزين المحلي
                $thumbName = Str::slug($validated['title']) . '-thumb-' . time() . '.' . $request->file('thumbnail')->getClientOriginalExtension();
                $thumbPath = 'courses/' . $courseId . '/thumbnails/' . $thumbName;

                // Get the uploaded thumbnail file
                $uploadedThumb = $request->file('thumbnail');

                // Create the full path where the file will be stored
                $thumbDirectory = storage_path('app/public/' . dirname($thumbPath));
                $fullThumbPath = $thumbDirectory . '/' . basename($thumbPath);

                // Log the paths for debugging
                Log::info('Attempting to store thumbnail during update', [
                    'storage_directory' => $thumbDirectory,
                    'full_path' => $fullThumbPath,
                    'directory_exists' => file_exists($thumbDirectory),
                    'is_writable' => is_writable($thumbDirectory),
                    'file_size' => $uploadedThumb->getSize(),
                    'file_mime' => $uploadedThumb->getMimeType()
                ]);

                // Ensure the directory exists
                if (!file_exists($thumbDirectory)) {
                    if (!mkdir($thumbDirectory, 0755, true)) {
                        throw new \Exception('Failed to create thumbnail directory: ' . $thumbDirectory);
                    }
                }

                // Move the uploaded thumbnail to the destination
                if (!$uploadedThumb->move($thumbDirectory, basename($thumbPath))) {
                    throw new \Exception('Failed to move uploaded thumbnail to destination');
                }

                // Verify the thumbnail was stored successfully
                if (!file_exists($fullThumbPath)) {
                    Log::error('Thumbnail not found after storage attempt during update', [
                        'full_path' => $fullThumbPath
                    ]);
                    throw new \Exception('Failed to store thumbnail - File not found after storage');
                }

                $video->thumbnail_url = 'storage/' . $thumbPath;
            }
        }

        $video->save();

        return redirect()->back()->with('success', 'Video updated successfully');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Video update error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->back()->with('error', 'An error occurred while updating the video: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified video resource using POST method.
     * This is an alternative to destroy() method to handle cases where DELETE method is not allowed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @param  int  $videoId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function deleteVideo(Request $request, $courseId, $videoId)
    {
        try {
        $user = Auth::user();

        // Verify the instructor owns this course
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', $user->user_id)
            ->firstOrFail();

        // Find the video
        $video = CourseVideo::where('video_id', $videoId)
            ->where('course_id', $courseId)
            ->firstOrFail();

            // Capture video details for logging
            $videoDetails = [
                'video_id' => $video->video_id,
                'title' => $video->title,
                'video_path' => $video->video_path,
                'thumbnail_url' => $video->thumbnail_url,
                'storage_disk' => $video->storage_disk
            ];
            
            Log::info('Starting video deletion process', $videoDetails);
    
            // Clear video directories to ensure all related files are removed
            if ($video->storage_disk === 's3') {
                // Delete from S3
                $videoStorageService = new VideoStorageService();
                $result = $videoStorageService->deleteFile($video->video_path);
                Log::info('S3 video deletion attempt', ['result' => $result]);
                
                if ($video->thumbnail_url) {
                    $thumbResult = $videoStorageService->deleteFile($video->thumbnail_url);
                    Log::info('S3 thumbnail deletion attempt', ['result' => $thumbResult]);
                }
            } else {
                // Delete from local storage - using both direct access and Storage facade
                
                // Clean potential video paths (handle both formats)
                $videoDirectory1 = 'public/videos/' . $courseId;
                $videoDirectory2 = 'public/courses/' . $courseId . '/videos';
                
                Log::info('Attempting to clean video directories', [
                    'directory1' => $videoDirectory1,
                    'directory2' => $videoDirectory2
                ]);
                
                // Delete both possible directory structures
                Storage::deleteDirectory($videoDirectory1);
                Storage::deleteDirectory($videoDirectory2);
                
                // Same for thumbnails
                $thumbDirectory1 = 'public/thumbnails/' . $courseId;
                $thumbDirectory2 = 'public/courses/' . $courseId . '/thumbnails';
                
                Storage::deleteDirectory($thumbDirectory1);
                Storage::deleteDirectory($thumbDirectory2);
                
                // Also ensure the individual file gets deleted if it exists
                if ($video->video_path) {
                    $videoPath = str_replace('storage/', 'public/', $video->video_path);
                    Storage::delete($videoPath);
        }

        if ($video->thumbnail_url) {
                    $thumbPath = str_replace('storage/', 'public/', $video->thumbnail_url);
                    Storage::delete($thumbPath);
                }
                
                // Create directories again to ensure clean state
                Storage::makeDirectory($videoDirectory1);
                Storage::makeDirectory($videoDirectory2);
                
                Log::info('Video directories cleaned and recreated');
            }
    
            // Delete the record from database
        $video->delete();
            Log::info('Video database record deleted successfully');

            // Return appropriate response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Video deleted successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Video deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting video: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting video: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error deleting video: ' . $e->getMessage());
        }
    }

    /**
     * Update the positions of multiple videos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePositions(Request $request, $courseId)
    {
        $user = Auth::user();

        // Verify the instructor owns this course
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', $user->user_id)
            ->firstOrFail();

        $validated = $request->validate([
            'positions' => 'required|array',
            'positions.*.id' => 'required|exists:course_videos,video_id',
            'positions.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['positions'] as $position) {
            CourseVideo::where('video_id', $position['id'])
                ->where('course_id', $courseId)
                ->update(['sequence_order' => $position['order']]);
        }

        return response()->json(['message' => 'Video positions updated successfully']);
    }

    /**
     * Remove the specified video resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @param  int  $videoId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $courseId, $videoId)
    {
        return $this->deleteVideo($request, $courseId, $videoId);
    }

    /**
     * Get upload error message based on error code
     * 
     * @param int $errorCode PHP upload error code
     * @return string Human-readable error message
     */
    private function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }
}