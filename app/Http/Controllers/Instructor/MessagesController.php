<?php

namespace App\Http\Controllers\Instructor;

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
        $this->middleware(\App\Http\Middleware\InstructorMiddleware::class);
        $this->contentFilterService = $contentFilterService;
    }

    /**
     * Display the messages page with list of enrolled students.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $instructor = Auth::user();
        
        // Get all students enrolled in any of the instructor's courses
        // Using a simplified approach to avoid SQL strict mode issues
        $enrolledStudentIds = DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
            ->where('courses.instructor_id', $instructor->user_id)
            ->select('enrollments.student_id')
            ->distinct()
            ->pluck('student_id');
            
        // Get student users who have the student role
        $contacts = User::whereIn('user_id', $enrolledStudentIds)
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('user_roles')
                      ->whereColumn('user_roles.user_id', 'users.user_id')
                      ->where('user_roles.role', 'student');
            })
            ->get();
            
        // Count unread messages for each student
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

        // Sort students by latest message time, if exists
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
    }

    /**
     * Show messages with a specific student.
     *
     * @param  int  $studentId
     * @return \Illuminate\View\View
     */
    public function show($studentId)
    {
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
                            ->with('error', 'You can only message students enrolled in your courses.');
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
            
        // Count unread messages and get last message time for each student
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

        // Sort students by latest message time, if exists
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
    }

    /**
     * Send a message to a student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        // تسجيل البيانات المستلمة للتشخيص
        \Illuminate\Support\Facades\Log::info('Received message request data:', $request->all());
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,user_id',
            'content' => 'required|string|max:1000',
            'course_id' => 'nullable|exists:courses,course_id'
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $instructor = Auth::user();
        
        // Log sending attempt
        \Illuminate\Support\Facades\Log::info('Instructor sending message', [
            'instructor_id' => $instructor->user_id,
            'receiver_id' => $request->receiver_id,
            'content_length' => strlen($request->content)
        ]);
        
        try {
            // إنشاء الرسالة
            $message = new DirectMessage();
            $message->user_id = $instructor->user_id;
            $message->sender_id = $instructor->user_id;
            $message->receiver_id = $request->receiver_id;
            $message->content = $request->content;
            $message->course_id = $request->course_id;
            $message->is_read = false;
            $message->save();
            
            // معالجة محتوى الرسالة وإشعار المشرفين إذا وجدت كلمات محظورة
            $filterResult = $this->contentFilterService->processMessageContent(
                $request->content,
                $instructor,
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
            \Illuminate\Support\Facades\Log::info('Message sent successfully', [
                'message_id' => $message->message_id,
                'instructor_id' => $instructor->user_id,
                'student_id' => $request->receiver_id,
                'was_filtered' => $message->is_filtered
            ]);

            // تخطي استخدام broadcast لتجنب مشكلة Pusher
            // broadcast(new NewMessageSent($message))->toOthers();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'formatted_time' => $message->created_at->format('h:i a'),
                    'was_filtered' => $message->is_filtered
                ]);
            }

            // Flash different message based on whether content was filtered
            $status = $message->is_filtered ? 'warning' : 'success';
            $statusMessage = $message->is_filtered 
                ? 'Message sent with some content filtered for inappropriate language.'
                : 'Message sent successfully.';

            return redirect()->route('instructor.messages.show', $request->receiver_id)
                            ->with($status, $statusMessage);
        } catch (\Exception $e) {
            // تسجيل الخطأ وإرجاع رسالة خطأ مناسبة
            \Illuminate\Support\Facades\Log::error('Error sending message:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
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
        // تحقق من نوع الطلب
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'student_id' => 'required|exists:users,user_id',
            'last_message_id' => 'nullable|integer'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $instructor = Auth::user();
        $studentId = $request->student_id;
        $lastMessageId = $request->last_message_id;
        
        \Illuminate\Support\Facades\Log::info('Instructor fetching new messages', [
            'instructor_id' => $instructor->user_id,
            'student_id' => $studentId,
            'last_message_id' => $lastMessageId
        ]);
        
        $query = DirectMessage::betweenUsers($instructor->user_id, $studentId)
                              ->orderBy('created_at');
                              
        if ($lastMessageId) {
            $query->where('message_id', '>', $lastMessageId);
        }
        
        $messages = $query->with('sender')->get();
        
        \Illuminate\Support\Facades\Log::info('Found messages count: ' . $messages->count());
        
        // Mark messages as read
        DirectMessage::where('sender_id', $studentId)
                    ->where('receiver_id', $instructor->user_id)
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);
                    
        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }
}
