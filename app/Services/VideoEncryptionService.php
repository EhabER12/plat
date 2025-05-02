<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VideoEncryptionService
{
    /**
     * Encryption key
     * 
     * @var string
     */
    protected $encryptionKey;
    
    /**
     * Initialization vector size
     * 
     * @var int
     */
    protected $ivSize;
    
    /**
     * Encryption method
     * 
     * @var string
     */
    protected $encryptionMethod;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Use a fixed key for encryption (store in .env in production)
        $this->encryptionKey = env('VIDEO_ENCRYPTION_KEY', 'your-32-char-encryption-key-here');
        $this->encryptionMethod = 'AES-256-CBC';
        $this->ivSize = openssl_cipher_iv_length($this->encryptionMethod);
    }
    
    /**
     * Process and encrypt a video file
     * 
     * @param string $sourcePath
     * @param int $courseId
     * @param int $videoId
     * @return array
     */
    public function processVideo($sourcePath, $courseId, $videoId)
    {
        try {
            // Generate a unique directory for this video
            $videoDir = "private/videos/{$courseId}/{$videoId}";
            
            // Ensure the directory exists
            if (!Storage::exists($videoDir)) {
                Storage::makeDirectory($videoDir);
            }
            
            // Get the source file content
            $sourceContent = file_get_contents($sourcePath);
            if (!$sourceContent) {
                throw new \Exception("Could not read source file: {$sourcePath}");
            }
            
            // Generate a unique encryption key for this video
            $videoKey = Str::random(32);
            
            // Store the encryption key (in a real app, you'd store this securely)
            $keyPath = "{$videoDir}/key.txt";
            Storage::put($keyPath, $videoKey);
            
            // Encrypt the video
            $encryptedPath = $this->encryptVideo($sourceContent, $videoDir, $videoKey);
            
            // Generate segments for streaming
            $segmentInfo = $this->generateSegments($encryptedPath, $videoDir);
            
            return [
                'success' => true,
                'video_dir' => $videoDir,
                'segments' => $segmentInfo['segments'],
                'duration' => $segmentInfo['duration'],
                'key_path' => $keyPath
            ];
        } catch (\Exception $e) {
            Log::error('Video encryption failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Encrypt a video file
     * 
     * @param string $content
     * @param string $videoDir
     * @param string $key
     * @return string
     */
    protected function encryptVideo($content, $videoDir, $key)
    {
        // Generate a random IV
        $iv = openssl_random_pseudo_bytes($this->ivSize);
        
        // Encrypt the content
        $encryptedContent = openssl_encrypt(
            $content,
            $this->encryptionMethod,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        
        // Prepend the IV to the encrypted content
        $encryptedContent = $iv . $encryptedContent;
        
        // Save the encrypted file
        $encryptedPath = "{$videoDir}/encrypted.bin";
        Storage::put($encryptedPath, $encryptedContent);
        
        return $encryptedPath;
    }
    
    /**
     * Generate video segments for streaming
     * 
     * @param string $encryptedPath
     * @param string $videoDir
     * @return array
     */
    protected function generateSegments($encryptedPath, $videoDir)
    {
        // Get the encrypted content
        $encryptedContent = Storage::get($encryptedPath);
        
        // Calculate segment size (1MB)
        $segmentSize = 1024 * 1024;
        
        // Calculate number of segments
        $totalSize = strlen($encryptedContent);
        $numSegments = ceil($totalSize / $segmentSize);
        
        // Create segments directory
        $segmentsDir = "{$videoDir}/segments";
        if (!Storage::exists($segmentsDir)) {
            Storage::makeDirectory($segmentsDir);
        }
        
        // Generate segments
        $segments = [];
        for ($i = 0; $i < $numSegments; $i++) {
            $offset = $i * $segmentSize;
            $length = min($segmentSize, $totalSize - $offset);
            $segmentContent = substr($encryptedContent, $offset, $length);
            
            // Generate a unique name for this segment
            $segmentName = "segment_{$i}.bin";
            $segmentPath = "{$segmentsDir}/{$segmentName}";
            
            // Save the segment
            Storage::put($segmentPath, $segmentContent);
            
            // Add segment info
            $segments[] = [
                'index' => $i,
                'path' => $segmentPath,
                'size' => $length
            ];
        }
        
        // Create a manifest file
        $manifest = [
            'total_segments' => $numSegments,
            'total_size' => $totalSize,
            'segment_size' => $segmentSize,
            'segments' => $segments
        ];
        
        $manifestPath = "{$videoDir}/manifest.json";
        Storage::put($manifestPath, json_encode($manifest));
        
        // For simplicity, we're estimating duration based on file size
        // In a real app, you'd use a library like FFmpeg to get the actual duration
        $estimatedDuration = ceil($totalSize / (1024 * 1024) * 10); // Rough estimate: 10 seconds per MB
        
        return [
            'segments' => $segments,
            'duration' => $estimatedDuration
        ];
    }
    
    /**
     * Decrypt a video segment
     * 
     * @param string $segmentPath
     * @param string $keyPath
     * @param int $segmentIndex
     * @param string $videoDir
     * @return string
     */
    public function decryptSegment($segmentPath, $keyPath, $segmentIndex, $videoDir)
    {
        try {
            // Get the encryption key
            $key = Storage::get($keyPath);
            if (!$key) {
                throw new \Exception("Could not read encryption key");
            }
            
            // Get the manifest
            $manifestPath = "{$videoDir}/manifest.json";
            $manifest = json_decode(Storage::get($manifestPath), true);
            if (!$manifest) {
                throw new \Exception("Could not read manifest");
            }
            
            // Get the segment content
            $segmentContent = Storage::get($segmentPath);
            if (!$segmentContent) {
                throw new \Exception("Could not read segment");
            }
            
            // If this is the first segment, extract the IV
            if ($segmentIndex === 0) {
                $iv = substr($segmentContent, 0, $this->ivSize);
                $segmentContent = substr($segmentContent, $this->ivSize);
            } else {
                // For subsequent segments, we need to get the IV from the first segment
                $firstSegmentPath = "{$videoDir}/segments/segment_0.bin";
                $firstSegment = Storage::get($firstSegmentPath);
                $iv = substr($firstSegment, 0, $this->ivSize);
            }
            
            // Decrypt the segment
            $decryptedContent = openssl_decrypt(
                $segmentContent,
                $this->encryptionMethod,
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );
            
            if ($decryptedContent === false) {
                throw new \Exception("Failed to decrypt segment");
            }
            
            return $decryptedContent;
        } catch (\Exception $e) {
            Log::error('Segment decryption failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
