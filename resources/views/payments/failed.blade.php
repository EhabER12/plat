@extends('layouts.app')

@section('title', 'فشل عملية الدفع')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">فشلت عملية الدفع</h4>
                </div>
                <div class="card-body text-center">
                    <div class="my-4">
                        <i class="fas fa-times-circle text-danger fa-5x"></i>
                    </div>
                    <h3 class="mb-3">فشلت عملية الدفع</h3>
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(isset($payment) && isset($payment->course))
                        <div class="my-4 p-3 border rounded bg-light">
                            <div class="row">
                                <div class="col-md-6 text-start">
                                    <p><strong>الدورة:</strong></p>
                                    <p><strong>المبلغ:</strong></p>
                                    <p><strong>رقم العملية:</strong></p>
                                </div>
                                <div class="col-md-6 text-start">
                                    <p>{{ $payment->course->title }}</p>
                                    <p>{{ $payment->amount }} EGP</p>
                                    <p>{{ $payment->payment_id }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <p class="mb-4">تعذر إتمام عملية الدفع الخاصة بك. يرجى المحاولة مرة أخرى أو استخدام وسيلة دفع مختلفة.</p>
                    
                    <div class="alert alert-info">
                        <h5>أسباب محتملة لفشل الدفع:</h5>
                        <ul class="text-start mt-2">
                            <li>معلومات بطاقة الائتمان/الخصم غير صحيحة</li>
                            <li>رصيد غير كافٍ في البطاقة</li>
                            <li>تم رفض المعاملة من قبل البنك لأسباب أمنية</li>
                            <li>انقطاع الاتصال أثناء المعاملة</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        @if(isset($payment) && isset($payment->course))
                            <a href="{{ route('payment.checkout', $payment->course_id) }}" class="btn btn-primary">
                                <i class="fas fa-sync-alt me-1"></i> محاولة مرة أخرى
                        </a>
                        @else 
                            <a href="{{ route('courses.index') }}" class="btn btn-primary">
                                <i class="fas fa-sync-alt me-1"></i> العودة للدورات
                            </a>
                        @endif
                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            <i class="fas fa-home me-1"></i> الصفحة الرئيسية
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
