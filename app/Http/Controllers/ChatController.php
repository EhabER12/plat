<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatParticipant;
use App\Models\Course;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display a listing of the chats.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all chats where the user is a participant
        $chats = Chat::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->user_id)
                  ->whereNull('left_at');
        })
        ->with(['participants.user', 'course', 'messages' => function ($query) {
            $query->latest()->limit(1);
        }])
        ->orderByRaw('COALESCE(last_message_at, created_at) DESC')
        ->get();
        
        return view('chats.index', compact('chats'));
    }

    /**
     * Show the form for creating a new chat.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get courses based on user role
        $isInstructor = DB::table('user_roles')
            ->where('user_id', $user->user_id)
            ->where('role', 'instructor')
            ->exists();
            
        if ($isInstructor) {
            $courses = Course::where('instructor_id', $user->user_id)
                            ->where('approval_status', 'approved')
                            ->get();
        } else {
            $courses = Course::whereHas('enrollments', function ($query) use ($user) {
                $query->where('student_id', $user->user_id);
            })
            ->where('approval_status', 'approved')
            ->get();
        }
        
        return view('chats.create', compact('courses'));
    }

    /**
     * Store a newly created chat in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,course_id',
            'is_group_chat' => 'boolean',
            'participants' => 'required|array',
            'participants.*' => 'exists:users,user_id',
        ]);
        
        $user = Auth::user();
        
        DB::beginTransaction();
        
        try {
            // Create the chat
            $chat = Chat::create([
                'title' => $validated['title'],
                'created_by' => $user->user_id,
                'course_id' => $validated['course_id'],
                'is_group_chat' => $validated['is_group_chat'] ?? false,
                'last_message_at' => now(),
            ]);
            
            // Add the creator as a participant and admin
            ChatParticipant::create([
                'chat_id' => $chat->chat_id,
                'user_id' => $user->user_id,
                'is_admin' => true,
                'joined_at' => now(),
            ]);
            
            // Add other participants
            foreach ($validated['participants'] as $participantId) {
                if ($participantId != $user->user_id) {
                    ChatParticipant::create([
                        'chat_id' => $chat->chat_id,
                        'user_id' => $participantId,
                        'joined_at' => now(),
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('chats.show', $chat->chat_id)
                ->with('success', 'Chat created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create chat: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified chat.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Check if the user is a participant in this chat
        $chat = Chat::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->user_id)
                  ->whereNull('left_at');
        })
        ->with(['participants.user', 'course', 'messages' => function ($query) {
            $query->with('sender')->orderBy('created_at', 'asc');
        }])
        ->findOrFail($id);
        
        // Mark all messages as read
        $participant = $chat->participants()->where('user_id', $user->user_id)->first();
        $participant->markAsRead();
        
        return view('chats.show', compact('chat'));
    }

    /**
     * Send a message in the chat.
     */
    public function sendMessage(Request $request, $chatId)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        $user = Auth::user();
        
        // Check if the user is a participant in this chat
        $chat = Chat::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->user_id)
                  ->whereNull('left_at');
        })->findOrFail($chatId);
        
        $messageData = [
            'chat_id' => $chat->chat_id,
            'user_id' => $user->user_id,
            'content' => $validated['content'],
            'read_by' => [$user->user_id], // Mark as read by sender
        ];
        
        // Handle attachment if present
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('chat_attachments', $filename, 'public');
            
            $messageData['attachment_url'] = $path;
            $messageData['attachment_type'] = $file->getClientMimeType();
        }
        
        // Create the message
        $message = Message::create($messageData);
        
        // Update the last message timestamp in the chat
        $chat->update(['last_message_at' => now()]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('sender'),
            ]);
        }
        
        return redirect()->route('chats.show', $chat->chat_id);
    }

    /**
     * Leave the chat.
     */
    public function leave($chatId)
    {
        $user = Auth::user();
        
        // Find the participant record
        $participant = ChatParticipant::where('chat_id', $chatId)
                                    ->where('user_id', $user->user_id)
                                    ->whereNull('left_at')
                                    ->firstOrFail();
        
        // Mark as left
        $participant->update(['left_at' => now()]);
        
        return redirect()->route('chats.index')
            ->with('success', 'You have left the chat');
    }

    /**
     * Add participants to the chat.
     */
    public function addParticipants(Request $request, $chatId)
    {
        $validated = $request->validate([
            'participants' => 'required|array',
            'participants.*' => 'exists:users,user_id',
        ]);
        
        $user = Auth::user();
        
        // Check if the user is an admin in this chat
        $chat = Chat::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->user_id)
                  ->where('is_admin', true)
                  ->whereNull('left_at');
        })->findOrFail($chatId);
        
        // Add new participants
        foreach ($validated['participants'] as $participantId) {
            // Check if the participant already exists
            $existingParticipant = ChatParticipant::where('chat_id', $chat->chat_id)
                                                ->where('user_id', $participantId)
                                                ->first();
            
            if ($existingParticipant) {
                // If they left, mark them as rejoined
                if ($existingParticipant->left_at) {
                    $existingParticipant->update([
                        'left_at' => null,
                        'joined_at' => now(),
                    ]);
                }
                // Otherwise they're already in the chat
            } else {
                // Add new participant
                ChatParticipant::create([
                    'chat_id' => $chat->chat_id,
                    'user_id' => $participantId,
                    'joined_at' => now(),
                ]);
            }
        }
        
        return redirect()->route('chats.show', $chat->chat_id)
            ->with('success', 'Participants added successfully');
    }
}
