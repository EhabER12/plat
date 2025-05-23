@extends('layouts.instructor')

@section('title', 'Request Withdrawal')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Request Withdrawal</h1>
        <a href="{{ route('instructor.earnings.withdrawals') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Withdrawals
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Withdrawal Request</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('instructor.earnings.store-withdrawal') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="amount">Withdrawal Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" 
                                    value="{{ old('amount', $minWithdrawalAmount) }}" 
                                    min="{{ $minWithdrawalAmount }}" 
                                    max="{{ $availableEarnings }}" 
                                    step="0.01">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Minimum: ${{ number_format($minWithdrawalAmount, 2) }} | 
                                Maximum: ${{ number_format($availableEarnings, 2) }} (your available balance)
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment_provider">طريقة الدفع</label>
                            <select class="form-control @error('payment_provider') is-invalid @enderror" id="payment_provider" name="payment_provider" required>
                                <option value="">اختر طريقة الدفع</option>
                                <option value="vodafone_cash" {{ old('payment_provider') == 'vodafone_cash' ? 'selected' : '' }}>فودافون كاش</option>
                                <option value="instapay" {{ old('payment_provider') == 'instapay' ? 'selected' : '' }}>إنستا باي</option>
                            </select>
                            @error('payment_provider')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="provider_account_id">رقم الحساب/الهاتف</label>
                            <input type="text" class="form-control @error('provider_account_id') is-invalid @enderror" id="provider_account_id" name="provider_account_id" value="{{ old('provider_account_id') }}" required>
                            @error('provider_account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">أدخل رقم فودافون كاش أو رقم إنستا باي حسب اختيارك.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Add any special instructions or notes for the admin processing your withdrawal.</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Submit Withdrawal Request</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Withdrawal Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Available Balance</h5>
                        <h2 class="text-success">${{ number_format($availableEarnings, 2) }}</h2>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Processing Time</h5>
                        <p>Withdrawal requests are typically processed within 3 business days.</p>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Important</h6>
                        <p class="mb-0">Make sure your payment account details are correct before submitting your withdrawal request.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
