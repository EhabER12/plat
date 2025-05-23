@extends('layouts.app')

@section('title', 'عملية الدفع ناجحة')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">تمت عملية الدفع بنجاح</h4>
                </div>
                <div class="card-body text-center">
                    <div class="my-4">
                        <i class="fas fa-check-circle text-success fa-5x"></i>
                    </div>
                    <h3 class="mb-3">تمت عملية الدفع بنجاح!</h3>
                    
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(isset($payment) && isset($payment->course))
                        <div class="my-4 p-3 border rounded bg-light">
                            <h5>تفاصيل عملية الدفع:</h5>
                            <div class="row mt-3">
                                <div class="col-md-6 text-start">
                                    <p><strong>الدورة:</strong></p>
                                    <p><strong>المبلغ:</strong></p>
                                    <p><strong>رقم العملية:</strong></p>
                                    <p><strong>تاريخ الدفع:</strong></p>
                                </div>
                                <div class="col-md-6 text-start">
                                    <p>{{ $payment->course->title }}</p>
                                    <p>{{ $payment->amount }} EGP</p>
                                    <p>{{ $payment->payment_id }}</p>
                                    <p>{{ $payment->payment_date->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                    </div>
                    @endif
                    
                    <p class="mb-4">شكراً لك! تم تسجيلك في الدورة وبإمكانك البدء في التعلم فوراً.</p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        @if(isset($payment) && isset($payment->course))
                            <a href="{{ route('student.course-content', $payment->course_id) }}" class="btn btn-primary">
                                <i class="fas fa-play-circle me-1"></i> بدء الدورة الآن
                        </a>
                        @endif
                        <a href="{{ route('student.my-courses') }}" class="btn btn-success">
                            <i class="fas fa-graduation-cap me-1"></i> دوراتي
                        </a>
                        <a href="{{ route('courses.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-search me-1"></i> تصفح المزيد من الدورات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
