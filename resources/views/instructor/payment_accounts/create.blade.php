@extends('layouts.instructor')

@section('title', 'Add Payment Account')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Payment Account</h1>
        <a href="{{ route('instructor.payment-accounts.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Accounts
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Payment Account Details</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.payment-accounts.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="payment_provider">Payment Provider</label>
                    <select class="form-control @error('payment_provider') is-invalid @enderror" id="payment_provider" name="payment_provider">
                        <option value="paymob" selected>Paymob</option>
                    </select>
                    @error('payment_provider')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="account_name">Account Name</label>
                    <input type="text" class="form-control @error('account_name') is-invalid @enderror" id="account_name" name="account_name" value="{{ old('account_name') }}" placeholder="e.g. My Paymob Account">
                    @error('account_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">A name to help you identify this account.</small>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter the email associated with your Paymob account">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter the phone number associated with your Paymob account">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div id="paymob-fields">
                    <div class="form-group">
                        <label for="bank_name">Bank Name (Optional)</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name') }}" placeholder="Enter your bank name if applicable">
                    </div>
                    
                    <div class="form-group">
                        <label for="account_number">Account Number (Optional)</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" value="{{ old('account_number') }}" placeholder="Enter your bank account number if applicable">
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_default">Set as default payment account</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Account</button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">About Paymob Integration</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <h5>Important Information</h5>
                <p>To receive payments through Paymob, you need to:</p>
                <ol>
                    <li>Have a registered Paymob account</li>
                    <li>Provide the email and phone number associated with your Paymob account</li>
                    <li>Ensure your Paymob account is verified and active</li>
                </ol>
                <p>If you don't have a Paymob account yet, <a href="https://paymob.com" target="_blank">sign up here</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
