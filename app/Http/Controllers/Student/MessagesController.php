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
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
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
     * Display the messages page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $student = Auth::user();

        try {
            // Get instructors for student's enrolled courses
            $instructorIds = DB::table('enrollments')
                ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
                ->where('enrollments.student_id', $student->user_id)
                ->select('courses.instructor_id')
                ->distinct()
                ->pluck('instructor_id');

            // Also get instructors the student has messaged with
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

            // Add metadata to each contact
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

                // Get shared courses
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

        } catch (\Exception $e) {
            Log::error('Error loading student messages page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('student.messages.index', [
                'contacts' => collect([]),
                'selectedContact' => null,
                'messages' => collect([]),
                'error' => 'حدث خطأ أثناء تحميل الرسائل. الرجاء المحاولة مرة أخرى.'
            ]);
        }
    }

    /**
     * Show messages with a specific instructor.
     *
     * @param  int  $instructorId
     * @return \Illuminate\View\View
     */
    public function show($instructorId)
    {
        try {
            $student = Auth::user();
            $selectedContact = User::findOrFail($instructorId);

            // Verify that the selected user is an instructor
            $isInstructor = DB::table('user_roles')
                            ->where('user_id', $instructorId)
                            ->where('role', 'instructor')
                            ->exists();

            if (!$isInstructor) {
                return redirect()->route('student.messages.index')
                                ->with('error', 'يمكنك فقط مراسلة المدرسين.');
            }

            // Get all instructors who have exchanged messages with this student or teach courses the student is enrolled in
            $contacts = User::whereIn('user_id', function($query) use ($student) {
                $query->select('courses.instructor_id')
                    ->from('enrollments')
                    ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
                    ->where('enrollments.student_id', $student->user_id)
                    ->union(
                        DB::table('messages')
                        ->select('sender_id')
                        ->where('receiver_id', $student->user_id)
                        ->whereIn('sender_id', function($q) {
                            $q->select('user_roles.user_id')
                            ->from('user_roles')
                            ->where('role', 'instructor');
                        })
                    )
                    ->union(
                        DB::table('messages')
                        ->select('receiver_id')
                        ->where('sender_id', $student->user_id)
                        ->whereIn('receiver_id', function($q) {
                            $q->select('user_roles.user_id')
                            ->from('user_roles')
                            ->where('role', 'instructor');
                        })
                    );
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

            // Add metadata to each contact
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

                // Get shared courses
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
        } catch (\Exception $e) {
            Log::error('Error loading student messages with instructor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'instructor_id' => $instructorId
            ]);

            return redirect()->route('student.messages.index')
                            ->with('error', 'حدث خطأ أثناء تحميل المحادثة. الرجاء المحاولة مرة أخرى.');
        }
    }

    /**
     * Send a message to an instructor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        // Validate the request
        $request->validate([
            'receiver_id' => 'required|exists:users,user_id',
            'content' => 'required|string|max:10000',
            'course_id' => 'nullable|exists:courses,course_id'
        ]);

        try {
            $student = Auth::user();
            $receiverId = $request->input('receiver_id');
            $content = $request->input('content');
            $courseId = $request->input('course_id');

            // Create the message
            $message = new DirectMessage();
            $message->user_id = $student->user_id;
            $message->sender_id = $student->user_id;
            $message->receiver_id = $receiverId;
            $message->content = $content;
            $message->course_id = $courseId;
            $message->chat_id = 0; // Default chat_id
            $message->is_read = false;

            // El filtrado de contenido se realizará automáticamente en el modelo

            $message->save();

            // Log success
            Log::info('Student message sent successfully', [
                'message_id' => $message->message_id,
                'sender_id' => $student->user_id,
                'receiver_id' => $receiverId,
                'contains_flagged_content' => $message->contains_flagged_content ?? false
            ]);

            // Show warning if message was filtered
            if ($message->is_filtered ?? false) {
                return redirect()->route('student.messages.show', $receiverId)
                                ->with('warning', 'تم إرسال الرسالة بنجاح ولكن تم تصفية بعض المحتوى المحظور.');
            }

            // Redirect back with success message
            return redirect()->route('student.messages.show', $receiverId)
                            ->with('success', 'تم إرسال الرسالة بنجاح');

        } catch (\Exception $e) {
            // Log error
            Log::error('Error sending student message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Redirect back with error message
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
        try {
            // Log the incoming request
            Log::info('Get new student messages request received', [
                'request_data' => $request->all(),
                'content_type' => $request->header('Content-Type'),
                'request_format' => $request->format()
            ]);

            // Validate request
            $validator = Validator::make($request->all(), [
                'instructor_id' => 'required|exists:users,user_id',
                'last_message_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                Log::warning('Get new student messages validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            $student = Auth::user();
            $instructorId = $request->instructor_id;
            $lastMessageId = $request->last_message_id ?? 0;

            // Get messages newer than last_message_id
            $query = DirectMessage::betweenUsers($student->user_id, $instructorId)
                                  ->orderBy('created_at');

            if ($lastMessageId > 0) {
                $query->where('message_id', '>', $lastMessageId);
            }

            $messages = $query->get();

            Log::info('Retrieved new student messages', [
                'count' => $messages->count(),
                'last_message_id' => $lastMessageId,
                'student_id' => $student->user_id,
                'instructor_id' => $instructorId
            ]);

            // Mark received messages as read
            DirectMessage::where('sender_id', $instructorId)
                        ->where('receiver_id', $student->user_id)
                        ->where('is_read', false)
                        ->update(['is_read' => true, 'read_at' => now()]);

            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching new student messages', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحميل الرسائل الجديدة: ' . $e->getMessage()
            ], 500);
        }
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

            // Make sure the message is sent to this student
            $message = DirectMessage::where('message_id', $messageId)
                        ->where('receiver_id', $student->user_id)
                        ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'الرسالة غير موجودة أو ليست موجهة إليك'
                ], 404);
            }

            // Mark as read
            $message->is_read = true;
            $message->read_at = now();
            $message->save();

            return response()->json([
                'success' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking student message as read', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة القراءة'
            ], 500);
        }
    }
}
