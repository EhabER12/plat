@extends('layouts.admin')

@section('title', 'عرض الشارة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">تفاصيل الشارة: {{ $badge->name }}</h3>
                    <div>
                        <a href="{{ route('admin.badges.edit', $badge->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('admin.badges.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="badge-display p-4 mb-4" style="background-color: #f8f9fa; border-radius: 10px;">
                                <div class="badge-icon mb-3">
                                    <i class="fas fa-{{ $badge->icon == 'explore' ? 'compass' : ($badge->icon == 'persistence' ? 'tasks' : ($badge->icon == 'streak' ? 'fire' : ($badge->icon == 'star' ? 'star' : ($badge->icon == 'perfect' ? 'award' : $badge->icon)))) }}" style="font-size: 5rem; color: #007bff;"></i>
                                </div>
                                <h3>{{ $badge->name }}</h3>
                                <div class="badge bg-primary mb-2">المستوى {{ $badge->level }}</div>
                                <p>{{ $badge->description }}</p>
                                <div class="badge {{ $badge->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $badge->is_active ? 'مفعل' : 'غير مفعل' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">معايير الحصول على الشارة</h5>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($badge->criteria, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                    
                                    <div class="mt-3">
                                        <h6>تفسير المعايير:</h6>
                                        <ul>
                                            @if(isset($badge->criteria['type']))
                                                @if($badge->criteria['type'] == 'quiz_attempts')
                                                    <li>نوع المعيار: إكمال عدد من الاختبارات</li>
                                                    <li>عدد الاختبارات المطلوبة: {{ $badge->criteria['count'] ?? 'غير محدد' }}</li>
                                                @elseif($badge->criteria['type'] == 'quiz_score')
                                                    <li>نوع المعيار: الحصول على درجة معينة</li>
                                                    <li>الحد الأدنى للدرجة: {{ $badge->criteria['min_score'] ?? 'غير محدد' }}%</li>
                                                @elseif($badge->criteria['type'] == 'quiz_streak')
                                                    <li>نوع المعيار: سلسلة نجاحات متتالية</li>
                                                    <li>عدد النجاحات المتتالية المطلوبة: {{ $badge->criteria['count'] ?? 'غير محدد' }}</li>
                                                @elseif($badge->criteria['type'] == 'quiz_perfect_streak')
                                                    <li>نوع المعيار: سلسلة درجات كاملة متتالية</li>
                                                    <li>عدد الدرجات الكاملة المتتالية المطلوبة: {{ $badge->criteria['count'] ?? 'غير محدد' }}</li>
                                                @elseif($badge->criteria['type'] == 'quiz_time')
                                                    <li>نوع المعيار: إكمال اختبار في وقت سريع</li>
                                                    <li>النسبة المئوية القصوى من الوقت المخصص: {{ $badge->criteria['max_time_percentage'] ?? 'غير محدد' }}%</li>
                                                @else
                                                    <li>نوع المعيار: {{ $badge->criteria['type'] }}</li>
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
                                    <h5 class="mb-0">الطلاب الحاصلون على هذه الشارة</h5>
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
                                            لا يوجد طلاب حاصلون على هذه الشارة حتى الآن.
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
