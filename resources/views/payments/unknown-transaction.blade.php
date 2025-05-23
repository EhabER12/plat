@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                @if(isset($alreadyEnrolled) && $alreadyEnrolled)
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">أنت مسجل بالفعل في هذه الدورة</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="mb-3">تم تسجيلك بالفعل في هذه الدورة</h5>
                            <p>يمكنك الوصول إلى محتوى الدورة على الفور.</p>
                            
                            @if(isset($course))
                                <div class="alert alert-info mt-3">
                                    <p>الدورة: <strong>{{ $course->title }}</strong></p>
                                    <p>السعر: <strong>{{ $course->price }} EGP</strong></p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('student.course-content', $courseId) }}" class="btn btn-primary btn-lg me-2">
                                <i class="fas fa-play-circle me-1"></i> الذهاب إلى الدورة
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">العودة إلى الصفحة الرئيسية</a>
                        </div>
                    </div>
                @elseif(isset($canCreateEnrollment) && $canCreateEnrollment && isset($course) && isset($user))
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">تأكيد عملية الدفع وتسجيل الدورة</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-check fa-3x text-success mb-3"></i>
                            <h5 class="mb-3">تم استلام إشعار بعملية دفع ناجحة</h5>
                            <p>يمكنك الآن تأكيد التسجيل في الدورة:</p>
                            
                            <div class="alert alert-info mt-3">
                                <p>الدورة: <strong>{{ $course->title }}</strong></p>
                                <p>السعر: <strong>{{ $course->price }} EGP</strong></p>
                                <p>المستخدم: <strong>{{ $user->name }}</strong> ({{ $user->email }})</p>
                            </div>
                            
                            <form action="{{ route('payment.process-unknown-transaction') }}" method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="courseId" value="{{ $courseId }}">
                                <input type="hidden" name="userId" value="{{ $userId }}">
                                <input type="hidden" name="orderId" value="{{ $orderId }}">
                                <input type="hidden" name="merchantOrderId" value="{{ $merchantOrderId }}">
                                
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check-circle me-1"></i> تأكيد التسجيل في الدورة
                                </button>
                            </form>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">العودة إلى الصفحة الرئيسية</a>
                        </div>
                    </div>
                @else
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0">تتم معالجة عملية الدفع</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-sync fa-spin fa-3x text-warning mb-3"></i>
                            <h5 class="mb-3">نحن نتحقق من عملية الدفع الخاصة بك</h5>
                            <p>لقد تلقينا إشعارًا بعملية دفع{{ isset($status) && $status == 'success' ? ' ناجحة' : '' }}، ولكننا نحتاج إلى مزيد من الوقت للتحقق من تفاصيلها.</p>
                            
                            @if(isset($course))
                                <div class="alert alert-info mt-3">
                                    <p>الدورة: <strong>{{ $course->title }}</strong></p>
                                    <p>السعر: <strong>{{ $course->price }} EGP</strong></p>
                                </div>
                            @endif
                            
                            <div class="mt-4">
                                <p>سيتم تفعيل وصولك إلى الدورة تلقائيًا بمجرد التحقق من الدفع.</p>
                                <p>إذا لم يتم تفعيل الوصول خلال 30 دقيقة، يرجى التواصل مع دعم العملاء مع تقديم المعلومات التالية:</p>
                                
                                <div class="alert alert-secondary mt-3 text-start">
                                    <p>رقم الطلب: <strong>{{ $orderId ?? 'غير متاح' }}</strong></p>
                                    <p>معرف الطلب التجاري: <strong>{{ $merchantOrderId ?? 'غير متاح' }}</strong></p>
                                    <p>معرف الدورة: <strong>{{ $courseId ?? 'غير متاح' }}</strong></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('home') }}" class="btn btn-primary me-2">العودة إلى الصفحة الرئيسية</a>
                            <a href="{{ route('courses.index') }}" class="btn btn-outline-primary">تصفح الدورات</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 