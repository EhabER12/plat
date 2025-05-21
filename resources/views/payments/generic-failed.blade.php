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
                    
                    <p class="mb-4">تعذر إتمام عملية الدفع الخاصة بك. يرجى المحاولة مرة أخرى أو استخدام وسيلة دفع مختلفة.</p>
                    
                    <div class="alert alert-info">
                        <h5>أسباب محتملة لفشل الدفع:</h5>
                        <ul class="text-start mt-2">
                            <li>معلومات بطاقة الائتمان/الخصم غير صحيحة</li>
                            <li>رصيد غير كافٍ في البطاقة</li>
                            <li>تم رفض المعاملة من قبل البنك لأسباب أمنية</li>
                            <li>انقطاع الاتصال أثناء المعاملة</li>
                            <li>مشكلة فنية في نظام الدفع</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="{{ route('courses.index') }}" class="btn btn-primary">
                            <i class="fas fa-sync-alt me-1"></i> العودة للدورات
                        </a>
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