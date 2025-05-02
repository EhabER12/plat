<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\AnswerOption;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Display a listing of available exams for the student.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $student = Auth::user();

        // Get all courses the student is enrolled in
        $enrolledCourseIds = Enrollment::where('student_id', $student->user_id)
            ->pluck('course_id');

        // Debug information
        $debug = [
            'student_id' => $student->user_id,
            'enrolled_courses' => $enrolledCourseIds->toArray(),
        ];

        // Get all exams for these courses (without is_published filter)
        $exams = Exam::whereIn('course_id', $enrolledCourseIds)
            ->with('course')
            ->get();

        // Add exam info to debug
        $debug['exam_count'] = $exams->count();
        $debug['exams'] = $exams->map(function($exam) {
            return [
                'id' => $exam->exam_id,
                'title' => $exam->title,
                'course_id' => $exam->course_id,
                'course' => $exam->course->title ?? 'Unknown',
            ];
        })->toArray();

        // Get the student's attempts for these exams
        $attempts = ExamAttempt::where('student_id', $student->user_id)
            ->whereIn('exam_id', $exams->pluck('exam_id'))
            ->get()
            ->keyBy('exam_id');

        return view('student.exams.index', compact('exams', 'attempts', 'debug'));
    }

    /**
     * Display the exam details and instructions.
     *
     * @param  int  $examId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($examId)
    {
        $student = Auth::user();

        // Find the exam
        $exam = Exam::with('course')
            ->findOrFail($examId);

        // Check if the student is enrolled in the course
        $isEnrolled = Enrollment::where('student_id', $student->user_id)
            ->where('course_id', $exam->course_id)
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.exams.index')
                ->with('error', 'You are not enrolled in the course for this exam.');
        }

        // Check if the exam is available
        $now = Carbon::now();
        $isAvailable = true;
        $availabilityMessage = null;

        if ($exam->available_from && $now->lt($exam->available_from)) {
            $isAvailable = false;
            $availabilityMessage = 'This exam will be available from ' . $exam->available_from->format('F j, Y, g:i a');
        } elseif ($exam->available_to && $now->gt($exam->available_to)) {
            $isAvailable = false;
            $availabilityMessage = 'This exam was available until ' . $exam->available_to->format('F j, Y, g:i a');
        }

        // Get the student's attempts for this exam
        $attempts = ExamAttempt::where('student_id', $student->user_id)
            ->where('exam_id', $examId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.exams.show', compact('exam', 'isAvailable', 'availabilityMessage', 'attempts'));
    }

    /**
     * Start a new exam attempt.
     *
     * @param  int  $examId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startAttempt($examId)
    {
        $student = Auth::user();

        // Find the exam
        $exam = Exam::findOrFail($examId);

        // Check if the student is enrolled in the course
        $isEnrolled = Enrollment::where('student_id', $student->user_id)
            ->where('course_id', $exam->course_id)
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.exams.index')
                ->with('error', 'You are not enrolled in the course for this exam.');
        }

        // Check if the exam is available
        $now = Carbon::now();

        if ($exam->available_from && $now->lt($exam->available_from)) {
            return redirect()->route('student.exams.show', $examId)
                ->with('error', 'This exam is not yet available.');
        }

        if ($exam->available_to && $now->gt($exam->available_to)) {
            return redirect()->route('student.exams.show', $examId)
                ->with('error', 'This exam is no longer available.');
        }

        // Check if there's an ongoing attempt
        $ongoingAttempt = ExamAttempt::where('student_id', $student->user_id)
            ->where('exam_id', $examId)
            ->whereNull('completed_at')
            ->first();

        if ($ongoingAttempt) {
            return redirect()->route('student.exams.take', $ongoingAttempt->attempt_id);
        }

        // Create a new attempt
        $attempt = new ExamAttempt();
        $attempt->student_id = $student->user_id;
        $attempt->exam_id = $examId;
        $attempt->started_at = $now;
        $attempt->save();

        return redirect()->route('student.exams.take', $attempt->attempt_id);
    }

    /**
     * Take the exam.
     *
     * @param  int  $attemptId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function takeExam($attemptId)
    {
        $student = Auth::user();

        // Find the attempt
        $attempt = ExamAttempt::with(['exam.questions.options'])
            ->where('student_id', $student->user_id)
            ->findOrFail($attemptId);

        // Check if the attempt is already completed
        if ($attempt->completed_at) {
            return redirect()->route('student.exams.result', $attemptId)
                ->with('info', 'This exam attempt has already been completed.');
        }

        // Check if the exam time has expired
        $now = Carbon::now();
        $examEndTime = Carbon::parse($attempt->started_at)->addMinutes($attempt->exam->duration);

        if ($now->gt($examEndTime)) {
            // Auto-submit the exam
            return $this->submitExam(new Request(), $attemptId);
        }

        // Calculate remaining time in seconds
        $remainingTime = $examEndTime->diffInSeconds($now);

        return view('student.exams.take', compact('attempt', 'remainingTime'));
    }

    /**
     * Submit the exam.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $attemptId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitExam(Request $request, $attemptId)
    {
        $student = Auth::user();

        // Find the attempt
        $attempt = ExamAttempt::with(['exam.questions.options'])
            ->where('student_id', $student->user_id)
            ->findOrFail($attemptId);

        // Check if the attempt is already completed
        if ($attempt->completed_at) {
            return redirect()->route('student.exams.result', $attemptId)
                ->with('info', 'This exam attempt has already been completed.');
        }

        // Get the answers from the request
        $answers = $request->input('answers', []);

        // Calculate the score
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($attempt->exam->questions as $question) {
            $totalPoints += $question->points;

            // Skip if no answer provided
            if (!isset($answers[$question->question_id])) {
                continue;
            }

            $answer = $answers[$question->question_id];

            // For multiple choice questions
            if ($question->question_type === 'multiple_choice') {
                // Get the selected option
                $selectedOption = AnswerOption::where('question_id', $question->question_id)
                    ->where('option_id', $answer)
                    ->first();

                // Award points if the selected option is correct
                if ($selectedOption && $selectedOption->is_correct) {
                    $earnedPoints += $question->points;
                }
            }

            // For true/false questions
            elseif ($question->question_type === 'true_false') {
                // Get the correct option
                $correctOption = AnswerOption::where('question_id', $question->question_id)
                    ->where('is_correct', true)
                    ->first();

                // Award points if the selected option is correct
                if ($correctOption && $correctOption->option_id == $answer) {
                    $earnedPoints += $question->points;
                }
            }

            // For short answer questions (these would need manual grading)
            elseif ($question->question_type === 'short_answer') {
                // Store the answer but don't award points yet
            }
        }

        // Calculate the score as a percentage
        $score = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;

        // Check if the student passed
        $passed = $score >= $attempt->exam->passing_score;

        // Update the attempt
        $attempt->completed_at = Carbon::now();
        $attempt->score = $score;
        $attempt->passed = $passed;
        $attempt->answers = $answers;
        $attempt->save();

        return redirect()->route('student.exams.result', $attemptId)
            ->with('success', 'Exam submitted successfully.');
    }

    /**
     * Display the exam result.
     *
     * @param  int  $attemptId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showResult($attemptId)
    {
        $student = Auth::user();

        // Find the attempt
        $attempt = ExamAttempt::with(['exam.questions.options'])
            ->where('student_id', $student->user_id)
            ->findOrFail($attemptId);

        // Check if the attempt is completed
        if (!$attempt->completed_at) {
            return redirect()->route('student.exams.take', $attemptId)
                ->with('info', 'This exam attempt is not yet completed.');
        }

        return view('student.exams.result', compact('attempt'));
    }
}
