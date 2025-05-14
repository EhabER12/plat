<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
            // Handle profile image upload if provided
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/profile_images'), $imageName);
                $profileImagePath = 'uploads/profile_images/' . $imageName;
            }

            // Create user with proper password field
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
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
                $file->move(public_path('uploads/parent_documents'), $fileName);
                $birthCertificatePath = 'uploads/parent_documents/' . $fileName;
            }
            
            $parentIdCardPath = null;
            if ($request->hasFile('parent_id_card')) {
                $file = $request->file('parent_id_card');
                $fileName = time() . '_id_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/parent_documents'), $fileName);
                $parentIdCardPath = 'uploads/parent_documents/' . $fileName;
            }
            
            $additionalDocumentPath = null;
            if ($request->hasFile('additional_document')) {
                $file = $request->file('additional_document');
                $fileName = time() . '_additional_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/parent_documents'), $fileName);
                $additionalDocumentPath = 'uploads/parent_documents/' . $fileName;
            }
            
            // إنشاء سجل في جدول parent_student_relations
            DB::table('parent_student_relations')->insert([
                'parent_id' => $user->user_id,
                'student_name' => $request->student_name,
                'verification_status' => 'pending',
                'birth_certificate' => $birthCertificatePath,
                'parent_id_card' => $parentIdCardPath,
                'additional_document' => $additionalDocumentPath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Log the user in
            Auth::login($user);

            return redirect('/')->with('success', 'تم إنشاء حسابك بنجاح. سيتم مراجعة طلب ربط الطالب من قبل الإدارة.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['email' => 'Registration failed. Please try again: ' . $e->getMessage()])->withInput();
        }
    }
} 