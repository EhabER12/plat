@extends('layouts.app')

@section('title', 'طلب ربط ولي أمر')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">طلب ربط ولي أمر</h1>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p class="mb-0">تلقيت طلب لربط حسابك بولي أمر. يرجى مراجعة المعلومات والرد على الطلب.</p>
                    </div>

                    <div class="parent-info mb-4">
                        <h3 class="mb-3">معلومات ولي الأمر</h3>
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <img src="{{ $parent->profile_image ? asset('storage/profile_images/' . $parent->profile_image) : asset('images/default_profile.jpg') }}" 
                                     class="img-fluid rounded-circle mb-2" 
                                     alt="{{ $parent->name }}" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <div class="col-md-9">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">اسم ولي الأمر</th>
                                        <td>{{ $parent->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>البريد الإلكتروني</th>
                                        <td>{{ $parent->email }}</td>
                                    </tr>
                                    @if($parent->phone)
                                    <tr>
                                        <th>رقم الهاتف</th>
                                        <td>{{ $parent->phone }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>نوع العلاقة</th>
                                        <td>
                                            @if($relation->relation_type == 'parent')
                                                والد/والدة
                                            @elseif($relation->relation_type == 'guardian')
                                                وصي قانوني
                                            @else
                                                {{ $relation->relation_type }}
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        @if($relation->notes)
                        <div class="notes mt-3">
                            <h5>ملاحظات إضافية</h5>
                            <div class="card">
                                <div class="card-body bg-light">
                                    {{ $relation->notes }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="alert alert-warning">
                        <h5><i class="fas fa-info-circle"></i> ما معنى الموافقة على هذا الطلب؟</h5>
                        <p>
                            بالموافقة على هذا الطلب، سيتمكن ولي الأمر من:
                        </p>
                        <ul>
                            <li>الاطلاع على تقدمك في الدورات التعليمية</li>
                            <li>معرفة نتائجك في الاختبارات والواجبات</li>
                            <li>متابعة حضورك ونشاطك على المنصة</li>
                            <li>تلقي تقارير دورية عن أدائك</li>
                        </ul>
                        <p class="mb-0">
                            لن يستطيع ولي الأمر الوصول إلى بيانات تسجيل الدخول الخاصة بك أو تغيير كلمة المرور.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('student.respond-to-parent-link', $relation->token) }}">
                        @csrf
                        <div class="text-center mt-4">
                            <div class="btn-group btn-group-lg" role="group">
                                <button type="submit" name="response" value="approve" class="btn btn-success">
                                    <i class="fas fa-check mr-1"></i> موافق
                                </button>
                                <button type="submit" name="response" value="reject" class="btn btn-danger">
                                    <i class="fas fa-times mr-1"></i> غير موافق
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 