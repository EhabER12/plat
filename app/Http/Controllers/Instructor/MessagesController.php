<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\DirectMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessagesController extends Controller
{
    /**
     * Display the messages page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $instructor = Auth::user();

        // Get all users who have exchanged messages with this instructor
        $contacts = User::whereIn('user_id', function($query) use ($instructor) {
            $query->select('sender_id')
                  ->from('messages')
                  ->where('receiver_id', $instructor->user_id)
                  ->union(
                      DB::table('messages')
                        ->select('receiver_id')
                        ->where('sender_id', $instructor->user_id)
                  );
        })
        ->whereExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('user_roles')
                  ->whereColumn('user_roles.user_id', 'users.user_id')
                  ->where('user_roles.role', 'student');
        })
        ->get();

        // Get the first contact's messages if there are any contacts
        $selectedContact = null;
        $messages = collect([]);

        if ($contacts->isNotEmpty()) {
            $selectedContact = $contacts->first();
            $messages = DirectMessage::betweenUsers($instructor->user_id, $selectedContact->user_id)
                                    ->orderBy('created_at')
                                    ->get();

            // Mark messages as read
            DirectMessage::where('sender_id', $selectedContact->user_id)
                        ->where('receiver_id', $instructor->user_id)
                        ->where('is_read', false)
                        ->update(['is_read' => true, 'read_at' => now()]);
        }

        // Count unread messages for each contact
        foreach ($contacts as $contact) {
            $contact->unread_count = DirectMessage::where('sender_id', $contact->user_id)
                                               ->where('receiver_id', $instructor->user_id)
                                               ->where('is_read', false)
                                               ->count();
        }

        return view('instructor.messages.index', compact('contacts', 'selectedContact', 'messages'));
    }

    /**
     * Show messages with a specific user.
     *
     * @param  int  $userId
     * @return \Illuminate\View\View
     */
    public function show($userId)
    {
        $instructor = Auth::user();
        $selectedContact = User::findOrFail($userId);

        // Get all users who have exchanged messages with this instructor
        $contacts = User::whereIn('user_id', function($query) use ($instructor) {
            $query->select('sender_id')
                  ->from('messages')
                  ->where('receiver_id', $instructor->user_id)
                  ->union(
                      DB::table('messages')
                        ->select('receiver_id')
                        ->where('sender_id', $instructor->user_id)
                  );
        })->get();

        // Get messages between the instructor and the selected contact
        $messages = DirectMessage::betweenUsers($instructor->user_id, $selectedContact->user_id)
                                ->orderBy('created_at')
                                ->get();

        // Mark messages as read
        DirectMessage::where('sender_id', $selectedContact->user_id)
                    ->where('receiver_id', $instructor->user_id)
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);

        // Count unread messages for each contact
        foreach ($contacts as $contact) {
            $contact->unread_count = DirectMessage::where('sender_id', $contact->user_id)
                                               ->where('receiver_id', $instructor->user_id)
                                               ->where('is_read', false)
                                               ->count();
        }

        return view('instructor.messages.index', compact('contacts', 'selectedContact', 'messages'));
    }

    /**
     * Send a message to a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,user_id',
            'content' => 'required|string|max:1000',
            'course_id' => 'nullable|exists:courses,course_id'
        ]);

        $message = DirectMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
            'course_id' => $request->course_id
        ]);

        return redirect()->route('instructor.messages.show', $request->receiver_id)
                         ->with('success', 'Message sent successfully.');
    }
}
