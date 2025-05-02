@extends('layouts.instructor')

@section('title', 'Edit Payment Account')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Payment Account</h1>
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
            <form action="{{ route('instructor.payment-accounts.update', $account->account_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="payment_provider">Payment Provider</label>
                    <input type="text" class="form-control" value="{{ ucfirst($account->payment_provider) }}" readonly>
                    <input type="hidden" name="payment_provider" value="{{ $account->payment_provider }}">
                </div>
                
                <div class="form-group">
                    <label for="account_name">Account Name</label>
                    <input type="text" class="form-control @error('account_name') is-invalid @enderror" id="account_name" name="account_name" value="{{ old('account_name', $account->account_name) }}">
                    @error('account_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $account->account_details['email'] ?? '') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $account->account_details['phone'] ?? '') }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                @if($account->payment_provider == 'paymob')
                    <div id="paymob-fields">
                        <div class="form-group">
                            <label for="bank_name">Bank Name (Optional)</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $account->account_details['bank_name'] ?? '') }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="account_number">Account Number (Optional)</label>
                            <input type="text" class="form-control" id="account_number" name="account_number" value="{{ old('account_number', $account->account_details['account_number'] ?? '') }}">
                        </div>
                    </div>
                @endif
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ $account->is_active ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Account is active</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_default" name="is_default" value="1" {{ $account->is_default ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_default">Set as default payment account</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Account</button>
            </form>
        </div>
    </div>
</div>
@endsection
