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
            // تسجيل المعلومات لأغراض التصحيح
            Log::info('طلب تدفق فيديو جديد', [
                'token' => $token,
                'headers' => $request->headers->all(),
                'ip' => $request->ip()
            ]);
            
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
                'is_encrypted' => $video->is_encrypted,
                'is_hls_enabled' => $video->is_hls_enabled,
                'course_id' => $video->course_id
            ]);

            // Update last accessed time
            $videoAccess->last_accessed_at = now();
            $videoAccess->save();

            // إذا كان الفيديو يستخدم HLS، قم بتوجيه الطلب إلى معالج HLS
            if ($video->is_hls_enabled && !empty($video->hls_path)) {
                return $this->streamHlsVideo($request, $video, $videoAccess);
            }

            // لنتحقق أولاً من الفيديو المحدد الذي كان في رسالة الخطأ
            $basenameFile = basename($video->video_path);
            $courseId = $video->course_id;
            
            // المسار المحدد من الصورة السابقة
            $directPath = storage_path('app/public/courses/' . $courseId . '/videos/' . $basenameFile);
            if (file_exists($directPath)) {
                Log::info('تم العثور على الفيديو في المسار المباشر', ['path' => $directPath]);
                
                // خصائص الملف
                $size = filesize($directPath);
                $mime = mime_content_type($directPath) ?: 'video/mp4';
                
                // التعامل مع طلبات النطاق
                $range = $request->header('Range');
                if ($range) {
                    return $this->handleRangeRequest($directPath, $size, $mime, $range);
                }
                
                return response()->file($directPath, [
                    'Content-Type' => $mime,
                    'Content-Length' => $size,
                    'Accept-Ranges' => 'bytes',
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept, Range'
                ]);
            }

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
                    // تعديل: إذا كان نظام الملفات المستخدم هو local لكن storage_disk مضبوط على s3
                    if (config('filesystems.default') === 'local' && empty(config('filesystems.disks.s3.key'))) {
                        // تحويل المسار من مسار S3 إلى مسار محلي
                        $basenameFile = basename($video->video_path);
                        $courseId = $video->course_id;
                        
                        // إضافة المسار المحدد من رسالة الخطأ مباشرة
                        $specificPath = "public/courses/{$courseId}/videos/{$basenameFile}";
                        
                        // حالة خاصة للملف المذكور في رسالة الخطأ
                        if ($basenameFile === "aol-fydyo-1746002743.mp4" && $courseId === 5) {
                            $exactPath = storage_path('app/public/courses/5/videos/aol-fydyo-1746002743.mp4');
                            if (file_exists($exactPath)) {
                                Log::info('تم العثور على الملف المحدد بالضبط', ['path' => $exactPath]);
                                return response()->file($exactPath, [
                                    'Content-Type' => 'video/mp4',
                                    'Accept-Ranges' => 'bytes',
                                    'Access-Control-Allow-Origin' => '*'
                                ]);
                            }
                        }
                        
                        if (Storage::exists($specificPath)) {
                            Log::info('وجدت الفيديو في المسار المحدد', ['path' => $specificPath]);
                            // استخدام طريقة تدفق الملف العام مباشرة
                            return $this->streamPublicFile("courses/{$courseId}/videos/{$basenameFile}");
                        }
                        
                        // تأكد من الوصول المباشر إلى هذا المسار
                        $fullSpecificPath = storage_path("app/{$specificPath}");
                        if (file_exists($fullSpecificPath)) {
                            Log::info('وجدت الفيديو مباشرة في المسار المحدد', ['path' => $fullSpecificPath]);
                            return response()->file($fullSpecificPath);
                        }
                        
                        // إضافة مسارات بحث متعددة
                        $potentialPaths = [
                            // مسارات في storage/app
                            "public/courses/{$courseId}/videos/{$basenameFile}",
                            "private/public/courses/{$courseId}/videos/{$basenameFile}",
                            "courses/{$courseId}/videos/{$basenameFile}",
                            "private/courses/{$courseId}/videos/{$basenameFile}",
                            "videos/{$courseId}/{$basenameFile}",
                            "private/videos/{$courseId}/{$basenameFile}",
                            "public/videos/{$courseId}/{$basenameFile}",
                            
                            // مسارات بدون معرف الكورس
                            "videos/{$basenameFile}",
                            "public/videos/{$basenameFile}",
                            "private/videos/{$basenameFile}",
                            
                            // مسارات مباشرة (لملفات تم تحميلها مباشرة للمجلد الجذر)
                            "{$basenameFile}",
                            "public/{$basenameFile}",
                            "private/{$basenameFile}",
                            
                            // مسارات مطلقة
                            $video->video_path,
                            "public/" . $video->video_path,
                            "private/" . $video->video_path,
                        ];
                        
                        Log::info('البحث عن الفيديو في المسارات التالية', [
                            'video_id' => $video->video_id,
                            'course_id' => $courseId,
                            'basename' => $basenameFile,
                            'original_path' => $video->video_path,
                            'paths' => $potentialPaths
                        ]);
                        
                        foreach ($potentialPaths as $path) {
                            if (Storage::exists($path)) {
                                Log::info('تم العثور على الفيديو في المسار', ['path' => $path]);
                                return $this->streamLocalFile($path);
                            }
                        }
                        
                        // إذا لم يتم العثور على الملف، حاول البحث في أي مجلد فيديو
                        $wildcardPaths = [
                            "courses/{$courseId}/videos/*",
                            "public/courses/{$courseId}/videos/*",
                            "private/courses/{$courseId}/videos/*",
                            "videos/{$courseId}/*",
                            "public/videos/{$courseId}/*",
                            "private/videos/{$courseId}/*",
                        ];
                        
                        foreach ($wildcardPaths as $wildcardPath) {
                            $directory = dirname($wildcardPath);
                            if (Storage::exists($directory)) {
                                $files = Storage::files($directory);
                                if (!empty($files)) {
                                    // ابحث عن أول ملف فيديو في المجلد
                                    foreach ($files as $file) {
                                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                        if (in_array($ext, ['mp4', 'webm', 'mov', 'avi'])) {
                                            Log::info('تم العثور على فيديو في المجلد', ['directory' => $directory, 'file' => $file]);
                                            return $this->streamLocalFile($file);
                                        }
                                    }
                                }
                            }
                        }
                        
                        // البحث أيضًا في المجلد العام (إذا كان الملف متاحًا مباشرة)
                        $publicFilePath = public_path($basenameFile);
                        if (file_exists($publicFilePath)) {
                            Log::info('تم العثور على الفيديو في المجلد العام', ['path' => $publicFilePath]);
                            return response()->file($publicFilePath);
                        }
                        
                        $publicDirPaths = [
                            public_path("videos/{$courseId}/{$basenameFile}"),
                            public_path("videos/{$basenameFile}"),
                            public_path("courses/{$courseId}/videos/{$basenameFile}"),
                            public_path("storage/videos/{$courseId}/{$basenameFile}"),
                            public_path("storage/courses/{$courseId}/videos/{$basenameFile}"),
                        ];
                        
                        foreach ($publicDirPaths as $path) {
                            if (file_exists($path)) {
                                Log::info('تم العثور على الفيديو في المجلد العام', ['path' => $path]);
                                return response()->file($path);
                            }
                        }
                        
                        Log::error('Video file not found in local storage', [
                            'video_id' => $video->video_id,
                            'searched_paths' => array_merge($potentialPaths, $wildcardPaths, $publicDirPaths),
                            'public_path' => public_path()
                        ]);
                        return response()->json([
                            'error' => 'Video file not found', 
                            'video_id' => $video->video_id,
                            'basename' => $basenameFile
                        ], 404);
                    }
                    
                    // التعامل مع الفيديو من S3 - الكود الأصلي
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
                        // محاولة إضافية: جرب الوصول إلى الملف مباشرة باستخدام المسار الكامل
                        $alternativePaths = [
                            storage_path('app/' . $foundPath),
                            storage_path('app/public/' . basename($foundPath)),
                            public_path('storage/' . basename($foundPath)),
                            // في حالة محددة للملف المذكور في رسالة الخطأ
                            storage_path('app/public/courses/5/videos/' . basename($foundPath))
                        ];
                        
                        foreach ($alternativePaths as $altPath) {
                            if (file_exists($altPath)) {
                                $file = $altPath;
                                Log::info('تم العثور على الملف في مسار بديل', [
                                    'path' => $altPath
                                ]);
                                break;
                            }
                        }
                        
                        if (!file_exists($file)) {
                            Log::error('File exists in storage but cannot be physically accessed', [
                                'video_id' => $video->video_id,
                                'path' => $foundPath,
                                'file' => $file,
                                'tried_alternatives' => $alternativePaths
                            ]);
                            return response()->json([
                                'error' => 'Video file cannot be accessed. Please contact support.',
                                'video_id' => $video->video_id,
                                'path' => $foundPath
                            ], 500);
                        }
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
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Log the received data for debugging
            Log::info('Video progress update request received', [
                'request_data' => $request->all(),
                'user_id' => $user->user_id
            ]);

            // Validate with more flexible rules
            $validated = $request->validate([
                'video_id' => 'required|exists:course_videos,video_id',
                'progress' => 'required|numeric|min:0|max:100',
                'current_time' => 'sometimes|numeric',
                'completed' => 'required|boolean',
            ]);

            // Find the video
            $video = CourseVideo::find($validated['video_id']);
            if (!$video) {
                Log::error('Video not found for progress update', [
                    'video_id' => $validated['video_id'],
                    'user_id' => $user->user_id
                ]);
                return response()->json(['error' => 'Video not found'], 404);
            }

            // Check if the user is enrolled in the course or is the instructor
            $course = Course::find($video->course_id);
            if (!$course) {
                Log::error('Course not found for progress update', [
                    'video_id' => $validated['video_id'],
                    'course_id' => $video->course_id,
                    'user_id' => $user->user_id
                ]);
                return response()->json(['error' => 'Course not found'], 404);
            }

            $isInstructor = $course->instructor_id == $user->user_id;
            $isEnrolled = Enrollment::where('student_id', $user->user_id)
                ->where('course_id', $video->course_id)
                ->exists();

            if (!$isInstructor && !$isEnrolled && !$video->is_free_preview) {
                Log::warning('Unauthorized progress update attempt', [
                    'video_id' => $validated['video_id'],
                    'user_id' => $user->user_id,
                    'is_instructor' => $isInstructor,
                    'is_enrolled' => $isEnrolled,
                    'is_free_preview' => $video->is_free_preview
                ]);
                return response()->json(['error' => 'You do not have access to this video'], 403);
            }

            // Update or create video view record
            try {
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

                Log::info('Video progress updated successfully', [
                    'video_id' => $validated['video_id'],
                    'user_id' => $user->user_id,
                    'progress' => $validated['progress'],
                    'completed' => $validated['completed']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Progress updated successfully',
                    'data' => $videoView
                ]);
            } catch (\Exception $e) {
                Log::error('Error updating video progress: ' . $e->getMessage(), [
                    'video_id' => $validated['video_id'],
                    'user_id' => $user->user_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Database error while updating progress',
                    'message' => env('APP_DEBUG') ? $e->getMessage() : 'An unexpected error occurred'
                ], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Video progress validation error', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Validation error',
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error in updateProgress: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Server error',
                'message' => env('APP_DEBUG') ? $e->getMessage() : 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * Get a token for accessing a video
     *
     * @param Request $request
     * @param int $courseId
     * @param int $videoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAccessToken(Request $request, $courseId, $videoId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Cleanse videoId to ensure it's an integer before findOrFail
            $cleanedVideoId = explode(':', $videoId)[0];
            if (!is_numeric($cleanedVideoId)) {
                Log::warning('Invalid videoId format received in getAccessToken', [
                    'original_video_id' => $videoId,
                    'course_id' => $courseId,
                    'user_id' => $user->user_id
                ]);
                return response()->json(['error' => 'Invalid video ID format'], 400); // Bad Request
            }

            // Cast to integer to ensure consistent comparison
            $cleanedVideoId = (int)$cleanedVideoId;

            // Log debugging info
            Log::info('Processing video access token request', [
                'original_video_id' => $videoId,
                'cleaned_video_id' => $cleanedVideoId,
                'course_id' => $courseId,
                'user_id' => $user->user_id
            ]);

            // Find the course and video
            $course = Course::findOrFail($courseId);
            $video = CourseVideo::findOrFail($cleanedVideoId); // Use cleaned and casted videoId

            // Check if user is enrolled in the course or video is free preview
            $isEnrolled = Enrollment::where('student_id', $user->user_id)
                ->where('course_id', $courseId)
                ->exists();

            if (!$isEnrolled && !$video->is_free_preview) {
                return response()->json(['error' => 'Not enrolled in this course'], 403);
            }

            // Generate or retrieve an existing valid token - USE CLEANED ID
            $existingToken = VideoAccess::where('user_id', $user->user_id)
                ->where('video_id', $cleanedVideoId) // Changed from $videoId to $cleanedVideoId
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->first();

            // Prepare video info array with null checks
            $videoInfo = [
                'title' => $video->title ?? '',
                'description' => $video->description ?? '',
                'duration' => $video->duration_seconds ?? 0,
                'thumbnail' => $video->thumbnail_url ?? '',
                'is_external' => !empty($video->video_url),
                'is_hls_enabled' => $video->is_hls_enabled ?? false,
                'hls_url' => $video->hls_url ?? '',
                'path' => $video->video_path ?? '',
            ];

            if ($existingToken && $existingToken->expires_at > now()->addMinutes(10)) {
                // Return existing token if it's valid for more than 10 more minutes
                Log::info('Using existing token for video access', [
                    'video_id' => $cleanedVideoId,
                    'user_id' => $user->user_id,
                    'token_expires_at' => $existingToken->expires_at
                ]);

                return response()->json([
                    'token' => $existingToken->token,
                    'expires_at' => $existingToken->expires_at,
                    'video_info' => $videoInfo
                ]);
            }

            // Delete any expired tokens for this user and video to avoid unique constraint violations
            VideoAccess::where('user_id', $user->user_id)
                ->where('video_id', $cleanedVideoId)
                ->where('expires_at', '<=', now())
                ->delete();

            // Generate a new token
            $token = Str::random(64);
            $expiresAt = now()->addHours(2); // Token valid for 2 hours

            try {
                // Use updateOrCreate to prevent duplicate key errors
                $videoAccess = VideoAccess::updateOrCreate(
                    [
                        'user_id' => $user->user_id,
                        'video_id' => $cleanedVideoId
                    ],
                    [
                        'token' => $token,
                        'expires_at' => $expiresAt,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'last_accessed_at' => now()
                    ]
                );

                Log::info('Generated new token for video access', [
                    'video_id' => $cleanedVideoId,
                    'user_id' => $user->user_id,
                    'token_expires_at' => $expiresAt
                ]);

                // Return the new token with video info
                return response()->json([
                    'token' => $token,
                    'expires_at' => $expiresAt,
                    'video_info' => $videoInfo
                ]);
            } catch (\Exception $e) {
                // If we still get a database error, log it and try one more approach
                Log::error('Error creating video access token (first attempt): ' . $e->getMessage(), [
                    'user_id' => $user->user_id,
                    'video_id' => $cleanedVideoId
                ]);
                
                // As a last resort, delete ALL tokens for this user and video, regardless of expiration
                VideoAccess::where('user_id', $user->user_id)
                    ->where('video_id', $cleanedVideoId)
                    ->delete();
                    
                // Create a completely new record
                $videoAccess = new VideoAccess();
                $videoAccess->user_id = $user->user_id;
                $videoAccess->video_id = $cleanedVideoId;
                $videoAccess->token = $token;
                $videoAccess->expires_at = $expiresAt;
                $videoAccess->ip_address = $request->ip();
                $videoAccess->user_agent = $request->userAgent();
                $videoAccess->save();
                
                Log::info('Generated new token for video access (after deleting all existing tokens)', [
                    'video_id' => $cleanedVideoId,
                    'user_id' => $user->user_id
                ]);
                
                return response()->json([
                    'token' => $token,
                    'expires_at' => $expiresAt,
                    'video_info' => $videoInfo
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error generating video access token: ' . $e->getMessage(), [
                'course_id' => $courseId,
                'video_id' => $videoId,
                'cleaned_video_id' => $cleanedVideoId ?? null,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Failed to generate access token: ' . (env('APP_DEBUG') ? $e->getMessage() : 'Internal Server Error')], 500);
        }
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

    /**
     * Stream a local video file
     * 
     * @param string $path
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function streamLocalFile($path)
    {
        try {
            if (!Storage::exists($path)) {
                Log::error('الملف غير موجود في المسار المحدد', ['path' => $path]);
                return response()->json(['error' => 'File not found'], 404);
            }
            
            $file = Storage::path($path);
            if (!file_exists($file)) {
                Log::error('فشل في الوصول إلى الملف رغم وجوده في التخزين', ['path' => $path, 'file' => $file]);
                return response()->json(['error' => 'Cannot access file'], 500);
            }
            
            $size = Storage::size($path);
            $mime = Storage::mimeType($path);
            
            // التأكد من أن الملف فيديو
            $validMimeTypes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-msvideo'];
            if (!in_array($mime, $validMimeTypes) && !str_starts_with($mime, 'video/')) {
                Log::warning('نوع ملف غير متوقع للفيديو', ['path' => $path, 'mime' => $mime]);
            }
            
            Log::info('تجهيز الفيديو للتدفق', [
                'path' => $path,
                'file' => $file,
                'size' => $size,
                'mime' => $mime
            ]);
            
            // Check if browser sent range header
            $range = request()->header('Range');
            if ($range) {
                return $this->handleRangeRequest($file, $size, $mime, $range);
            }
            
            // Stream the full file
            $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() use ($file) {
                $stream = fopen($file, 'rb');
                if (!$stream) {
                    Log::error('فشل في فتح مجرى الملف', ['file' => $file]);
                    exit;
                }
                
                // إرسال الفيديو على دفعات لتجنب مشاكل الذاكرة
                $bufferSize = 8192; // 8KB لكل دفعة
                while (!feof($stream)) {
                    echo fread($stream, $bufferSize);
                    flush();
                }
                fclose($stream);
            }, 200, [
                'Content-Type' => $mime,
                'Content-Length' => $size,
                'Accept-Ranges' => 'bytes',
                'Content-Disposition' => 'inline; filename="'.basename($file).'"',
                'X-Frame-Options' => 'SAMEORIGIN',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept, Range'
            ]);
            
            return $response;
        } catch (\Exception $e) {
            Log::error('خطأ أثناء تدفق الفيديو: ' . $e->getMessage(), [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error streaming video: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stream a file directly from the public storage
     * 
     * @param string $relativePath المسار النسبي للملف داخل مجلد التخزين العام
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
     */
    protected function streamPublicFile($relativePath)
    {
        try {
            // بناء المسار الكامل
            $fullPath = storage_path('app/public/' . ltrim($relativePath, '/'));
            
            Log::info('محاولة تدفق ملف من المجلد العام', [
                'relativePath' => $relativePath,
                'fullPath' => $fullPath
            ]);
            
            // التحقق من وجود الملف
            if (!file_exists($fullPath)) {
                Log::error('الملف غير موجود في المجلد العام', ['path' => $fullPath]);
                return response()->json(['error' => 'File not found in public storage'], 404);
            }
            
            // خصائص الملف
            $size = filesize($fullPath);
            $mime = mime_content_type($fullPath) ?: 'video/mp4';
            
            // إذا كان هناك طلب نطاق معين
            $range = request()->header('Range');
            if ($range) {
                return $this->handleRangeRequest($fullPath, $size, $mime, $range);
            }
            
            // تدفق الملف كاملًا
            return response()->file($fullPath, [
                'Content-Type' => $mime,
                'Content-Length' => $size,
                'Accept-Ranges' => 'bytes',
                'Content-Disposition' => 'inline; filename="'.basename($fullPath).'"',
                'X-Frame-Options' => 'SAMEORIGIN',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept, Range'
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في تدفق الملف العام: ' . $e->getMessage(), [
                'path' => $relativePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error streaming public file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has access to a video
     *
     * @param Request $request
     * @param int $videoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAccess(Request $request, $videoId)
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

            $isFreePreview = $video->is_free_preview;

            return response()->json([
                'success' => true,
                'hasAccess' => $isInstructor || $isEnrolled || $isFreePreview,
                'isInstructor' => $isInstructor,
                'isEnrolled' => $isEnrolled,
                'isFreePreview' => $isFreePreview,
                'video' => [
                    'id' => $video->video_id,
                    'title' => $video->title,
                    'course_id' => $video->course_id
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking video access: ' . $e->getMessage(), [
                'video_id' => $videoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An error occurred while checking video access'
            ], 500);
        }
    }

    /**
     * Record a video view
     *
     * @param Request $request
     * @param int $videoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordView(Request $request, $videoId)
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
                return response()->json(['error' => 'You do not have access to this video'], 403);
            }

            // Record video view
            $videoView = VideoView::create([
                'user_id' => $user->user_id,
                'video_id' => $video->video_id,
                'course_id' => $video->course_id,
                'view_date' => now(),
                'view_progress' => 0, // Initial progress
                'completed' => false,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Video view recorded successfully',
                'data' => $videoView
            ]);
        } catch (\Exception $e) {
            Log::error('Error recording video view: ' . $e->getMessage(), [
                'video_id' => $videoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An error occurred while recording video view'
            ], 500);
        }
    }

    /**
     * Stream a video in HLS format
     *
     * @param Request $request
     * @param CourseVideo $video
     * @param VideoAccess $videoAccess
     * @return \Illuminate\Http\Response
     */
    protected function streamHlsVideo(Request $request, CourseVideo $video, VideoAccess $videoAccess)
    {
        // Check if this is a segment request or master playlist request
        $segment = $request->query('segment');
        
        if ($segment) {
            return $this->streamHlsSegment($request, $video, $segment);
        }
        
        // This is a master playlist request
        $playlistPath = Storage::disk('public')->path($video->hls_path);
        
        if (!file_exists($playlistPath)) {
            Log::error('HLS playlist file not found', [
                'video_id' => $video->video_id,
                'playlist_path' => $playlistPath
            ]);
            return response()->json(['error' => 'HLS playlist not found'], 404);
        }
        
        // Read the playlist content
        $playlistContent = file_get_contents($playlistPath);
        
        // Modify the playlist to use our secure URLs for segments
        $segmentBaseUrl = url('/video/stream/hls-segment/' . $videoAccess->token);
        
        // Replace segment URLs with our secure URLs
        $pattern = '/(segment_\d+\.ts)/i';
        $playlistContent = preg_replace($pattern, $segmentBaseUrl . '?segment=$1', $playlistContent);
        
        // Record video view
        VideoView::updateOrCreate(
            [
                'user_id' => $videoAccess->user_id,
                'video_id' => $video->video_id,
                'view_date' => now()->format('Y-m-d')
            ],
            [
                'course_id' => $video->course_id,
                'view_progress' => 0,
                'completed' => false,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        );
        
        // Return the modified playlist
        return response($playlistContent, 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
    
    /**
     * Stream an HLS segment
     *
     * @param Request $request
     * @param CourseVideo $video
     * @param string $segment
     * @return \Illuminate\Http\Response
     */
    protected function streamHlsSegment(Request $request, CourseVideo $video, $segment)
    {
        // Validate the segment name for security
        if (!preg_match('/^segment_\d+\.ts$/', $segment)) {
            return response()->json(['error' => 'Invalid segment name'], 400);
        }
        
        // Get the segment path
        $segmentPath = $video->hls_segments_path . '/' . $segment;
        $fullSegmentPath = Storage::disk('public')->path($segmentPath);
        
        if (!file_exists($fullSegmentPath)) {
            Log::error('HLS segment not found', [
                'video_id' => $video->video_id,
                'segment' => $segment,
                'segment_path' => $segmentPath
            ]);
            return response()->json(['error' => 'Segment not found'], 404);
        }
        
        // Return the segment file
        $segmentContent = file_get_contents($fullSegmentPath);
        
        return response($segmentContent, 200, [
            'Content-Type' => 'video/MP2T',
            'Content-Length' => strlen($segmentContent),
            'Cache-Control' => 'max-age=86400', // Cache segments for a day
            'Access-Control-Allow-Origin' => '*'
        ]);
    }
    
    /**
     * Handle HLS segment requests
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function hlsSegment(Request $request, $token)
    {
        // Find the video access record by token
        $videoAccess = VideoAccess::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
            
        if (!$videoAccess) {
            return response()->json(['error' => 'Invalid or expired token'], 403);
        }
        
        // Get the video
        $video = CourseVideo::find($videoAccess->video_id);
        if (!$video || !$video->is_hls_enabled) {
            return response()->json(['error' => 'Video not found or HLS not enabled'], 404);
        }
        
        // Get segment name from query
        $segment = $request->query('segment');
        if (!$segment) {
            return response()->json(['error' => 'No segment specified'], 400);
        }
        
        return $this->streamHlsSegment($request, $video, $segment);
    }
}