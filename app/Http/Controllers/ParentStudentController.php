<?php

namespace App\Http\Controllers;

use App\Models\ParentStudentRelation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ParentStudentController extends Controller
{
    /**
     * Display a listing of parent-student relations for verification (admin view).
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Verify if user is admin
        $user = Auth::user();
        $userRoles = DB::table('user_roles')->where('user_id', $user->user_id)->pluck('role')->toArray();
        if (!in_array('admin', $userRoles)) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الصفحة.');
        }

        // Get all relations for admin to review
        $relations = ParentStudentRelation::with(['parent', 'student'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.parent_verifications.index', compact('relations'));
    }

    /**
     * Display relations that are pending verification.
     *
     * @return \Illuminate\Http\Response
     */
    public function pendingVerifications()
    {
        // Verify if user is admin
        $user = Auth::user();
        $userRoles = DB::table('user_roles')->where('user_id', $user->user_id)->pluck('role')->toArray();
        if (!in_array('admin', $userRoles)) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الصفحة.');
        }

        // Get pending relations
        $relations = ParentStudentRelation::with(['parent', 'student'])
            ->where('verification_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('admin.parent_verifications.pending', compact('relations'));
    }

    /**
     * Show the form for reviewing a specific parent-student relation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Verify if user is admin
        $user = Auth::user();
        $userRoles = DB::table('user_roles')->where('user_id', $user->user_id)->pluck('role')->toArray();
        if (!in_array('admin', $userRoles)) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الصفحة.');
        }

        // Get the relation with parent and student data
        $relation = ParentStudentRelation::with(['parent', 'student'])->findOrFail($id);
        
        // Get potential matching students based on name (for display)
        $matchingStudents = User::whereHas('roles', function($query) {
                $query->where('role', 'student');
            })
            ->where('name', 'like', '%' . $relation->student_name . '%')
            ->orderBy('name')
            ->get();
            
        // Get all students for the selection dropdown
        $allStudents = User::whereHas('roles', function($query) {
                $query->where('role', 'student');
            })
            ->orderBy('name')
            ->get();

        // Check if the students query is returning any results, if not, try direct DB query
        if ($allStudents->isEmpty()) {
            // Alternative approach using direct DB query
            $studentUserIds = DB::table('user_roles')
                ->where('role', 'student')
                ->pluck('user_id')
                ->toArray();
                
            $allStudents = User::whereIn('user_id', $studentUserIds)
                ->orderBy('name')
                ->get();
        }

        return view('admin.parent_verifications.show', compact('relation', 'matchingStudents', 'allStudents'));
    }

    /**
     * Process verification of a parent-student relation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request, $id)
    {
        // Verify if user is admin
        $user = Auth::user();
        $userRoles = DB::table('user_roles')->where('user_id', $user->user_id)->pluck('role')->toArray();
        if (!in_array('admin', $userRoles)) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الصفحة.');
        }

        $request->validate([
            'verification_status' => 'required|in:approved,rejected',
            'student_id' => 'required_if:verification_status,approved|nullable|exists:users,user_id',
            'verification_notes' => 'nullable|string|max:500',
        ]);

        $relation = ParentStudentRelation::findOrFail($id);
        $previousStatus = $relation->verification_status;
        
        $relation->verification_status = $request->verification_status;
        $relation->verification_notes = $request->verification_notes;
        $relation->verified_at = now();
        $relation->verified_by = $user->user_id;
        
        if ($request->verification_status === 'approved') {
            $relation->student_id = $request->student_id;
        }
        
        $relation->save();

        // نتجاهل إرسال الإشعارات مؤقتًا لتجنب مشكلة الجدول
        // سنكتفي بعرض رسالة نجاح للمدير

        return redirect()->route('admin.parent-verifications.index')
            ->with('success', 'تم تحديث حالة التحقق بنجاح. يمكن لولي الأمر الآن الوصول إلى لوحة التحكم ومتابعة الطالب.');
    }

    /**
     * Show the parent dashboard with student information.
     *
     * @return \Illuminate\Http\Response
     */
    public function parentDashboard()
    {
        // Verify if user is parent
        $user = Auth::user();
        $userRoles = DB::table('user_roles')->where('user_id', $user->user_id)->pluck('role')->toArray();
        if (!in_array('parent', $userRoles)) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الصفحة.');
        }

        // Get verified students for this parent
        $students = $user->verifiedStudents;
        $pendingRelations = ParentStudentRelation::where('parent_id', $user->user_id)
            ->where('verification_status', 'pending')
            ->get();
        $rejectedRelations = ParentStudentRelation::where('parent_id', $user->user_id)
            ->where('verification_status', 'rejected')
            ->get();

        return view('parent.dashboard', compact('students', 'pendingRelations', 'rejectedRelations'));
    }

    /**
     * Show student activity details for parent.
     *
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function studentActivity($studentId)
    {
        // Verify if user is parent
        $user = Auth::user();
        $userRoles = DB::table('user_roles')->where('user_id', $user->user_id)->pluck('role')->toArray();
        if (!in_array('parent', $userRoles)) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الصفحة.');
        }

        // Verify if this parent is authorized to view this student's data
        $hasAccess = ParentStudentRelation::where('parent_id', $user->user_id)
            ->where('student_id', $studentId)
            ->where('verification_status', 'approved')
            ->exists();
            
        if (!$hasAccess) {
            return redirect()->route('parent.dashboard')->with('error', 'غير مصرح لك بعرض بيانات هذا الطالب.');
        }

        // Get student information
        $student = User::findOrFail($studentId);
        
        // Get student's activity
        $loginActivities = $student->activities()
            ->where('activity_type', 'login')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        $courseActivities = $student->activities()
            ->where('activity_type', 'course_view')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Get enrolled courses
        $enrolledCourses = $student->enrolledCourses;
        
        return view('parent.student_activity', compact('student', 'loginActivities', 'courseActivities', 'enrolledCourses'));
    }

    /**
     * Download document attached to parent-student relation.
     *
     * @param  int  $id
     * @param  string  $documentType
     * @return \Illuminate\Http\Response
     */
    public function downloadDocument($id, $documentType)
    {
        // Verify if user is admin
        $user = Auth::user();
        $userRoles = DB::table('user_roles')->where('user_id', $user->user_id)->pluck('role')->toArray();
        if (!in_array('admin', $userRoles)) {
            return redirect()->route('home')->with('error', 'غير مصرح لك بالوصول إلى هذه الصفحة.');
        }

        $relation = ParentStudentRelation::findOrFail($id);
        
        if (!in_array($documentType, ['birth_certificate', 'parent_id_card', 'additional_document'])) {
            return back()->with('error', 'نوع المستند غير صالح.');
        }
        
        if (empty($relation->$documentType)) {
            return back()->with('error', 'المستند غير موجود.');
        }
        
        $filePath = public_path($relation->$documentType);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'الملف غير موجود.');
        }
        
        return response()->download($filePath);
    }
}
