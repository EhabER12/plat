<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller
{
    /**
     * Start a new quiz attempt.
     */
    public function start(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $user = Auth::user();
        
        // Check if quiz is available
        if (!$quiz->isActive()) {
            if ($quiz->hasNotStarted()) {
                return redirect()->route('student.quizzes.show', $quiz->quiz_id)
                    ->with('error', 'لم يبدأ هذا الامتحان بعد، يرجى العودة لاحقاً.');
            } elseif ($quiz->hasEnded()) {
                return redirect()->route('student.quizzes.show', $quiz->quiz_id)
                    ->with('error', 'انتهت فترة هذا الامتحان ولم يعد متاحاً.');
            } else {
                return redirect()->route('student.quizzes.show', $quiz->quiz_id)
                    ->with('error', 'هذا الامتحان غير متاح حالياً.');
            }
        }
        
        // Check attempts limit
        if ($quiz->max_attempts !== null) {
            $attemptsCount = $quiz->attempts()
                ->where('user_id', $user->user_id)
                ->count();
                
            if ($attemptsCount >= $quiz->max_attempts) {
                return redirect()->route('student.quizzes.show', $quiz->quiz_id)
                    ->with('error', 'لقد وصلت إلى الحد الأقصى من المحاولات المسموحة لهذا الامتحان.');
            }
        }
        
        // Check if user already has an in-progress attempt
        $inProgressAttempt = $quiz->attempts()
            ->where('user_id', $user->user_id)
            ->where('status', 'in_progress')
            ->first();
            
        if ($inProgressAttempt) {
            return redirect()->route('student.quiz-attempts.take', $inProgressAttempt->attempt_id);
        }
        
        // Create new attempt
        $attempt = new QuizAttempt();
        $attempt->quiz_id = $quiz->quiz_id;
        $attempt->user_id = $user->user_id;
        $attempt->start_time = now();
        $attempt->end_time = now()->addMinutes($quiz->duration_minutes);
        $attempt->status = 'in_progress';
        $attempt->answers_json = [];
        $attempt->save();
        
        return redirect()->route('student.quiz-attempts.take', $attempt->attempt_id);
    }
    
    /**
     * Continue an in-progress quiz attempt.
     */
    public function continue($attemptId)
    {
        $attempt = QuizAttempt::with('quiz')->findOrFail($attemptId);
        $user = Auth::user();
        
        // Check if this attempt belongs to current user
        if ($attempt->user_id !== $user->user_id) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه المحاولة.');
        }
        
        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.quiz-attempts.show', $attempt->attempt_id);
        }
        
        // Check if time has expired
        if (now()->gt($attempt->end_time)) {
            // Auto-submit the attempt
            $this->submitAttempt($attempt);
            return redirect()->route('student.quiz-attempts.show', $attempt->attempt_id)
                ->with('info', 'انتهى وقت المحاولة وتم تسليمها تلقائياً.');
        }
        
        return redirect()->route('student.quiz-attempts.take', $attempt->attempt_id);
    }
    
    /**
     * Take a quiz.
     */
    public function take($attemptId)
    {
        $attempt = QuizAttempt::with('quiz')->findOrFail($attemptId);
        $user = Auth::user();
        
        // Check if this attempt belongs to current user
        if ($attempt->user_id !== $user->user_id) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه المحاولة.');
        }
        
        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.quiz-attempts.show', $attempt->attempt_id);
        }
        
        // Check if time has expired
        if (now()->gt($attempt->end_time)) {
            // Auto-submit the attempt
            $this->submitAttempt($attempt);
            return redirect()->route('student.quiz-attempts.show', $attempt->attempt_id)
                ->with('info', 'انتهى وقت المحاولة وتم تسليمها تلقائياً.');
        }
        
        return view('student.quiz-attempts.take', [
            'attempt' => $attempt,
            'quiz' => $attempt->quiz,
        ]);
    }
    
    /**
     * Save progress during a quiz attempt.
     */
    public function saveProgress(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::findOrFail($attemptId);
        $user = Auth::user();
        
        // Check if this attempt belongs to current user
        if ($attempt->user_id !== $user->user_id) {
            return response()->json(['success' => false, 'message' => 'ليس لديك صلاحية الوصول إلى هذه المحاولة.'], 403);
        }
        
        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'هذه المحاولة قد انتهت بالفعل.'], 400);
        }
        
        // Save answers
        $attempt->answers_json = $request->input('answers', []);
        $attempt->save();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Submit a quiz attempt.
     */
    public function submit(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::with('quiz')->findOrFail($attemptId);
        $user = Auth::user();
        
        // Check if this attempt belongs to current user
        if ($attempt->user_id !== $user->user_id) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه المحاولة.');
        }
        
        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.quiz-attempts.show', $attempt->attempt_id);
        }
        
        // Save final answers
        $attempt->answers_json = $request->input('answers', []);
        
        // Submit attempt
        $this->submitAttempt($attempt);
        
        return redirect()->route('student.quiz-attempts.show', $attempt->attempt_id)
            ->with('success', 'تم تسليم الامتحان بنجاح.');
    }
    
    /**
     * Helper method to calculate scores and submit an attempt.
     */
    private function submitAttempt(QuizAttempt $attempt)
    {
        $quiz = $attempt->quiz;
        $answers = $attempt->answers_json;
        $questions = $quiz->questions_json;
        
        $score = 0;
        $correctAnswersCount = 0;
        
        foreach ($questions as $index => $question) {
            $questionId = $question['id'] ?? $index;
            $userAnswer = $answers[$questionId] ?? null;
            $isCorrect = false;
            
            // Check if answer is correct based on question type
            if ($question['type'] == 'multiple_choice') {
                $correctOptions = collect($question['options'])->where('is_correct', true)->pluck('text')->toArray();
                $isCorrect = !empty($userAnswer) && (is_array($userAnswer) ? count(array_diff($correctOptions, $userAnswer)) === 0 && count(array_diff($userAnswer, $correctOptions)) === 0 : in_array($userAnswer, $correctOptions));
            } elseif ($question['type'] == 'true_false') {
                $isCorrect = !empty($userAnswer) && $userAnswer == $question['correct_answer'];
            } elseif ($question['type'] == 'short_answer') {
                $isCorrect = !empty($userAnswer) && strtolower(trim($userAnswer)) == strtolower(trim($question['correct_answer']));
            }
            
            // Add points if correct
            if ($isCorrect) {
                $score += $question['points'];
                $correctAnswersCount++;
            }
        }
        
        // Update attempt
        $attempt->end_time = min($attempt->end_time, now());
        $attempt->status = 'completed';
        $attempt->score = $score;
        $attempt->score_percentage = $quiz->total_possible_score > 0 ? 
            ($score / $quiz->total_possible_score) * 100 : 0;
        $attempt->is_passed = $attempt->score_percentage >= $quiz->passing_percentage;
        $attempt->time_spent_seconds = $attempt->start_time->diffInSeconds($attempt->end_time);
        $attempt->correct_answers_count = $correctAnswersCount;
        $attempt->save();
        
        return $attempt;
    }
    
    /**
     * Show a completed quiz attempt (results).
     */
    public function show($attemptId)
    {
        $attempt = QuizAttempt::with('quiz')->findOrFail($attemptId);
        $user = Auth::user();
        
        // Check if this attempt belongs to current user
        if ($attempt->user_id !== $user->user_id) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه المحاولة.');
        }
        
        // If attempt is still in progress, redirect to take page
        if ($attempt->status === 'in_progress') {
            return redirect()->route('student.quiz-attempts.continue', $attempt->attempt_id);
        }
        
        // Check if user can retake the quiz
        $canRetake = false;
        $quiz = $attempt->quiz;
        
        if ($quiz->isActive()) {
            if ($quiz->max_attempts === null) {
                $canRetake = true;
            } else {
                $attemptsCount = $quiz->attempts()
                    ->where('user_id', $user->user_id)
                    ->count();
                    
                $canRetake = $attemptsCount < $quiz->max_attempts;
            }
        }
        
        return view('student.quiz-attempts.result', [
            'attempt' => $attempt,
            'quiz' => $quiz,
            'canRetake' => $canRetake,
        ]);
    }
} 