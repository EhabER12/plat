<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParentRegistrationController extends Controller
{
    /**
     * Show the parent registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.parent-register');
    }

    /**
     * Handle a parent registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        Log::info('Parent registration attempt', [
            'email' => $request->email,
            'name' => $request->name,
            'student_name' => $request->student_name
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'student_name' => 'required|string|max:255',
            'birth_certificate' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:10240',
            'parent_id_card' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:10240',
            'additional_document' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240',
        ]);

        // Start a transaction to ensure both user and role are created
        DB::beginTransaction();

        try {
            // Create directories if they don't exist
            $profileDir = public_path('uploads/profile_images');
            $documentsDir = public_path('uploads/parent_documents');

            if (!file_exists($profileDir)) {
                mkdir($profileDir, 0755, true);
            }
            if (!file_exists($documentsDir)) {
                mkdir($documentsDir, 0755, true);
            }

            // Handle profile image upload if provided
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move($profileDir, $imageName);
                $profileImagePath = 'uploads/profile_images/' . $imageName;
            }

            // Create user with proper password field
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'profile_image' => $profileImagePath,
                'status' => true,
            ]);

            // Add parent role
            DB::table('user_roles')->insert([
                'user_id' => $user->user_id,
                'role' => 'parent',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // معالجة ملفات ولي الأمر
            $birthCertificatePath = null;
            if ($request->hasFile('birth_certificate')) {
                $file = $request->file('birth_certificate');
                $fileName = time() . '_birth_' . $file->getClientOriginalName();
                $file->move($documentsDir, $fileName);
                $birthCertificatePath = 'uploads/parent_documents/' . $fileName;
            }

            $parentIdCardPath = null;
            if ($request->hasFile('parent_id_card')) {
                $file = $request->file('parent_id_card');
                $fileName = time() . '_id_' . $file->getClientOriginalName();
                $file->move($documentsDir, $fileName);
                $parentIdCardPath = 'uploads/parent_documents/' . $fileName;
            }

            $additionalDocumentPath = null;
            if ($request->hasFile('additional_document')) {
                $file = $request->file('additional_document');
                $fileName = time() . '_additional_' . $file->getClientOriginalName();
                $file->move($documentsDir, $fileName);
                $additionalDocumentPath = 'uploads/parent_documents/' . $fileName;
            }

            // إنشاء سجل في جدول parent_student_relations
            DB::table('parent_student_relations')->insert([
                'parent_id' => $user->user_id,
                'student_id' => null, // سيتم تحديده لاحقاً عند التحقق
                'student_name' => $request->student_name,
                'verification_status' => 'pending',
                'birth_certificate' => $birthCertificatePath,
                'parent_id_card' => $parentIdCardPath,
                'additional_document' => $additionalDocumentPath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            Log::info('Parent registration successful', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'student_name' => $request->student_name
            ]);

            // Log the user in
            Auth::login($user);

            return redirect('/')->with('success', 'تم إنشاء حسابك بنجاح. سيتم مراجعة طلب ربط الطالب من قبل الإدارة.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Parent registration failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['email' => 'فشل في التسجيل. الرجاء المحاولة مرة أخرى: ' . $e->getMessage()])->withInput();
        }
    }
}