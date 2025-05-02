<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseVideo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    /**
     * Show the video data for editing.
     *
     * @param  int  $courseId
     * @param  int  $videoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($courseId, $videoId)
    {
        // التحقق من وجود الكورس والفيديو
        $course = Course::findOrFail($courseId);
        $video = CourseVideo::where('course_id', $courseId)
            ->where('video_id', $videoId)
            ->firstOrFail();

        return response()->json([
            'video' => $video
        ]);
    }

    /**
     * Store a newly created video in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $courseId)
    {
        // التحقق من وجود الكورس
        $course = Course::findOrFail($courseId);

        // التحقق من البيانات
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_type' => 'required|in:upload,external',
            'video_file' => 'required_if:video_type,upload|file|mimes:mp4,webm,mov|max:204800', // 200MB max
            'video_url' => 'required_if:video_type,external|url',
            'thumbnail' => 'nullable|image|max:5120', // 5MB max
            'duration_seconds' => 'required|integer|min:1',
            'sequence_order' => 'nullable|integer|min:0',
            'is_free_preview' => 'nullable|boolean',
        ]);

        // إنشاء سجل فيديو جديد
        $video = new CourseVideo();
        $video->course_id = $courseId;
        $video->title = $validated['title'];
        $video->description = $validated['description'] ?? null;
        $video->duration_seconds = $validated['duration_seconds'];
        $video->sequence_order = $validated['sequence_order'] ?? 0;
        $video->is_free_preview = $request->has('is_free_preview');

        // معالجة الفيديو حسب النوع
        if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
            // إنشاء اسم فريد للملف
            $fileName = Str::slug($validated['title']) . '-' . time() . '.' . $request->file('video_file')->getClientOriginalExtension();
            $filePath = 'courses/' . $courseId . '/videos/' . $fileName;

            // تخزين ملف الفيديو
            $request->file('video_file')->storeAs('public/' . dirname($filePath), basename($filePath));
            $video->video_path = 'storage/' . $filePath;
            $video->video_url = null;
        } else {
            // تخزين رابط الفيديو الخارجي
            $video->video_url = $validated['video_url'];
            $video->video_path = null;
        }

        // تخزين الصورة المصغرة إذا تم توفيرها
        if ($request->hasFile('thumbnail')) {
            $thumbName = Str::slug($validated['title']) . '-thumb-' . time() . '.' . $request->file('thumbnail')->getClientOriginalExtension();
            $thumbPath = 'courses/' . $courseId . '/thumbnails/' . $thumbName;

            $request->file('thumbnail')->storeAs('public/' . dirname($thumbPath), basename($thumbPath));
            $video->thumbnail_url = 'storage/' . $thumbPath;
        }

        $video->save();

        // التحقق مما إذا كان الطلب AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تمت إضافة الفيديو بنجاح',
                'video' => $video
            ]);
        }

        return redirect()->route('admin.courses.show', $courseId)
            ->with('success', 'تمت إضافة الفيديو بنجاح');
    }

    /**
     * Update the specified video in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @param  int  $videoId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $courseId, $videoId)
    {
        // التحقق من وجود الكورس والفيديو
        $course = Course::findOrFail($courseId);
        $video = CourseVideo::where('course_id', $courseId)
            ->where('video_id', $videoId)
            ->firstOrFail();

        // التحقق من البيانات
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_type' => 'required|in:upload,external,unchanged',
            'video_file' => 'required_if:video_type,upload|file|mimes:mp4,webm,mov|max:204800', // 200MB max
            'video_url' => 'required_if:video_type,external|url',
            'thumbnail' => 'nullable|image|max:5120', // 5MB max
            'duration_seconds' => 'required|integer|min:1',
            'sequence_order' => 'required|integer|min:0',
            'is_free_preview' => 'nullable|boolean',
        ]);

        // تحديث بيانات الفيديو
        $video->title = $validated['title'];
        $video->description = $validated['description'] ?? null;
        $video->duration_seconds = $validated['duration_seconds'];
        $video->sequence_order = $validated['sequence_order'];
        $video->is_free_preview = $request->has('is_free_preview');

        // معالجة الفيديو حسب النوع
        if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
            // حذف الملف القديم إذا كان موجودًا
            if ($video->video_path) {
                $oldPath = str_replace('storage/', 'public/', $video->video_path);
                Storage::delete($oldPath);
            }

            // إنشاء اسم فريد للملف
            $fileName = Str::slug($validated['title']) . '-' . time() . '.' . $request->file('video_file')->getClientOriginalExtension();
            $filePath = 'courses/' . $courseId . '/videos/' . $fileName;

            // تخزين ملف الفيديو
            $request->file('video_file')->storeAs('public/' . dirname($filePath), basename($filePath));
            $video->video_path = 'storage/' . $filePath;
            $video->video_url = null;
        } elseif ($request->video_type === 'external') {
            // تحديث رابط الفيديو الخارجي
            $video->video_url = $validated['video_url'];

            // حذف ملف الفيديو القديم إذا كان موجودًا
            if ($video->video_path) {
                $oldPath = str_replace('storage/', 'public/', $video->video_path);
                Storage::delete($oldPath);
                $video->video_path = null;
            }
        }

        // تحديث الصورة المصغرة إذا تم توفيرها
        if ($request->hasFile('thumbnail')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($video->thumbnail_url) {
                $oldThumbPath = str_replace('storage/', 'public/', $video->thumbnail_url);
                Storage::delete($oldThumbPath);
            }

            $thumbName = Str::slug($validated['title']) . '-thumb-' . time() . '.' . $request->file('thumbnail')->getClientOriginalExtension();
            $thumbPath = 'courses/' . $courseId . '/thumbnails/' . $thumbName;

            $request->file('thumbnail')->storeAs('public/' . dirname($thumbPath), basename($thumbPath));
            $video->thumbnail_url = 'storage/' . $thumbPath;
        }

        $video->save();

        return redirect()->route('admin.courses.show', $courseId)
            ->with('success', 'تم تحديث الفيديو بنجاح');
    }

    /**
     * Remove the specified video from storage.
     *
     * @param  int  $courseId
     * @param  int  $videoId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($courseId, $videoId)
    {
        // التحقق من وجود الكورس والفيديو
        $course = Course::findOrFail($courseId);
        $video = CourseVideo::where('course_id', $courseId)
            ->where('video_id', $videoId)
            ->firstOrFail();

        // حذف ملفات الفيديو والصور المصغرة
        if ($video->video_path) {
            $videoPath = str_replace('storage/', 'public/', $video->video_path);
            Storage::delete($videoPath);
        }

        if ($video->thumbnail_url) {
            $thumbPath = str_replace('storage/', 'public/', $video->thumbnail_url);
            Storage::delete($thumbPath);
        }

        // حذف سجل الفيديو
        $video->delete();

        return redirect()->route('admin.courses.show', $courseId)
            ->with('success', 'تم حذف الفيديو بنجاح');
    }
}
