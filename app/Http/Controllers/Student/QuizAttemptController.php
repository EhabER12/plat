<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Badge;
use App\Models\Achievement;
use App\Services\StudentMotivationService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuizAttemptController extends Controller
{
    protected $motivationService;
    protected $notificationService;

    /**
     * Create a new controller instance.
     *
     * @param StudentMotivationService $motivationService
     * @param NotificationService $notificationService
     */
    public function __construct(StudentMotivationService $motivationService, NotificationService $notificationService)
    {
        $this->motivationService = $motivationService;
        $this->notificationService = $notificationService;
    }

    /**
     * Start a new quiz attempt.
     */
    public function start(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        $user = Auth::user();

        // Check if quiz is available
        if (!$quiz->isActive()) {
            if ($quiz->hasNotStarted()) {
                return redirect()->route('student.quizzes.show', $quiz->id)
                    ->with('error', 'لم يبدأ هذا الامتحان بعد، يرجى العودة لاحقاً.');
            } elseif ($quiz->hasEnded()) {
                return redirect()->route('student.quizzes.show', $quiz->id)
                    ->with('error', 'انتهت فترة هذا الامتحان ولم يعد متاحاً.');
            } else {
                return redirect()->route('student.quizzes.show', $quiz->id)
                    ->with('error', 'هذا الامتحان غير متاح حالياً.');
            }
        }

        // Check attempts limit
        if ($quiz->max_attempts !== null) {
            $attemptsCount = $quiz->attempts()
                ->where('user_id', $user->user_id)
                ->count();

            if ($attemptsCount >= $quiz->max_attempts) {
                return redirect()->route('student.quizzes.show', $quiz->id)
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
        $attempt->quiz_id = $quiz->id;
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

        // Update student motivation badges and achievements
        $this->updateStudentMotivation($attempt);

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

    /**
     * Update student motivation badges and achievements based on quiz attempt.
     *
     * @param QuizAttempt $attempt
     * @return void
     */
    private function updateStudentMotivation(QuizAttempt $attempt)
    {
        try {
            $student = Auth::user();

            // Check for badges
            $this->checkAndAwardBadges($student, $attempt);

            // Check for achievements
            $this->checkAndAwardAchievements($student, $attempt);

            // Send notification based on quiz result
            if ($attempt->is_passed) {
                $this->notificationService->createQuizPassedNotification($student, $attempt);
            } else {
                $this->notificationService->createQuizFailedNotification($student, $attempt);
            }

        } catch (\Exception $e) {
            // Log error but don't interrupt the quiz submission process
            Log::error('Error updating student motivation: ' . $e->getMessage());
        }
    }

    /**
     * Check and award badges based on quiz attempt.
     *
     * @param \App\Models\User $student
     * @param QuizAttempt $attempt
     * @return void
     */
    private function checkAndAwardBadges($student, $attempt)
    {
        // Get all active badges
        $badges = Badge::where('is_active', true)->get();

        foreach ($badges as $badge) {
            // Skip if student already has this badge
            if ($student->badges()->where('badge_id', $badge->id)->exists()) {
                continue;
            }

            $criteria = $badge->criteria;
            $shouldAward = false;

            if (!$criteria || !isset($criteria['type'])) {
                continue;
            }

            // Check badge criteria
            switch ($criteria['type']) {
                case 'quiz_attempts':
                    // Award badge based on number of attempts
                    $attemptsCount = QuizAttempt::where('user_id', $student->user_id)
                        ->where('status', 'completed')
                        ->count();

                    $shouldAward = $attemptsCount >= ($criteria['count'] ?? 1);
                    break;

                case 'quiz_score':
                    // Award badge based on score
                    $shouldAward = $attempt->score_percentage >= ($criteria['min_score'] ?? 90);
                    break;

                case 'quiz_streak':
                    // Award badge based on streak of passed quizzes
                    $attempts = QuizAttempt::where('user_id', $student->user_id)
                        ->where('status', 'completed')
                        ->orderBy('end_time', 'desc')
                        ->take($criteria['count'] ?? 3)
                        ->get();

                    $shouldAward = $attempts->count() >= ($criteria['count'] ?? 3) &&
                                  $attempts->every(function ($item) {
                                      return $item->is_passed;
                                  });
                    break;

                case 'quiz_perfect_streak':
                    // Award badge based on streak of perfect scores
                    $attempts = QuizAttempt::where('user_id', $student->user_id)
                        ->where('status', 'completed')
                        ->orderBy('end_time', 'desc')
                        ->take($criteria['count'] ?? 3)
                        ->get();

                    $shouldAward = $attempts->count() >= ($criteria['count'] ?? 3) &&
                                  $attempts->every(function ($item) {
                                      return $item->score_percentage == 100;
                                  });
                    break;

                case 'quiz_time':
                    // Award badge based on completing quiz quickly
                    if ($attempt->time_spent_seconds && $attempt->quiz->time_limit_minutes) {
                        $timeLimit = $attempt->quiz->time_limit_minutes * 60; // convert to seconds
                        $maxTimePercentage = $criteria['max_time_percentage'] ?? 50;
                        $shouldAward = $attempt->time_spent_seconds <= ($timeLimit * $maxTimePercentage / 100);
                    }
                    break;
            }

            // Award badge if criteria met
            if ($shouldAward) {
                // Check if student already has this badge
                if (!$student->badges()->where('badge_id', $badge->id)->exists()) {
                    $student->badges()->attach($badge->id, ['earned_at' => now()]);

                    // Send notification for new badge
                    $this->notificationService->createBadgeNotification($student, $badge);
                }
            }
        }
    }

    /**
     * Check and award achievements based on quiz attempt.
     *
     * @param \App\Models\User $student
     * @param QuizAttempt $attempt
     * @return void
     */
    private function checkAndAwardAchievements($student, $attempt)
    {
        // Get all active achievements
        $achievements = Achievement::where('is_active', true)->get();

        foreach ($achievements as $achievement) {
            // Skip if student already has this achievement
            if ($student->achievements()->where('achievement_id', $achievement->id)->exists()) {
                continue;
            }

            $criteria = $achievement->criteria;
            $shouldAward = false;

            if (!$criteria || !isset($criteria['type'])) {
                continue;
            }

            // Check achievement criteria
            switch ($criteria['type']) {
                case 'quiz_attempts':
                    // Award achievement based on number of attempts
                    $query = QuizAttempt::where('user_id', $student->user_id)
                        ->where('status', 'completed');

                    // Check if attempts need to be passed
                    if (isset($criteria['passed']) && $criteria['passed']) {
                        $query->where('is_passed', true);
                    }

                    $attemptsCount = $query->count();
                    $shouldAward = $attemptsCount >= ($criteria['count'] ?? 10);
                    break;

                case 'quiz_pass_rate':
                    // Award achievement based on pass rate
                    $attemptsCount = QuizAttempt::where('user_id', $student->user_id)
                        ->where('status', 'completed')
                        ->count();

                    if ($attemptsCount >= ($criteria['min_attempts'] ?? 5)) {
                        $passedCount = QuizAttempt::where('user_id', $student->user_id)
                            ->where('status', 'completed')
                            ->where('is_passed', true)
                            ->count();

                        $passRate = ($passedCount / $attemptsCount) * 100;
                        $shouldAward = $passRate >= ($criteria['min_rate'] ?? 80);
                    }
                    break;

                case 'quiz_streak':
                    // Award achievement based on streak of passed quizzes
                    $attempts = QuizAttempt::where('user_id', $student->user_id)
                        ->where('status', 'completed')
                        ->orderBy('end_time', 'desc')
                        ->take($criteria['count'] ?? 5)
                        ->get();

                    $shouldAward = $attempts->count() >= ($criteria['count'] ?? 5) &&
                                  $attempts->every(function ($item) {
                                      return $item->is_passed;
                                  });
                    break;

                case 'quiz_score_streak':
                    // Award achievement based on streak of high scores
                    $attempts = QuizAttempt::where('user_id', $student->user_id)
                        ->where('status', 'completed')
                        ->orderBy('end_time', 'desc')
                        ->take($criteria['count'] ?? 10)
                        ->get();

                    $minScore = $criteria['min_score'] ?? 90;
                    $shouldAward = $attempts->count() >= ($criteria['count'] ?? 10) &&
                                  $attempts->every(function ($item) use ($minScore) {
                                      return $item->score_percentage >= $minScore;
                                  });
                    break;

                case 'quiz_categories':
                    // Award achievement based on completing quizzes in all categories
                    if (isset($criteria['all_categories']) && $criteria['all_categories']) {
                        $categoryIds = \App\Models\Category::pluck('id')->toArray();
                        $completedCategoryIds = QuizAttempt::where('user_id', $student->user_id)
                            ->where('status', 'completed')
                            ->where('is_passed', true)
                            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
                            ->join('courses', 'quizzes.course_id', '=', 'courses.id')
                            ->pluck('courses.category_id')
                            ->unique()
                            ->toArray();

                        $shouldAward = count(array_diff($categoryIds, $completedCategoryIds)) === 0;
                    }
                    break;
            }

            // Award achievement if criteria met
            if ($shouldAward) {
                // Check if student already has this achievement
                if (!$student->achievements()->where('achievement_id', $achievement->id)->exists()) {
                    $student->achievements()->attach($achievement->id, ['earned_at' => now()]);

                    // Send notification for new achievement
                    $this->notificationService->createAchievementNotification($student, $achievement);
                }
            }
        }
    }
}