@extends('layouts.admin')

@section('title', 'عرض الإنجاز')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">تفاصيل الإنجاز: {{ $achievement->name }}</h3>
                    <div>
                        <a href="{{ route('admin.achievements.edit', $achievement->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('admin.achievements.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="achievement-display p-4 mb-4" style="background-color: #f8f9fa; border-radius: 10px;">
                                <div class="achievement-icon mb-3">
                                    <i class="fas fa-{{ $achievement->icon }}" style="font-size: 5rem; color: #fd7e14;"></i>
                                </div>
                                <h3>{{ $achievement->name }}</h3>
                                <div class="badge bg-warning text-dark mb-2">{{ $achievement->points }} نقطة</div>
                                <p>{{ $achievement->description }}</p>
                                <div class="badge {{ $achievement->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $achievement->is_active ? 'مفعل' : 'غير مفعل' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">معايير تحقيق الإنجاز</h5>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($achievement->criteria, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                    
                                    <div class="mt-3">
                                        <h6>تفسير المعايير:</h6>
                                        <ul>
                                            @if(isset($achievement->criteria['type']))
                                                @if($achievement->criteria['type'] == 'quiz_attempts')
                                                    <li>نوع المعيار: إكمال عدد من الاختبارات</li>
                                                    <li>عدد الاختبارات المطلوبة: {{ $achievement->criteria['count'] ?? 'غير محدد' }}</li>
                                                    @if(isset($achievement->criteria['passed']))
                                                        <li>يجب أن تكون الاختبارات ناجحة: {{ $achievement->criteria['passed'] ? 'نعم' : 'لا' }}</li>
                                                    @endif
                                                @elseif($achievement->criteria['type'] == 'quiz_pass_rate')
                                                    <li>نوع المعيار: معدل نجاح عالي في الاختبارات</li>
                                                    <li>الحد الأدنى لمعدل النجاح: {{ $achievement->criteria['min_rate'] ?? 'غير محدد' }}%</li>
                                                    <li>الحد الأدنى لعدد الاختبارات: {{ $achievement->criteria['min_attempts'] ?? 'غير محدد' }}</li>
                                                @elseif($achievement->criteria['type'] == 'quiz_streak')
                                                    <li>نوع المعيار: سلسلة نجاحات متتالية</li>
                                                    <li>عدد النجاحات المتتالية المطلوبة: {{ $achievement->criteria['count'] ?? 'غير محدد' }}</li>
                                                @elseif($achievement->criteria['type'] == 'quiz_score_streak')
                                                    <li>نوع المعيار: سلسلة درجات عالية متتالية</li>
                                                    <li>الحد الأدنى للدرجة: {{ $achievement->criteria['min_score'] ?? 'غير محدد' }}%</li>
                                                    <li>عدد الاختبارات المتتالية: {{ $achievement->criteria['count'] ?? 'غير محدد' }}</li>
                                                @elseif($achievement->criteria['type'] == 'quiz_categories')
                                                    <li>نوع المعيار: إكمال اختبارات في فئات متنوعة</li>
                                                    <li>جميع الفئات مطلوبة: {{ isset($achievement->criteria['all_categories']) && $achievement->criteria['all_categories'] ? 'نعم' : 'لا' }}</li>
                                                @else
                                                    <li>نوع المعيار: {{ $achievement->criteria['type'] }}</li>
                                                @endif
                                            @else
                                                <li>لم يتم تحديد معايير واضحة</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">الطلاب الحاصلون على هذا الإنجاز</h5>
                                    <span class="badge bg-info">{{ $students->total() }} طالب</span>
                                </div>
                                <div class="card-body">
                                    @if($students->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>اسم الطالب</th>
                                                        <th>البريد الإلكتروني</th>
                                                        <th>تاريخ الحصول</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($students as $student)
                                                        <tr>
                                                            <td>{{ $student->id }}</td>
                                                            <td>{{ $student->name }}</td>
                                                            <td>{{ $student->email }}</td>
                                                            <td>{{ $student->pivot->earned_at->format('Y-m-d H:i') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div class="mt-3">
                                            {{ $students->links() }}
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            لا يوجد طلاب حاصلون على هذا الإنجاز حتى الآن.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
