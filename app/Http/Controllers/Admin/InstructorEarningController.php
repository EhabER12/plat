<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorEarning;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InstructorEarningController extends Controller
{
    /**
     * Display a listing of all instructor earnings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get statistics
        $totalEarnings = InstructorEarning::sum('amount');
        $pendingEarnings = InstructorEarning::where('status', InstructorEarning::STATUS_PENDING)->sum('amount');
        $availableEarnings = InstructorEarning::where('status', InstructorEarning::STATUS_AVAILABLE)->sum('amount');
        $withdrawnEarnings = InstructorEarning::where('status', InstructorEarning::STATUS_WITHDRAWN)->sum('amount');
        $platformFees = InstructorEarning::sum('platform_fee');
        
        // Get top earning instructors
        $topInstructors = User::whereHas('roles', function($query) {
                $query->where('role', 'instructor');
            })
            ->withSum(['earnings' => function($query) {
                $query->where('status', '!=', InstructorEarning::STATUS_CANCELLED);
            }], 'amount')
            ->orderByDesc('earnings_sum_amount')
            ->take(10)
            ->get();
        
        // Get recent earnings
        $recentEarnings = InstructorEarning::with(['instructor', 'course', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.instructor_earnings.index', compact(
            'totalEarnings',
            'pendingEarnings',
            'availableEarnings',
            'withdrawnEarnings',
            'platformFees',
            'topInstructors',
            'recentEarnings'
        ));
    }

    /**
     * Display a listing of all withdrawal requests.
     *
     * @return \Illuminate\View\View
     */
    public function withdrawals()
    {
        // Get statistics
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        $pendingAmount = Withdrawal::where('status', 'pending')->sum('amount');
        $completedWithdrawals = Withdrawal::where('status', 'completed')->count();
        $completedAmount = Withdrawal::where('status', 'completed')->sum('amount');
        
        // Get withdrawal requests with pagination
        $withdrawals = Withdrawal::with('instructor')
            ->orderBy('requested_at', 'desc')
            ->paginate(20);
        
        return view('admin.instructor_earnings.withdrawals', compact(
            'pendingWithdrawals',
            'pendingAmount',
            'completedWithdrawals',
            'completedAmount',
            'withdrawals'
        ));
    }

    /**
     * Display the specified withdrawal request.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showWithdrawal($id)
    {
        $withdrawal = Withdrawal::with('instructor')
            ->findOrFail($id);
            
        // Get the earnings included in this withdrawal
        $earnings = InstructorEarning::where('withdrawal_id', $withdrawal->withdrawal_id)
            ->with(['course', 'payment'])
            ->get();
            
        return view('admin.instructor_earnings.show_withdrawal', compact('withdrawal', 'earnings'));
    }

    /**
     * Process the specified withdrawal request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function processWithdrawal(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
            'transfer_receipt' => 'required_if:action,approve|image|mimes:jpeg,png,jpg,gif|max:4096'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $validator->errors()->first()], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Get the withdrawal with related instructor and earnings
            $withdrawal = Withdrawal::with(['instructor', 'earnings'])->findOrFail($id);
            
            // Check if the withdrawal is already processed
            if ($withdrawal->status !== 'pending') {
                Log::warning('Attempted to process already processed withdrawal', [
                    'admin_id' => Auth::id(),
                    'withdrawal_id' => $id,
                    'current_status' => $withdrawal->status
                ]);
                
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'لا يمكن معالجة هذا الطلب، حيث أنه تم معالجته بالفعل (الحالة الحالية: ' . $withdrawal->status . ')'], 400);
                }
                
                return redirect()->back()
                    ->with('error', 'لا يمكن معالجة هذا الطلب، حيث أنه تم معالجته بالفعل (الحالة الحالية: ' . $withdrawal->status . ')');
            }
            
            $adminId = Auth::id();
            $adminName = Auth::user()->name;
            
            Log::info('Processing withdrawal request', [
                'admin_id' => $adminId,
                'admin_name' => $adminName,
                'withdrawal_id' => $id,
                'action' => $request->action,
                'instructor_id' => $withdrawal->instructor_id,
                'instructor_name' => $withdrawal->instructor->name,
                'amount' => $withdrawal->amount
            ]);
            
            if ($request->action === 'approve') {
                $transferReceiptPath = null;
                if ($request->hasFile('transfer_receipt')) {
                    $file = $request->file('transfer_receipt');
                    $filename = 'receipt_' . $withdrawal->withdrawal_id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $transferReceiptPath = $file->storeAs('uploads/receipts', $filename, 'public');
                    
                    Log::info('Transfer receipt uploaded', [
                        'withdrawal_id' => $id,
                        'filename' => $filename,
                        'path' => $transferReceiptPath
                    ]);
                }
                
                // Approve the withdrawal
                $withdrawal->update([
                    'status' => 'completed',
                    'processed_at' => now(),
                    'processed_by' => $adminId,
                    'notes' => $request->notes,
                    'transfer_receipt' => $transferReceiptPath ? 'storage/' . $transferReceiptPath : null
                ]);

                // Update all connected earnings statuses
                foreach ($withdrawal->earnings as $earning) {
                    $earning->update([
                        'status' => InstructorEarning::STATUS_WITHDRAWN
                    ]);
                    
                    Log::info('Earning status updated to withdrawn', [
                        'earning_id' => $earning->id,
                        'amount' => $earning->amount,
                        'course_id' => $earning->course_id
                    ]);
                }

                // Send notification to instructor
                $withdrawal->instructor->notify(new \App\Notifications\WithdrawalProcessed(
                    'تمت الموافقة على طلب السحب الخاص بك',
                    'تمت الموافقة على طلب سحب الأموال بقيمة $' . number_format($withdrawal->amount, 2),
                    'success',
                    route('instructor.earnings.show-withdrawal', $withdrawal->withdrawal_id)
                ));
                
                Log::info('Withdrawal request approved', [
                    'withdrawal_id' => $id,
                    'admin_id' => $adminId,
                    'admin_name' => $adminName,
                    'amount' => $withdrawal->amount,
                    'instructor_id' => $withdrawal->instructor_id
                ]);

                $message = 'تمت الموافقة على طلب السحب بنجاح وتم إشعار المدرس.';
            } else {
                // Reject the withdrawal
                $withdrawal->update([
                    'status' => 'rejected',
                    'processed_at' => now(),
                    'processed_by' => $adminId,
                    'notes' => $request->notes
                ]);
                
                // Get the earnings included in this withdrawal
                $earnings = InstructorEarning::where('withdrawal_id', $withdrawal->withdrawal_id)->get();
                
                // Mark the earnings as available again
                foreach ($earnings as $earning) {
                    $earning->update([
                        'status' => InstructorEarning::STATUS_AVAILABLE,
                        'withdrawal_id' => null
                    ]);
                    
                    Log::info('Earning status reset to available', [
                        'earning_id' => $earning->id,
                        'amount' => $earning->amount,
                        'course_id' => $earning->course_id
                    ]);
                }

                // Send notification to instructor
                $withdrawal->instructor->notify(new \App\Notifications\WithdrawalProcessed(
                    'تم رفض طلب السحب الخاص بك',
                    'تم رفض طلب سحب الأموال بقيمة $' . number_format($withdrawal->amount, 2) . '. السبب: ' . $request->notes,
                    'error',
                    route('instructor.earnings.show-withdrawal', $withdrawal->withdrawal_id)
                ));
                
                Log::info('Withdrawal request rejected', [
                    'withdrawal_id' => $id,
                    'admin_id' => $adminId,
                    'admin_name' => $adminName,
                    'amount' => $withdrawal->amount,
                    'instructor_id' => $withdrawal->instructor_id,
                    'reason' => $request->notes
                ]);
                
                $message = 'تم رفض طلب السحب وإعادة المبلغ إلى رصيد المدرب المتاح.';
            }
            
            DB::commit();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('admin.instructor-earnings.withdrawals')
                ]);
            }
            
            return redirect()->route('admin.instructor-earnings.withdrawals')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process withdrawal request', [
                'error' => $e->getMessage(),
                'withdrawal_id' => $id,
                'admin_id' => Auth::id(),
                'action' => $request->action ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'حدث خطأ أثناء معالجة طلب السحب: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء معالجة طلب السحب: ' . $e->getMessage());
        }
    }

    /**
     * Display the commission settings.
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        // Get commission settings
        $instructorRate = Setting::where('key', 'instructor_commission_rate')->first()->value ?? 70;
        $platformRate = Setting::where('key', 'platform_commission_rate')->first()->value ?? 30;
        $minWithdrawalAmount = Setting::where('key', 'minimum_withdrawal_amount')->first()->value ?? 100;
        $processingDays = Setting::where('key', 'withdrawal_processing_days')->first()->value ?? 3;
        
        return view('admin.instructor_earnings.settings', compact(
            'instructorRate',
            'platformRate',
            'minWithdrawalAmount',
            'processingDays'
        ));
    }

    /**
     * Update the commission settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instructor_rate' => 'required|numeric|min:0|max:100',
            'platform_rate' => 'required|numeric|min:0|max:100',
            'min_withdrawal_amount' => 'required|numeric|min:0',
            'processing_days' => 'required|numeric|min:1|max:30'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate that instructor_rate + platform_rate = 100
        if ($request->instructor_rate + $request->platform_rate != 100) {
            return redirect()->back()
                ->with('error', 'The instructor rate and platform rate must add up to 100%.')
                ->withInput();
        }

        try {
            // Update instructor commission rate
            Setting::updateOrCreate(
                ['key' => 'instructor_commission_rate'],
                ['value' => $request->instructor_rate, 'description' => 'The percentage of course revenue that goes to instructors']
            );
            
            // Update platform commission rate
            Setting::updateOrCreate(
                ['key' => 'platform_commission_rate'],
                ['value' => $request->platform_rate, 'description' => 'The percentage of course revenue that goes to the platform']
            );
            
            // Update minimum withdrawal amount
            Setting::updateOrCreate(
                ['key' => 'minimum_withdrawal_amount'],
                ['value' => $request->min_withdrawal_amount, 'description' => 'The minimum amount that instructors can withdraw']
            );
            
            // Update withdrawal processing days
            Setting::updateOrCreate(
                ['key' => 'withdrawal_processing_days'],
                ['value' => $request->processing_days, 'description' => 'The number of days it takes to process a withdrawal request']
            );
            
            return redirect()->route('admin.instructor-earnings.settings')
                ->with('success', 'Commission settings updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to update commission settings: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update commission settings. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display earnings for a specific instructor.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function instructorEarnings($id)
    {
        $instructor = User::whereHas('roles', function($query) {
                $query->where('role', 'instructor');
            })
            ->findOrFail($id);
        
        // Get earnings statistics
        $totalEarnings = $instructor->earnings()->sum('amount');
        $availableEarnings = $instructor->available_earnings;
        $pendingEarnings = $instructor->pending_earnings;
        $withdrawnEarnings = $instructor->withdrawn_earnings;
        
        // Get earnings with pagination
        $earnings = $instructor->earnings()
            ->with(['course', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get withdrawals with pagination
        $withdrawals = $instructor->withdrawals()
            ->orderBy('requested_at', 'desc')
            ->paginate(10);
        
        return view('admin.instructor_earnings.instructor', compact(
            'instructor',
            'totalEarnings',
            'availableEarnings',
            'pendingEarnings',
            'withdrawnEarnings',
            'earnings',
            'withdrawals'
        ));
    }
}
