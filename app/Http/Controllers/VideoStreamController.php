<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseVideo;
use App\Models\Enrollment;
use App\Models\VideoAccess;
use App\Models\VideoView;
use App\Services\VideoEncryptionService;
use App\Services\VideoStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VideoStreamController extends Controller
{
    /**
     * Stream a video file with security checks
     *
     * @param Request $request
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function stream(Request $request, $token)
    {
        try {
            // Find the video access record by token
            $videoAccess = VideoAccess::where('token', $token)
                ->where('expires_at', '>', now())
                ->first();

            if (!$videoAccess) {
                Log::warning('Invalid or expired token', ['token' => $token]);
                return response()->json(['error' => 'Invalid or expired token'], 403);
            }

            // Get the video
            $video = CourseVideo::find($videoAccess->video_id);
            if (!$video) {
                Log::warning('Video not found', ['video_id' => $videoAccess->video_id]);
                return response()->json(['error' => 'Video not found'], 404);
            }

            // Debug information - Log the video details
            Log::info('Video details found', [
                'video_id' => $video->video_id,
                'title' => $video->title,
                'path' => $video->video_path,
                'storage_disk' => $video->storage_disk,
                'is_encrypted' => $video->is_encrypted
            ]);

            // Update last accessed time
            $videoAccess->last_accessed_at = now();

            // Check for fingerprint (additional security)
            $fingerprint = $request->cookie('video_fingerprint');
            if (!$fingerprint && env('APP_ENV') === 'production') {
                // Log suspicious activity
                Log::warning('Video access attempt without fingerprint', [
                    'token' => $token,
                    'user_id' => $videoAccess->user_id,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                // For security, invalidate the token in production
                $videoAccess->expires_at = now()->subMinute();
                $videoAccess->save();

                return response()->json(['error' => 'Invalid request signature'], 403);
            }

            // Store the fingerprint with the access record
            if ($fingerprint) {
                $videoAccess->fingerprint = $fingerprint;
            }
            $videoAccess->save();

            // Record video view
            VideoView::create([
                'user_id' => $videoAccess->user_id,
                'video_id' => $video->video_id,
                'course_id' => $video->course_id,
                'view_date' => now(),
                'view_progress' => 0, // Initial progress
                'completed' => false,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Log the access
            Log::info('Video accessed', [
                'video_id' => $video->video_id,
                'user_id' => $videoAccess->user_id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Check if the video is external (YouTube, Vimeo, etc.)
            if ($video->video_url) {
                // If AJAX request or API request
                if ($request->ajax() || $request->wantsJson() || $request->header('Accept') == 'application/json') {
                    return response()->json([
                        'success' => true,
                        'videoInfo' => [
                            'video_id' => $video->video_id,
                            'title' => $video->title,
                            'description' => $video->description,
                            'video_url' => $video->video_url,
                            'is_external' => true
                        ]
                    ]);
                }
                
                // For regular requests, redirect to the external URL
                return redirect()->away($video->video_url);
            }

            // For local videos, check if it's encrypted
            if ($video->is_encrypted) {
                // Get the segment index from the request
                $segmentIndex = $request->query('segment', 0);

                // Get the video directory
                $videoDir = $video->video_path;

                // Get the manifest file
                $manifestPath = $videoDir . '/manifest.json';
                if (!Storage::exists($manifestPath)) {
                    Log::error('Video manifest not found', [
                        'video_id' => $video->video_id,
                        'manifest_path' => $manifestPath
                    ]);
                    return response()->json(['error' => 'Video manifest not found'], 404);
                }

                // Parse the manifest
                $manifest = json_decode(Storage::get($manifestPath), true);
                if (!$manifest) {
                    Log::error('Invalid video manifest', [
                        'video_id' => $video->video_id,
                        'manifest_path' => $manifestPath
                    ]);
                    return response()->json(['error' => 'Invalid video manifest'], 500);
                }

                // Check if the requested segment exists
                if ($segmentIndex >= count($manifest['segments'])) {
                    Log::error('Segment not found', [
                        'video_id' => $video->video_id,
                        'segment_index' => $segmentIndex,
                        'total_segments' => count($manifest['segments'])
                    ]);
                    return response()->json(['error' => 'Segment not found'], 404);
                }

                // Get the segment path
                $segmentPath = $manifest['segments'][$segmentIndex]['path'];
                if (!Storage::exists($segmentPath)) {
                    Log::error('Segment file not found', [
                        'video_id' => $video->video_id,
                        'segment_path' => $segmentPath
                    ]);
                    return response()->json(['error' => 'Segment file not found'], 404);
                }

                // Get the key path
                $keyPath = $videoDir . '/key.txt';
                if (!Storage::exists($keyPath)) {
                    Log::error('Encryption key not found', [
                        'video_id' => $video->video_id,
                        'key_path' => $keyPath
                    ]);
                    return response()->json(['error' => 'Encryption key not found'], 404);
                }

                try {
                    // Decrypt the segment
                    $videoEncryptionService = new VideoEncryptionService();
                    $decryptedContent = $videoEncryptionService->decryptSegment($segmentPath, $keyPath, $segmentIndex, $videoDir);

                    // Set the content type and size
                    $size = strlen($decryptedContent);
                    $mime = 'video/mp4';

                    // Stream the decrypted content
                    $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() use ($decryptedContent) {
                        echo $decryptedContent;
                    }, 200, [
                        'Content-Type' => $mime,
                        'Content-Length' => $size,
                        'Accept-Ranges' => 'bytes',
                        'Content-Disposition' => 'inline',
                        'X-Frame-Options' => 'SAMEORIGIN',
                        'X-Content-Type-Options' => 'nosniff',
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                        'Access-Control-Allow-Origin' => '*',
                        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                        'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept, Range'
                    ]);

                    return $response;
                } catch (\Exception $e) {
                    Log::error('Failed to decrypt segment: ' . $e->getMessage(), [
                        'video_id' => $video->video_id,
                        'segment_index' => $segmentIndex,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return response()->json(['error' => 'Failed to decrypt video segment: ' . $e->getMessage()], 500);
                }
            } else {
                // For non-encrypted videos, stream the file directly
                if ($video->storage_disk === 's3') {
                    // التعامل مع الفيديو من S3
                    try {
                        // Usar AWS SDK directamente si está disponible
                        if (class_exists('Aws\S3\S3Client')) {
                            // Obtener configuración de S3 desde el archivo de configuración
                            $s3Config = config('filesystems.disks.s3');
                            $bucket = $s3Config['bucket'];
                            $region = $s3Config['region'];
                            
                            // Construir URL de S3 manualmente
                            $s3Url = "https://{$bucket}.s3.{$region}.amazonaws.com/{$video->video_path}";
                            
                            if (isset($s3Config['url'])) {
                                // Si hay una URL base configurada, usarla
                                $s3Url = $s3Config['url'] . '/' . $video->video_path;
                            }
                        } else {
                            // Construir URL genérica como último recurso
                            $s3Url = env('AWS_URL', 'https://s3.amazonaws.com') . 
                                   '/' . env('AWS_BUCKET', 'default-bucket') . 
                                   '/' . $video->video_path;
                        }
                        
                        // تسجيل الوصول
                        Log::info('Redirecting to S3 video', [
                            'video_id' => $video->video_id,
                            'path' => $video->video_path,
                            'url' => $s3Url
                        ]);
                        
                        // إعادة توجيه إلى رابط S3 مع إضافة بارامترات الأمان
                        $separator = (strpos($s3Url, '?') !== false) ? '&' : '?';
                        $response = redirect()->away($s3Url . $separator . 'token=' . $token);

                        // Add CORS headers for video streaming
                        $response->headers->set('Access-Control-Allow-Origin', '*');
                        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
                        $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Range');

                        return $response;
                    } catch (\Exception $e) {
                        // في حالة فشل إنشاء رابط S3 (مثلاً عند عدم تكوين S3 بشكل صحيح)
                        Log::error('Failed to generate S3 URL: ' . $e->getMessage(), [
                            'video_id' => $video->video_id,
                            'path' => $video->video_path,
                            'error' => $e->getMessage()
                        ]);
                        
                        return response()->json([
                            'error' => 'Could not access cloud storage. Please contact support.',
                            'message' => env('APP_DEBUG') ? $e->getMessage() : null
                        ], 500);
                    }
                } else {
                    // Handle local videos - This needs improvement
                    $originalPath = $video->video_path;
                    $isPrivate = str_starts_with($originalPath, 'private/');
                    
                    // Log video information for debugging
                    Log::info('Attempting to access video', [
                        'video_id' => $video->video_id,
                        'original_path' => $originalPath,
                        'is_private' => $isPrivate,
                        'is_encrypted' => $video->is_encrypted
                    ]);
                    
                    // Find the correct video path - Try multiple options
                    $potentialPaths = [
                        $originalPath,
                        str_replace('storage/', 'public/', $originalPath),
                        'public/' . $originalPath,
                        str_replace('public/', '', $originalPath),
                        'videos/' . $video->course_id . '/' . basename($originalPath),
                        'public/videos/' . $video->course_id . '/' . basename($originalPath),
                        'private/videos/' . $video->course_id . '/' . basename($originalPath),
                        'courses/' . $video->course_id . '/videos/' . basename($originalPath),
                        'public/courses/' . $video->course_id . '/videos/' . basename($originalPath),
                        // Search in any video directory by basename only - last resort
                        'videos/' . $video->course_id . '/*', // Try to find any file in the course videos directory
                        'public/videos/' . $video->course_id . '/*',
                        'courses/' . $video->course_id . '/videos/*',
                        'public/courses/' . $video->course_id . '/videos/*',
                        // Add Windows-specific path conversions
                        str_replace('\\', '/', $originalPath),
                        str_replace('/', '\\', $originalPath)
                    ];
                    
                    Log::info('Trying potential video paths', [
                        'paths' => $potentialPaths,
                        'video_path_from_db' => $originalPath,
                        'basename' => basename($originalPath)
                    ]);
                    
                    $foundPath = null;
                    foreach ($potentialPaths as $path) {
                        if (strpos($path, '*') !== false) {
                            // Handle wildcard searches
                            $directory = dirname($path);
                            if (Storage::exists($directory)) {
                                // Get all files in this directory
                                $files = Storage::files($directory);
                                if (!empty($files)) {
                                    // Sort by creation time (newest first)
                                    usort($files, function($a, $b) {
                                        return Storage::lastModified($b) - Storage::lastModified($a);
                                    });
                                    
                                    // Get the most recent video file
                                    foreach ($files as $file) {
                                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                                        if (in_array(strtolower($ext), ['mp4', 'webm', 'mov', 'avi'])) {
                                            $foundPath = $file;
                                            Log::info('Found most recent video in directory', [
                                                'directory' => $directory,
                                                'file' => $file
                                            ]);
                                            break 2; // Break both loops
                                        }
                                    }
                                }
                            }
                        } elseif (Storage::exists($path)) {
                            $foundPath = $path;
                            Log::info('Found video at exact path', ['path' => $path]);
                            break;
                        }
                    }
                    
                    if (!$foundPath) {
                        Log::error('Video file not found after trying multiple paths', [
                            'video_id' => $video->video_id,
                            'original_path' => $originalPath,
                            'tried_paths' => $potentialPaths
                        ]);
                        
                        return response()->json([
                            'error' => 'Video file not found. Please contact support.',
                            'video_id' => $video->video_id,
                            'path' => $originalPath
                        ], 404);
                    }
                    
                    // Get the file from storage and prepare it for streaming
                    $file = Storage::path($foundPath);
                    
                    // Verify file is accessible
                    if (!file_exists($file)) {
                        Log::error('File exists in storage but cannot be physically accessed', [
                            'video_id' => $video->video_id,
                            'path' => $foundPath,
                            'file' => $file
                        ]);
                        return response()->json([
                            'error' => 'Video file cannot be accessed. Please contact support.',
                            'video_id' => $video->video_id,
                            'path' => $foundPath
                        ], 500);
                    }
                    
                    $size = filesize($file);
                    $mime = mime_content_type($file);
                    
                    Log::info('Streaming video file', [
                        'video_id' => $video->video_id,
                        'path' => $foundPath,
                        'file' => $file,
                        'size' => $size,
                        'mime' => $mime
                    ]);
                    
                    // Set streaming headers
                    $headers = [
                        'Content-Type' => $mime,
                        'Content-Length' => $size,
                        'Accept-Ranges' => 'bytes',
                        'Content-Disposition' => 'inline; filename="'.basename($file).'"',
                        'X-Frame-Options' => 'SAMEORIGIN',
                        'X-Content-Type-Options' => 'nosniff',
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                        'Access-Control-Allow-Origin' => '*',
                        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                        'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept, Range'
                    ];

                    // Handle range requests for video seeking
                    $range = $request->header('Range');
                    if ($range) {
                        return $this->handleRangeRequest($file, $size, $mime, $range);
                    }

                    try {
                        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() use ($file) {
                            set_time_limit(0);
                            $stream = fopen($file, 'rb');
                            if ($stream === false) {
                                Log::error('Failed to open file stream', ['file' => $file]);
                                exit;
                            }
                            
                            // Output the file in chunks to avoid memory issues with large files
                            $bufferSize = 102400; // 100KB chunks
                            while (!feof($stream)) {
                                echo fread($stream, $bufferSize);
                                flush();
                            }
                            fclose($stream);
                        }, 200, $headers);

                        // Add CORS headers for video streaming
                        $response->headers->set('Access-Control-Allow-Origin', '*');
                        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
                        $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Range');

                        return $response;
                    } catch (\Exception $e) {
                        Log::error('Error in stream response', [
                            'error' => $e->getMessage(),
                            'file' => $file
                        ]);
                        throw $e;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Unexpected error in video streaming: ' . $e->getMessage(), [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An unexpected error occurred while streaming the video: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle range requests for video seeking
     *
     * @param string $file
     * @param int $size
     * @param string $mime
     * @param string $range
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function handleRangeRequest($file, $size, $mime, $range)
    {
        $ranges = explode('=', $range, 2);
        if (count($ranges) != 2 || $ranges[0] != 'bytes') {
            // Malformed range header
            return response('Requested Range Not Satisfiable', 416);
        }
        
        $ranges = explode('-', $ranges[1], 2);
        if (count($ranges) != 2) {
            // Malformed range header
            return response('Requested Range Not Satisfiable', 416);
        }

        $start = isset($ranges[0]) && strlen($ranges[0]) > 0 ? intval($ranges[0]) : 0;
        $end = isset($ranges[1]) && strlen($ranges[1]) > 0 ? intval($ranges[1]) : $size - 1;
        
        // Validate range
        if ($start >= $size || $end >= $size || $start > $end) {
            return response('Requested Range Not Satisfiable', 416);
        }

        $length = $end - $start + 1;

        $headers = [
            'Content-Type' => $mime,
            'Content-Length' => $length,
            'Content-Range' => "bytes $start-$end/$size",
            'Accept-Ranges' => 'bytes',
            'Content-Disposition' => 'inline; filename="'.basename($file).'"',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept, Range'
        ];

        return Response::stream(function() use ($file, $start, $length) {
            set_time_limit(0);
            $stream = fopen($file, 'rb');
            if ($stream === false) {
                Log::error('Failed to open file stream for range request', ['file' => $file]);
                exit;
            }
            
            fseek($stream, $start);
            $bufferSize = min(102400, $length); // 100KB chunks or less if length is smaller
            $bytesRemaining = $length;
            
            while (!feof($stream) && $bytesRemaining > 0) {
                $readSize = min($bufferSize, $bytesRemaining);
                $data = fread($stream, $readSize);
                echo $data;
                flush();
                $bytesRemaining -= strlen($data);
                
                if (connection_aborted()) {
                    break;
                }
            }
            
            fclose($stream);
        }, 206, $headers);
    }

    /**
     * Update video progress
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProgress(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'video_id' => 'required|integer|exists:course_videos,video_id',
            'progress' => 'required|numeric|min:0|max:100',
            'completed' => 'required|boolean',
        ]);

        // Find the video
        $video = CourseVideo::find($validated['video_id']);
        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        // Check if the user is enrolled in the course or is the instructor
        $course = Course::find($video->course_id);
        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $isInstructor = $course->instructor_id == $user->user_id;
        $isEnrolled = Enrollment::where('student_id', $user->user_id)
            ->where('course_id', $video->course_id)
            ->exists();

        if (!$isInstructor && !$isEnrolled && !$video->is_free_preview) {
            return response()->json(['error' => 'You do not have access to this video'], 403);
        }

        // Update or create video view record
        $videoView = VideoView::updateOrCreate(
            [
                'user_id' => $user->user_id,
                'video_id' => $video->video_id,
                'course_id' => $video->course_id,
                'view_date' => now()->format('Y-m-d'),
            ],
            [
                'view_progress' => $validated['progress'],
                'completed' => $validated['completed'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully',
            'data' => $videoView
        ]);
    }

    /**
     * Generate a secure token for video access
     *
     * @param Request $request
     * @param int $courseId
     * @param int $videoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAccessToken(Request $request, $courseId, $videoId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the video
        $video = CourseVideo::where('video_id', $videoId)
            ->where('course_id', $courseId)
            ->first();

        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        // Add a timestamp to prevent browser caching
        $timestamp = time();
        
        // Check if the user is the instructor of the course or enrolled in the course
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $isInstructor = $course->instructor_id == $user->user_id;
        $isEnrolled = Enrollment::where('student_id', $user->user_id)
            ->where('course_id', $courseId)
            ->exists();

        // Check if the video is a free preview
        $isFreePreview = $video->is_free_preview;

        if (!$isInstructor && !$isEnrolled && !$isFreePreview) {
            return response()->json(['error' => 'You do not have access to this video'], 403);
        }

        // Generate a unique token
        $token = Str::random(64);

        // Set expiration time (e.g., 2 hours from now)
        $expiresAt = now()->addHours(2);

        // Create or update the video access record
        $videoAccess = VideoAccess::updateOrCreate(
            ['user_id' => $user->user_id, 'video_id' => $video->video_id],
            [
                'token' => $token,
                'expires_at' => $expiresAt,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        );

        return response()->json([
            'token' => $token,
            'expires_at' => $expiresAt->toIso8601String(),
            'timestamp' => $timestamp, // Add timestamp to force new version
            'cache_buster' => uniqid() // Additional cache buster
        ]);
    }

    /**
     * Handle encrypted video viewing
     *
     * @param Request $request
     * @param int $videoId
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function encryptedVideo(Request $request, $videoId, $token)
    {
        try {
            // Find the video access record by token
            $videoAccess = VideoAccess::where('token', $token)
                ->where('expires_at', '>', now())
                ->first();

            if (!$videoAccess) {
                Log::warning('Invalid or expired token for encrypted video', ['token' => $token]);
                return response()->json(['error' => 'Invalid or expired token'], 403);
            }

            // Get the video
            $video = CourseVideo::find($videoId);
            if (!$video) {
                Log::warning('Encrypted video not found', ['video_id' => $videoId]);
                return response()->json(['error' => 'Video not found'], 404);
            }

            // Make sure this is an encrypted video
            if (!$video->is_encrypted) {
                return redirect()->route('video.stream', ['token' => $token]);
            }

            // Return view with video player for encrypted content
            return response()->json([
                'success' => true,
                'message' => 'هذا فيديو مشفر، يتطلب مشغل خاص لعرضه.',
                'video' => [
                    'id' => $video->video_id,
                    'title' => $video->title,
                    'path' => $video->video_path,
                    'is_encrypted' => true,
                    'token' => $token
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling encrypted video: ' . $e->getMessage(), [
                'video_id' => $videoId,
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'حدث خطأ أثناء محاولة عرض الفيديو المشفر. يرجى المحاولة مرة أخرى لاحقًا.'], 500);
        }
    }

    /**
     * Get video information for a specific video
     * 
     * @param Request $request
     * @param int $videoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVideoInfo(Request $request, $videoId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Get the video
            $video = CourseVideo::find($videoId);
            if (!$video) {
                return response()->json(['error' => 'Video not found'], 404);
            }

            // Check if the user has access to this video
            $course = Course::find($video->course_id);
            if (!$course) {
                return response()->json(['error' => 'Course not found'], 404);
            }

            $isInstructor = $course->instructor_id == $user->user_id;
            $isEnrolled = Enrollment::where('student_id', $user->user_id)
                ->where('course_id', $video->course_id)
                ->exists();

            if (!$isInstructor && !$isEnrolled && !$video->is_free_preview) {
                return response()->json(['error' => 'Access denied'], 403);
            }

            $videoInfo = [
                'video_id' => $video->video_id,
                'title' => $video->title,
                'description' => $video->description,
                'duration_seconds' => $video->duration_seconds,
                'is_encrypted' => $video->is_encrypted,
                'is_free_preview' => $video->is_free_preview
            ];

            // Add video URL for external videos
            if ($video->video_url) {
                $videoInfo['video_url'] = $video->video_url;
                $videoInfo['is_external'] = true;
            } else {
                $videoInfo['is_external'] = false;
            }

            return response()->json([
                'success' => true,
                'videoInfo' => $videoInfo
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting video info: ' . $e->getMessage(), [
                'video_id' => $videoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An error occurred while getting video information'
            ], 500);
        }
    }
}
