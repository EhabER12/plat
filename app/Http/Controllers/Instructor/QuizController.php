<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    /**
     * Display a listing of the quizzes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $quizzes = Quiz::whereHas('course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->with('course')
            ->latest()
            ->get();

        return view('instructor.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new quiz.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View
     */
    public function create($courseId = null)
    {
        $courses = Course::where('instructor_id', Auth::id())->get();
        $selectedCourse = null;

        if ($courseId) {
            $selectedCourse = Course::where('instructor_id', Auth::id())
                ->where('course_id', $courseId)
                ->first();

            if (!$selectedCourse) {
                return redirect()->route('instructor.quizzes.create')
                    ->with('error', 'لا تملك صلاحية إنشاء اختبار لهذه الدورة.');
            }
        }

        return view('instructor.quizzes.create', compact('courses', 'selectedCourse'));
    }

    /**
     * Store a newly created quiz in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the exam data
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,course_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'passing_percentage' => 'required|integer|min:1|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_published' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'questions' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if the course belongs to the authenticated instructor
        $course = Course::where('instructor_id', Auth::id())
            ->where('course_id', $request->course_id)
            ->first();

        if (!$course) {
            return redirect()->back()
                ->with('error', 'لا تملك صلاحية إنشاء اختبار لهذه الدورة.')
                ->withInput();
        }

        // Format questions with unique IDs
        $questions = [];
        foreach ($request->questions as $question) {
            $questionId = Str::uuid()->toString();
            $questions[] = [
                'id' => $questionId,
                'type' => $question['type'],
                'text' => $question['text'],
                'points' => (int) $question['points'],
                'options' => $question['options'] ?? [],
                'correct_answer' => $question['correct_answer'] ?? '',
            ];
        }

        // Create the quiz
        $quiz = new Quiz();
        $quiz->course_id = $request->course_id;
        $quiz->title = $request->title;
        $quiz->description = $request->description;
        $quiz->duration_minutes = $request->duration_minutes;
        $quiz->passing_percentage = $request->passing_percentage;
        $quiz->is_active = true;
        $quiz->is_published = $request->has('is_published');
        $quiz->created_by = Auth::id();
        $quiz->start_date = $request->start_date;
        $quiz->end_date = $request->end_date;
        $quiz->questions_json = $questions;
        $quiz->max_attempts = $request->max_attempts;
        $quiz->save();

        return redirect()->route('instructor.quizzes.show', $quiz->id)
            ->with('success', 'تم إنشاء الاختبار بنجاح.');
    }

    /**
     * Display the specified quiz.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $quiz = Quiz::with(['course', 'attempts.user'])
            ->whereHas('course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->findOrFail($id);

        return view('instructor.quizzes.show', compact('quiz'));
    }

    /**
     * Show the form for editing the specified quiz.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $quiz = Quiz::whereHas('course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->findOrFail($id);

        $courses = Course::where('instructor_id', Auth::id())->get();

        return view('instructor.quizzes.edit', compact('quiz', 'courses'));
    }

    /**
     * Update the specified quiz in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validate the quiz data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'passing_percentage' => 'required|integer|min:1|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_published' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Find the quiz and check if it belongs to the authenticated instructor
        $quiz = Quiz::whereHas('course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->findOrFail($id);

        // Update the quiz
        $quiz->title = $request->title;
        $quiz->description = $request->description;
        $quiz->duration_minutes = $request->duration_minutes;
        $quiz->passing_percentage = $request->passing_percentage;
        $quiz->is_published = $request->has('is_published');
        $quiz->start_date = $request->start_date;
        $quiz->end_date = $request->end_date;
        $quiz->max_attempts = $request->max_attempts;
        $quiz->save();

        return redirect()->route('instructor.quizzes.show', $quiz->id)
            ->with('success', 'تم تحديث الاختبار بنجاح.');
    }

    /**
     * Update questions for a quiz.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateQuestions(Request $request, $id)
    {
        // Validate the questions data
        $validator = Validator::make($request->all(), [
            'questions' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Find the quiz and check if it belongs to the authenticated instructor
        $quiz = Quiz::whereHas('course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->findOrFail($id);

        // Format questions with unique IDs for new questions
        $questions = [];
        foreach ($request->questions as $question) {
            $questionId = $question['id'] ?? Str::uuid()->toString();
            $questions[] = [
                'id' => $questionId,
                'type' => $question['type'],
                'text' => $question['text'],
                'points' => (int) $question['points'],
                'options' => $question['options'] ?? [],
                'correct_answer' => $question['correct_answer'] ?? '',
            ];
        }

        // Update the quiz questions
        $quiz->questions_json = $questions;
        $quiz->save();

        return redirect()->route('instructor.quizzes.show', $quiz->id)
            ->with('success', 'تم تحديث أسئلة الاختبار بنجاح.');
    }

    /**
     * Remove the specified quiz from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Find the quiz and check if it belongs to the authenticated instructor
        $quiz = Quiz::whereHas('course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->findOrFail($id);

        // Check if there are any attempts
        $attemptsCount = $quiz->attempts()->count();
        if ($attemptsCount > 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف الاختبار لأنه يحتوي على ' . $attemptsCount . ' محاولة.');
        }

        // Delete the quiz
        $quiz->delete();

        return redirect()->route('instructor.quizzes.index')
            ->with('success', 'تم حذف الاختبار بنجاح.');
    }

    /**
     * View quiz attempt details
     *
     * @param  int  $attemptId
     * @return \Illuminate\View\View
     */
    public function viewAttempt($attemptId)
    {
        $attempt = QuizAttempt::with(['quiz', 'user'])
            ->whereHas('quiz.course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->findOrFail($attemptId);

        return view('instructor.quizzes.attempt', compact('attempt'));
    }

    /**
     * Provide feedback on a quiz attempt
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $attemptId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function provideFeedback(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::whereHas('quiz.course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->findOrFail($attemptId);

        $attempt->instructor_feedback = $request->feedback;
        $attempt->save();

        return redirect()->back()
            ->with('success', 'تم حفظ الملاحظات بنجاح.');
    }
}