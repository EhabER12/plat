@extends('admin.layout')

@section('title', 'التحقق من علاقات أولياء الأمور والطلاب')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0">التحقق من علاقات أولياء الأمور والطلاب</h5>
                    <div>
                        <a href="{{ route('admin.parent-verifications.pending') }}" class="btn btn-accent btn-sm">
                            <i class="fas fa-clock me-1"></i> الطلبات المعلقة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>الرقم</th>
                                    <th>ولي الأمر</th>
                                    <th>اسم الطالب</th>
                                    <th>تاريخ الطلب</th>
                                    <th>حالة التحقق</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($relations as $relation)
                                    <tr>
                                        <td>{{ $relation->id }}</td>
                                        <td>
                                            @if($relation->parent)
                                                {{ $relation->parent->name }}
                                                <small class="d-block text-muted">{{ $relation->parent->email }}</small>
                                            @else
                                                <span class="text-muted">غير متوفر</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $relation->student_name }}
                                            @if($relation->student)
                                                <span class="badge bg-success">مطابق</span>
                                                <small class="d-block text-muted">{{ $relation->student->email }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $relation->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if($relation->verification_status == 'pending')
                                                <span class="badge bg-accent text-dark">معلق</span>
                                            @elseif($relation->verification_status == 'approved')
                                                <span class="badge bg-success">تمت الموافقة</span>
                                                <small class="d-block">{{ $relation->verified_at?->format('Y-m-d') }}</small>
                                            @elseif($relation->verification_status == 'rejected')
                                                <span class="badge bg-danger">مرفوض</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.parent-verifications.show', $relation->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> عرض
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">لا توجد علاقات للتحقق منها</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $relations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 