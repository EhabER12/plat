<?php

namespace App\Http\Middleware;

use App\Models\VideoAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VideoAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->route('token');
        
        if (!$token) {
            return response()->json(['error' => 'Invalid access token'], 403);
        }
        
        // Find the video access record by token
        $videoAccess = VideoAccess::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
            
        if (!$videoAccess) {
            return response()->json(['error' => 'Invalid or expired token'], 403);
        }
        
        // Check if the user is authenticated and matches the token's user
        if (Auth::check() && Auth::id() !== $videoAccess->user_id) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }
        
        // Check if the IP address matches (optional, can be strict)
        if (env('APP_ENV') === 'production' && $videoAccess->ip_address && $request->ip() !== $videoAccess->ip_address) {
            // Log suspicious activity
            Log::warning('Suspicious video access attempt', [
                'token' => $token,
                'expected_ip' => $videoAccess->ip_address,
                'actual_ip' => $request->ip(),
                'user_id' => $videoAccess->user_id
            ]);
            
            // For security, you might want to invalidate the token
            $videoAccess->expires_at = now()->subMinute();
            $videoAccess->save();
            
            return response()->json(['error' => 'Access denied'], 403);
        }
        
        return $next($request);
    }
}
