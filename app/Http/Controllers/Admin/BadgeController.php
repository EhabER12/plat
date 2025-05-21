<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BadgeController extends Controller
{
    /**
     * Display a listing of the badges.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $badges = Badge::orderBy('created_at', 'desc')->get();
        return view('admin.badges.index', compact('badges'));
    }

    /**
     * Show the form for creating a new badge.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.badges.create');
    }

    /**
     * Store a newly created badge in storage.
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
            'level' => 'required|integer|min:1',
            'criteria' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $badge = new Badge();
        $badge->name = $request->name;
        $badge->description = $request->description;
        $badge->icon = $request->icon;
        $badge->level = $request->level;
        $badge->criteria = $request->criteria ? json_decode($request->criteria, true) : null;
        $badge->is_active = $request->has('is_active');
        $badge->save();

        return redirect()->route('admin.badges.index')
            ->with('success', 'تم إنشاء الشارة بنجاح.');
    }

    /**
     * Display the specified badge.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $badge = Badge::findOrFail($id);
        $students = $badge->students()->paginate(10);
        
        return view('admin.badges.show', compact('badge', 'students'));
    }

    /**
     * Show the form for editing the specified badge.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $badge = Badge::findOrFail($id);
        return view('admin.badges.edit', compact('badge'));
    }

    /**
     * Update the specified badge in storage.
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
            'level' => 'required|integer|min:1',
            'criteria' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $badge = Badge::findOrFail($id);
        $badge->name = $request->name;
        $badge->description = $request->description;
        $badge->icon = $request->icon;
        $badge->level = $request->level;
        $badge->criteria = $request->criteria ? json_decode($request->criteria, true) : null;
        $badge->is_active = $request->has('is_active');
        $badge->save();

        return redirect()->route('admin.badges.index')
            ->with('success', 'تم تحديث الشارة بنجاح.');
    }

    /**
     * Remove the specified badge from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $badge = Badge::findOrFail($id);
        $badge->delete();

        return redirect()->route('admin.badges.index')
            ->with('success', 'تم حذف الشارة بنجاح.');
    }
}
