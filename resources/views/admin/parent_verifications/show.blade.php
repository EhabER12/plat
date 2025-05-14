@extends('admin.layout')

@section('title', 'عرض تفاصيل طلب التحقق')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0">عرض تفاصيل طلب التحقق #{{ $relation->id }}</h5>
                    <div>
                        <a href="{{ route('admin.parent-verifications.index') }}" class="btn btn-accent btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> العودة للقائمة
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

                    <div class="row">
                        <!-- بيانات الحالة -->
                        <div class="col-md-6">
                            <div class="card mb-4 dashboard-card">
                                <div class="card-header">
                                    <h6 class="mb-0">حالة طلب التحقق</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold">حالة التحقق:</span>
                                            @if($relation->verification_status == 'pending')
                                                <span class="badge bg-accent text-dark">معلق</span>
                                            @elseif($relation->verification_status == 'approved')
                                                <span class="badge bg-success">تمت الموافقة</span>
                                            @elseif($relation->verification_status == 'rejected')
                                                <span class="badge bg-danger">مرفوض</span>
                                            @endif
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold">تاريخ الطلب:</span>
                                            <span>{{ $relation->created_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                        @if($relation->verified_at)
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-bold">تاريخ التحقق:</span>
                                                <span>{{ $relation->verified_at->format('Y-m-d H:i') }}</span>
                                            </div>
                                        @endif
                                        @if($relation->verification_notes)
                                            <div class="mt-3">
                                                <span class="fw-bold d-block mb-2">ملاحظات التحقق:</span>
                                                <div class="p-2 bg-supportive rounded">{{ $relation->verification_notes }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- المستندات -->
                            <div class="card mb-4 dashboard-card">
                                <div class="card-header">
                                    <h6 class="mb-0">المستندات المرفقة</h6>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        @if($relation->birth_certificate)
                                            <a href="{{ route('admin.parent-verifications.document', [$relation->id, 'birth_certificate']) }}" class="list-group-item list-group-item-action" target="_blank">
                                                <i class="fas fa-file-alt me-2"></i> شهادة الميلاد
                                            </a>
                                        @endif
                                        
                                        @if($relation->parent_id_card)
                                            <a href="{{ route('admin.parent-verifications.document', [$relation->id, 'parent_id_card']) }}" class="list-group-item list-group-item-action" target="_blank">
                                                <i class="fas fa-id-card me-2"></i> بطاقة هوية ولي الأمر
                                            </a>
                                        @endif
                                        
                                        @if($relation->additional_document)
                                            <a href="{{ route('admin.parent-verifications.document', [$relation->id, 'additional_document']) }}" class="list-group-item list-group-item-action" target="_blank">
                                                <i class="fas fa-file me-2"></i> مستند إضافي
                                            </a>
                                        @endif
                                        
                                        @if(!$relation->birth_certificate && !$relation->parent_id_card && !$relation->additional_document)
                                            <div class="list-group-item text-center text-muted">
                                                <i class="fas fa-exclamation-circle me-2"></i> لا توجد مستندات مرفقة
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات ولي الأمر والطالب -->
                        <div class="col-md-6">
                            <div class="card mb-4 dashboard-card">
                                <div class="card-header">
                                    <h6 class="mb-0">معلومات ولي الأمر</h6>
                                </div>
                                <div class="card-body">
                                    @if($relation->parent)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-bold">الاسم:</span>
                                                <span>{{ $relation->parent->name }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-bold">البريد الإلكتروني:</span>
                                                <span>{{ $relation->parent->email }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-bold">رقم الهاتف:</span>
                                                <span>{{ $relation->parent->phone ?? 'غير متوفر' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-bold">تاريخ التسجيل:</span>
                                                <span>{{ $relation->parent->created_at->format('Y-m-d') }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            بيانات ولي الأمر غير متوفرة
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card mb-4 dashboard-card">
                                <div class="card-header">
                                    <h6 class="mb-0">معلومات الطالب</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold">اسم الطالب المدخل:</span>
                                            <span>{{ $relation->student_name }}</span>
                                        </div>
                                        
                                        @if($relation->student)
                                            <div class="alert alert-success mt-3">
                                                <h6 class="alert-heading">تم ربط الطالب بالفعل</h6>
                                                <hr>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="fw-bold">الاسم:</span>
                                                    <span>{{ $relation->student->name }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="fw-bold">البريد الإلكتروني:</span>
                                                    <span>{{ $relation->student->email }}</span>
                                                </div>
                                            </div>
                                        @elseif($relation->verification_status === 'pending')
                                            <div class="alert bg-supportive mt-3">
                                                <h6 class="alert-heading">الطلاب المطابقون للاسم</h6>
                                                <hr>
                                                @if(count($matchingStudents) > 0)
                                                    <div class="list-group">
                                                        @foreach($matchingStudents as $student)
                                                            <div class="list-group-item">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <strong>{{ $student->name }}</strong>
                                                                        <small class="d-block text-muted">{{ $student->email }}</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="mb-0">لم يتم العثور على طلاب مطابقين</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- نموذج التحقق -->
                            @if($relation->verification_status === 'pending')
                                <div class="card mb-4 dashboard-card">
                                    <div class="card-header">
                                        <h6 class="mb-0">إجراء التحقق</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.parent-verifications.verify', $relation->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">حالة التحقق</label>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="verification_status" id="status_approved" value="approved" required>
                                                    <label class="form-check-label" for="status_approved">
                                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i> موافقة</span>
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="verification_status" id="status_rejected" value="rejected">
                                                    <label class="form-check-label" for="status_rejected">
                                                        <span class="text-danger"><i class="fas fa-times-circle me-1"></i> رفض</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="mb-3" id="student_selection">
                                                <label for="student_id" class="form-label fw-bold">اختر الطالب</label>
                                                <select class="form-select" name="student_id" id="student_id">
                                                    <option value="">-- اختر الطالب --</option>
                                                    @foreach($allStudents as $student)
                                                        <option value="{{ $student->user_id }}">{{ $student->name }} ({{ $student->email }})</option>
                                                    @endforeach
                                                </select>
                                                <div class="form-text">في حالة الموافقة، يجب تحديد الطالب الذي سيتم ربطه بولي الأمر</div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="verification_notes" class="form-label fw-bold">ملاحظات التحقق</label>
                                                <textarea class="form-control" id="verification_notes" name="verification_notes" rows="3"></textarea>
                                            </div>

                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i> حفظ نتيجة التحقق
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const approvedRadio = document.getElementById('status_approved');
        const rejectedRadio = document.getElementById('status_rejected');
        const studentSelection = document.getElementById('student_selection');

        if (approvedRadio && rejectedRadio && studentSelection) {
            // Initial state
            studentSelection.style.display = approvedRadio.checked ? 'block' : 'none';

            // Add event listeners
            approvedRadio.addEventListener('change', function() {
                studentSelection.style.display = this.checked ? 'block' : 'none';
            });

            rejectedRadio.addEventListener('change', function() {
                studentSelection.style.display = this.checked ? 'none' : 'block';
            });
        }
    });
</script>
@endsection 