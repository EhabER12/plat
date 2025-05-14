@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Checkout</div>
                <div class="card-body">
                    <h3 class="mb-4">Payment</h3>

                    @if(isset($course))
                        <div class="course-info mb-4">
                            <h4>{{ $course->title }}</h4>
                            <p class="text-muted">Original Price: ${{ number_format($course->price, 2) }}</p>
                            
                            @if(isset($appliedCoupon))
                                <div class="alert alert-success">
                                    <p><i class="fas fa-tag"></i> Coupon <strong>{{ $appliedCoupon->code }}</strong> applied successfully!</p>
                                    <p>Discount: {{ $appliedCoupon->type === 'percentage' ? $appliedCoupon->value . '%' : '$' . number_format($appliedCoupon->value, 2) }}</p>
                                    <p>Final Price: <strong>${{ number_format($finalPrice, 2) }}</strong></p>
                                    <form action="{{ route('payment.remove-coupon', $course->course_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove Coupon</button>
                                    </form>
                                </div>
                            @else
                                <!-- Coupon Form -->
                                <div class="coupon-form mb-3">
                                    <form action="{{ route('payment.apply-coupon', $course->course_id) }}" method="POST" class="row g-3">
                                        @csrf
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="coupon_code" placeholder="Enter Coupon Code" value="{{ old('coupon_code') }}">
                                                <button type="submit" class="btn btn-outline-primary">Apply</button>
                                            </div>
                                            @if(session('coupon_error'))
                                                <div class="text-danger mt-1">{{ session('coupon_error') }}</div>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                                
                                <p class="font-weight-bold">Final Price: <strong>${{ number_format($course->price, 2) }}</strong></p>
                            @endif
                        </div>
                    @endif

                    @if(isset($iframeUrl) && $iframeUrl)
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="{{ $iframeUrl }}" width="100%" height="600px" frameborder="0"></iframe>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p>Please select a payment method to continue:</p>
                            <div class="mt-4">
                                @if(isset($enabledPaymentMethods) && in_array('paymob', $enabledPaymentMethods))
                                    <form id="paymob-form" action="{{ isset($course) ? route('payment.process.paymob', $course->course_id) : '#' }}" method="POST" class="d-inline">
                                        @csrf
                                        <!-- يمكن إضافة بيانات افتراضية للاختبار -->
                                        <input type="hidden" name="first_name" value="{{ $user->name ?? 'Test' }}">
                                        <input type="hidden" name="last_name" value="User">
                                        <input type="hidden" name="email" value="{{ $user->email ?? 'test@example.com' }}">
                                        <input type="hidden" name="phone_number" value="{{ $user->phone ?? '01012345678' }}">
                                        <input type="hidden" name="street" value="Test Street">
                                        <input type="hidden" name="city" value="Cairo">
                                        <input type="hidden" name="country" value="EG">
                                        <input type="hidden" name="state" value="Cairo">
                                        <input type="hidden" name="postal_code" value="12345">
                                        @if(isset($appliedCoupon))
                                            <input type="hidden" name="coupon_id" value="{{ $appliedCoupon->coupon_id }}">
                                            <input type="hidden" name="discount_amount" value="{{ $course->price - $finalPrice }}">
                                        @endif
                                        <button type="submit" class="btn btn-primary">Pay with Credit Card</button>
                                    </form>
                    @endif

                                @if(isset($enabledPaymentMethods) && in_array('vodafone_cash', $enabledPaymentMethods))
                                    <form id="vodafone-form" action="{{ isset($course) ? route('payment.process.vodafone', $course->course_id) : '#' }}" method="POST" class="d-inline ml-2">
                            @csrf
                                        <input type="hidden" name="phone_number" value="{{ $user->phone ?? '01012345678' }}">
                                        <input type="hidden" name="transaction_reference" value="TEST{{ rand(100000, 999999) }}">
                                        @if(isset($appliedCoupon))
                                            <input type="hidden" name="coupon_id" value="{{ $appliedCoupon->coupon_id }}">
                                            <input type="hidden" name="discount_amount" value="{{ $course->price - $finalPrice }}">
                                        @endif
                                        <button type="submit" class="btn btn-danger">Pay with Vodafone Cash</button>
                                    </form>
                                @endif
                                
                                <!-- Adding a simulate payment option for testing -->
                                <form id="simulate-form" action="{{ isset($course) ? route('payment.test.simulate', [$course->course_id, 'paymob']) : '#' }}" method="GET" class="d-inline ml-2">
                                    @if(isset($appliedCoupon))
                                        <input type="hidden" name="coupon_id" value="{{ $appliedCoupon->coupon_id }}">
                                        <input type="hidden" name="discount_amount" value="{{ $course->price - $finalPrice }}">
                                    @endif
                                    <button type="submit" class="btn btn-success">Simulate Payment (Test)</button>
                        </form>
                            </div>
                            </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


