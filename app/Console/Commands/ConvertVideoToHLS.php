<?php

namespace App\Console\Commands;

use App\Models\CourseVideo;
use App\Services\VideoEncryptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConvertVideoToHLS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:convert-to-hls 
                            {video_id? : Optional video ID to convert a specific video}
                            {--all : Convert all videos that are not already HLS}
                            {--force : Force reconversion even if already converted}
                            {--encrypt : Encrypt the HLS stream}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert MP4 videos to HLS format for improved streaming';

    /**
     * FFmpeg binary path
     */
    protected $ffmpegPath;

    /**
     * FFprobe binary path
     */
    protected $ffprobePath;

    /**
     * Initialize command with proper FFmpeg paths
     */
    public function __construct()
    {
        parent::__construct();
        
        // Set default paths for FFmpeg binaries - change as needed in .env
        $this->ffmpegPath = env('FFMPEG_PATH', '/usr/bin/ffmpeg');
        $this->ffprobePath = env('FFPROBE_PATH', '/usr/bin/ffprobe');
        
        // For Windows
        if (in_array(PHP_OS, ['WIN32', 'WINNT', 'Windows'])) {
            $this->ffmpegPath = env('FFMPEG_PATH', 'C:\ffmpeg\bin\ffmpeg.exe');
            $this->ffprobePath = env('FFPROBE_PATH', 'C:\ffmpeg\bin\ffprobe.exe');
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if FFmpeg is available
        if (!$this->isFFmpegAvailable()) {
            $this->error('FFmpeg is not available. Please install FFmpeg or set correct path in .env file.');
            return 1;
        }

        // Get arguments and options
        $videoId = $this->argument('video_id');
        $convertAll = $this->option('all');
        $force = $this->option('force');
        $encrypt = $this->option('encrypt');

        // Validate arguments
        if (!$videoId && !$convertAll) {
            $this->error('Please provide a video_id or use --all option to convert all videos.');
            return 1;
        }

        // Query videos to convert
        $query = CourseVideo::query();
        
        // Filter by video ID if specified
        if ($videoId) {
            $query->where('video_id', $videoId);
        }
        
        // Skip already converted videos unless forced
        if (!$force) {
            $query->where(function($q) {
                $q->where('is_hls_enabled', false)
                  ->orWhereNull('is_hls_enabled');
            });
        }
        
        // Skip videos without a local file path
        $query->whereNotNull('video_path')
              ->where('video_path', '!=', '');
        
        // Get videos to convert
        $videos = $query->get();
        
        $count = $videos->count();
        if ($count === 0) {
            $this->info('No videos found to convert.');
            return 0;
        }
        
        $this->info("Found {$count} videos to convert to HLS format.");
        
        // Process each video
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $converted = 0;
        $failed = 0;
        
        foreach ($videos as $video) {
            $this->info("\nProcessing video {$video->video_id}: {$video->title}");
            
            try {
                $result = $this->convertVideoToHLS($video, $encrypt);
                if ($result) {
                    $converted++;
                    $this->info("Converted {$video->title} to HLS successfully.");
                } else {
                    $failed++;
                    $this->error("Failed to convert {$video->title} to HLS.");
                }
            } catch (\Exception $e) {
                $failed++;
                $this->error("Error converting {$video->title}: " . $e->getMessage());
                Log::error("Error converting video {$video->video_id} to HLS: " . $e->getMessage(), [
                    'video_id' => $video->video_id,
                    'exception' => $e
                ]);
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("Conversion completed: {$converted} videos converted, {$failed} failed.");
        
        return 0;
    }
    
    /**
     * Check if FFmpeg is available
     * 
     * @return bool
     */
    protected function isFFmpegAvailable()
    {
        // Check FFmpeg
        $ffmpegOutput = shell_exec("{$this->ffmpegPath} -version");
        if (empty($ffmpegOutput) || !strpos($ffmpegOutput, 'ffmpeg')) {
            return false;
        }
        
        // Check FFprobe
        $ffprobeOutput = shell_exec("{$this->ffprobePath} -version");
        if (empty($ffprobeOutput) || !strpos($ffprobeOutput, 'ffprobe')) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Convert video to HLS format
     * 
     * @param CourseVideo $video
     * @param bool $encrypt
     * @return bool
     */
    protected function convertVideoToHLS(CourseVideo $video, $encrypt = false)
    {
        // Get source file path
        $sourcePath = Storage::disk('public')->path($video->video_path);
        
        // Check if source file exists
        if (!file_exists($sourcePath)) {
            $this->error("Source file not found: {$sourcePath}");
            return false;
        }
        
        // Create HLS directory
        $courseId = $video->course_id;
        $videoId = $video->video_id;
        $hlsDir = "courses/{$courseId}/videos/hls/{$videoId}";
        $fullHlsDir = Storage::disk('public')->path($hlsDir);
        
        if (!file_exists($fullHlsDir)) {
            mkdir($fullHlsDir, 0755, true);
        }
        
        // Output paths
        $playlistPath = "{$hlsDir}/playlist.m3u8";
        $fullPlaylistPath = Storage::disk('public')->path($playlistPath);
        $segmentPattern = "{$hlsDir}/segment_%03d.ts";
        $fullSegmentPattern = Storage::disk('public')->path($segmentPattern);
        
        // Generate encryption key if needed
        $keyPath = null;
        $keyInfo = null;
        
        if ($encrypt) {
            // Generate a random key
            $encryptionKey = random_bytes(16);
            $keyFile = "{$hlsDir}/key.bin";
            $fullKeyPath = Storage::disk('public')->path($keyFile);
            
            // Store the key
            file_put_contents($fullKeyPath, $encryptionKey);
            
            // Create key info file
            $keyInfo = "{$hlsDir}/key_info";
            $fullKeyInfoPath = Storage::disk('public')->path($keyInfo);
            $keyUrl = asset("storage/{$keyFile}");
            
            // Write key info file (used by FFmpeg)
            file_put_contents($fullKeyInfoPath, "{$keyUrl}\n{$fullKeyPath}");
            
            $keyPath = $fullKeyInfoPath;
        }
        
        // Build FFmpeg command
        $cmd = "{$this->ffmpegPath} -y -i \"{$sourcePath}\" ";
        
        // Add encryption parameters if needed
        if ($encrypt && $keyPath) {
            $cmd .= "-hls_key_info_file \"{$keyPath}\" ";
        }
        
        // HLS parameters
        $cmd .= "-profile:v baseline -level 3.0 ";
        $cmd .= "-start_number 0 -hls_time 10 -hls_list_size 0 ";
        $cmd .= "-f hls \"{$fullPlaylistPath}\"";
        
        // Execute command
        $this->info("Executing: {$cmd}");
        $output = [];
        $returnValue = null;
        exec($cmd, $output, $returnValue);
        
        // Check if command was successful
        if ($returnValue !== 0) {
            $this->error("FFmpeg command failed with code {$returnValue}");
            Log::error("FFmpeg failed to convert video {$video->video_id}", [
                'video_id' => $video->video_id,
                'command' => $cmd,
                'output' => $output,
                'return_value' => $returnValue
            ]);
            return false;
        }
        
        // Update the video record
        $video->hls_path = $playlistPath;
        $video->hls_url = asset("storage/{$playlistPath}");
        $video->is_hls_enabled = true;
        $video->hls_segments_path = $hlsDir;
        
        if ($encrypt && $keyPath) {
            // Store the encrypted key in the database - in production you might want to encrypt this again
            $video->encryption_key = base64_encode($encryptionKey);
        }
        
        $video->save();
        
        return true;
    }
}
