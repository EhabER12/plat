<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorEarning;
use App\Models\InstructorPaymentAccount;
use App\Models\Setting;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EarningController extends Controller
{
    /**
     * Display a listing of the instructor's earnings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get earnings statistics
        $totalEarnings = $user->earnings()->sum('amount');
        $availableEarnings = $user->available_earnings;
        $pendingEarnings = $user->pending_earnings;
        $withdrawnEarnings = $user->withdrawn_earnings;
        
        // Get recent earnings
        $recentEarnings = $user->earnings()
            ->with(['course', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Get minimum withdrawal amount from settings
        $minWithdrawalAmount = Setting::where('key', 'minimum_withdrawal_amount')
            ->first()->value ?? 100;
        
        // Check if the instructor has a payment account
        $hasPaymentAccount = $user->paymentAccounts()->where('is_active', true)->exists();
        
        return view('instructor.earnings.index', compact(
            'totalEarnings',
            'availableEarnings',
            'pendingEarnings',
            'withdrawnEarnings',
            'recentEarnings',
            'minWithdrawalAmount',
            'hasPaymentAccount'
        ));
    }

    /**
     * Display the instructor's earnings history.
     *
     * @return \Illuminate\View\View
     */
    public function history()
    {
        $user = Auth::user();
        
        // Get all earnings with pagination
        $earnings = $user->earnings()
            ->with(['course', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('instructor.earnings.history', compact('earnings'));
    }

    /**
     * Display the instructor's withdrawal history.
     *
     * @return \Illuminate\View\View
     */
    public function withdrawals()
    {
        $user = Auth::user();
        
        // Get all withdrawals with pagination
        $withdrawals = $user->withdrawals()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get the available earnings of the user
        $availableEarnings = $user->available_earnings;
        
        // Get minimum withdrawal amount from settings
        $minWithdrawalAmount = Setting::where('key', 'minimum_withdrawal_amount')
            ->first()->value ?? 100;
        
        return view('instructor.earnings.withdrawals', compact('withdrawals', 'availableEarnings', 'minWithdrawalAmount'));
    }

    /**
     * Show the form for creating a new withdrawal request.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function createWithdrawal()
    {
        $user = Auth::user();
        $availableEarnings = $user->available_earnings;
        
        // Get minimum withdrawal amount from settings
        $minWithdrawalAmount = Setting::where('key', 'minimum_withdrawal_amount')
            ->first()->value ?? 100;
        
        // Check if the instructor has enough available earnings
        if ($availableEarnings < $minWithdrawalAmount) {
            return redirect()->route('instructor.earnings.index')
                ->with('error', "You need at least $minWithdrawalAmount to make a withdrawal.");
        }
        
        // Get the instructor's payment accounts
        $paymentAccounts = $user->paymentAccounts()
            ->where('is_active', true)
            ->get();
        
        // Check if the instructor has any payment accounts
        if ($paymentAccounts->isEmpty()) {
            return redirect()->route('instructor.payment-accounts.create')
                ->with('info', 'You need to add a payment account before you can make a withdrawal.');
        }
        
        return view('instructor.earnings.create-withdrawal', compact(
            'availableEarnings',
            'minWithdrawalAmount',
            'paymentAccounts'
        ));
    }

    /**
     * Store a newly created withdrawal request in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeWithdrawal(Request $request)
    {
        $user = Auth::user();
        $availableEarnings = $user->available_earnings;
        
        // Get minimum withdrawal amount from settings
        $minWithdrawalAmount = Setting::where('key', 'minimum_withdrawal_amount')
            ->first()->value ?? 100;
        
        $validator = Validator::make($request->all(), [
            'amount' => "required|numeric|min:$minWithdrawalAmount|max:$availableEarnings",
            'payment_provider' => 'required|in:vodafone_cash,instapay',
            'provider_account_id' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Create the withdrawal request
            $withdrawal = Withdrawal::create([
                'instructor_id' => $user->user_id,
                'amount' => $request->amount,
                'status' => 'pending',
                'payment_provider' => $request->payment_provider,
                'provider_account_id' => $request->provider_account_id,
                'requested_at' => now(),
                'notes' => $request->notes
            ]);
            
            // Get available earnings up to the requested amount
            $earnings = $user->earnings()
                ->where('status', InstructorEarning::STATUS_AVAILABLE)
                ->orderBy('created_at', 'asc')
                ->get();
            
            $remainingAmount = $request->amount;
            foreach ($earnings as $earning) {
                if ($remainingAmount <= 0) {
                    break;
                }
                // Mark the earning as withdrawn
                $earning->markAsWithdrawn($withdrawal->withdrawal_id);
                $remainingAmount -= $earning->amount;
            }
            
            DB::commit();
            
            return redirect()->route('instructor.earnings.withdrawals')
                ->with('success', 'Withdrawal request submitted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create withdrawal request: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to create withdrawal request. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified withdrawal.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showWithdrawal($id)
    {
        $user = Auth::user();
        $withdrawal = Withdrawal::where('instructor_id', $user->user_id)
            ->where('withdrawal_id', $id)
            ->firstOrFail();
            
        // Get the earnings included in this withdrawal
        $earnings = InstructorEarning::where('withdrawal_id', $withdrawal->withdrawal_id)
            ->with(['course', 'payment'])
            ->get();
            
        return view('instructor.earnings.show-withdrawal', compact('withdrawal', 'earnings'));
    }

    /**
     * Cancel the specified withdrawal request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function cancelWithdrawal($id)
    {
        // Get the current authenticated user
        $user = Auth::user();
        
        try {
            // First check if withdrawal exists and can be cancelled
            $withdrawal = Withdrawal::where('instructor_id', $user->user_id)
                ->where('withdrawal_id', $id)
                ->where('status', 'pending')
                ->firstOrFail();
            
            DB::beginTransaction();
            
            // Update the withdrawal status - we can now use 'cancelled' after the migration
            $withdrawal->update([
                'status' => 'cancelled', // Now we can use 'cancelled' since we've added it to the enum
                'notes' => ($withdrawal->notes ? $withdrawal->notes . "\n" : '') . 'Cancelled by instructor at ' . now(),
                'processed_at' => now()
            ]);
            
            // Get the earnings included in this withdrawal
            $earnings = InstructorEarning::where('withdrawal_id', $withdrawal->withdrawal_id)->get();
            
            // Mark the earnings as available again
            foreach ($earnings as $earning) {
                $earning->update([
                    'status' => InstructorEarning::STATUS_AVAILABLE,
                    'withdrawal_id' => null
                ]);
            }
            
            DB::commit();
            
            // Log the successful cancellation
            Log::info('Withdrawal request cancelled successfully', [
                'withdrawal_id' => $id,
                'instructor_id' => $user->user_id
            ]);
            
            // Check if the request is AJAX
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Withdrawal cancelled successfully']);
            }
            
            return redirect()->route('instructor.earnings.withdrawals')
                ->with('success', 'Withdrawal request cancelled successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error with more details
            Log::error('Failed to cancel withdrawal request', [
                'error' => $e->getMessage(),
                'withdrawal_id' => $id,
                'instructor_id' => $user->user_id,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return appropriate response based on request type
            if (request()->ajax()) {
                return response()->json([
                    'error' => 'Failed to cancel withdrawal request: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to cancel withdrawal request. Please try again.');
        }
    }
}
