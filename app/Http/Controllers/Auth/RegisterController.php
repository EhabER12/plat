<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return redirect('/login?show=register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:student,parent,instructor',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
        
        // إضافة قواعد التحقق لحقول ولي الأمر إذا تم اختيار دور "parent"
        if ($request->role === 'parent') {
            $validationRules['student_name'] = 'required|string|max:255';
            $validationRules['birth_certificate'] = 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:10240';
            $validationRules['parent_id_card'] = 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:10240';
            $validationRules['additional_document'] = 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240';
        }
        
        $request->validate($validationRules);

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

            // Add user role from selection
            DB::table('user_roles')->insert([
                'user_id' => $user->user_id,
                'role' => $request->role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // إذا كان الدور هو "parent"، أضف سجل في جدول parent_student_relations
            if ($request->role === 'parent') {
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
            }

            DB::commit();

            // Log the user in
            Auth::login($user);

            // Redirect based on user role
            if ($request->role === 'instructor') {
                return redirect()->route('instructor.verification.form')
                    ->with('success', 'Your account has been created successfully. Please complete your instructor profile for verification.');
            } else if ($request->role === 'parent') {
                return redirect('/')->with('success', 'تم إنشاء حسابك بنجاح. سيتم مراجعة طلب ربط الطالب من قبل الإدارة.');
            }

            return redirect('/');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput();
        }
    }
}
