<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AchievementController extends Controller
{
    /**
     * Display a listing of the achievements.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $achievements = Achievement::orderBy('created_at', 'desc')->get();
        return view('admin.achievements.index', compact('achievements'));
    }

    /**
     * Show the form for creating a new achievement.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.achievements.create');
    }

    /**
     * Store a newly created achievement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:255',
            'points' => 'required|integer|min:0',
            'criteria' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $achievement = new Achievement();
        $achievement->name = $request->name;
        $achievement->description = $request->description;
        $achievement->icon = $request->icon;
        $achievement->points = $request->points;
        $achievement->criteria = $request->criteria ? json_decode($request->criteria, true) : null;
        $achievement->is_active = $request->has('is_active');
        $achievement->save();

        return redirect()->route('admin.achievements.index')
            ->with('success', 'تم إنشاء الإنجاز بنجاح.');
    }

    /**
     * Display the specified achievement.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $achievement = Achievement::findOrFail($id);
        $students = $achievement->students()->paginate(10);
        
        return view('admin.achievements.show', compact('achievement', 'students'));
    }

    /**
     * Show the form for editing the specified achievement.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $achievement = Achievement::findOrFail($id);
        return view('admin.achievements.edit', compact('achievement'));
    }

    /**
     * Update the specified achievement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:255',
            'points' => 'required|integer|min:0',
            'criteria' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $achievement = Achievement::findOrFail($id);
        $achievement->name = $request->name;
        $achievement->description = $request->description;
        $achievement->icon = $request->icon;
        $achievement->points = $request->points;
        $achievement->criteria = $request->criteria ? json_decode($request->criteria, true) : null;
        $achievement->is_active = $request->has('is_active');
        $achievement->save();

        return redirect()->route('admin.achievements.index')
            ->with('success', 'تم تحديث الإنجاز بنجاح.');
    }

    /**
     * Remove the specified achievement from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $achievement = Achievement::findOrFail($id);
        $achievement->delete();

        return redirect()->route('admin.achievements.index')
            ->with('success', 'تم حذف الإنجاز بنجاح.');
    }
}
