@extends('admin.layout')

@section('title', 'طلبات التحقق المعلقة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0">طلبات التحقق المعلقة من علاقات أولياء الأمور والطلاب</h5>
                    <div>
                        <a href="{{ route('admin.parent-verifications.index') }}" class="btn btn-accent btn-sm">
                            <i class="fas fa-list me-1"></i> جميع الطلبات
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
                                    <th>المستندات</th>
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
                                        <td>{{ $relation->student_name }}</td>
                                        <td>{{ $relation->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if($relation->birth_certificate)
                                                    <a href="{{ route('admin.parent-verifications.document', [$relation->id, 'birth_certificate']) }}" class="badge bg-primary" target="_blank">
                                                        <i class="fas fa-file-alt me-1"></i> شهادة الميلاد
                                                    </a>
                                                @endif
                                                
                                                @if($relation->parent_id_card)
                                                    <a href="{{ route('admin.parent-verifications.document', [$relation->id, 'parent_id_card']) }}" class="badge bg-primary" target="_blank">
                                                        <i class="fas fa-id-card me-1"></i> بطاقة الهوية
                                                    </a>
                                                @endif
                                                
                                                @if($relation->additional_document)
                                                    <a href="{{ route('admin.parent-verifications.document', [$relation->id, 'additional_document']) }}" class="badge bg-primary" target="_blank">
                                                        <i class="fas fa-file me-1"></i> مستند إضافي
                                                    </a>
                                                @endif
                                                
                                                @if(!$relation->birth_certificate && !$relation->parent_id_card && !$relation->additional_document)
                                                    <span class="badge bg-secondary">لا توجد مستندات</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.parent-verifications.show', $relation->id) }}" class="btn btn-accent btn-sm">
                                                <i class="fas fa-check me-1"></i> مراجعة
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">لا توجد طلبات معلقة</td>
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