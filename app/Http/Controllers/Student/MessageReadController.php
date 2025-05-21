<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DirectMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageReadController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\StudentMiddleware::class);
    }

    /**
     * Mark message as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markRead(Request $request)
    {
        try {
            $request->validate([
                'message_id' => 'required|exists:messages,message_id'
            ]);
            
            $student = Auth::user();
            $messageId = $request->message_id;
            
            Log::info('طالب: تحديد رسالة كمقروءة', [
                'student_id' => $student->user_id,
                'message_id' => $messageId
            ]);
            
            // تأكد أن الرسالة موجهة لهذا الطالب
            $message = DirectMessage::where('message_id', $messageId)
                        ->where('receiver_id', $student->user_id)
                        ->first();
                        
            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'الرسالة غير موجودة أو ليست موجهة إليك'
                ], 404);
            }
            
            // تحديث حالة القراءة
            $message->is_read = true;
            $message->read_at = now();
            $message->save();
            
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في تحديث حالة قراءة الرسالة للطالب', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة القراءة'
            ], 500);
        }
    }
}
