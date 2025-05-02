<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorPaymentAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentAccountController extends Controller
{
    /**
     * Display a listing of the instructor's payment accounts.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $accounts = $user->paymentAccounts()->get();
        
        return view('instructor.payment_accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new payment account.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('instructor.payment_accounts.create');
    }

    /**
     * Store a newly created payment account in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_provider' => 'required|string|in:paymob',
            'account_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            /** @var User $user */
            $user = Auth::user();
            
            // Create account details array
            $accountDetails = [
                'email' => $request->email,
                'phone' => $request->phone
            ];
            
            // Add additional fields if provided
            if ($request->has('bank_name')) {
                $accountDetails['bank_name'] = $request->bank_name;
            }
            
            if ($request->has('account_number')) {
                $accountDetails['account_number'] = $request->account_number;
            }
            
            // Create the payment account
            $account = InstructorPaymentAccount::create([
                'instructor_id' => $user->user_id,
                'payment_provider' => $request->payment_provider,
                'account_name' => $request->account_name,
                'account_details' => $accountDetails,
                'is_active' => true,
                'is_default' => $request->has('is_default') && $request->is_default ? true : false
            ]);
            
            // If this is the first account or set as default, make it the default
            if ($request->has('is_default') && $request->is_default) {
                $account->setAsDefault();
            } else {
                // If this is the first account, make it default anyway
                $accountCount = $user->paymentAccounts()->count();
                if ($accountCount === 1) {
                    $account->setAsDefault();
                }
            }
            
            return redirect()->route('instructor.payment-accounts.index')
                ->with('success', 'Payment account created successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to create payment account: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to create payment account. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified payment account.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        /** @var User $user */
        $user = Auth::user();
        $account = InstructorPaymentAccount::where('instructor_id', $user->user_id)
            ->where('account_id', $id)
            ->firstOrFail();
            
        return view('instructor.payment_accounts.edit', compact('account'));
    }

    /**
     * Update the specified payment account in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'account_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'is_default' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            /** @var User $user */
            $user = Auth::user();
            $account = InstructorPaymentAccount::where('instructor_id', $user->user_id)
                ->where('account_id', $id)
                ->firstOrFail();
            
            // Update account details
            $accountDetails = $account->account_details;
            $accountDetails['email'] = $request->email;
            $accountDetails['phone'] = $request->phone;
            
            // Add additional fields if provided
            if ($request->has('bank_name')) {
                $accountDetails['bank_name'] = $request->bank_name;
            }
            
            if ($request->has('account_number')) {
                $accountDetails['account_number'] = $request->account_number;
            }
            
            // Update the account
            $account->update([
                'account_name' => $request->account_name,
                'account_details' => $accountDetails,
                'is_active' => $request->has('is_active') ? $request->is_active : $account->is_active
            ]);
            
            // Handle default status
            if ($request->has('is_default') && $request->is_default) {
                $account->setAsDefault();
            }
            
            return redirect()->route('instructor.payment-accounts.index')
                ->with('success', 'Payment account updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to update payment account: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update payment account. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified payment account from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $account = InstructorPaymentAccount::where('instructor_id', $user->user_id)
                ->where('account_id', $id)
                ->firstOrFail();
            
            // Cannot delete the default account if it's the only one
            if ($account->is_default && $user->paymentAccounts()->count() === 1) {
                return redirect()->back()
                    ->with('error', 'Cannot delete the only payment account. Please add another account first.');
            }
            
            // If deleting the default account, make another one default
            if ($account->is_default) {
                $newDefault = $user->paymentAccounts()
                    ->where('account_id', '!=', $id)
                    ->where('is_active', true)
                    ->first();
                    
                if ($newDefault) {
                    $newDefault->setAsDefault();
                }
            }
            
            $account->delete();
            
            return redirect()->route('instructor.payment-accounts.index')
                ->with('success', 'Payment account deleted successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to delete payment account: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to delete payment account. Please try again.');
        }
    }

    /**
     * Set the specified payment account as default.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setDefault($id)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $account = InstructorPaymentAccount::where('instructor_id', $user->user_id)
                ->where('account_id', $id)
                ->firstOrFail();
            
            $account->setAsDefault();
            
            return redirect()->route('instructor.payment-accounts.index')
                ->with('success', 'Default payment account updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to set default payment account: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to set default payment account. Please try again.');
        }
    }
}
