@extends('layouts.instructor')

@section('title', 'Payment Accounts')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payment Accounts</h1>
        <a href="{{ route('instructor.payment-accounts.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Account
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Your Payment Accounts</h6>
        </div>
        <div class="card-body">
            @if($accounts->isEmpty())
                <div class="alert alert-info">
                    <p class="mb-0">You don't have any payment accounts yet. <a href="{{ route('instructor.payment-accounts.create') }}">Add your first payment account</a> to receive earnings from your courses.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Account Name</th>
                                <th>Provider</th>
                                <th>Status</th>
                                <th>Default</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                                <tr>
                                    <td>{{ $account->account_name }}</td>
                                    <td>
                                        @if($account->payment_provider == 'paymob')
                                            <span class="badge badge-primary">Paymob</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $account->payment_provider }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($account->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($account->is_default)
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Default</span>
                                        @else
                                            <form action="{{ route('instructor.payment-accounts.set-default', $account->account_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">Set as Default</button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('instructor.payment-accounts.edit', $account->account_id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        
                                        @if(!$account->is_default)
                                            <form action="{{ route('instructor.payment-accounts.destroy', $account->account_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this account?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">About Payment Accounts</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Why add a payment account?</h5>
                    <p>Payment accounts allow you to receive earnings from your courses. When students purchase your courses, the earnings will be credited to your account and you can withdraw them to your preferred payment method.</p>
                </div>
                <div class="col-md-6">
                    <h5>Supported Payment Providers</h5>
                    <ul>
                        <li><strong>Paymob:</strong> Receive payments directly to your Paymob account.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
