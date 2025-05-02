<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SectionController extends Controller
{
    /**
     * Store a newly created section in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $courseId)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Check if the course exists and belongs to the instructor
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        // Get the maximum position
        $maxPosition = CourseSection::where('course_id', $courseId)->max('position') ?? 0;

        // Create new section
        $section = new CourseSection([
            'course_id' => $courseId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'position' => $maxPosition + 1,
            'is_published' => true,
        ]);

        $section->save();

        return redirect()->route('instructor.courses.manage', $courseId)
            ->with('success', 'Section created successfully.');
    }

    /**
     * Update the specified section in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @param  int  $sectionId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $courseId, $sectionId)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        // Check if the course exists and belongs to the instructor
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        // Find the section
        $section = CourseSection::where('section_id', $sectionId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Update section
        $section->title = $validated['title'];
        $section->description = $validated['description'] ?? $section->description;
        
        if (isset($validated['is_published'])) {
            $section->is_published = $validated['is_published'];
        }

        $section->save();

        return redirect()->route('instructor.courses.manage', $courseId)
            ->with('success', 'Section updated successfully.');
    }

    /**
     * Remove the specified section from storage.
     *
     * @param  int  $courseId
     * @param  int  $sectionId
     * @return \Illuminate\Http\Response
     */
    public function destroy($courseId, $sectionId)
    {
        // Check if the course exists and belongs to the instructor
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        // Find the section
        $section = CourseSection::where('section_id', $sectionId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Delete section
        $section->delete();

        return redirect()->route('instructor.courses.manage', $courseId)
            ->with('success', 'Section deleted successfully.');
    }

    /**
     * Update positions of multiple sections.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function updatePositions(Request $request, $courseId)
    {
        $validated = $request->validate([
            'positions' => 'required|array',
            'positions.*.section_id' => 'required|integer|exists:course_sections,section_id',
            'positions.*.position' => 'required|integer|min:0',
        ]);

        // Check if the course exists and belongs to the instructor
        $course = Course::where('course_id', $courseId)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        // Update positions
        foreach ($validated['positions'] as $positionData) {
            CourseSection::where('section_id', $positionData['section_id'])
                ->where('course_id', $courseId)
                ->update(['position' => $positionData['position']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Section positions updated successfully'
        ]);
    }
}
