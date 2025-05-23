<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class CheckStorageLink
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $publicStoragePath = public_path('storage');
        
        // Check if the public/storage directory exists and is a symbolic link
        if (!file_exists($publicStoragePath) || !is_link($publicStoragePath)) {
            Log::warning('Storage Link Middleware - Symbolic link missing or incorrect', [
                'public_storage_path' => $publicStoragePath,
                'exists' => file_exists($publicStoragePath),
                'is_link' => is_link($publicStoragePath)
            ]);
            
            // Run the command to check and create the symbolic link
            Artisan::call('storage:check-link');
        }
        
        return $next($request);
    }
}
