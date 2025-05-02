<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExamApiController extends Controller
{
    /**
     * Get available exams for the authenticated student.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function availableExams()
    {
        $user = Auth::user();

        // Get all courses the student is enrolled in
        $enrolledCourseIds = Enrollment::where('student_id', $user->user_id)
            ->pluck('course_id');

        // Get all exams for these courses
        $exams = Exam::whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', true)
            ->with('course')
            ->get();

        // Get the student's attempts for these exams
        $attempts = ExamAttempt::where('student_id', $user->user_id)
            ->whereIn('exam_id', $exams->pluck('exam_id'))
            ->get()
            ->keyBy('exam_id');

        // Add attempt information to each exam
        $exams->each(function ($exam) use ($attempts, $user) {
            $exam->attempt = $attempts->get($exam->exam_id);
            $exam->can_attempt = $exam->canBeAttemptedBy($user->user_id);
        });

        return response()->json([
            'exams' => $exams,
            'message' => 'Available exams retrieved successfully'
        ]);
    }

    /**
     * Get exam details.
     *
     * @param  int  $examId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($examId)
    {
        $user = Auth::user();

        // Find the exam
        $exam = Exam::with('course')
            ->where('is_published', true)
            ->findOrFail($examId);

        // Check if the student is enrolled in the course
        $isEnrolled = Enrollment::where('student_id', $user->user_id)
            ->where('course_id', $exam->course_id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'message' => 'You are not enrolled in the course for this exam'
            ], 403);
        }

        // Check if the exam is available
        $isAvailable = $exam->isAvailable();
        $availabilityMessage = null;

        if (!$isAvailable) {
            if ($exam->start_date && now()->lt($exam->start_date)) {
                $availabilityMessage = 'This exam will be available from ' . $exam->start_date->format('F j, Y, g:i a');
            } elseif ($exam->end_date && now()->gt($exam->end_date)) {
                $availabilityMessage = 'This exam was available until ' . $exam->end_date->format('F j, Y, g:i a');
            } else {
                $availabilityMessage = 'This exam is not currently available';
            }
        }

        // Get the student's attempts for this exam
        $attempts = ExamAttempt::where('student_id', $user->user_id)
            ->where('exam_id', $examId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if the student can attempt the exam
        $canAttempt = $exam->canBeAttemptedBy($user->user_id);

        // Don't include questions data unless starting an exam
        $exam->makeHidden('questions_data');

        return response()->json([
            'exam' => $exam,
            'is_available' => $isAvailable,
            'availability_message' => $availabilityMessage,
            'can_attempt' => $canAttempt,
            'attempts' => $attempts,
            'message' => 'Exam details retrieved successfully'
        ]);
    }

    /**
     * Start an exam attempt.
     *
     * @param  int  $examId
     * @return \Illuminate\Http\JsonResponse
     */
    public function startExam($examId)
    {
        $user = Auth::user();

        // Find the exam
        $exam = Exam::findOrFail($examId);

        // Check if the student is enrolled in the course
        $isEnrolled = Enrollment::where('student_id', $user->user_id)
            ->where('course_id', $exam->course_id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'message' => 'You are not enrolled in the course for this exam'
            ], 403);
        }

        // Check if the exam is available
        if (!$exam->isAvailable()) {
            return response()->json([
                'message' => 'This exam is not currently available'
            ], 403);
        }

        // Check if the student can attempt the exam
        if (!$exam->canBeAttemptedBy($user->user_id)) {
            return response()->json([
                'message' => 'You have reached the maximum number of attempts for this exam'
            ], 403);
        }

        // Create a new attempt
        $attempt = ExamAttempt::create([
            'exam_id' => $examId,
            'student_id' => $user->user_id,
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        // Prepare questions for the attempt (remove answers)
        $questions = collect($exam->questions_data)->map(function ($question) {
            // Remove correct answer information
            $options = collect($question['options'] ?? [])->map(function ($option) {
                return [
                    'id' => $option['id'],
                    'text' => $option['text']
                ];
            })->toArray();

            return [
                'id' => $question['id'],
                'text' => $question['text'],
                'type' => $question['type'],
                'points' => $question['points'],
                'options' => $options
            ];
        })->toArray();

        return response()->json([
            'attempt_id' => $attempt->attempt_id,
            'exam' => [
                'exam_id' => $exam->exam_id,
                'title' => $exam->title,
                'description' => $exam->description,
                'duration_minutes' => $exam->duration_minutes,
                'questions' => $questions,
            ],
            'start_time' => $attempt->start_time,
            'end_time' => $attempt->start_time->addMinutes($exam->duration_minutes),
            'message' => 'Exam attempt started successfully'
        ]);
    }

    /**
     * Submit an exam attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $attemptId
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitExam(Request $request, $attemptId)
    {
        $user = Auth::user();

        // Find the attempt
        $attempt = ExamAttempt::where('attempt_id', $attemptId)
            ->where('student_id', $user->user_id)
            ->firstOrFail();

        // Check if the attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return response()->json([
                'message' => 'This attempt has already been submitted'
            ], 400);
        }

        // Validate the answers
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer',
            'answers.*.selected_option_id' => 'nullable|integer',
            'answers.*.text_answer' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the exam
        $exam = Exam::findOrFail($attempt->exam_id);

        // Check if the time limit has been exceeded
        $endTime = $attempt->start_time->addMinutes($exam->duration_minutes);
        $isTimeExpired = now()->gt($endTime);

        // Process the answers
        $answers = collect($request->answers)->keyBy('question_id')->toArray();

        // Calculate the score
        $score = $this->calculateScore($exam, $answers);
        $isPassed = $score >= $exam->passing_score;

        // Update the attempt
        $attempt->end_time = now();
        $attempt->answers = $answers;
        $attempt->score = $score;
        $attempt->is_passed = $isPassed;
        $attempt->status = $isTimeExpired ? 'timed_out' : 'completed';
        $attempt->time_spent_seconds = now()->diffInSeconds($attempt->start_time);
        $attempt->save();

        return response()->json([
            'attempt' => $attempt,
            'score' => $score,
            'passing_score' => $exam->passing_score,
            'is_passed' => $isPassed,
            'message' => 'Exam submitted successfully'
        ]);
    }

    /**
     * Calculate the score for an exam attempt.
     *
     * @param  \App\Models\Exam  $exam
     * @param  array  $answers
     * @return float
     */
    private function calculateScore($exam, $answers)
    {
        $totalScore = 0;
        $questions = collect($exam->questions_data);

        foreach ($questions as $question) {
            $questionId = $question['id'];
            $points = $question['points'] ?? 1;

            // Skip if no answer provided for this question
            if (!isset($answers[$questionId])) {
                continue;
            }

            $answer = $answers[$questionId];

            // Handle different question types
            if ($question['type'] === 'multiple_choice') {
                $correctOptionId = collect($question['options'])
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->first();

                if (isset($answer['selected_option_id']) && $answer['selected_option_id'] == $correctOptionId) {
                    $totalScore += $points;
                }
            } elseif ($question['type'] === 'multiple_answer') {
                $correctOptionIds = collect($question['options'])
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->toArray();

                $selectedOptionIds = $answer['selected_option_ids'] ?? [];
                $isCorrect = count($correctOptionIds) === count($selectedOptionIds) &&
                    empty(array_diff($correctOptionIds, $selectedOptionIds));

                if ($isCorrect) {
                    $totalScore += $points;
                }
            } elseif ($question['type'] === 'true_false') {
                $correctAnswer = $question['correct_answer'] ?? false;
                $userAnswer = $answer['selected_option_id'] === 'true';

                if ($userAnswer === $correctAnswer) {
                    $totalScore += $points;
                }
            }
            // Text/essay questions would need manual grading
        }

        return $totalScore;
    }

    /**
     * Get exam attempt results.
     *
     * @param  int  $attemptId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttemptResults($attemptId)
    {
        $user = Auth::user();

        // Find the attempt
        $attempt = ExamAttempt::where('attempt_id', $attemptId)
            ->where('student_id', $user->user_id)
            ->firstOrFail();

        // Get the exam
        $exam = Exam::findOrFail($attempt->exam_id);

        // Prepare the results
        $questions = collect($exam->questions_data);
        $answers = collect($attempt->answers);

        $results = $questions->map(function ($question) use ($answers) {
            $questionId = $question['id'];
            $userAnswer = $answers->get($questionId);

            $result = [
                'question_id' => $questionId,
                'question_text' => $question['text'],
                'question_type' => $question['type'],
                'points' => $question['points'] ?? 1,
                'user_answer' => null,
                'correct_answer' => null,
                'is_correct' => false
            ];

            // Handle different question types
            if ($question['type'] === 'multiple_choice') {
                $correctOption = collect($question['options'])
                    ->where('is_correct', true)
                    ->first();

                $userSelectedOption = null;
                if ($userAnswer && isset($userAnswer['selected_option_id'])) {
                    $userSelectedOption = collect($question['options'])
                        ->where('id', $userAnswer['selected_option_id'])
                        ->first();
                }

                $result['user_answer'] = $userSelectedOption ? $userSelectedOption['text'] : null;
                $result['correct_answer'] = $correctOption ? $correctOption['text'] : null;
                $result['is_correct'] = $userSelectedOption && $correctOption && 
                    $userSelectedOption['id'] === $correctOption['id'];
            } elseif ($question['type'] === 'multiple_answer') {
                $correctOptions = collect($question['options'])
                    ->where('is_correct', true)
                    ->pluck('text')
                    ->toArray();

                $userSelectedOptionIds = $userAnswer['selected_option_ids'] ?? [];
                $userSelectedOptions = collect($question['options'])
                    ->whereIn('id', $userSelectedOptionIds)
                    ->pluck('text')
                    ->toArray();

                $result['user_answer'] = $userSelectedOptions;
                $result['correct_answer'] = $correctOptions;
                $result['is_correct'] = count($correctOptions) === count($userSelectedOptions) &&
                    empty(array_diff(
                        collect($question['options'])->where('is_correct', true)->pluck('id')->toArray(),
                        $userSelectedOptionIds
                    ));
            } elseif ($question['type'] === 'true_false') {
                $correctAnswer = $question['correct_answer'] ?? false;
                $userAnswerValue = $userAnswer && isset($userAnswer['selected_option_id']) && 
                    $userAnswer['selected_option_id'] === 'true';

                $result['user_answer'] = $userAnswerValue ? 'True' : 'False';
                $result['correct_answer'] = $correctAnswer ? 'True' : 'False';
                $result['is_correct'] = $userAnswerValue === $correctAnswer;
            } elseif ($question['type'] === 'text' || $question['type'] === 'essay') {
                $result['user_answer'] = $userAnswer['text_answer'] ?? null;
                $result['correct_answer'] = 'Requires manual grading';
                $result['is_correct'] = null; // Cannot automatically determine correctness
            }

            return $result;
        });

        return response()->json([
            'attempt' => $attempt,
            'exam' => [
                'exam_id' => $exam->exam_id,
                'title' => $exam->title,
                'description' => $exam->description,
                'duration_minutes' => $exam->duration_minutes,
                'passing_score' => $exam->passing_score,
            ],
            'results' => $results,
            'score' => $attempt->score,
            'is_passed' => $attempt->is_passed,
            'message' => 'Exam results retrieved successfully'
        ]);
    }

    /**
     * Get exams for a course (instructor view).
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function courseExams($courseId)
    {
        $user = Auth::user();

        // Check if the user is the instructor of this course
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', $user->user_id)
            ->firstOrFail();

        $exams = Exam::where('course_id', $courseId)
            ->withCount('attempts')
            ->get();

        return response()->json([
            'exams' => $exams,
            'message' => 'Course exams retrieved successfully'
        ]);
    }

    /**
     * Create a new exam.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validate the exam data
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,course_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'passing_percentage' => 'required|integer|min:1|max:100',
            'is_published' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|string|in:multiple_choice,multiple_answer,true_false,text,essay',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.options' => 'required_if:questions.*.type,multiple_choice,multiple_answer|array',
            'questions.*.options.*.text' => 'required_if:questions.*.type,multiple_choice,multiple_answer|string',
            'questions.*.options.*.is_correct' => 'required_if:questions.*.type,multiple_choice,multiple_answer|boolean',
            'questions.*.correct_answer' => 'required_if:questions.*.type,true_false|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user is the instructor of this course
        $course = Course::where('course_id', $request->course_id)
            ->where('instructor_id', $user->user_id)
            ->firstOrFail();

        // Prepare questions data
        $questionsData = collect($request->questions)->map(function ($question, $index) {
            $questionData = [
                'id' => $index + 1,
                'text' => $question['text'],
                'type' => $question['type'],
                'points' => $question['points'],
            ];

            if (in_array($question['type'], ['multiple_choice', 'multiple_answer'])) {
                $questionData['options'] = collect($question['options'])->map(function ($option, $optionIndex) {
                    return [
                        'id' => $optionIndex + 1,
                        'text' => $option['text'],
                        'is_correct' => $option['is_correct'],
                    ];
                })->toArray();
            } elseif ($question['type'] === 'true_false') {
                $questionData['correct_answer'] = $question['correct_answer'];
            }

            return $questionData;
        })->toArray();

        // Create the exam
        $exam = Exam::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'passing_percentage' => $request->passing_percentage,
            'is_active' => true,
            'is_published' => $request->is_published ?? false,
            'created_by' => $user->user_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'questions_data' => $questionsData,
            'max_attempts' => $request->max_attempts,
        ]);

        return response()->json([
            'message' => 'Exam created successfully',
            'exam' => $exam
        ], 201);
    }

    /**
     * Update an exam.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $examId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $examId)
    {
        $user = Auth::user();

        // Find the exam
        $exam = Exam::findOrFail($examId);

        // Check if user is the instructor of this course
        $course = Course::where('course_id', $exam->course_id)
            ->where('instructor_id', $user->user_id)
            ->firstOrFail();

        // Validate the exam data
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'sometimes|required|integer|min:1',
            'passing_percentage' => 'sometimes|required|integer|min:1|max:100',
            'is_published' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'questions' => 'sometimes|required|array|min:1',
            'questions.*.text' => 'required_with:questions|string',
            'questions.*.type' => 'required_with:questions|string|in:multiple_choice,multiple_answer,true_false,text,essay',
            'questions.*.points' => 'required_with:questions|integer|min:1',
            'questions.*.options' => 'required_if:questions.*.type,multiple_choice,multiple_answer|array',
            'questions.*.options.*.text' => 'required_if:questions.*.type,multiple_choice,multiple_answer|string',
            'questions.*.options.*.is_correct' => 'required_if:questions.*.type,multiple_choice,multiple_answer|boolean',
            'questions.*.correct_answer' => 'required_if:questions.*.type,true_false|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update exam fields
        if ($request->has('title')) $exam->title = $request->title;
        if ($request->has('description')) $exam->description = $request->description;
        if ($request->has('duration_minutes')) $exam->duration_minutes = $request->duration_minutes;
        if ($request->has('passing_percentage')) $exam->passing_percentage = $request->passing_percentage;
        if ($request->has('is_published')) $exam->is_published = $request->is_published;
        if ($request->has('start_date')) $exam->start_date = $request->start_date;
        if ($request->has('end_date')) $exam->end_date = $request->end_date;
        if ($request->has('max_attempts')) $exam->max_attempts = $request->max_attempts;

        // Update questions if provided
        if ($request->has('questions')) {
            $questionsData = collect($request->questions)->map(function ($question, $index) {
                $questionData = [
                    'id' => $index + 1,
                    'text' => $question['text'],
                    'type' => $question['type'],
                    'points' => $question['points'],
                ];

                if (in_array($question['type'], ['multiple_choice', 'multiple_answer'])) {
                    $questionData['options'] = collect($question['options'])->map(function ($option, $optionIndex) {
                        return [
                            'id' => $optionIndex + 1,
                            'text' => $option['text'],
                            'is_correct' => $option['is_correct'],
                        ];
                    })->toArray();
                } elseif ($question['type'] === 'true_false') {
                    $questionData['correct_answer'] = $question['correct_answer'];
                }

                return $questionData;
            })->toArray();

            $exam->questions_data = $questionsData;
        }

        $exam->save();

        return response()->json([
            'message' => 'Exam updated successfully',
            'exam' => $exam
        ]);
    }

    /**
     * Delete an exam.
     *
     * @param  int  $examId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($examId)
    {
        $user = Auth::user();

        // Find the exam
        $exam = Exam::findOrFail($examId);

        // Check if user is the instructor of this course
        $course = Course::where('course_id', $exam->course_id)
            ->where('instructor_id', $user->user_id)
            ->firstOrFail();

        // Check if there are any attempts
        $attemptsCount = ExamAttempt::where('exam_id', $examId)->count();
        if ($attemptsCount > 0) {
            return response()->json([
                'message' => 'Cannot delete exam with existing attempts',
                'attempts_count' => $attemptsCount
            ], 400);
        }

        // Delete the exam
        $exam->delete();

        return response()->json([
            'message' => 'Exam deleted successfully'
        ]);
    }
}
