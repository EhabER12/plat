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
        $course = Course::where('id', $courseId)
            ->where('instructor_id', $user->id)
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
            'material_file' => 'required|file|max:51200', // 50MB max
        ]);
        
        // Handle file upload
        if ($request->hasFile('material_file')) {
            $file = $request->file('material_file');
            $fileName = Str::slug($validated['title']) . '-' . time() . '.' . $file->getClientOriginalExtension();
            
            // Move file to storage
            $filePath = 'courses/' . $courseId . '/materials/' . $fileName;
            $file->storeAs('public/' . dirname($filePath), basename($filePath));
            
            // Create material record
            $material = new CourseMaterial();
            $material->course_id = $courseId;
            $material->title = $validated['title'];
            $material->description = $validated['description'] ?? null;
            $material->file_path = 'storage/' . $filePath;
            $material->file_type = $file->getClientOriginalExtension();
            $material->file_size = $file->getSize();
            $material->download_count = 0;
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
        $course = Course::where('id', $courseId)
            ->where('instructor_id', $user->id)
            ->firstOrFail();
        
        // Find the material
        $material = CourseMaterial::where('id', $materialId)
            ->where('course_id', $courseId)
            ->firstOrFail();
        
        // Delete the file
        if (file_exists(public_path($material->file_path))) {
            unlink(public_path($material->file_path));
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
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($courseId, $materialId)
    {
        // Find the material
        $material = CourseMaterial::where('id', $materialId)
            ->where('course_id', $courseId)
            ->firstOrFail();
        
        // Increment download count
        $material->download_count += 1;
        $material->save();
        
        // Return the file for download
        return response()->download(public_path($material->file_path), $material->title . '.' . $material->file_type);
    }
} 