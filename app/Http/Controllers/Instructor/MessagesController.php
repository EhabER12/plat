<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\DirectMessage;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
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
        $this->middleware(\App\Http\Middleware\InstructorMiddleware::class);
    }

    /**
     * Display the messages page with list of enrolled students.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $instructor = Auth::user();

            // Get all students enrolled in the instructor's courses
            $enrolledStudentIds = DB::table('enrollments')
                ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
                ->where('courses.instructor_id', $instructor->user_id)
                ->select('enrollments.student_id')
                ->distinct()
                ->pluck('student_id');

            // Get student users with student role
            $contacts = User::whereIn('user_id', $enrolledStudentIds)
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('user_roles')
                        ->whereColumn('user_roles.user_id', 'users.user_id')
                        ->where('user_roles.role', 'student');
                })
                ->get();

            // Add message counts and course data to each student
            foreach ($contacts as $student) {
                $student->unread_count = DirectMessage::where('sender_id', $student->user_id)
                                                ->where('receiver_id', $instructor->user_id)
                                                ->where('is_read', false)
                                                ->count();

                // Get the latest message time
                $latestMessage = DirectMessage::betweenUsers($instructor->user_id, $student->user_id)
                                            ->orderBy('created_at', 'desc')
                                            ->first();

                $student->last_message_time = $latestMessage ? $latestMessage->created_at : null;

                // Get enrolled courses for this student with this instructor
                $student->enrolled_courses = Course::select('courses.title', 'courses.course_id')
                                                ->join('enrollments', 'courses.course_id', '=', 'enrollments.course_id')
                                                ->where('enrollments.student_id', $student->user_id)
                                                ->where('courses.instructor_id', $instructor->user_id)
                                                ->get();
            }

            // Sort students by latest message time
            $contacts = $contacts->sortByDesc(function($student) {
                return $student->last_message_time ?? $student->created_at;
            });

            // Get the first student's messages if there are any students
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

                // Ensure selectedContact has enrolled_courses property set
                $selectedContact->enrolled_courses = Course::select('courses.title', 'courses.course_id')
                                                        ->join('enrollments', 'courses.course_id', '=', 'enrollments.course_id')
                                                        ->where('enrollments.student_id', $selectedContact->user_id)
                                                        ->where('courses.instructor_id', $instructor->user_id)
                                                        ->get();
            }

            // Get instructor's courses for the dropdown
            $instructorCourses = Course::where('instructor_id', $instructor->user_id)
                                    ->where(function($query) {
                                        $query->where('approval_status', 'approved')
                                            ->orWhere('status', 'published');
                                    })
                                    ->get();

            return view('instructor.messages.index', [
                'contacts' => $contacts,
                'selectedContact' => $selectedContact,
                'messages' => $messages,
                'courses' => $instructorCourses
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading instructor messages page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('instructor.messages.index', [
                'contacts' => collect([]),
                'selectedContact' => null,
                'messages' => collect([]),
                'courses' => collect([]),
                'error' => 'حدث خطأ أثناء تحميل الرسائل. الرجاء المحاولة مرة أخرى.'
            ]);
        }
    }

    /**
     * Show messages with a specific student.
     *
     * @param  int  $studentId
     * @return \Illuminate\View\View
     */
    public function show($studentId)
    {
        try {
            $instructor = Auth::user();
            $selectedContact = User::findOrFail($studentId);

            // Verify that the student is enrolled in at least one of the instructor's courses
            $isEnrolled = Enrollment::whereHas('course', function($query) use ($instructor) {
                                $query->where('instructor_id', $instructor->user_id);
                            })
                            ->where('student_id', $studentId)
                            ->exists();

            if (!$isEnrolled) {
                return redirect()->route('instructor.messages.index')
                                ->with('error', 'يمكنك فقط مراسلة الطلاب المسجلين في دوراتك.');
            }

            // Get all enrolled students for sidebar
            $contacts = User::select('users.*')
                ->join('enrollments', 'users.user_id', '=', 'enrollments.student_id')
                ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
                ->where('courses.instructor_id', $instructor->user_id)
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('user_roles')
                        ->whereColumn('user_roles.user_id', 'users.user_id')
                        ->where('user_roles.role', 'student');
                })
                ->distinct()
                ->get();

            // Add metadata to each student
            foreach ($contacts as $student) {
                $student->unread_count = DirectMessage::where('sender_id', $student->user_id)
                                                ->where('receiver_id', $instructor->user_id)
                                                ->where('is_read', false)
                                                ->count();

                // Get the latest message time
                $latestMessage = DirectMessage::betweenUsers($instructor->user_id, $student->user_id)
                                            ->orderBy('created_at', 'desc')
                                            ->first();

                $student->last_message_time = $latestMessage ? $latestMessage->created_at : null;

                // Get enrolled courses for this student with this instructor
                $student->enrolled_courses = Course::select('courses.title', 'courses.course_id')
                                                ->join('enrollments', 'courses.course_id', '=', 'enrollments.course_id')
                                                ->where('enrollments.student_id', $student->user_id)
                                                ->where('courses.instructor_id', $instructor->user_id)
                                                ->get();
            }

            // Sort students by latest message time
            $contacts = $contacts->sortByDesc(function($student) {
                return $student->last_message_time ?? $student->created_at;
            });

            // Get messages between the instructor and the selected student
            $messages = DirectMessage::betweenUsers($instructor->user_id, $selectedContact->user_id)
                                    ->orderBy('created_at')
                                    ->get();

            // Mark messages as read
            DirectMessage::where('sender_id', $selectedContact->user_id)
                        ->where('receiver_id', $instructor->user_id)
                        ->where('is_read', false)
                        ->update(['is_read' => true, 'read_at' => now()]);

            // Get instructor's courses for the dropdown
            $instructorCourses = Course::where('instructor_id', $instructor->user_id)
                                    ->where(function($query) {
                                        $query->where('approval_status', 'approved')
                                            ->orWhere('status', 'published');
                                    })
                                    ->get();

            // Ensure selectedContact has enrolled_courses property set
            $selectedContact->enrolled_courses = Course::select('courses.title', 'courses.course_id')
                                                    ->join('enrollments', 'courses.course_id', '=', 'enrollments.course_id')
                                                    ->where('enrollments.student_id', $selectedContact->user_id)
                                                    ->where('courses.instructor_id', $instructor->user_id)
                                                    ->get();

            return view('instructor.messages.index', [
                'contacts' => $contacts,
                'selectedContact' => $selectedContact,
                'messages' => $messages,
                'courses' => $instructorCourses
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading instructor messages with student', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'student_id' => $studentId
            ]);

            return redirect()->route('instructor.messages.index')
                            ->with('error', 'حدث خطأ أثناء تحميل المحادثة. الرجاء المحاولة مرة أخرى.');
        }
    }

    /**
     * Send a message to a student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        // Log the incoming request
        Log::info('Instructor message send request received', [
            'request_data' => $request->all(),
            'is_ajax' => $request->ajax(),
            'content_type' => $request->header('Content-Type')
        ]);

        // Validate the request
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,user_id',
            'content' => 'required|string|max:10000',
            'course_id' => 'nullable|exists:courses,course_id'
        ]);

        if ($validator->fails()) {
            Log::warning('Instructor message validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $instructor = Auth::user();

            // Create the message
            $message = new DirectMessage();
            $message->user_id = $instructor->user_id;
            $message->sender_id = $instructor->user_id;
            $message->receiver_id = $request->receiver_id;
            $message->content = $request->content;
            $message->course_id = $request->course_id;
            $message->is_read = false;
            $message->save();

            // Load sender relation
            $message->load('sender');

            DB::commit();

            // Try to broadcast the event (non-critical)
            try {
                event(new NewMessageSent($message));
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast message event', [
                    'message_id' => $message->message_id,
                    'error' => $e->getMessage()
                ]);
            }

            Log::info('Instructor message sent successfully', [
                'message_id' => $message->message_id
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'formatted_time' => $message->created_at->format('g:i A')
                ]);
            }

            return redirect()->route('instructor.messages.show', $request->receiver_id)
                            ->with('success', 'تم إرسال الرسالة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Instructor message send error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إرسال الرسالة: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                            ->with('error', 'حدث خطأ أثناء إرسال الرسالة')
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
            Log::info('Get new messages request received', [
                'request_data' => $request->all(),
                'content_type' => $request->header('Content-Type')
            ]);

            // Validate request
            $validator = Validator::make($request->all(), [
                'student_id' => 'required|exists:users,user_id',
                'last_message_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                Log::warning('Get new messages validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            $instructor = Auth::user();
            $studentId = $request->student_id;
            $lastMessageId = $request->last_message_id;

            // Get messages newer than last_message_id
            $query = DirectMessage::betweenUsers($instructor->user_id, $studentId)
                                  ->orderBy('created_at');

            if ($lastMessageId) {
                $query->where('message_id', '>', $lastMessageId);
            }

            $messages = $query->get();

            Log::info('Retrieved new messages', [
                'count' => $messages->count(),
                'last_message_id' => $lastMessageId
            ]);

            // Mark received messages as read
            DirectMessage::where('sender_id', $studentId)
                        ->where('receiver_id', $instructor->user_id)
                        ->where('is_read', false)
                        ->update(['is_read' => true, 'read_at' => now()]);

            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching new instructor messages', [
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

            $instructor = Auth::user();
            $messageId = $request->message_id;

            // Make sure the message is sent to this instructor
            $message = DirectMessage::where('message_id', $messageId)
                        ->where('receiver_id', $instructor->user_id)
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
            Log::error('Error marking instructor message as read', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة القراءة'
            ], 500);
        }
    }
}
