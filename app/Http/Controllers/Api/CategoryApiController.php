<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryApiController extends Controller
{
    /**
     * Display a listing of the categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::withCount('courses')->get();

        return response()->json([
            'categories' => $categories,
            'message' => 'Categories retrieved successfully'
        ]);
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Check if user is an admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'message' => 'You do not have permission to create categories'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:categories,category_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
    }

    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = Category::with('parent', 'children')
            ->withCount('courses')
            ->findOrFail($id);

        // Get courses in this category
        $courses = Course::where('category_id', $id)
            ->where('approval_status', 'approved')
            ->with('instructor:user_id,name,profile_picture')
            ->paginate(10);

        return response()->json([
            'category' => $category,
            'courses' => $courses,
            'message' => 'Category retrieved successfully'
        ]);
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Check if user is an admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'message' => 'You do not have permission to update categories'
            ], 403);
        }

        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id . ',category_id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:categories,category_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Prevent category from being its own parent
        if ($request->has('parent_id') && $request->parent_id == $id) {
            return response()->json([
                'message' => 'A category cannot be its own parent'
            ], 422);
        }

        // Update category fields
        if ($request->has('name')) {
            $category->name = $request->name;
            $category->slug = Str::slug($request->name);
        }
        if ($request->has('description')) $category->description = $request->description;
        if ($request->has('icon')) $category->icon = $request->icon;
        if ($request->has('parent_id')) $category->parent_id = $request->parent_id;

        $category->save();

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ]);
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Check if user is an admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'message' => 'You do not have permission to delete categories'
            ], 403);
        }

        $category = Category::findOrFail($id);

        // Check if category has courses
        $coursesCount = Course::where('category_id', $id)->count();
        if ($coursesCount > 0) {
            return response()->json([
                'message' => 'Cannot delete category with existing courses',
                'courses_count' => $coursesCount
            ], 400);
        }

        // Check if category has children
        $childrenCount = Category::where('parent_id', $id)->count();
        if ($childrenCount > 0) {
            return response()->json([
                'message' => 'Cannot delete category with child categories',
                'children_count' => $childrenCount
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Get courses in a category.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourses($id, Request $request)
    {
        $category = Category::findOrFail($id);

        $query = Course::where('category_id', $id)
            ->where('approval_status', 'approved');

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->level);
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
        $courses = $query->with('instructor:user_id,name,profile_picture')->paginate($perPage);

        return response()->json([
            'category' => $category,
            'courses' => $courses,
            'message' => 'Category courses retrieved successfully'
        ]);
    }
}
