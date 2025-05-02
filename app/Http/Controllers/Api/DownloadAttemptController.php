<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DownloadAttemptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DownloadAttemptController extends Controller
{
    /**
     * The download attempt service instance.
     *
     * @var \App\Services\DownloadAttemptService
     */
    protected $downloadAttemptService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\DownloadAttemptService  $downloadAttemptService
     * @return void
     */
    public function __construct(DownloadAttemptService $downloadAttemptService)
    {
        $this->downloadAttemptService = $downloadAttemptService;
    }

    /**
     * Report a download attempt from the client side
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function report(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'video_id' => 'required|integer|exists:course_videos,video_id',
            'details' => 'required|string',
            'type' => 'required|string|in:dev_tools,network,other'
        ]);

        $details = $validated['type'] . ': ' . $validated['details'];
        $result = $this->downloadAttemptService->recordAttempt($request, $user->user_id, $validated['video_id'], $details);

        return response()->json([
            'success' => true,
            'blocked' => $result['blocked'],
            'remaining_attempts' => $result['blocked'] ? 0 : $result['remaining_attempts'],
            'message' => $result['blocked'] ? $result['message'] : 'Attempt recorded'
        ]);
    }

    /**
     * Check if a user is blocked
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkBlocked(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'video_id' => 'required|integer|exists:course_videos,video_id'
        ]);

        $result = $this->downloadAttemptService->checkIfBlocked($user->user_id, $validated['video_id'], $request->ip());

        return response()->json([
            'success' => true,
            'blocked' => $result['blocked'],
            'remaining_time' => $result['blocked'] ? $result['remaining_time'] : 0,
            'message' => $result['blocked'] ? $result['message'] : 'Not blocked'
        ]);
    }
}
