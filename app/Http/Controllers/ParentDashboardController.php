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

        // Apply verified parent middleware only to specific methods that require verification
        $this->middleware('verified.parent')->only(['index', 'studentActivity', 'activities', 'reports', 'profile', 'updatePassword']);
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
        $overallStats = [
            'total_students' => count($students),
            'total_courses' => 0,
            'total_exams' => 0,
            'total_certificates' => 0,
            'avg_progress' => 0
        ];

        if (count($students) > 0) {
            $totalProgress = 0;
            foreach ($students as $student) {
                $stats = $this->getStudentStatistics($student->user_id);
                $studentStats[$student->user_id] = $stats;

                // Add to overall stats
                $overallStats['total_courses'] += $stats['courses_count'];
                $overallStats['total_exams'] += $stats['exams_count'];
                $overallStats['total_certificates'] += $stats['certificates_earned'] ?? 0;
                $totalProgress += $stats['avg_progress'];

                // Generate alerts for this student
                $studentAlerts = $this->generateStudentAlerts($student);
                $alerts = array_merge($alerts, $studentAlerts);

                // Get recent activities
                $studentActivities = $this->getStudentRecentActivities($student, 3);
                $recentActivities = array_merge($recentActivities, $studentActivities);
            }

            // Calculate average progress
            $overallStats['avg_progress'] = count($students) > 0 ? round($totalProgress / count($students), 1) : 0;

            // Sort activities by time
            usort($recentActivities, function($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });

            // Limit to 10 most recent activities
            $recentActivities = array_slice($recentActivities, 0, 10);
        }

        // Get recent admin messages for parent
        $parent = Auth::user();
        $adminMessages = \App\Models\DirectMessage::where('receiver_id', $parent->user_id)
            ->whereHas('sender.roles', function($query) {
                $query->where('role', 'admin');
            })
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Count unread admin messages
        $unreadAdminMessages = \App\Models\DirectMessage::where('receiver_id', $parent->user_id)
            ->whereHas('sender.roles', function($query) {
                $query->where('role', 'admin');
            })
            ->where('is_read', false)
            ->count();

        return view('parent.dashboard', compact('students', 'pendingRelations', 'rejectedRelations', 'studentStats', 'alerts', 'recentActivities', 'adminMessages', 'unreadAdminMessages', 'overallStats'));
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

        // Get detailed statistics
        $detailedStats = $this->getStudentStatistics($studentId);

        // Get learning analytics
        $learningAnalytics = $this->getLearningAnalytics($studentId);

        // Get performance trends
        $performanceTrends = $this->getPerformanceTrends($studentId);

        // Get certificates
        $certificates = $this->getStudentCertificates($studentId);

        // Get weekly and monthly reports
        $weeklyReport = $this->getWeeklyReport($studentId);
        $monthlyReport = $this->getMonthlyReport($studentId);

        // Get study goals and progress
        $studyGoals = $this->getStudyGoals($studentId);

        return view('parent.student_activity', compact(
            'student',
            'enrolledCourses',
            'courseContents',
            'completedExams',
            'avgScore',
            'examAttempts',
            'activities',
            'badges',
            'alerts',
            'detailedStats',
            'learningAnalytics',
            'performanceTrends',
            'certificates',
            'weeklyReport',
            'monthlyReport',
            'studyGoals'
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
     * Show detailed reports.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function reports()
    {
        $parentId = Auth::id();

        // Get all verified students linked to this parent
        $students = $this->getVerifiedStudents($parentId);

        // Initialize statistics array for each student
        $studentStats = [];
        $overallStats = [
            'total_students' => count($students),
            'total_courses' => 0,
            'total_exams' => 0,
            'total_certificates' => 0,
            'avg_progress' => 0
        ];

        if (count($students) > 0) {
            $totalProgress = 0;
            foreach ($students as $student) {
                $stats = $this->getStudentStatistics($student->user_id);
                $studentStats[$student->user_id] = $stats;

                // Add to overall stats
                $overallStats['total_courses'] += $stats['courses_count'];
                $overallStats['total_exams'] += $stats['exams_count'];
                $overallStats['total_certificates'] += $stats['certificates_earned'] ?? 0;
                $totalProgress += $stats['avg_progress'];
            }

            // Calculate average progress
            $overallStats['avg_progress'] = count($students) > 0 ? round($totalProgress / count($students), 1) : 0;
        }

        // Get monthly analytics for all students
        $monthlyAnalytics = $this->getMonthlyAnalytics($students);

        // Get comparative data
        $comparativeData = $this->getComparativeData($students, $studentStats);

        return view('parent.reports', compact(
            'students',
            'studentStats',
            'overallStats',
            'monthlyAnalytics',
            'comparativeData'
        ));
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

        // Completed courses count
        $completedCoursesCount = DB::table('enrollments')
            ->where('student_id', $studentId)
            ->where('progress', '>=', 100)
            ->count();

        // Exams count
        $examsCount = ExamAttempt::where('student_id', $studentId)
            ->whereNotNull('completed_at')
            ->count();

        // Passed exams count (score >= 60%)
        $passedExamsCount = ExamAttempt::where('student_id', $studentId)
            ->whereNotNull('completed_at')
            ->where('score', '>=', 60)
            ->count();

        // Average exam score
        $avgExamScore = ExamAttempt::where('student_id', $studentId)
            ->whereNotNull('completed_at')
            ->avg('score');
        $avgExamScore = round($avgExamScore ?? 0, 1);

        // Average progress across all courses
        $avgProgress = DB::table('enrollments')
            ->where('student_id', $studentId)
            ->avg('progress');
        $avgProgress = round($avgProgress ?? 0);

        // Certificates earned
        $certificatesEarned = DB::table('certificates')
            ->where('student_id', $studentId)
            ->where('status', 'issued')
            ->count();

        // Study time this week
        $weeklyStudyTime = $this->getWeeklyStudyTime($studentId);

        // Learning streak (consecutive days)
        $learningStreak = $this->getLearningStreak($studentId);

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
            'completed_courses_count' => $completedCoursesCount,
            'exams_count' => $examsCount,
            'passed_exams_count' => $passedExamsCount,
            'avg_exam_score' => $avgExamScore,
            'avg_progress' => $avgProgress,
            'certificates_earned' => $certificatesEarned,
            'weekly_study_time' => $weeklyStudyTime,
            'learning_streak' => $learningStreak,
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
     * Get consecutive days active for a student.
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
            'assignment' => 'مهمة',
            'audio' => 'ملف صوتي'
        ];

        return $types[$type] ?? $type;
    }






    /**
     * Get weekly study time for a student.
     *
     * @param  int  $studentId
     * @return string
     */
    private function getWeeklyStudyTime($studentId)
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $totalSeconds = DB::table('video_views')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->sum('view_duration');

        return $this->formatDuration($totalSeconds);
    }

    /**
     * Get learning streak for a student.
     *
     * @param  int  $studentId
     * @return int
     */
    private function getLearningStreak($studentId)
    {
        $activities = DB::table('student_activities')
            ->where('student_id', $studentId)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->unique()
            ->values()
            ->toArray();

        if (empty($activities)) {
            return 0;
        }

        $consecutiveDays = 1;
        for ($i = 0; $i < count($activities) - 1; $i++) {
            $currentDate = Carbon::parse($activities[$i]);
            $nextDate = Carbon::parse($activities[$i + 1]);

            if ($currentDate->diffInDays($nextDate) == 1) {
                $consecutiveDays++;
            } else {
                break;
            }
        }

        return $consecutiveDays;
    }

    /**
     * Get learning analytics for a student.
     *
     * @param  int  $studentId
     * @return array
     */
    private function getLearningAnalytics($studentId)
    {
        // Daily activity for the last 30 days
        $dailyActivity = DB::table('student_activities')
            ->where('student_id', $studentId)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as activities')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill missing dates with 0 activities
        $analytics = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $analytics[] = [
                'date' => $date,
                'activities' => $dailyActivity->get($date)->activities ?? 0
            ];
        }

        return $analytics;
    }

    /**
     * Get performance trends for a student.
     *
     * @param  int  $studentId
     * @return array
     */
    private function getPerformanceTrends($studentId)
    {
        // Exam scores over time
        $examScores = ExamAttempt::where('student_id', $studentId)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at')
            ->select('score', 'completed_at')
            ->get()
            ->map(function($attempt) {
                return [
                    'date' => Carbon::parse($attempt->completed_at)->format('Y-m-d'),
                    'score' => $attempt->score
                ];
            });

        // Course progress over time
        $courseProgress = DB::table('enrollments')
            ->where('student_id', $studentId)
            ->join('courses', 'enrollments.course_id', '=', 'courses.course_id')
            ->select('courses.title', 'enrollments.progress', 'enrollments.updated_at')
            ->orderBy('enrollments.updated_at')
            ->get()
            ->map(function($enrollment) {
                return [
                    'course' => $enrollment->title,
                    'progress' => $enrollment->progress,
                    'date' => Carbon::parse($enrollment->updated_at)->format('Y-m-d')
                ];
            });

        return [
            'exam_scores' => $examScores,
            'course_progress' => $courseProgress
        ];
    }

    /**
     * Get student certificates.
     *
     * @param  int  $studentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getStudentCertificates($studentId)
    {
        return DB::table('certificates')
            ->join('courses', 'certificates.course_id', '=', 'courses.course_id')
            ->where('certificates.user_id', $studentId)
            ->where('certificates.is_valid', true)
            ->select('certificates.*', 'courses.title as course_title')
            ->orderBy('certificates.created_at', 'desc')
            ->get();
    }

    /**
     * Format duration from seconds to human readable format.
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
     * Get weekly report for a student.
     *
     * @param  int  $studentId
     * @return array
     */
    private function getWeeklyReport($studentId)
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        // Videos watched this week
        $videosWatched = DB::table('video_views')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        // Study time this week
        $studyTime = DB::table('video_views')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->sum('view_duration');

        // Exams taken this week
        $examsTaken = ExamAttempt::where('student_id', $studentId)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        // Average score this week
        $avgScore = ExamAttempt::where('student_id', $studentId)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->avg('score');

        // Course progress this week
        $courseProgress = DB::table('enrollments')
            ->where('student_id', $studentId)
            ->whereBetween('updated_at', [$weekStart, $weekEnd])
            ->avg('progress');

        return [
            'videos_watched' => $videosWatched,
            'study_time' => $this->formatDuration($studyTime),
            'exams_taken' => $examsTaken,
            'avg_score' => round($avgScore ?? 0, 1),
            'course_progress' => round($courseProgress ?? 0, 1),
            'period' => 'هذا الأسبوع'
        ];
    }

    /**
     * Get monthly report for a student.
     *
     * @param  int  $studentId
     * @return array
     */
    private function getMonthlyReport($studentId)
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        // Videos watched this month
        $videosWatched = DB::table('video_views')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        // Study time this month
        $studyTime = DB::table('video_views')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('view_duration');

        // Exams taken this month
        $examsTaken = ExamAttempt::where('student_id', $studentId)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        // Average score this month
        $avgScore = ExamAttempt::where('student_id', $studentId)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->avg('score');

        // Courses completed this month
        $coursesCompleted = DB::table('enrollments')
            ->where('student_id', $studentId)
            ->where('progress', '>=', 100)
            ->whereBetween('updated_at', [$monthStart, $monthEnd])
            ->count();

        return [
            'videos_watched' => $videosWatched,
            'study_time' => $this->formatDuration($studyTime),
            'exams_taken' => $examsTaken,
            'avg_score' => round($avgScore ?? 0, 1),
            'courses_completed' => $coursesCompleted,
            'period' => 'هذا الشهر'
        ];
    }

    /**
     * Get study goals for a student.
     *
     * @param  int  $studentId
     * @return array
     */
    private function getStudyGoals($studentId)
    {
        // This could be expanded to include actual goal setting functionality
        // For now, we'll return some basic goals based on current performance

        $currentStats = $this->getStudentStatistics($studentId);

        // Weekly goals
        $weeklyGoals = [
            'study_hours' => [
                'target' => 10, // 10 hours per week
                'current' => $this->getWeeklyStudyHours($studentId),
                'unit' => 'ساعة'
            ],
            'videos' => [
                'target' => 15, // 15 videos per week
                'current' => $this->getWeeklyVideosWatched($studentId),
                'unit' => 'فيديو'
            ],
            'exams' => [
                'target' => 3, // 3 exams per week
                'current' => $this->getWeeklyExamsTaken($studentId),
                'unit' => 'اختبار'
            ]
        ];

        // Calculate progress percentages
        foreach ($weeklyGoals as &$goal) {
            $goal['progress'] = $goal['target'] > 0 ? min(100, round(($goal['current'] / $goal['target']) * 100)) : 0;
        }

        return [
            'weekly' => $weeklyGoals,
            'streak_goal' => [
                'target' => 7, // 7 consecutive days
                'current' => $currentStats['learning_streak'],
                'progress' => min(100, round(($currentStats['learning_streak'] / 7) * 100))
            ]
        ];
    }

    /**
     * Get weekly study hours for a student.
     *
     * @param  int  $studentId
     * @return float
     */
    private function getWeeklyStudyHours($studentId)
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $totalSeconds = DB::table('video_views')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->sum('view_duration');

        return round($totalSeconds / 3600, 1); // Convert to hours
    }

    /**
     * Get weekly videos watched for a student.
     *
     * @param  int  $studentId
     * @return int
     */
    private function getWeeklyVideosWatched($studentId)
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        return DB::table('video_views')
            ->where('student_id', $studentId)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
    }

    /**
     * Get weekly exams taken for a student.
     *
     * @param  int  $studentId
     * @return int
     */
    private function getWeeklyExamsTaken($studentId)
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        return ExamAttempt::where('student_id', $studentId)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
    }

    /**
     * Get monthly analytics for all students.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $students
     * @return array
     */
    private function getMonthlyAnalytics($students)
    {
        if ($students->isEmpty()) {
            return [];
        }

        $studentIds = $students->pluck('user_id')->toArray();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        // Get daily activity for the current month
        $dailyActivity = DB::table('student_activities')
            ->whereIn('student_id', $studentIds)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as activities')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill missing dates with 0 activities
        $analytics = [];
        $currentDate = $monthStart->copy();
        while ($currentDate <= $monthEnd) {
            $dateStr = $currentDate->format('Y-m-d');
            $analytics[] = [
                'date' => $dateStr,
                'activities' => $dailyActivity->get($dateStr)->activities ?? 0,
                'formatted_date' => $currentDate->format('d/m')
            ];
            $currentDate->addDay();
        }

        return $analytics;
    }

    /**
     * Get comparative data for students.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $students
     * @param  array  $studentStats
     * @return array
     */
    private function getComparativeData($students, $studentStats)
    {
        if ($students->isEmpty()) {
            return [];
        }

        $comparativeData = [];

        foreach ($students as $student) {
            $stats = $studentStats[$student->user_id] ?? [];

            // Calculate trend (simplified - could be enhanced with historical data)
            $trend = 'neutral';
            if (($stats['avg_progress'] ?? 0) > 75) {
                $trend = 'up';
            } elseif (($stats['avg_progress'] ?? 0) < 50) {
                $trend = 'down';
            }

            $comparativeData[] = [
                'student_id' => $student->user_id,
                'student_name' => $student->name,
                'courses_count' => $stats['courses_count'] ?? 0,
                'avg_progress' => $stats['avg_progress'] ?? 0,
                'exams_count' => $stats['exams_count'] ?? 0,
                'avg_exam_score' => $stats['avg_exam_score'] ?? 0,
                'certificates_earned' => $stats['certificates_earned'] ?? 0,
                'learning_streak' => $stats['learning_streak'] ?? 0,
                'trend' => $trend
            ];
        }

        // Sort by average progress (descending)
        usort($comparativeData, function($a, $b) {
            return $b['avg_progress'] - $a['avg_progress'];
        });

        return $comparativeData;
    }

    /**
     * Show waiting approval page for parents without verified relations.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function waitingApproval()
    {
        $parentId = Auth::id();

        // Get all relation requests for this parent
        $relationRequests = ParentStudentRelation::where('parent_id', $parentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if parent has any approved relations
        $hasApprovedRelation = ParentStudentRelation::where('parent_id', $parentId)
            ->where('verification_status', 'approved')
            ->exists();

        // If parent has approved relations, redirect to dashboard
        if ($hasApprovedRelation) {
            return redirect()->route('parent.dashboard');
        }

        return view('parent.waiting_approval', compact('relationRequests'));
    }

    /**
     * Show and handle parent profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse
     */
    public function profile(Request $request)
    {
        $parent = Auth::user();

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $parent->user_id . ',user_id',
                'phone' => 'nullable|string|max:20',
            ]);

            $parent->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            return redirect()->route('parent.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح.');
        }

        return view('parent.profile', compact('parent'));
    }

    /**
     * Update parent password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $parent = Auth::user();

        if (!Hash::check($request->current_password, $parent->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        $parent->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('parent.profile')->with('success', 'تم تحديث كلمة المرور بنجاح.');
    }
}