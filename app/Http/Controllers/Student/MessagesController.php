<?php

namespace App\Http\Controllers\Student;

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
        $student = Auth::user();

        // Get all instructors who have exchanged messages with this student
        $contacts = User::whereIn('user_id', function($query) use ($student) {
            $query->select('sender_id')
                  ->from('messages')
                  ->where('receiver_id', $student->user_id)
                  ->union(
                      DB::table('messages')
                        ->select('receiver_id')
                        ->where('sender_id', $student->user_id)
                  );
        })
        ->whereExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('user_roles')
                  ->whereColumn('user_roles.user_id', 'users.user_id')
                  ->where('user_roles.role', 'instructor');
        })
        ->get();

        // Get the first contact's messages if there are any contacts
        $selectedContact = null;
        $messages = collect([]);

        if ($contacts->isNotEmpty()) {
            $selectedContact = $contacts->first();
            $messages = DirectMessage::betweenUsers($student->user_id, $selectedContact->user_id)
                                    ->orderBy('created_at')
                                    ->get();

            // Mark messages as read
            DirectMessage::where('sender_id', $selectedContact->user_id)
                        ->where('receiver_id', $student->user_id)
                        ->where('is_read', false)
                        ->update(['is_read' => true, 'read_at' => now()]);
        }

        // Count unread messages for each contact
        foreach ($contacts as $contact) {
            $contact->unread_count = DirectMessage::where('sender_id', $contact->user_id)
                                               ->where('receiver_id', $student->user_id)
                                               ->where('is_read', false)
                                               ->count();
        }

        return view('student.messages.index', compact('contacts', 'selectedContact', 'messages'));
    }

    /**
     * Show messages with a specific instructor.
     *
     * @param  int  $instructorId
     * @return \Illuminate\View\View
     */
    public function show($instructorId)
    {
        $student = Auth::user();
        $selectedContact = User::findOrFail($instructorId);

        // Verify that the selected user is an instructor
        $isInstructor = DB::table('user_roles')
                          ->where('user_id', $instructorId)
                          ->where('role', 'instructor')
                          ->exists();

        if (!$isInstructor) {
            return redirect()->route('student.messages.index')
                             ->with('error', 'You can only message instructors.');
        }

        // Get all instructors who have exchanged messages with this student
        $contacts = User::whereIn('user_id', function($query) use ($student) {
            $query->select('sender_id')
                  ->from('messages')
                  ->where('receiver_id', $student->user_id)
                  ->union(
                      DB::table('messages')
                        ->select('receiver_id')
                        ->where('sender_id', $student->user_id)
                  );
        })
        ->whereExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('user_roles')
                  ->whereColumn('user_roles.user_id', 'users.user_id')
                  ->where('user_roles.role', 'instructor');
        })
        ->get();

        // Get messages between the student and the selected instructor
        $messages = DirectMessage::betweenUsers($student->user_id, $selectedContact->user_id)
                                ->orderBy('created_at')
                                ->get();

        // Mark messages as read
        DirectMessage::where('sender_id', $selectedContact->user_id)
                    ->where('receiver_id', $student->user_id)
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);

        // Count unread messages for each contact
        foreach ($contacts as $contact) {
            $contact->unread_count = DirectMessage::where('sender_id', $contact->user_id)
                                               ->where('receiver_id', $student->user_id)
                                               ->where('is_read', false)
                                               ->count();
        }

        return view('student.messages.index', compact('contacts', 'selectedContact', 'messages'));
    }

    /**
     * Send a message to an instructor.
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

        // Verify that the receiver is an instructor
        $isInstructor = DB::table('user_roles')
                          ->where('user_id', $request->receiver_id)
                          ->where('role', 'instructor')
                          ->exists();

        if (!$isInstructor) {
            return redirect()->back()
                             ->with('error', 'You can only message instructors.')
                             ->withInput();
        }

        $message = DirectMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
            'course_id' => $request->course_id
        ]);

        return redirect()->route('student.messages.show', $request->receiver_id)
                         ->with('success', 'Message sent successfully.');
    }
}
