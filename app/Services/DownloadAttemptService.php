<?php

namespace App\Services;

use App\Models\DownloadAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DownloadAttemptService
{
    /**
     * Maximum number of allowed download attempts before blocking
     * 
     * @var int
     */
    protected $maxAttempts = 3;
    
    /**
     * Block duration in minutes
     * 
     * @var int
     */
    protected $blockDuration = 60; // 1 hour
    
    /**
     * Check if a user is blocked from accessing videos
     * 
     * @param int $userId
     * @param int $videoId
     * @param string $ipAddress
     * @return array
     */
    public function checkIfBlocked($userId, $videoId, $ipAddress)
    {
        // Check for existing block
        $attempt = DownloadAttempt::where('user_id', $userId)
            ->where('video_id', $videoId)
            ->where('ip_address', $ipAddress)
            ->where('is_blocked', true)
            ->where('blocked_until', '>', now())
            ->first();
            
        if ($attempt) {
            return [
                'blocked' => true,
                'remaining_time' => $attempt->getRemainingBlockTime(),
                'message' => 'Access temporarily blocked due to suspicious activity',
                'attempt' => $attempt
            ];
        }
        
        return [
            'blocked' => false
        ];
    }
    
    /**
     * Record a download attempt
     * 
     * @param Request $request
     * @param int $userId
     * @param int $videoId
     * @param string $details
     * @return array
     */
    public function recordAttempt(Request $request, $userId, $videoId, $details = '')
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $fingerprint = $request->cookie('video_fingerprint');
        
        // Check if already blocked
        $blockCheck = $this->checkIfBlocked($userId, $videoId, $ipAddress);
        if ($blockCheck['blocked']) {
            return $blockCheck;
        }
        
        // Find or create attempt record
        $attempt = DownloadAttempt::where('user_id', $userId)
            ->where('video_id', $videoId)
            ->where('ip_address', $ipAddress)
            ->first();
            
        if ($attempt) {
            // Increment attempt count
            $attempt->attempt_count += 1;
            
            // Check if max attempts reached
            if ($attempt->attempt_count >= $this->maxAttempts) {
                $attempt->is_blocked = true;
                $attempt->blocked_until = now()->addMinutes($this->blockDuration);
                
                // Log the blocking
                Log::warning('User blocked from video access due to suspicious activity', [
                    'user_id' => $userId,
                    'video_id' => $videoId,
                    'ip_address' => $ipAddress,
                    'attempt_count' => $attempt->attempt_count,
                    'blocked_until' => $attempt->blocked_until
                ]);
            }
            
            // Update request details
            $attempt->request_details = $details;
            $attempt->user_agent = $userAgent;
            $attempt->fingerprint = $fingerprint;
            $attempt->save();
            
            if ($attempt->is_blocked) {
                return [
                    'blocked' => true,
                    'remaining_time' => $attempt->getRemainingBlockTime(),
                    'message' => 'Access temporarily blocked due to suspicious activity',
                    'attempt' => $attempt
                ];
            }
        } else {
            // Create new attempt record
            $attempt = DownloadAttempt::create([
                'user_id' => $userId,
                'video_id' => $videoId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'fingerprint' => $fingerprint,
                'request_details' => $details,
                'attempt_count' => 1,
                'is_blocked' => false
            ]);
        }
        
        return [
            'blocked' => false,
            'attempt' => $attempt,
            'remaining_attempts' => $this->maxAttempts - $attempt->attempt_count
        ];
    }
    
    /**
     * Reset attempts for a user
     * 
     * @param int $userId
     * @param int $videoId
     * @return bool
     */
    public function resetAttempts($userId, $videoId = null)
    {
        $query = DownloadAttempt::where('user_id', $userId);
        
        if ($videoId) {
            $query->where('video_id', $videoId);
        }
        
        return $query->update([
            'attempt_count' => 0,
            'is_blocked' => false,
            'blocked_until' => null
        ]);
    }
}
