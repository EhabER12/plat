@extends('layouts.app')

@section('title', 'Payment Form')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Make a Payment</div>
                <div class="card-body">
                    <form method="GET" action="{{ route('checkout') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (EGP)</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" value="100" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Proceed to Checkout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 