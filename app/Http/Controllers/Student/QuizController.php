<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QuizController extends Controller
{
    /**
     * Display a listing of available quizzes for the student.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $student = Auth::user();

        // Get all courses the student is enrolled in
        $enrolledCourseIds = Enrollment::where('student_id', $student->user_id)
            ->pluck('course_id');
            
        // Debug: Show enrolled course IDs
        $debug = [
            'student_id' => $student->user_id,
            'enrolled_courses' => $enrolledCourseIds->toArray(),
        ];

        // Get all quizzes for these courses - عرض جميع الامتحانات حتى غير المنشورة
        $quizzes = Quiz::whereIn('course_id', $enrolledCourseIds)
            ->with('course')
            ->get();
            
        // Debug: Add quiz info
        $debug['quiz_count'] = $quizzes->count();
        $debug['quizzes'] = $quizzes->map(function($quiz) {
            return [
                'id' => $quiz->quiz_id,
                'title' => $quiz->title,
                'course_id' => $quiz->course_id,
                'is_published' => $quiz->is_published,
                'is_active' => $quiz->is_active,
                'start_date' => $quiz->start_date,
                'end_date' => $quiz->end_date,
            ];
        })->toArray();

        // Get the student's attempts for these quizzes
        $attempts = QuizAttempt::where('user_id', $student->user_id)
            ->whereIn('quiz_id', $quizzes->pluck('quiz_id')->toArray())
            ->get()
            ->groupBy('quiz_id');
            
        // Debug: Add attempts info
        $debug['attempts_count'] = $attempts->count();

        return view('student.quizzes.index', compact('quizzes', 'attempts', 'debug'));
    }

    /**
     * Display the quiz details and instructions.
     *
     * @param  int  $quizId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($quizId)
    {
        $student = Auth::user();

        // Find the quiz
        $quiz = Quiz::with('course')
            ->findOrFail($quizId);

        // Check if the student is enrolled in the course
        $isEnrolled = Enrollment::where('student_id', $student->user_id)
            ->where('course_id', $quiz->course_id)
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'أنت غير مسجل في الدورة التي تحتوي على هذا الاختبار.');
        }

        // Check if the quiz is available
        $isAvailable = $quiz->isAvailable();
        $availabilityMessage = null;

        if (!$isAvailable) {
            if ($quiz->start_date && Carbon::now()->lt($quiz->start_date)) {
                $availabilityMessage = 'هذا الاختبار سيكون متاحًا اعتبارًا من ' . $quiz->start_date->format('Y-m-d H:i');
            } elseif ($quiz->end_date && Carbon::now()->gt($quiz->end_date)) {
                $availabilityMessage = 'انتهت مدة الاختبار في ' . $quiz->end_date->format('Y-m-d H:i');
            } else {
                $availabilityMessage = 'هذا الاختبار غير متاح حاليًا.';
            }
        }

        // Get the student's attempts for this quiz
        $attempts = QuizAttempt::where('user_id', $student->user_id)
            ->where('quiz_id', $quizId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if student can take the quiz
        $canTakeQuiz = $quiz->canBeAttemptedBy($student->user_id);

        // Calculate attempts left
        $attemptsLeft = $quiz->max_attempts === null ? null : max(0, $quiz->max_attempts - $attempts->count());
        
        // Check if the student has an in-progress attempt
        $hasInProgressAttempt = $attempts->where('status', 'in_progress')->count() > 0;

        return view('student.quizzes.show', compact(
            'quiz', 
            'isAvailable', 
            'availabilityMessage', 
            'attempts', 
            'canTakeQuiz',
            'attemptsLeft',
            'hasInProgressAttempt'
        ));
    }

    /**
     * Start a new quiz attempt.
     *
     * @param  int  $quizId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startAttempt($quizId)
    {
        $student = Auth::user();

        // Find the quiz
        $quiz = Quiz::findOrFail($quizId);

        // Check if the student is enrolled in the course
        $isEnrolled = Enrollment::where('student_id', $student->user_id)
            ->where('course_id', $quiz->course_id)
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'أنت غير مسجل في الدورة التي تحتوي على هذا الاختبار.');
        }

        // Check if the quiz is available
        if (!$quiz->isAvailable()) {
            return redirect()->route('student.quizzes.show', $quizId)
                ->with('error', 'هذا الاختبار غير متاح حاليًا.');
        }

        // Check if the student can attempt the quiz
        if (!$quiz->canBeAttemptedBy($student->user_id)) {
            return redirect()->route('student.quizzes.show', $quizId)
                ->with('error', 'لقد وصلت إلى الحد الأقصى من المحاولات لهذا الاختبار.');
        }

        // Check if there's an ongoing attempt
        $ongoingAttempt = QuizAttempt::where('user_id', $student->user_id)
            ->where('quiz_id', $quizId)
            ->where('status', 'in_progress')
            ->first();

        if ($ongoingAttempt) {
            return redirect()->route('student.quizzes.take', $ongoingAttempt->attempt_id);
        }

        // Create a new attempt
        $attempt = new QuizAttempt();
        $attempt->quiz_id = $quizId;
        $attempt->user_id = $student->user_id;
        $attempt->start_time = Carbon::now();
        $attempt->status = 'in_progress';
        $attempt->answers_json = [];
        $attempt->save();

        return redirect()->route('student.quizzes.take', $attempt->attempt_id);
    }

    /**
     * Take the quiz.
     *
     * @param  int  $attemptId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function takeQuiz($attemptId)
    {
        $student = Auth::user();

        // Find the attempt
        $attempt = QuizAttempt::with('quiz')
            ->where('user_id', $student->user_id)
            ->findOrFail($attemptId);

        // Check if the attempt is already completed
        if ($attempt->isCompleted()) {
            return redirect()->route('student.quizzes.result', $attemptId)
                ->with('info', 'لقد أكملت هذا الاختبار بالفعل.');
        }

        // Check if the quiz time has expired
        if ($attempt->isTimedOut()) {
            // Auto-submit the quiz
            $attempt->complete($attempt->answers_json);
            return redirect()->route('student.quizzes.result', $attemptId)
                ->with('info', 'انتهى وقت الاختبار وتم تقديمه تلقائيًا.');
        }

        // Calculate remaining time in seconds
        $remainingTime = $attempt->remaining_time;

        return view('student.quizzes.take', compact('attempt', 'remainingTime'));
    }

    /**
     * Save answers during the quiz.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $attemptId
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveAnswers(Request $request, $attemptId)
    {
        $student = Auth::user();

        // Find the attempt
        $attempt = QuizAttempt::where('user_id', $student->user_id)
            ->where('status', 'in_progress')
            ->findOrFail($attemptId);

        // Update the answers
        $attempt->answers_json = $request->answers;
        $attempt->save();

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ الإجابات بنجاح.'
        ]);
    }

    /**
     * Submit the quiz.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $attemptId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitQuiz(Request $request, $attemptId)
    {
        $student = Auth::user();

        try {
        // Find the attempt
        $attempt = QuizAttempt::where('user_id', $student->user_id)
            ->where('status', 'in_progress')
            ->findOrFail($attemptId);

        // Get the answers from the request
        $answers = $request->input('answers', []);

        // Complete the attempt
        $attempt->complete($answers);

        return redirect()->route('student.quizzes.result', $attemptId)
            ->with('success', 'تم تقديم الاختبار بنجاح.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error submitting quiz: ' . $e->getMessage());
            
            return redirect()->route('student.quizzes.show', $attempt->quiz_id ?? $request->input('quiz_id'))
                ->with('error', 'حدث خطأ أثناء تسليم الامتحان. الرجاء المحاولة مرة أخرى.');
        }
    }

    /**
     * Show the result of a quiz attempt.
     *
     * @param  int  $attemptId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showResult($attemptId)
    {
        $student = Auth::user();

        // Find the attempt
        $attempt = QuizAttempt::with('quiz')
            ->where('user_id', $student->user_id)
            ->findOrFail($attemptId);

        // Check if the attempt is completed
        if (!$attempt->isCompleted()) {
            return redirect()->route('student.quizzes.take', $attemptId)
                ->with('error', 'يجب عليك إكمال الاختبار أولاً لعرض النتائج.');
        }

        return view('student.quizzes.result', compact('attempt'));
    }
} 