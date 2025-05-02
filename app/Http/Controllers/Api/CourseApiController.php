<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\CourseVideo;
use App\Models\CourseMaterial;
use App\Models\Enrollment;
use App\Models\CourseReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseApiController extends Controller
{
    /**
     * Display a listing of the courses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Course::query();

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by instructor
        if ($request->has('instructor_id')) {
            $query->where('instructor_id', $request->instructor_id);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by approval status
        if ($request->has('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        } else {
            // By default, only show approved courses
            $query->where('approval_status', 'approved');
        }

        // Search by title or description
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Sort by
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $allowedSortFields = ['title', 'price', 'created_at'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $courses = $query->with(['instructor', 'category'])->paginate($perPage);

        return response()->json([
            'courses' => $courses,
            'message' => 'Courses retrieved successfully'
        ]);
    }

    /**
     * Store a newly created course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Check if user is an instructor
        if (!Auth::user()->hasRole('instructor')) {
            return response()->json([
                'message' => 'You do not have permission to create courses'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'thumbnail' => 'nullable|image|max:2048', // 2MB max
            'duration' => 'nullable|integer',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'language' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course_thumbnails', 'public');
        }

        // Create course
        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'instructor_id' => Auth::id(),
            'category_id' => $request->category_id,
            'thumbnail' => $thumbnailPath,
            'duration' => $request->duration,
            'level' => $request->level ?? 'beginner',
            'language' => $request->language ?? 'en',
            'approval_status' => 'pending', // All new courses start as pending
        ]);

        return response()->json([
            'message' => 'Course created successfully',
            'course' => $course
        ], 201);
    }

    /**
     * Display the specified course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $course = Course::with([
            'instructor',
            'category',
            'videos',
            'materials',
            'reviews' => function($query) {
                $query->where('is_published', true);
            }
        ])->findOrFail($id);

        // Check if user is enrolled or is the instructor
        $isEnrolled = false;
        $isInstructor = false;

        if (Auth::check()) {
            $user = Auth::user();
            $isEnrolled = Enrollment::where('course_id', $id)
                ->where('student_id', $user->user_id)
                ->exists();

            $isInstructor = $course->instructor_id == $user->user_id;
        }

        // Calculate average rating
        $averageRating = $course->reviews()->avg('rating') ?? 0;
        $reviewsCount = $course->reviews()->count();

        return response()->json([
            'course' => $course,
            'is_enrolled' => $isEnrolled,
            'is_instructor' => $isInstructor,
            'average_rating' => $averageRating,
            'reviews_count' => $reviewsCount,
            'message' => 'Course retrieved successfully'
        ]);
    }

    /**
     * Update the specified course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        // Check if user is the instructor of this course
        if (Auth::id() != $course->instructor_id && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'message' => 'You do not have permission to update this course'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,category_id',
            'thumbnail' => 'nullable|image|max:2048', // 2MB max
            'duration' => 'nullable|integer',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'language' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }

            $thumbnailPath = $request->file('thumbnail')->store('course_thumbnails', 'public');
            $course->thumbnail = $thumbnailPath;
        }

        // Update course fields
        if ($request->has('title')) $course->title = $request->title;
        if ($request->has('description')) $course->description = $request->description;
        if ($request->has('price')) $course->price = $request->price;
        if ($request->has('category_id')) $course->category_id = $request->category_id;
        if ($request->has('duration')) $course->duration = $request->duration;
        if ($request->has('level')) $course->level = $request->level;
        if ($request->has('language')) $course->language = $request->language;

        // If admin is updating, they can change approval status
        if (Auth::user()->hasRole('admin') && $request->has('approval_status')) {
            $course->approval_status = $request->approval_status;
        }

        $course->save();

        return response()->json([
            'message' => 'Course updated successfully',
            'course' => $course
        ]);
    }

    /**
     * Remove the specified course from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        // Check if user is the instructor of this course or an admin
        if (Auth::id() != $course->instructor_id && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'message' => 'You do not have permission to delete this course'
            ], 403);
        }

        // Delete thumbnail if exists
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        // Delete course
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully'
        ]);
    }

    /**
     * Get courses created by the authenticated instructor.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function myCourses()
    {
        // Check if user is an instructor
        if (!Auth::user()->hasRole('instructor')) {
            return response()->json([
                'message' => 'You do not have permission to access this resource'
            ], 403);
        }

        $courses = Course::where('instructor_id', Auth::id())
            ->with(['category'])
            ->withCount('enrollments')
            ->get();

        return response()->json([
            'courses' => $courses,
            'message' => 'Instructor courses retrieved successfully'
        ]);
    }

    /**
     * Add a video to a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addVideo(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        // Check if user is the instructor of this course
        if (Auth::id() != $course->instructor_id) {
            return response()->json([
                'message' => 'You do not have permission to add videos to this course'
            ], 403);
        }

        // Validate based on video type
        if ($request->has('video_type') && $request->video_type === 'upload') {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video_type' => 'required|in:upload,external',
                'video_file' => 'required|file|mimes:mp4,webm,mov|max:204800', // 200MB max
                'thumbnail' => 'nullable|image|max:5120', // 5MB max
                'duration_seconds' => 'required|integer|min:1',
                'sequence_order' => 'nullable|integer',
                'is_free_preview' => 'nullable|boolean',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video_type' => 'required|in:upload,external',
                'video_url' => 'required_if:video_type,external|url',
                'thumbnail' => 'nullable|image|max:5120', // 5MB max
                'duration_seconds' => 'required|integer|min:1',
                'sequence_order' => 'nullable|integer',
                'is_free_preview' => 'nullable|boolean',
            ]);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create video record
        $video = new CourseVideo();
        $video->course_id = $courseId;
        $video->title = $request->title;
        $video->description = $request->description ?? null;
        $video->duration_seconds = $request->duration_seconds;
        $video->sequence_order = $request->sequence_order ?? 0;
        $video->is_free_preview = $request->has('is_free_preview');

        // Handle video based on type
        if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
            // Generate a unique filename
            $fileName = Str::slug($request->title) . '-' . time() . '.' . $request->file('video_file')->getClientOriginalExtension();
            $filePath = 'courses/' . $courseId . '/videos/' . $fileName;

            // Store the video file
            $request->file('video_file')->storeAs('public/' . dirname($filePath), basename($filePath));
            $video->video_path = 'storage/' . $filePath;
            $video->video_url = null;
        } else {
            // Store external video URL
            $video->video_url = $request->video_url;
            $video->video_path = null;
        }

        // Store thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            $thumbName = Str::slug($request->title) . '-thumb-' . time() . '.' . $request->file('thumbnail')->getClientOriginalExtension();
            $thumbPath = 'courses/' . $courseId . '/thumbnails/' . $thumbName;

            $request->file('thumbnail')->storeAs('public/' . dirname($thumbPath), basename($thumbPath));
            $video->thumbnail_url = 'storage/' . $thumbPath;
        }

        $video->save();

        return response()->json([
            'message' => 'Video added successfully',
            'video' => $video
        ], 201);
    }

    /**
     * Add a material to a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addMaterial(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        // Check if user is the instructor of this course
        if (Auth::id() != $course->instructor_id) {
            return response()->json([
                'message' => 'You do not have permission to add materials to this course'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'nullable|string|in:document,presentation,spreadsheet,pdf,image,archive',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle file upload
        $filePath = $request->file('file')->store('course_materials', 'public');
        $fileType = $request->type ?? $this->determineFileType($request->file('file'));

        $material = CourseMaterial::create([
            'course_id' => $courseId,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $request->file('file')->getSize(),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'message' => 'Material added successfully',
            'material' => $material
        ], 201);
    }

    /**
     * Determine file type based on mime type.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    private function determineFileType($file)
    {
        $mimeType = $file->getMimeType();

        if (Str::startsWith($mimeType, 'image/')) {
            return 'image';
        } elseif ($mimeType === 'application/pdf') {
            return 'pdf';
        } elseif (Str::contains($mimeType, ['word', 'msword', 'vnd.openxmlformats-officedocument.wordprocessingml'])) {
            return 'document';
        } elseif (Str::contains($mimeType, ['excel', 'spreadsheet', 'vnd.openxmlformats-officedocument.spreadsheetml'])) {
            return 'spreadsheet';
        } elseif (Str::contains($mimeType, ['powerpoint', 'presentation', 'vnd.openxmlformats-officedocument.presentationml'])) {
            return 'presentation';
        } elseif (Str::contains($mimeType, ['zip', 'rar', 'tar', 'gzip', 'x-compressed'])) {
            return 'archive';
        }

        return 'other';
    }

    /**
     * Submit a review for a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitReview(Request $request, $courseId)
    {
        // Check if user is enrolled in the course
        $isEnrolled = Enrollment::where('course_id', $courseId)
            ->where('student_id', Auth::id())
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'message' => 'You must be enrolled in the course to submit a review'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user has already reviewed this course
        $existingReview = CourseReview::where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            // Update existing review
            $existingReview->update([
                'rating' => $request->rating,
                'review_text' => $request->review_text,
                'is_published' => true, // Auto-publish or set to false if admin approval is required
            ]);

            $review = $existingReview;
            $message = 'Review updated successfully';
        } else {
            // Create new review
            $review = CourseReview::create([
                'course_id' => $courseId,
                'user_id' => Auth::id(),
                'rating' => $request->rating,
                'review_text' => $request->review_text,
                'is_published' => true, // Auto-publish or set to false if admin approval is required
            ]);

            $message = 'Review submitted successfully';
        }

        return response()->json([
            'message' => $message,
            'review' => $review
        ], 201);
    }

    /**
     * Get reviews for a course.
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReviews($courseId)
    {
        $course = Course::findOrFail($courseId);

        $reviews = CourseReview::where('course_id', $courseId)
            ->where('is_published', true)
            ->with('user:user_id,name,profile_picture')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $averageRating = CourseReview::where('course_id', $courseId)
            ->where('is_published', true)
            ->avg('rating') ?? 0;

        $ratingCounts = CourseReview::where('course_id', $courseId)
            ->where('is_published', true)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();

        // Fill in missing ratings with 0
        for ($i = 1; $i <= 5; $i++) {
            if (!isset($ratingCounts[$i])) {
                $ratingCounts[$i] = 0;
            }
        }

        return response()->json([
            'reviews' => $reviews,
            'average_rating' => $averageRating,
            'rating_counts' => $ratingCounts,
            'message' => 'Reviews retrieved successfully'
        ]);
    }
}
