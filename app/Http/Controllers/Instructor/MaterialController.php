<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialController extends Controller
{
    /**
     * Store a newly created material resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $courseId)
    {
        $user = Auth::user();
        
        // Verify the instructor owns this course
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', $user->user_id)
            ->firstOrFail();
        
        // Check if course_materials table exists
        if (!Schema::hasTable('course_materials')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Materials feature is not available at the moment.'], 503);
            }
            return redirect()->back()->with('error', 'Materials feature is not available at the moment.');
        }
        
        // Validate request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'section_id' => 'nullable|integer|exists:course_sections,section_id',
            'material_file' => 'required|file|max:51200', // 50MB max
        ]);
        
        // Handle file upload
        if ($request->hasFile('material_file')) {
            $file = $request->file('material_file');
            $fileName = Str::slug($validated['title']) . '-' . time() . '.' . $file->getClientOriginalExtension();
            
            // Create storage directories if they don't exist
            $relativePath = 'courses/' . $courseId . '/materials';
            $fullStoragePath = storage_path('app/public/' . $relativePath);
            
            if (!File::exists($fullStoragePath)) {
                File::makeDirectory($fullStoragePath, 0755, true);
            }
            
            // Store the file in public disk
            $path = $file->storeAs($relativePath, $fileName, 'public');
            
            // Create material record
            $material = new CourseMaterial();
            $material->course_id = $courseId;
            $material->title = $validated['title'];
            $material->description = $validated['description'] ?? null;
            
            // Associate with section if provided
            if (!empty($validated['section_id'])) {
                $material->section_id = $validated['section_id'];
            }
            
            $material->setAttribute('file_url', $path);  // Explicitly set the attribute
            $material->file_type = $file->getClientOriginalExtension();
            $material->file_size = $file->getSize();
            $material->save();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Material uploaded successfully',
                    'material' => $material
                ]);
            }
            
            return redirect()->back()->with('success', 'Material uploaded successfully!');
        }
        
        if ($request->expectsJson()) {
            return response()->json(['error' => 'No file provided'], 422);
        }
        
        return redirect()->back()->with('error', 'No file provided');
    }
    
    /**
     * Remove the specified material resource.
     *
     * @param  int  $courseId
     * @param  int  $materialId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $courseId, $materialId)
    {
        $user = Auth::user();
        
        // Verify the instructor owns this course
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', $user->user_id)
            ->firstOrFail();
        
        // Find the material
        $material = CourseMaterial::where('material_id', $materialId)
            ->where('course_id', $courseId)
            ->firstOrFail();
        
        // Get the file URL from attributes
        $fileUrl = $material->getAttribute('file_url');
        
        // Delete the file if it exists
        if ($fileUrl && Storage::disk('public')->exists($fileUrl)) {
            Storage::disk('public')->delete($fileUrl);
        }
        
        // Delete the record
        $material->delete();
        
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Material deleted successfully']);
        }
        
        return redirect()->back()->with('success', 'Material deleted successfully!');
    }
    
    /**
     * Download a course material.
     *
     * @param  int  $courseId
     * @param  int  $materialId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($courseId, $materialId)
    {
        // Find the material
        $material = CourseMaterial::where('material_id', $materialId)
            ->where('course_id', $courseId)
            ->firstOrFail();
        
        // Create the file name
        $fileName = $material->title . '.' . $material->file_type;
        
        // Get the file url from attributes
        $fileUrl = $material->getAttribute('file_url');
        
        if (!$fileUrl) {
            return redirect()->back()->with('error', 'الملف غير موجود. يرجى الاتصال بالمسؤول.');
        }
        
        // Check if file exists in storage and return it for download
        $filePath = Storage::disk('public')->path($fileUrl);
        
        if (file_exists($filePath)) {
            return response()->download($filePath, $fileName);
        }
        
        // If file not found, return an error
        return redirect()->back()->with('error', 'الملف غير موجود. يرجى الاتصال بالمسؤول.');
    }
} 