<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DirectMessage;
use App\Models\User;
use App\Models\UserRole;
use App\Services\ContentFilterService;
use App\Notifications\AdminMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\AdminMiddleware::class);
    }

    /**
     * Display the admin messages dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $admin = Auth::user();

        // Get all messages involving admin
        $adminMessages = DirectMessage::where('sender_id', $admin->user_id)
            ->orWhere('receiver_id', $admin->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Group messages by conversation partner
        $conversations = [];
        foreach ($adminMessages as $message) {
            $otherUserId = ($message->sender_id == $admin->user_id)
                ? $message->receiver_id
                : $message->sender_id;

            if (!isset($conversations[$otherUserId])) {
                $conversations[$otherUserId] = [
                    'user_id' => $otherUserId,
                    'messages' => [],
                    'last_message_time' => $message->created_at,
                    'unread_count' => 0
                ];
            }

            $conversations[$otherUserId]['messages'][] = $message;

            // Count unread messages from other user to admin
            if ($message->receiver_id == $admin->user_id && !$message->is_read) {
                $conversations[$otherUserId]['unread_count']++;
            }
        }

        // Get user details and prepare conversation data
        $conversationData = [];
        foreach ($conversations as $userId => $conversation) {
            $user = User::find($userId);
            if ($user) {
                $lastMessage = collect($conversation['messages'])->first(); // Already ordered by desc

                $conversationData[] = [
                    'user' => $user,
                    'last_message' => $lastMessage,
                    'message_count' => count($conversation['messages']),
                    'unread_count' => $conversation['unread_count'],
                    'last_message_time' => $conversation['last_message_time']
                ];
            }
        }

        // Sort by last message time
        usort($conversationData, function($a, $b) {
            return $b['last_message_time'] <=> $a['last_message_time'];
        });

        return view('admin.messages.index', compact('conversationData'));
    }

    /**
     * Show the form for creating a new conversation.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get all users except admins
        $users = User::with('roles')->whereDoesntHave('roles', function($q) {
            $q->where('role', 'admin');
        })->orderBy('name')->get();

        return view('admin.messages.create', compact('users'));
    }

    /**
     * Store a new conversation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'content' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $admin = Auth::user();
            $receiverId = $request->input('user_id');
            $content = $request->input('content');

            // Create the message
            $message = new DirectMessage();
            $message->user_id = $admin->user_id;
            $message->sender_id = $admin->user_id;
            $message->receiver_id = $receiverId;
            $message->content = $content;
            $message->chat_id = 0; // Default chat_id
            $message->is_read = false;

            $message->save();

            // Send notification to the user
            $receiver = User::find($receiverId);
            if ($receiver) {
                try {
                    $receiver->notify(new AdminMessageNotification($message, $admin));

                    // Fix the notifiable_id in the notification record
                    \DB::table('notifications')
                        ->where('user_id', $receiver->user_id)
                        ->whereNull('notifiable_id')
                        ->update(['notifiable_id' => $receiver->user_id]);

                } catch (\Exception $e) {
                    \Log::error('Error sending admin message notification', [
                        'error' => $e->getMessage(),
                        'admin_id' => $admin->user_id,
                        'receiver_id' => $receiverId
                    ]);
                }
            }

            return redirect()->route('admin.messages.conversation', $receiverId)
                ->with('success', 'تم إرسال الرسالة بنجاح');

        } catch (\Exception $e) {
            Log::error('Error sending admin message', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
                'receiver_id' => $request->input('user_id')
            ]);

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إرسال الرسالة')
                ->withInput();
        }
    }

    /**
     * Display conversation with a specific user.
     *
     * @param  int  $userId
     * @return \Illuminate\View\View
     */
    public function conversation($userId)
    {
        $admin = Auth::user();
        $user = User::findOrFail($userId);

        // Get all messages between admin and user
        $messages = DirectMessage::where(function($query) use ($admin, $user) {
            $query->where('sender_id', $admin->user_id)
                  ->where('receiver_id', $user->user_id);
        })->orWhere(function($query) use ($admin, $user) {
            $query->where('sender_id', $user->user_id)
                  ->where('receiver_id', $admin->user_id);
        })->orderBy('created_at', 'asc')->get();

        // Mark messages from user to admin as read
        DirectMessage::where('sender_id', $user->user_id)
            ->where('receiver_id', $admin->user_id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('admin.messages.conversation', compact('user', 'messages'));
    }

    /**
     * Send a message via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,user_id',
            'content' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 422);
        }

        try {
            $admin = Auth::user();
            $receiverId = $request->input('receiver_id');
            $content = $request->input('content');

            // Create the message
            $message = new DirectMessage();
            $message->user_id = $admin->user_id;
            $message->sender_id = $admin->user_id;
            $message->receiver_id = $receiverId;
            $message->content = $content;
            $message->chat_id = 0; // Default chat_id
            $message->is_read = false;

            $message->save();

            // Send notification to the user
            $receiver = User::find($receiverId);
            if ($receiver) {
                $receiver->notify(new AdminMessageNotification($message, $admin));
            }

            return response()->json([
                'success' => true,
                'message' => [
                    'message_id' => $message->message_id,
                    'content' => $message->content,
                    'sender_name' => $admin->name,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'is_admin' => true
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending admin message via AJAX', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
                'receiver_id' => $request->input('receiver_id')
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء إرسال الرسالة'
            ], 500);
        }
    }

    /**
     * Get new messages for a conversation via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'last_message_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 422);
        }

        try {
            $admin = Auth::user();
            $userId = $request->user_id;
            $lastMessageId = $request->last_message_id ?? 0;

            // Get messages newer than last_message_id
            $query = DirectMessage::where(function($query) use ($admin, $userId) {
                $query->where('sender_id', $admin->user_id)
                      ->where('receiver_id', $userId);
            })->orWhere(function($query) use ($admin, $userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $admin->user_id);
            })->orderBy('created_at');

            if ($lastMessageId > 0) {
                $query->where('message_id', '>', $lastMessageId);
            }

            $messages = $query->get();

            // Mark new messages from user as read
            DirectMessage::where('sender_id', $userId)
                ->where('receiver_id', $admin->user_id)
                ->where('message_id', '>', $lastMessageId)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            $messagesData = $messages->map(function($message) use ($admin) {
                return [
                    'message_id' => $message->message_id,
                    'content' => $message->content,
                    'sender_name' => $message->sender->name,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'is_admin' => $message->sender_id == $admin->user_id
                ];
            });

            return response()->json([
                'success' => true,
                'messages' => $messagesData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting new admin messages', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
                'user_id' => $request->user_id
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء جلب الرسائل الجديدة'
            ], 500);
        }
    }


}
