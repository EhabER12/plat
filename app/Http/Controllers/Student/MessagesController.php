<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DirectMessage;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\ContentFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\NewMessageSent;

class MessagesController extends Controller
{
    /**
     * @var ContentFilterService
     */
    protected $contentFilterService;

    /**
     * Create a new controller instance.
     *
     * @param ContentFilterService $contentFilterService
     */
    public function __construct(ContentFilterService $contentFilterService)
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\StudentMiddleware::class);
        $this->contentFilterService = $contentFilterService;
    }

    /**
     * Display the messages page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $student = Auth::user();

        // Get instructors for student's enrolled courses - simpler query approach
        // First get instructors of courses the student is enrolled in
        $instructorIds = DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
            ->where('enrollments.student_id', $student->user_id)
            ->select('courses.instructor_id')
            ->distinct()
            ->pluck('instructor_id');
            
        // Then get any instructors the student has messaged with
        $messageInstructorIds = DB::table('messages')
            ->where('sender_id', $student->user_id)
            ->select('receiver_id')
            ->distinct()
            ->union(
                DB::table('messages')
                ->where('receiver_id', $student->user_id)
                ->select('sender_id')
                ->distinct()
            )
            ->pluck('receiver_id');
            
        // Combine both sets
        $allInstructorIds = $instructorIds->merge($messageInstructorIds)->unique();
        
        // Get instructor users
        $contacts = User::whereIn('user_id', $allInstructorIds)
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
                                               
            // Get the latest message time
            $latestMessage = DirectMessage::betweenUsers($student->user_id, $contact->user_id)
                                         ->orderBy('created_at', 'desc')
                                         ->first();
                                         
            $contact->last_message_time = $latestMessage ? $latestMessage->created_at : null;
            
            // Get courses taught by this instructor in which the student is enrolled
            $contact->enrolled_courses = Course::select('courses.title', 'courses.course_id')
                                              ->join('enrollments', 'courses.course_id', '=', 'enrollments.course_id')
                                              ->where('enrollments.student_id', $student->user_id)
                                              ->where('courses.instructor_id', $contact->user_id)
                                              ->get();
        }
        
        // Sort contacts by latest message time
        $contacts = $contacts->sortByDesc(function($contact) {
            return $contact->last_message_time ?? $contact->created_at;
        });

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
        // or instructors of courses the student is enrolled in
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
        ->orWhereIn('user_id', function($query) use ($student) {
            $query->select('courses.instructor_id')
                  ->from('enrollments')
                  ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
                  ->where('enrollments.student_id', $student->user_id);
        })
        ->whereExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('user_roles')
                  ->whereColumn('user_roles.user_id', 'users.user_id')
                  ->where('user_roles.role', 'instructor');
        })
        ->distinct()
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
                                               
            // Get the latest message time
            $latestMessage = DirectMessage::betweenUsers($student->user_id, $contact->user_id)
                                         ->orderBy('created_at', 'desc')
                                         ->first();
                                         
            $contact->last_message_time = $latestMessage ? $latestMessage->created_at : null;
            
            // Get courses taught by this instructor in which the student is enrolled
            $contact->enrolled_courses = Course::select('courses.title', 'courses.course_id')
                                              ->join('enrollments', 'courses.course_id', '=', 'enrollments.course_id')
                                              ->where('enrollments.student_id', $student->user_id)
                                              ->where('courses.instructor_id', $contact->user_id)
                                              ->get();
        }
        
        // Sort contacts by latest message time
        $contacts = $contacts->sortByDesc(function($contact) {
            return $contact->last_message_time ?? $contact->created_at;
        });
        
        // Ensure selectedContact has enrolled_courses property set
        $selectedContact->enrolled_courses = Course::select('courses.title', 'courses.course_id')
                                                  ->join('enrollments', 'courses.course_id', '=', 'enrollments.course_id')
                                                  ->where('enrollments.student_id', $student->user_id)
                                                  ->where('courses.instructor_id', $selectedContact->user_id)
                                                  ->get();

        return view('student.messages.index', compact('contacts', 'selectedContact', 'messages'));
    }

    /**
     * Send a message to an instructor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        // تسجيل البيانات المستلمة للتشخيص
        Log::info('Received student message request data:', $request->all());
        
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
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only message instructors.'
                ], 422);
            }
            
            return redirect()->back()
                             ->with('error', 'You can only message instructors.')
                             ->withInput();
        }

        $student = Auth::user();
        
        // Log sending attempt
        Log::info('Student sending message', [
            'student_id' => $student->user_id,
            'receiver_id' => $request->receiver_id,
            'content_length' => strlen($request->content)
        ]);

        try {
            // إنشاء الرسالة
            $message = new DirectMessage();
            $message->user_id = $student->user_id;
            $message->sender_id = $student->user_id;
            $message->receiver_id = $request->receiver_id;
            $message->content = $request->content;
            $message->course_id = $request->course_id;
            $message->is_read = false;
            $message->save();
            
            // معالجة محتوى الرسالة وإشعار المشرفين إذا وجدت كلمات محظورة
            $filterResult = $this->contentFilterService->processMessageContent(
                $request->content,
                $student,
                $message,
                true // إرسال إشعار للمشرفين
            );
            
            // تحديث سجل الرسالة بنتائج الفلترة إذا وجدت كلمات محظورة
            if ($filterResult['has_banned_content']) {
                $message->contains_flagged_content = true;
                $message->flagged_severity = $filterResult['highest_severity'];
                $message->content = $filterResult['filtered_content'];
                $message->is_filtered = true;
                $message->save();
            }
            
            $message->load('sender');
            
            // Log message sent
            Log::info('Message sent successfully', [
                'message_id' => $message->message_id,
                'student_id' => $student->user_id,
                'instructor_id' => $request->receiver_id,
                'was_filtered' => $message->is_filtered
            ]);
            
            // Broadcast the new message event
            broadcast(new NewMessageSent($message))->toOthers();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'formatted_time' => $message->created_at->format('g:i A'),
                    'was_filtered' => $message->is_filtered
                ]);
            }

            // Flash different message based on whether content was filtered
            $status = $message->is_filtered ? 'warning' : 'success';
            $statusMessage = $message->is_filtered 
                ? 'Message sent with some content filtered for inappropriate language.'
                : 'Message sent successfully.';

            return redirect()->route('student.messages.show', $request->receiver_id)
                             ->with($status, $statusMessage);
                             
        } catch (\Exception $e) {
            // تسجيل الخطأ وإرجاع رسالة خطأ مناسبة
            Log::error('Error sending message:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إرسال الرسالة: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إرسال الرسالة: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Get new messages (for AJAX polling).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewMessages(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|exists:users,user_id',
            'last_message_id' => 'nullable|integer'
        ]);
        
        $student = Auth::user();
        $instructorId = $request->instructor_id;
        $lastMessageId = $request->last_message_id;
        
        Log::info('Fetching new messages', [
            'student_id' => $student->user_id,
            'instructor_id' => $instructorId,
            'last_message_id' => $lastMessageId
        ]);
        
        $query = DirectMessage::betweenUsers($student->user_id, $instructorId)
                             ->orderBy('created_at');
                              
        if ($lastMessageId) {
            $query->where('message_id', '>', $lastMessageId);
        }
        
        $messages = $query->with('sender')->get();
        
        Log::info('Found messages count: ' . $messages->count());
        
        // Mark messages as read
        DirectMessage::where('sender_id', $instructorId)
                    ->where('receiver_id', $student->user_id)
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);
                    
        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }
}
