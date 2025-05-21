<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentMotivationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MotivationController extends Controller
{
    protected $motivationService;

    /**
     * Create a new controller instance.
     *
     * @param StudentMotivationService $motivationService
     */
    public function __construct(StudentMotivationService $motivationService)
    {
        $this->middleware('auth');
        $this->motivationService = $motivationService;
    }

    /**
     * Display the student's motivation dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $student = Auth::user();
        $motivationalContent = $this->motivationService->getMotivationalContent($student);

        return view('student.motivation.index', [
            'motivationalContent' => $motivationalContent,
            'student' => $student
        ]);
    }

    /**
     * Display the student's badges.
     *
     * @return \Illuminate\View\View
     */
    public function badges()
    {
        $student = Auth::user();
        $badges = $student->badges()->with('badge')->get();

        // Create a motivationalContent array with badges
        $motivationalContent = [
            'badges' => []
        ];

        // Transform badges into the expected format
        foreach ($badges as $badge) {
            $motivationalContent['badges'][] = [
                'name' => $badge->badge->name ?? 'شارة',
                'description' => $badge->badge->description ?? 'وصف الشارة',
                'icon' => $badge->badge->icon ?? 'award',
                'level' => $badge->badge->level ?? 1,
            ];
        }

        return view('student.motivation.badges', [
            'badges' => $badges,
            'student' => $student,
            'motivationalContent' => $motivationalContent
        ]);
    }

    /**
     * Display the student's achievements.
     *
     * @return \Illuminate\View\View
     */
    public function achievements()
    {
        $student = Auth::user();
        $achievements = $student->achievements()->with('achievement')->get();

        // Create a motivationalContent array with achievements
        $motivationalContent = [
            'achievements' => []
        ];

        // Transform achievements into the expected format
        foreach ($achievements as $achievement) {
            $motivationalContent['achievements'][] = [
                'name' => $achievement->achievement->name ?? 'إنجاز',
                'description' => $achievement->achievement->description ?? 'وصف الإنجاز',
                'date_earned' => $achievement->earned_at ? $achievement->earned_at->format('Y-m-d') : date('Y-m-d')
            ];
        }

        return view('student.motivation.achievements', [
            'achievements' => $achievements,
            'student' => $student,
            'motivationalContent' => $motivationalContent
        ]);
    }
}
