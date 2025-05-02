<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\AnswerOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    /**
     * Display a listing of the exams.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $exams = Exam::whereHas('course', function ($query) {
            $query->where('instructor_id', Auth::id());
        })->with('course')->latest()->get();
        
        return view('instructor.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new exam.
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
                return redirect()->route('instructor.exams.create')
                    ->with('error', 'You do not have permission to create an exam for this course.');
            }
        }
        
        return view('instructor.exams.create', compact('courses', 'selectedCourse'));
    }

    /**
     * Store a newly created exam in storage.
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
            'duration' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1|max:100',
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after_or_equal:available_from',
            'is_published' => 'boolean',
            
            // Validate questions
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'questions.*.points' => 'required|integer|min:1',
            
            // Validate options for multiple choice questions
            'questions.*.options' => 'required_if:questions.*.question_type,multiple_choice|array|min:2',
            'questions.*.options.*.option_text' => 'required_if:questions.*.question_type,multiple_choice|string',
            'questions.*.options.*.is_correct' => 'required_if:questions.*.question_type,multiple_choice|boolean',
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
                ->with('error', 'You do not have permission to create an exam for this course.')
                ->withInput();
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Create the exam
            $exam = new Exam();
            $exam->course_id = $request->course_id;
            $exam->title = $request->title;
            $exam->description = $request->description;
            $exam->duration = $request->duration;
            $exam->passing_score = $request->passing_score;
            $exam->is_published = $request->has('is_published');
            $exam->available_from = $request->available_from;
            $exam->available_to = $request->available_to;
            $exam->save();
            
            // Create questions and options
            foreach ($request->questions as $questionData) {
                $question = new Question();
                $question->exam_id = $exam->exam_id;
                $question->question_text = $questionData['question_text'];
                $question->question_type = $questionData['question_type'];
                $question->points = $questionData['points'];
                $question->save();
                
                // Create options for multiple choice questions
                if ($questionData['question_type'] === 'multiple_choice' && isset($questionData['options'])) {
                    foreach ($questionData['options'] as $optionData) {
                        $option = new AnswerOption();
                        $option->question_id = $question->question_id;
                        $option->option_text = $optionData['option_text'];
                        $option->is_correct = $optionData['is_correct'] ?? false;
                        $option->save();
                    }
                }
                
                // For true/false questions, create Yes/No options
                if ($questionData['question_type'] === 'true_false') {
                    // True option
                    $trueOption = new AnswerOption();
                    $trueOption->question_id = $question->question_id;
                    $trueOption->option_text = 'True';
                    $trueOption->is_correct = $questionData['correct_answer'] === 'true';
                    $trueOption->save();
                    
                    // False option
                    $falseOption = new AnswerOption();
                    $falseOption->question_id = $question->question_id;
                    $falseOption->option_text = 'False';
                    $falseOption->is_correct = $questionData['correct_answer'] === 'false';
                    $falseOption->save();
                }
            }
            
            DB::commit();
            
            return redirect()->route('instructor.exams.show', $exam->exam_id)
                ->with('success', 'Exam created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while creating the exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified exam.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $exam = Exam::with(['course', 'questions.options'])
            ->whereHas('course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->findOrFail($id);
        
        $attempts = ExamAttempt::with('student')
            ->where('exam_id', $id)
            ->latest()
            ->get();
        
        return view('instructor.exams.show', compact('exam', 'attempts'));
    }

    /**
     * Show the form for editing the specified exam.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $exam = Exam::with(['course', 'questions.options'])
            ->whereHas('course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->findOrFail($id);
        
        return view('instructor.exams.edit', compact('exam'));
    }

    /**
     * Update the specified exam in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validate the exam data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1|max:100',
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after_or_equal:available_from',
            'is_published' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Find the exam and check if it belongs to the authenticated instructor
        $exam = Exam::whereHas('course', function ($query) {
            $query->where('instructor_id', Auth::id());
        })->findOrFail($id);
        
        // Update the exam
        $exam->title = $request->title;
        $exam->description = $request->description;
        $exam->duration = $request->duration;
        $exam->passing_score = $request->passing_score;
        $exam->is_published = $request->has('is_published');
        $exam->available_from = $request->available_from;
        $exam->available_to = $request->available_to;
        $exam->save();
        
        return redirect()->route('instructor.exams.show', $exam->exam_id)
            ->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified exam from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Find the exam and check if it belongs to the authenticated instructor
        $exam = Exam::whereHas('course', function ($query) {
            $query->where('instructor_id', Auth::id());
        })->findOrFail($id);
        
        // Check if there are any attempts
        $hasAttempts = ExamAttempt::where('exam_id', $id)->exists();
        
        if ($hasAttempts) {
            return redirect()->back()
                ->with('error', 'Cannot delete exam because there are student attempts associated with it.');
        }
        
        // Delete the exam
        $exam->delete();
        
        return redirect()->route('instructor.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }
    
    /**
     * Add a question to an exam.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $examId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addQuestion(Request $request, $examId)
    {
        // Validate the question data
        $validator = Validator::make($request->all(), [
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'points' => 'required|integer|min:1',
            'options' => 'required_if:question_type,multiple_choice|array|min:2',
            'options.*.option_text' => 'required_if:question_type,multiple_choice|string',
            'options.*.is_correct' => 'required_if:question_type,multiple_choice|boolean',
            'correct_answer' => 'required_if:question_type,true_false|in:true,false',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Find the exam and check if it belongs to the authenticated instructor
        $exam = Exam::whereHas('course', function ($query) {
            $query->where('instructor_id', Auth::id());
        })->findOrFail($examId);
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Create the question
            $question = new Question();
            $question->exam_id = $exam->exam_id;
            $question->question_text = $request->question_text;
            $question->question_type = $request->question_type;
            $question->points = $request->points;
            $question->save();
            
            // Create options for multiple choice questions
            if ($request->question_type === 'multiple_choice' && isset($request->options)) {
                foreach ($request->options as $optionData) {
                    $option = new AnswerOption();
                    $option->question_id = $question->question_id;
                    $option->option_text = $optionData['option_text'];
                    $option->is_correct = $optionData['is_correct'] ?? false;
                    $option->save();
                }
            }
            
            // For true/false questions, create Yes/No options
            if ($request->question_type === 'true_false') {
                // True option
                $trueOption = new AnswerOption();
                $trueOption->question_id = $question->question_id;
                $trueOption->option_text = 'True';
                $trueOption->is_correct = $request->correct_answer === 'true';
                $trueOption->save();
                
                // False option
                $falseOption = new AnswerOption();
                $falseOption->question_id = $question->question_id;
                $falseOption->option_text = 'False';
                $falseOption->is_correct = $request->correct_answer === 'false';
                $falseOption->save();
            }
            
            DB::commit();
            
            return redirect()->route('instructor.exams.edit', $exam->exam_id)
                ->with('success', 'Question added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while adding the question: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update a question.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $questionId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateQuestion(Request $request, $questionId)
    {
        // Validate the question data
        $validator = Validator::make($request->all(), [
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Find the question and check if it belongs to the authenticated instructor
        $question = Question::whereHas('exam.course', function ($query) {
            $query->where('instructor_id', Auth::id());
        })->findOrFail($questionId);
        
        // Update the question
        $question->question_text = $request->question_text;
        $question->points = $request->points;
        $question->save();
        
        return redirect()->route('instructor.exams.edit', $question->exam_id)
            ->with('success', 'Question updated successfully.');
    }
    
    /**
     * Remove a question.
     *
     * @param  int  $questionId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeQuestion($questionId)
    {
        // Find the question and check if it belongs to the authenticated instructor
        $question = Question::whereHas('exam.course', function ($query) {
            $query->where('instructor_id', Auth::id());
        })->findOrFail($questionId);
        
        $examId = $question->exam_id;
        
        // Delete the question
        $question->delete();
        
        return redirect()->route('instructor.exams.edit', $examId)
            ->with('success', 'Question removed successfully.');
    }
    
    /**
     * View exam results.
     *
     * @param  int  $examId
     * @return \Illuminate\View\View
     */
    public function results($examId)
    {
        $exam = Exam::with(['course'])
            ->whereHas('course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->findOrFail($examId);
        
        $attempts = ExamAttempt::with('student')
            ->where('exam_id', $examId)
            ->latest()
            ->get();
        
        return view('instructor.exams.results', compact('exam', 'attempts'));
    }
    
    /**
     * View a specific student's exam attempt.
     *
     * @param  int  $attemptId
     * @return \Illuminate\View\View
     */
    public function viewAttempt($attemptId)
    {
        $attempt = ExamAttempt::with(['exam.questions.options', 'student'])
            ->whereHas('exam.course', function ($query) {
                $query->where('instructor_id', Auth::id());
            })
            ->findOrFail($attemptId);
        
        return view('instructor.exams.attempt', compact('attempt'));
    }
}
