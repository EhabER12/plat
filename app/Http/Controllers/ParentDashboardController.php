<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\ExamAttempt;
use App\Models\Notification;
use App\Models\StudentProgress;
use App\Models\ParentStudentRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParentDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\RoleMiddleware::class.':parent');
    }

    /**
     * Display parent dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $parentId = Auth::id();
        
        // Get all verified students linked to this parent
        $students = $this->getVerifiedStudents($parentId);
        
        // Get pending relations
        $pendingRelations = ParentStudentRelation::where('parent_id', $parentId)
            ->where('verification_status', 'pending')
            ->get();
            
        // Get rejected relations
        $rejectedRelations = ParentStudentRelation::where('parent_id', $parentId)
            ->where('verification_status', 'rejected')
            ->get();
        
        // Initialize statistics array for each student
        $studentStats = [];
        $alerts = [];
        $recentActivities = [];
        
        if (count($students) > 0) {
            foreach ($students as $student) {
                $stats = $this->getStudentStatistics($student->user_id);
                $studentStats[$student->user_id] = $stats;
                
                // Generate alerts for this student
                $studentAlerts = $this->generateStudentAlerts($student);
                $alerts = array_merge($alerts, $studentAlerts);
                
                // Get recent activities
                $studentActivities = $this->getStudentRecentActivities($student, 3);
                $recentActivities = array_merge($recentActivities, $studentActivities);
            }
            
            // Sort activities by time
            usort($recentActivities, function($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });
            
            // Limit to 10 most recent activities
            $recentActivities = array_slice($recentActivities, 0, 10);
        }
        
        return view('parent.dashboard', compact('students', 'pendingRelations', 'rejectedRelations', 'studentStats', 'alerts', 'recentActivities'));
    }
    
    /**
     * Show student activity details.
     *
     * @param  int  $studentId
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function studentActivity($studentId)
    {
        $parentId = Auth::id();
        
        // Check if this student is verified and linked to this parent
        $relation = ParentStudentRelation::where('parent_id', $parentId)
            ->where('student_id', $studentId)
            ->where('verification_status', 'approved')
            ->first();
            
        if (!$relation) {
            return redirect()->route('parent.dashboard')->with('error', 'غير مسموح لك بعرض هذا الطالب.');
        }
        
        // Get student data
        $student = User::join('user_roles', function($join) {
                $join->on('users.user_id', '=', 'user_roles.user_id')
                    ->where('user_roles.role', '=', 'student');
            })
            ->where('users.user_id', $studentId)
            ->select('users.*')
            ->first();
            
        if (!$student) {
            return redirect()->route('parent.dashboard')->with('error', 'لم يتم العثور على الطالب.');
        }
        
        // Get courses enrolled
        $enrolledCourses = Course::join('enrollments', 'courses.course_id', '=', 'enrollments.course_id')
            ->where('enrollments.student_id', $studentId)
            ->select('courses.*', 'enrollments.enrolled_at', 'enrollments.updated_at as last_activity_at', 'enrollments.progress')
            ->get();
            
        // Convert to collection with proper pivot structure
        foreach ($enrolledCourses as $course) {
            // Create a pivot property if it doesn't exist
            if (!isset($course->pivot)) {
                $course->pivot = new \stdClass();
            }
            
            // Ensure pivot has all the required properties
            $course->pivot->progress = $course->progress ?? 0;
            $course->pivot->enrolled_at = $course->enrolled_at;
            $course->pivot->last_activity_at = $course->last_activity_at;
        }
            
        // Get course contents
        $courseContents = [];
        foreach ($enrolledCourses as $course) {
            $contents = $this->getCourseContents($studentId, $course->course_id);
            $courseContents[$course->course_id] = $contents;
        }
        
        // Get exam attempts
        $completedExams = ExamAttempt::where('student_id', $studentId)
            ->whereNotNull('completed_at')
            ->count();
            
        // Get average exam score
        $avgScore = ExamAttempt::where('student_id', $studentId)
            ->whereNotNull('completed_at')
            ->avg('score');
        $avgScore = round($avgScore ?? 0);
        
        // Get formatted exam attempts for display
        $examAttempts = $this->getExamAttempts($studentId);
        
        // Get recent activities
        $activities = $this->getStudentDetailedActivities($studentId);
        
        // Generate badges/achievements
        $badges = $this->generateStudentBadges($studentId);
        
        // Generate alerts for this student
        $alerts = $this->generateStudentAlerts($student);
        
        return view('parent.student_activity', compact(
            'student', 
            'enrolledCourses', 
            'courseContents', 
            'completedExams', 
            'avgScore', 
            'examAttempts', 
            'activities', 
            'badges',
            'alerts'
        ));
    }
    
    /**
     * Show all activities.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function activities(Request $request)
    {
        $parentId = Auth::id();
        
        // Get all verified students linked to this parent
        $students = $this->getVerifiedStudents($parentId);
        
        $studentIds = $students->pluck('user_id')->toArray();
        
        // Filter parameters
        $selectedStudentId = $request->input('student');
        $activityType = $request->input('activity_type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        // Build query to get activities
        $query = DB::table('student_activities')
            ->whereIn('student_id', $studentIds);
            
        if ($selectedStudentId) {
            $query->where('student_id', $selectedStudentId);
        }
        
        if ($activityType) {
            $query->where('activity_type', $activityType);
        }
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        // Get paginated results
        $activitiesPagination = $query->orderBy('created_at', 'desc')
            ->paginate(20);
            
        // Format the activities for display
        $activities = [];
        foreach ($activitiesPagination as $activity) {
            $student = $students->firstWhere('user_id', $activity->student_id);
            
            $activities[] = [
                'id' => $activity->id,
                'student_id' => $activity->student_id,
                'student_name' => $student ? $student->name : 'غير معروف',
                'type' => $activity->activity_type,
                'title' => $activity->title,
                'description' => $activity->description,
                'timestamp' => $activity->created_at,
                'related_id' => $activity->related_id
            ];
        }
        
        return view('parent.activities', compact('students', 'activities', 'activitiesPagination'));
    }
    
    /**
     * Show link request form.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function linkRequestForm()
    {
        $parentId = Auth::id();
        
        // Get existing relation requests
        $relationRequests = ParentStudentRelation::where('parent_id', $parentId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('parent.link_request', compact('relationRequests'));
    }
    
    /**
     * Store link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeLinkRequest(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'relation' => 'required|string|in:parent,guardian,other',
            'birth_certificate' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'parent_id_card' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'additional_document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $parentId = Auth::id();
        
        // Upload documents
        $birthCertificatePath = $request->file('birth_certificate')->store('verification_documents', 'public');
        $parentIdCardPath = $request->file('parent_id_card')->store('verification_documents', 'public');
        $additionalDocumentPath = null;
        
        if ($request->hasFile('additional_document')) {
            $additionalDocumentPath = $request->file('additional_document')->store('verification_documents', 'public');
        }
        
        // Create the relation request
        ParentStudentRelation::create([
            'parent_id' => $parentId,
            'student_name' => $request->student_name,
            'relation_type' => $request->relation,
            'birth_certificate' => $birthCertificatePath,
            'parent_id_card' => $parentIdCardPath,
            'additional_document' => $additionalDocumentPath,
            'notes' => $request->notes,
            'verification_status' => 'pending',
            'token' => Str::random(40)
        ]);
        
        // Notify administrators about the new request
        $admins = User::join('user_roles', function($join) {
                $join->on('users.user_id', '=', 'user_roles.user_id')
                    ->where('user_roles.role', '=', 'admin');
            })
            ->select('users.*')
            ->get();
            
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->user_id,
                'title' => 'طلب ربط جديد لولي أمر',
                'message' => 'تم استلام طلب ربط جديد من ولي الأمر: ' . Auth::user()->name,
                'type' => 'parent_verification',
                'related_entity' => 'parent_student_relation',
                'entity_id' => $parentId,
                'sent_at' => now()
            ]);
        }
        
        return redirect()->route('parent.student.link.request')
            ->with('success', 'تم إرسال طلب الربط بنجاح. سيتم مراجعته من قبل الإدارة قريباً.');
    }
    
    /**
     * Resubmit a rejected link request.
     *
     * @param  int  $requestId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resubmitLinkRequest($requestId)
    {
        $parentId = Auth::id();
        
        $relationRequest = ParentStudentRelation::where('id', $requestId)
            ->where('parent_id', $parentId)
            ->where('verification_status', 'rejected')
            ->first();
            
        if (!$relationRequest) {
            return redirect()->route('parent.student.link.request')
                ->with('error', 'لم يتم العثور على طلب الربط أو لا يمكن إعادة تقديمه.');
        }
        
        // Update to pending status
        $relationRequest->update([
            'verification_status' => 'pending',
            'verification_notes' => null,
            'updated_at' => now()
        ]);
        
        // Notify administrators about the resubmitted request
        $admins = User::join('user_roles', function($join) {
                $join->on('users.user_id', '=', 'user_roles.user_id')
                    ->where('user_roles.role', '=', 'admin');
            })
            ->select('users.*')
            ->get();
            
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->user_id,
                'title' => 'إعادة تقديم طلب ربط ولي أمر',
                'message' => 'تم إعادة تقديم طلب ربط من ولي الأمر: ' . Auth::user()->name,
                'type' => 'parent_verification',
                'related_entity' => 'parent_student_relation',
                'entity_id' => $parentId,
                'sent_at' => now()
            ]);
        }
        
        return redirect()->route('parent.student.link.request')
            ->with('success', 'تم إعادة تقديم طلب الربط بنجاح. سيتم مراجعته من قبل الإدارة قريباً.');
    }
    
    /**
     * Get verified students linked to a parent.
     *
     * @param  int  $parentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getVerifiedStudents($parentId)
    {
        $relations = ParentStudentRelation::where('parent_id', $parentId)
            ->where('verification_status', 'approved')
            ->get();
            
        $studentIds = $relations->pluck('student_id')->toArray();
        
        // Join with user_roles to filter by role instead of using the non-existent 'role' column
        return User::join('user_roles', function($join) {
                $join->on('users.user_id', '=', 'user_roles.user_id')
                    ->where('user_roles.role', '=', 'student');
            })
            ->whereIn('users.user_id', $studentIds)
            ->select('users.*')
            ->get();
    }
    
    /**
     * Get statistics for a student.
     *
     * @param  int  $studentId
     * @return array
     */
    private function getStudentStatistics($studentId)
    {
        // Courses count
        $coursesCount = DB::table('enrollments')
            ->where('student_id', $studentId)
            ->count();
            
        // Exams count
        $examsCount = ExamAttempt::where('student_id', $studentId)
            ->whereNotNull('completed_at')
            ->count();
            
        // Average progress across all courses
        $avgProgress = DB::table('enrollments')
            ->where('student_id', $studentId)
            ->avg('progress');
        $avgProgress = round($avgProgress ?? 0);
        
        // Recent courses
        $recentCourses = [];
        $courses = Course::join('enrollments', 'courses.course_id', '=', 'enrollments.course_id')
            ->where('enrollments.student_id', $studentId)
            ->orderBy('enrollments.updated_at', 'desc')
            ->select('courses.course_id', 'courses.title', 'enrollments.progress', 'enrollments.updated_at as last_activity_at')
            ->limit(3)
            ->get();
            
        foreach ($courses as $course) {
            $recentCourses[] = [
                'id' => $course->course_id,
                'title' => $course->title,
                'progress' => $course->progress,
                'last_activity' => $course->last_activity_at ? Carbon::parse($course->last_activity_at)->diffForHumans() : 'لا يوجد نشاط'
            ];
        }
        
        return [
            'courses_count' => $coursesCount,
            'exams_count' => $examsCount,
            'avg_progress' => $avgProgress,
            'recent_courses' => $recentCourses
        ];
    }
    
    /**
     * Generate alerts for a student.
     *
     * @param  \App\Models\User  $student
     * @return array
     */
    private function generateStudentAlerts($student)
    {
        $alerts = [];
        
        // Check for inactive students (no activity in the last 7 days)
        $lastActivity = DB::table('student_activities')
            ->where('student_id', $student->user_id)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$lastActivity || Carbon::parse($lastActivity->created_at)->diffInDays(now()) > 7) {
            $alerts[] = [
                'student_id' => $student->user_id,
                'student_name' => $student->name,
                'title' => 'عدم نشاط الطالب',
                'message' => 'لم يقم الطالب بأي نشاط على المنصة منذ أكثر من 7 أيام.',
                'time' => now()->format('Y-m-d H:i:s'),
                'priority' => 'medium',
                'type' => 'warning'
            ];
        }
        
        // Check for failed exams (score less than 60%)
        $failedExams = ExamAttempt::where('student_id', $student->user_id)
            ->whereNotNull('completed_at')
            ->where('score', '<', 60)
            ->where('completed_at', '>=', now()->subDays(14))
            ->get();
            
        foreach ($failedExams as $exam) {
            $examInfo = DB::table('exams')->where('exam_id', $exam->exam_id)->first();
            $examTitle = $examInfo ? $examInfo->title : 'اختبار';
            
            $alerts[] = [
                'student_id' => $student->user_id,
                'student_name' => $student->name,
                'title' => 'رسوب في اختبار',
                'message' => "حصل الطالب على درجة منخفضة ({$exam->score}%) في {$examTitle}.",
                'time' => Carbon::parse($exam->completed_at)->format('Y-m-d H:i:s'),
                'priority' => 'high',
                'type' => 'alert'
            ];
        }
        
        // Check for low progress in courses (enrolled for more than 14 days with less than 30% progress)
        $lowProgressCourses = DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
            ->where('enrollments.student_id', $student->user_id)
            ->whereRaw('DATEDIFF(NOW(), enrollments.enrolled_at) > 14')
            ->where('enrollments.progress', '<', 30)
            ->select('courses.title', 'enrollments.progress', 'enrollments.enrolled_at')
            ->get();
            
        foreach ($lowProgressCourses as $course) {
            $alerts[] = [
                'student_id' => $student->user_id,
                'student_name' => $student->name,
                'title' => 'تقدم بطيء في دورة',
                'message' => "تقدم الطالب بطيء ({$course->progress}%) في دورة {$course->title} رغم التسجيل منذ " . Carbon::parse($course->enrolled_at)->diffForHumans() . ".",
                'time' => now()->format('Y-m-d H:i:s'),
                'priority' => 'medium',
                'type' => 'warning'
            ];
        }
        
        // Check for upcoming exam deadlines
        $upcomingExams = DB::table('exams')
            ->join('courses', 'exams.course_id', '=', 'courses.course_id')
            ->join('enrollments', function($join) use ($student) {
                $join->on('courses.course_id', '=', 'enrollments.course_id')
                    ->where('enrollments.student_id', '=', $student->user_id);
            })
            ->whereRaw('exams.available_to IS NOT NULL')
            ->whereRaw('exams.available_to > NOW()')
            ->whereRaw('DATEDIFF(exams.available_to, NOW()) <= 3')
            ->whereNotExists(function($query) use ($student) {
                $query->select(DB::raw(1))
                    ->from('exam_attempts')
                    ->whereRaw('exam_attempts.exam_id = exams.exam_id')
                    ->where('exam_attempts.student_id', $student->user_id)
                    ->whereNotNull('exam_attempts.completed_at');
            })
            ->select('exams.exam_id', 'exams.title', 'exams.available_to as deadline')
            ->get();
            
        foreach ($upcomingExams as $exam) {
            $alerts[] = [
                'student_id' => $student->user_id,
                'student_name' => $student->name,
                'title' => 'موعد اختبار قريب',
                'message' => "اختبار {$exam->title} ينتهي في " . Carbon::parse($exam->deadline)->diffForHumans() . " والطالب لم يقم به بعد.",
                'time' => now()->format('Y-m-d H:i:s'),
                'priority' => 'high',
                'type' => 'alert'
            ];
        }
        
        // Check for newly achieved high scores
        $highScores = ExamAttempt::where('student_id', $student->user_id)
            ->whereNotNull('completed_at')
            ->where('score', '>=', 90)
            ->where('completed_at', '>=', now()->subDays(7))
            ->get();
            
        foreach ($highScores as $exam) {
            $examInfo = DB::table('exams')->where('exam_id', $exam->exam_id)->first();
            $examTitle = $examInfo ? $examInfo->title : 'اختبار';
            
            $alerts[] = [
                'student_id' => $student->user_id,
                'student_name' => $student->name,
                'title' => 'تميز في اختبار',
                'message' => "حصل الطالب على درجة متميزة ({$exam->score}%) في {$examTitle}.",
                'time' => Carbon::parse($exam->completed_at)->format('Y-m-d H:i:s'),
                'priority' => 'low',
                'type' => 'info'
            ];
        }
        
        return $alerts;
    }
    
    /**
     * Get recent activities for a student.
     *
     * @param  \App\Models\User  $student
     * @param  int  $limit
     * @return array
     */
    private function getStudentRecentActivities($student, $limit = 5)
    {
        $activities = DB::table('student_activities')
            ->where('student_id', $student->user_id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
            
        $result = [];
        foreach ($activities as $activity) {
            $result[] = [
                'student_id' => $student->user_id,
                'student_name' => $student->name,
                'type' => $activity->activity_type,
                'description' => $activity->description,
                'time' => Carbon::parse($activity->created_at)->diffForHumans(),
                'timestamp' => $activity->created_at
            ];
        }
        
        return $result;
    }
    
    /**
     * Get detailed activities for a student.
     *
     * @param  int  $studentId
     * @param  int  $limit
     * @return array
     */
    private function getStudentDetailedActivities($studentId, $limit = 10)
    {
        $activities = DB::table('student_activities')
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
            
        $result = [];
        foreach ($activities as $activity) {
            $result[] = [
                'id' => $activity->id,
                'title' => $activity->title,
                'description' => $activity->description,
                'time' => Carbon::parse($activity->created_at)->diffForHumans(),
                'timestamp' => $activity->created_at
            ];
        }
        
        return $result;
    }
    
    /**
     * Get course contents and progress for a student.
     *
     * @param  int  $studentId
     * @param  int  $courseId
     * @return array
     */
    private function getCourseContents($studentId, $courseId)
    {
        // This would need to be adjusted based on your actual content structure
        $contents = DB::table('course_contents')
            ->where('course_id', $courseId)
            ->orderBy('order_index')
            ->get();
            
        $result = [];
        foreach ($contents as $content) {
            // Get progress for this content
            $progress = StudentProgress::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->where('content_id', $content->content_id)
                ->first();
                
            $progressValue = $progress ? $progress->progress_percentage : 0;
            
            // Determine status and color
            $status = 'لم يبدأ';
            $statusColor = 'secondary';
            
            if ($progressValue > 0 && $progressValue < 100) {
                $status = 'قيد التقدم';
                $statusColor = 'warning';
            } elseif ($progressValue >= 100) {
                $status = 'مكتمل';
                $statusColor = 'success';
            }
            
            $result[] = [
                'id' => $content->content_id,
                'title' => $content->title,
                'type' => $this->getContentTypeName($content->content_type),
                'duration' => $this->formatDuration($content->duration),
                'progress' => $progressValue,
                'status' => $status,
                'status_color' => $statusColor,
                'last_activity' => $progress && $progress->updated_at ? Carbon::parse($progress->updated_at)->diffForHumans() : null
            ];
        }
        
        return $result;
    }
    
    /**
     * Get formatted exam attempts for a student.
     *
     * @param  int  $studentId
     * @param  int  $limit
     * @return array
     */
    private function getExamAttempts($studentId, $limit = 5)
    {
        $attempts = ExamAttempt::where('student_id', $studentId)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->limit($limit)
            ->get();
            
        $result = [];
        foreach ($attempts as $attempt) {
            $exam = DB::table('exams')->where('exam_id', $attempt->exam_id)->first();
            
            $result[] = [
                'id' => $attempt->attempt_id,
                'title' => $exam ? $exam->title : 'اختبار',
                'score' => round($attempt->score),
                'date' => Carbon::parse($attempt->completed_at)->format('d M, Y'),
                'passed' => $attempt->passed
            ];
        }
        
        return $result;
    }
    
    /**
     * Generate badges and achievements for a student.
     *
     * @param  int  $studentId
     * @return array
     */
    private function generateStudentBadges($studentId)
    {
        $badges = [];
        
        // Course completion badge
        $completedCourses = DB::table('enrollments')
            ->where('student_id', $studentId)
            ->where('progress', 100)
            ->count();
            
        if ($completedCourses >= 5) {
            $badges[] = [
                'title' => 'متعلم متميز',
                'description' => 'أكمل 5 دورات بنجاح',
                'icon' => 'fa-award',
                'status' => 'completed'
            ];
        } elseif ($completedCourses > 0) {
            $badges[] = [
                'title' => 'متعلم متميز',
                'description' => "أكمل {$completedCourses} من 5 دورات",
                'icon' => 'fa-award',
                'status' => 'progress'
            ];
        } else {
            $badges[] = [
                'title' => 'متعلم متميز',
                'description' => 'أكمل 5 دورات بنجاح',
                'icon' => 'fa-award',
                'status' => 'locked'
            ];
        }
        
        // Perfect score badge
        $perfectScores = ExamAttempt::where('student_id', $studentId)
            ->where('score', 100)
            ->whereNotNull('completed_at')
            ->count();
            
        if ($perfectScores >= 3) {
            $badges[] = [
                'title' => 'العلامة الكاملة',
                'description' => 'حصل على العلامة الكاملة في 3 اختبارات',
                'icon' => 'fa-star',
                'status' => 'completed'
            ];
        } elseif ($perfectScores > 0) {
            $badges[] = [
                'title' => 'العلامة الكاملة',
                'description' => "حصل على العلامة الكاملة في {$perfectScores} من 3 اختبارات",
                'icon' => 'fa-star',
                'status' => 'progress'
            ];
        } else {
            $badges[] = [
                'title' => 'العلامة الكاملة',
                'description' => 'حصل على العلامة الكاملة في 3 اختبارات',
                'icon' => 'fa-star',
                'status' => 'locked'
            ];
        }
        
        // Regular learner badge
        $consecutiveDays = $this->getConsecutiveDaysActive($studentId);
        
        if ($consecutiveDays >= 7) {
            $badges[] = [
                'title' => 'متعلم منتظم',
                'description' => 'نشط لمدة 7 أيام متتالية',
                'icon' => 'fa-calendar-check',
                'status' => 'completed'
            ];
        } elseif ($consecutiveDays > 0) {
            $badges[] = [
                'title' => 'متعلم منتظم',
                'description' => "نشط لمدة {$consecutiveDays} من 7 أيام متتالية",
                'icon' => 'fa-calendar-check',
                'status' => 'progress'
            ];
        } else {
            $badges[] = [
                'title' => 'متعلم منتظم',
                'description' => 'نشط لمدة 7 أيام متتالية',
                'icon' => 'fa-calendar-check',
                'status' => 'locked'
            ];
        }
        
        return $badges;
    }
    
    /**
     * Get the number of consecutive days a student has been active.
     *
     * @param  int  $studentId
     * @return int
     */
    private function getConsecutiveDaysActive($studentId)
    {
        $activityDates = DB::table('student_activities')
            ->where('student_id', $studentId)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as activity_date')
            ->distinct()
            ->orderBy('activity_date', 'desc')
            ->pluck('activity_date')
            ->toArray();
            
        if (empty($activityDates)) {
            return 0;
        }
        
        $consecutiveDays = 1;
        for ($i = 0; $i < count($activityDates) - 1; $i++) {
            $currentDate = Carbon::parse($activityDates[$i]);
            $nextDate = Carbon::parse($activityDates[$i + 1]);
            
            if ($currentDate->diffInDays($nextDate) == 1) {
                $consecutiveDays++;
            } else {
                break;
            }
        }
        
        return $consecutiveDays;
    }
    
    /**
     * Format content duration.
     *
     * @param  int  $seconds
     * @return string
     */
    private function formatDuration($seconds)
    {
        if (!$seconds) {
            return '0 دقيقة';
        }
        
        $minutes = floor($seconds / 60);
        
        if ($minutes < 60) {
            return $minutes . ' دقيقة';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes == 0) {
            return $hours . ' ساعة';
        }
        
        return $hours . ' ساعة و ' . $remainingMinutes . ' دقيقة';
    }
    
    /**
     * Get content type name.
     *
     * @param  string  $type
     * @return string
     */
    private function getContentTypeName($type)
    {
        $types = [
            'video' => 'فيديو',
            'document' => 'مستند',
            'quiz' => 'اختبار قصير',
            'assignment' => 'واجب',
            'presentation' => 'عرض تقديمي',
            'audio' => 'ملف صوتي'
        ];
        
        return $types[$type] ?? $type;
    }
    
    /**
     * Display and manage parent profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile(Request $request)
    {
        $parent = Auth::user();
        
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $parent->user_id . ',user_id',
                'phone' => 'nullable|string|max:20',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($parent->profile_image) {
                    Storage::delete('public/profiles/' . $parent->profile_image);
                }
                
                // Upload new image
                $imageName = time() . '.' . $request->profile_image->extension();
                $request->profile_image->storeAs('public/profiles', $imageName);
                
                // Update the user record in the database
                User::where('user_id', $parent->user_id)->update([
                    'profile_image' => $imageName
                ]);
            }
            
            // Update other fields
            User::where('user_id', $parent->user_id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'bio' => $request->bio
            ]);
            
            return redirect()->route('parent.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح');
        }
        
        // Get linked students count for display
        $studentsCount = ParentStudentRelation::where('parent_id', $parent->user_id)
            ->where('verification_status', 'approved')
            ->count();
            
        return view('parent.profile', compact('parent', 'studentsCount'));
    }
    
    /**
     * Update parent's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        
        $parent = Auth::user();
        
        // Check if current password is correct
        if (!Hash::check($request->current_password, $parent->password_hash)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }
        
        // Update password
        User::where('user_id', $parent->user_id)->update([
            'password_hash' => Hash::make($request->new_password)
        ]);
        
        return redirect()->route('parent.profile')->with('success', 'تم تحديث كلمة المرور بنجاح');
    }
} 