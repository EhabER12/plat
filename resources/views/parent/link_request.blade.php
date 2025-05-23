@extends('layouts.app')

@section('title', 'طلب ربط طالب')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<style>
    :root {
        --primary-color: #003366;
        --secondary-color: #FFD700;
        --accent-color: #FFD700;
        --background-color: #FAFAFA;
        --text-color: #1F1F1F;
        --border-color: #003366;
    }
    
    .link-request-container {
        padding: 30px 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #002244 100%);
        color: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    
    .page-header h1 {
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .page-header p {
        font-size: 1.1rem;
        opacity: 0.8;
    }
    
    .page-header::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 215, 0, 0.1);
        border-radius: 50%;
        z-index: 1;
    }
    
    .form-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        padding: 30px;
        margin-bottom: 30px;
    }
    
    .form-card h2 {
        color: var(--primary-color);
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .steps-container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .steps-container h3 {
        color: var(--primary-color);
        margin-bottom: 20px;
    }
    
    .step-item {
        display: flex;
        margin-bottom: 25px;
    }
    
    .step-item:last-child {
        margin-bottom: 0;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-left: 15px;
        flex-shrink: 0;
    }
    
    .step-content h4 {
        font-size: 1.1rem;
        margin-bottom: 5px;
        color: var(--primary-color);
    }
    
    .step-content p {
        color: #666;
        font-size: 0.95rem;
        margin-bottom: 0;
    }
    
    .form-label {
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
    }
    
    .form-control {
        border-radius: 5px;
        padding: 10px 15px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(0, 51, 102, 0.25);
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        padding: 10px 25px;
        border-radius: 5px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background-color: #002244;
        border-color: #002244;
        transform: translateY(-2px);
    }
    
    .document-preview {
        width: 100px;
        height: 100px;
        border: 1px dashed #ddd;
        border-radius: 5px;
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #aaa;
    }
    
    .document-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .existing-relations {
        margin-top: 40px;
    }
    
    .existing-relations h3 {
        color: var(--primary-color);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .relation-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 15px;
        border: 1px solid #eee;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .relation-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    
    .relation-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .relation-header h5 {
        margin-bottom: 0;
        color: var(--primary-color);
    }
    
    .relation-body {
        padding: 15px;
    }
    
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .status-badge.pending {
        background-color: #fff8e1;
        color: #f57c00;
    }
    
    .status-badge.approved {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
    
    .status-badge.rejected {
        background-color: #ffebee;
        color: #c62828;
    }
    
    .file-upload-info {
        font-size: 0.85rem;
        color: #666;
        margin-top: 5px;
    }
</style>
@endsection

@section('content')
<div class="container link-request-container">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right me-1"></i> العودة إلى لوحة التحكم
        </a>
    </div>
    
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-md-8">
                <h1>طلب ربط طالب</h1>
                <p>يمكنك إرسال طلب لربط حسابك بحساب ابنك الطالب لمتابعة تقدمه في الدورات والاختبارات</p>
            </div>
            <div class="col-md-4 text-end">
                <img src="{{ asset('img/link-request.svg') }}" alt="Link Request" class="img-fluid" style="max-height: 100px;">
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-7">
            <!-- Request Form -->
            <div class="form-card">
                <h2><i class="fas fa-link me-2"></i> طلب ربط جديد</h2>
                
                <form action="{{ route('parent.student.link.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="student_name" class="form-label">اسم الطالب</label>
                        <input type="text" class="form-control @error('student_name') is-invalid @enderror" id="student_name" name="student_name" value="{{ old('student_name') }}" required>
                        @error('student_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="relation" class="form-label">نوع صلة القرابة</label>
                        <select class="form-select @error('relation') is-invalid @enderror" id="relation" name="relation" required>
                            <option value="" selected disabled>اختر صلة القرابة</option>
                            <option value="parent" {{ old('relation') == 'parent' ? 'selected' : '' }}>أب/أم</option>
                            <option value="guardian" {{ old('relation') == 'guardian' ? 'selected' : '' }}>وصي</option>
                            <option value="other" {{ old('relation') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                        @error('relation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="birth_certificate" class="form-label">شهادة الميلاد</label>
                        <input type="file" class="form-control @error('birth_certificate') is-invalid @enderror" id="birth_certificate" name="birth_certificate" accept="image/*, application/pdf" required>
                        <small class="file-upload-info">يرجى تحميل نسخة واضحة من شهادة الميلاد (JPG, PNG, PDF) - الحد الأقصى: 10MB</small>
                        @error('birth_certificate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="parent_id_card" class="form-label">بطاقة هوية ولي الأمر</label>
                        <input type="file" class="form-control @error('parent_id_card') is-invalid @enderror" id="parent_id_card" name="parent_id_card" accept="image/*, application/pdf" required>
                        <small class="file-upload-info">يرجى تحميل نسخة واضحة من بطاقة الهوية (JPG, PNG, PDF) - الحد الأقصى: 10MB</small>
                        @error('parent_id_card')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="additional_document" class="form-label">مستند إضافي (اختياري)</label>
                        <input type="file" class="form-control @error('additional_document') is-invalid @enderror" id="additional_document" name="additional_document" accept="image/*, application/pdf">
                        <small class="file-upload-info">يمكنك تحميل مستند إضافي إذا لزم الأمر (JPG, PNG, PDF) - الحد الأقصى: 10MB</small>
                        @error('additional_document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات إضافية (اختياري)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> إرسال الطلب
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-5">
            <!-- Steps -->
            <div class="steps-container">
                <h3><i class="fas fa-info-circle me-2"></i> كيفية الربط</h3>
                
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h4>ملء نموذج الطلب</h4>
                        <p>قم بتعبئة النموذج بالمعلومات المطلوبة وارفق المستندات اللازمة للتحقق.</p>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h4>انتظار المراجعة</h4>
                        <p>سيتم مراجعة طلبك من قبل إدارة المنصة، وقد يستغرق ذلك ما يصل إلى 24 ساعة عمل.</p>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h4>الموافقة والربط</h4>
                        <p>بعد الموافقة على طلبك، سيتم ربط حساب الطالب بحسابك وستتمكن من متابعة تقدمه.</p>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h4>بدء المتابعة</h4>
                        <p>يمكنك الآن متابعة أداء الطالب والاطلاع على تقدمه في الدورات والاختبارات.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Existing Relation Requests -->
    @if(count($relationRequests) > 0)
    <div class="existing-relations">
        <h3><i class="fas fa-history me-2"></i> طلبات الربط السابقة</h3>
        
        <div class="row">
            @foreach($relationRequests as $request)
            <div class="col-md-6">
                <div class="relation-card">
                    <div class="relation-header d-flex justify-content-between align-items-center">
                        <h5>{{ $request->student_name }}</h5>
                        <span class="status-badge {{ $request->verification_status }}">
                            @if($request->verification_status == 'pending')
                                قيد المراجعة
                            @elseif($request->verification_status == 'approved')
                                تمت الموافقة
                            @elseif($request->verification_status == 'rejected')
                                مرفوض
                            @endif
                        </span>
                    </div>
                    <div class="relation-body">
                        <p><strong>تاريخ الطلب:</strong> {{ $request->created_at->format('Y/m/d') }}</p>
                        
                        @if($request->verification_status == 'approved')
                        <p><strong>تاريخ الموافقة:</strong> {{ $request->verified_at->format('Y/m/d') }}</p>
                        @endif
                        
                        @if($request->verification_status == 'rejected' && $request->verification_notes)
                        <p><strong>سبب الرفض:</strong> {{ $request->verification_notes }}</p>
                        
                        <div class="mt-3">
                            <form action="{{ route('parent.student.link.resubmit', $request->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-sync-alt me-1"></i> إعادة تقديم الطلب
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Preview images when uploaded
    document.addEventListener('DOMContentLoaded', function() {
        const previewFile = function(input, previewElement) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // Check if file is PDF
                    if (file.type === 'application/pdf') {
                        previewElement.innerHTML = '<i class="fas fa-file-pdf fa-3x"></i>';
                    } else {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        previewElement.innerHTML = '';
                        previewElement.appendChild(img);
                    }
                }
                
                reader.readAsDataURL(file);
            }
        };
        
        // Create preview elements
        const birthCertificateInput = document.getElementById('birth_certificate');
        const parentIdCardInput = document.getElementById('parent_id_card');
        const additionalDocInput = document.getElementById('additional_document');
        
        if (birthCertificateInput) {
            const previewContainer = document.createElement('div');
            previewContainer.className = 'preview-container';
            const preview = document.createElement('div');
            preview.className = 'document-preview';
            preview.innerHTML = '<i class="fas fa-upload"></i>';
            previewContainer.appendChild(preview);
            
            birthCertificateInput.parentNode.appendChild(previewContainer);
            
            birthCertificateInput.addEventListener('change', function() {
                previewFile(this, preview);
            });
        }
        
        if (parentIdCardInput) {
            const previewContainer = document.createElement('div');
            previewContainer.className = 'preview-container';
            const preview = document.createElement('div');
            preview.className = 'document-preview';
            preview.innerHTML = '<i class="fas fa-upload"></i>';
            previewContainer.appendChild(preview);
            
            parentIdCardInput.parentNode.appendChild(previewContainer);
            
            parentIdCardInput.addEventListener('change', function() {
                previewFile(this, preview);
            });
        }
        
        if (additionalDocInput) {
            const previewContainer = document.createElement('div');
            previewContainer.className = 'preview-container';
            const preview = document.createElement('div');
            preview.className = 'document-preview';
            preview.innerHTML = '<i class="fas fa-upload"></i>';
            previewContainer.appendChild(preview);
            
            additionalDocInput.parentNode.appendChild(previewContainer);
            
            additionalDocInput.addEventListener('change', function() {
                previewFile(this, preview);
            });
        }
    });
</script>
@endsection 