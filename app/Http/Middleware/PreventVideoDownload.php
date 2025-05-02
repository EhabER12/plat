<?php

namespace App\Http\Middleware;

use App\Models\VideoAccess;
use App\Services\DownloadAttemptService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PreventVideoDownload
{
    /**
     * The download attempt service instance.
     *
     * @var \App\Services\DownloadAttemptService
     */
    protected $downloadAttemptService;

    /**
     * Create a new middleware instance.
     *
     * @param  \App\Services\DownloadAttemptService  $downloadAttemptService
     * @return void
     */
    public function __construct(DownloadAttemptService $downloadAttemptService)
    {
        $this->downloadAttemptService = $downloadAttemptService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the token from the route parameter
        $token = $request->route('token');
        if (!$token) {
            return response()->json(['error' => 'Invalid request'], 403);
        }

        // Get the video access record
        $videoAccess = VideoAccess::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$videoAccess) {
            return response()->json(['error' => 'Invalid or expired token'], 403);
        }

        // Get the user and video IDs
        $userId = $videoAccess->user_id;
        $videoId = $videoAccess->video_id;

        // Check if the user is already blocked
        $blockCheck = $this->downloadAttemptService->checkIfBlocked($userId, $videoId, $request->ip());
        if ($blockCheck['blocked']) {
            // Redirect to blocked access page
            return redirect()->route('blocked.access', ['video_id' => $videoId])->with('error', $blockCheck['message']);
        }

        // Check for download tools and bots
        $userAgent = $request->header('User-Agent');
        $downloadTools = [
            'wget', 'curl', 'python-requests', 'go-http-client', 'java', 'httpclient',
            'download', 'bot', 'spider', 'crawler', 'scraper', 'youtube-dl', 'ffmpeg'
        ];

        foreach ($downloadTools as $tool) {
            if (stripos($userAgent, $tool) !== false) {
                // Record the download attempt
                $details = "Download tool detected: {$tool}";
                $attemptResult = $this->downloadAttemptService->recordAttempt($request, $userId, $videoId, $details);

                Log::warning('Download tool detected', [
                    'user_agent' => $userAgent,
                    'ip' => $request->ip(),
                    'tool' => $tool,
                    'user_id' => $userId,
                    'video_id' => $videoId,
                    'is_blocked' => $attemptResult['blocked']
                ]);

                if ($attemptResult['blocked']) {
                    return redirect()->route('blocked.access', ['video_id' => $videoId])->with('error', $attemptResult['message']);
                }

                return response()->json(['error' => 'Access denied'], 403);
            }
        }

        // Check for common download headers
        $downloadHeaders = [
            'Range', // Used by download managers for resumable downloads
            'If-Range',
            'If-Modified-Since',
            'If-Unmodified-Since'
        ];

        $suspiciousHeaders = [];
        foreach ($downloadHeaders as $header) {
            if ($request->header($header)) {
                $suspiciousHeaders[$header] = $request->header($header);
            }
        }

        // If multiple download headers are present, it might be a download tool
        if (count($suspiciousHeaders) >= 2) {
            // Record the download attempt
            $details = 'Multiple download headers detected: ' . json_encode($suspiciousHeaders);
            $attemptResult = $this->downloadAttemptService->recordAttempt($request, $userId, $videoId, $details);

            Log::warning('Multiple download headers detected', [
                'headers' => $suspiciousHeaders,
                'user_agent' => $userAgent,
                'ip' => $request->ip(),
                'user_id' => $userId,
                'video_id' => $videoId,
                'is_blocked' => $attemptResult['blocked']
            ]);

            if ($attemptResult['blocked']) {
                return redirect()->route('blocked.access', ['video_id' => $videoId])->with('error', $attemptResult['message']);
            }

            return response()->json(['error' => 'Access denied'], 403);
        }

        // Check for referer
        $referer = $request->header('Referer');
        if (!$referer || !str_contains($referer, $request->getHost())) {
            // Record the download attempt
            $details = 'Missing or invalid referer: ' . ($referer ?: 'none');
            $attemptResult = $this->downloadAttemptService->recordAttempt($request, $userId, $videoId, $details);

            Log::warning('Missing or invalid referer', [
                'referer' => $referer,
                'user_agent' => $userAgent,
                'ip' => $request->ip(),
                'user_id' => $userId,
                'video_id' => $videoId,
                'is_blocked' => $attemptResult['blocked']
            ]);

            if ($attemptResult['blocked']) {
                return redirect()->route('blocked.access', ['video_id' => $videoId])->with('error', $attemptResult['message']);
            }

            return response()->json(['error' => 'Access denied'], 403);
        }

        // Add security headers to the response
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Content-Security-Policy', "default-src 'self'; media-src 'self'; object-src 'none'");
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
