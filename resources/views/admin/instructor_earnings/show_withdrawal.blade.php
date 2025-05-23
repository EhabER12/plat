@extends('layouts.admin')

@section('title', 'Withdrawal Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Withdrawal Details #{{ $withdrawal->withdrawal_id }}</h1>
        <a href="{{ route('admin.instructor-earnings.withdrawals') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Withdrawals
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Withdrawal Information</h6>
                    <div>
                        @if($withdrawal->status == 'pending')
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Withdrawal Details</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th>Amount:</th>
                                    <td>${{ number_format($withdrawal->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($withdrawal->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($withdrawal->status == 'completed')
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($withdrawal->status == 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @elseif($withdrawal->status == 'cancelled')
                                            <span class="badge badge-secondary">Cancelled</span>
                                        @else
                                            <span class="badge badge-info">{{ ucfirst($withdrawal->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Requested:</th>
                                    <td>{{ $withdrawal->requested_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Processed:</th>
                                    <td>{{ $withdrawal->processed_at ? $withdrawal->processed_at->format('M d, Y h:i A') : 'Not yet processed' }}</td>
                                </tr>
                                @if($withdrawal->processed_by)
                                    <tr>
                                        <th>Processed By:</th>
                                        <td>{{ $withdrawal->processor->name }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Instructor Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $withdrawal->instructor->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $withdrawal->instructor->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $withdrawal->instructor->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Joined:</th>
                                    <td>{{ $withdrawal->instructor->created_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Payment Method</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Method:</strong> 
                                                @if($withdrawal->payment_provider == 'vodafone_cash')
                                                    فودافون كاش
                                                @elseif($withdrawal->payment_provider == 'instapay')
                                                    إنستا باي
                                                @else
                                                    {{ ucfirst($withdrawal->payment_provider) }}
                                                @endif
                                            </p>
                                            <p><strong>Account/Phone:</strong> {{ $withdrawal->provider_account_id ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($withdrawal->transfer_receipt)
                                                <p><strong>إثبات التحويل:</strong></p>
                                                <div class="receipt-preview">
                                                    <a href="{{ asset($withdrawal->transfer_receipt) }}" target="_blank" class="receipt-link">
                                                        <img src="{{ asset($withdrawal->transfer_receipt) }}" alt="إثبات التحويل" class="img-thumbnail receipt-image">
                                                        <div class="receipt-overlay">
                                                            <i class="fas fa-search-plus"></i>
                                                            <span>انقر للمعاينة</span>
                                                        </div>
                                                    </a>
                                                </div>
                                            @else
                                                <p class="text-muted">لا يوجد إثبات تحويل بعد.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($withdrawal->notes)
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Notes</h5>
                                <p class="card-text">{{ $withdrawal->notes }}</p>
                            </div>
                        </div>
                    @endif

                    @if($withdrawal->status == 'pending')
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Pending Withdrawal</h5>
                            <p>This withdrawal request is pending your approval. Please review the details and take appropriate action.</p>
                        </div>
                    @elseif($withdrawal->status == 'completed')
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> Completed Withdrawal</h5>
                            <p>This withdrawal has been processed and completed.</p>
                        </div>
                    @elseif($withdrawal->status == 'rejected')
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-times-circle"></i> Rejected Withdrawal</h5>
                            <p>This withdrawal request was rejected.</p>
                        </div>
                    @elseif($withdrawal->status == 'cancelled')
                        <div class="alert alert-secondary">
                            <h5><i class="fas fa-ban"></i> Cancelled Withdrawal</h5>
                            <p>This withdrawal request was cancelled by the instructor.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Earnings Included in This Withdrawal</h6>
                </div>
                <div class="card-body">
                    @if($earnings->isEmpty())
                        <div class="alert alert-info">
                            <p class="mb-0">No earnings details available for this withdrawal.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Course</th>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Platform Fee</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($earnings as $earning)
                                        <tr>
                                            <td>{{ $earning->created_at->format('M d, Y') }}</td>
                                            <td>{{ $earning->course->title }}</td>
                                            <td>{{ $earning->payment->student->name }}</td>
                                            <td>${{ number_format($earning->amount, 2) }}</td>
                                            <td>${{ number_format($earning->platform_fee, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-right">Total:</th>
                                        <th>${{ number_format($earnings->sum('amount'), 2) }}</th>
                                        <th>${{ number_format($earnings->sum('platform_fee'), 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Instructor Earnings Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Total Earnings</h5>
                        <h2 class="text-primary">${{ number_format($withdrawal->instructor->earnings()->sum('amount'), 2) }}</h2>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Available Balance</h5>
                        <h3 class="text-success">${{ number_format($withdrawal->instructor->available_earnings, 2) }}</h3>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Pending Earnings</h5>
                        <h3 class="text-warning">${{ number_format($withdrawal->instructor->pending_earnings, 2) }}</h3>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Withdrawn Earnings</h5>
                        <h3 class="text-info">${{ number_format($withdrawal->instructor->withdrawn_earnings, 2) }}</h3>
                    </div>
                    
                    <a href="{{ route('admin.instructor-earnings.instructor', $withdrawal->instructor_id) }}" class="btn btn-block btn-primary">
                        <i class="fas fa-chart-line"></i> View Full Earnings Report
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Withdrawal Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Withdrawal Requested</h6>
                                <p class="timeline-date">{{ $withdrawal->requested_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        
                        @if($withdrawal->status == 'pending')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Awaiting Approval</h6>
                                    <p class="timeline-date">Current Status</p>
                                </div>
                            </div>
                        @elseif($withdrawal->status == 'completed')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Withdrawal Approved</h6>
                                    <p class="timeline-date">{{ $withdrawal->processed_at->format('M d, Y h:i A') }}</p>
                                    <p class="text-muted">By: {{ $withdrawal->processor->name }}</p>
                                </div>
                            </div>
                        @elseif($withdrawal->status == 'rejected')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Withdrawal Rejected</h6>
                                    <p class="timeline-date">{{ $withdrawal->processed_at->format('M d, Y h:i A') }}</p>
                                    <p class="text-muted">By: {{ $withdrawal->processor->name }}</p>
                                </div>
                            </div>
                        @elseif($withdrawal->status == 'cancelled')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-secondary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Withdrawal Cancelled</h6>
                                    <p class="timeline-date">{{ $withdrawal->processed_at ? $withdrawal->processed_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.instructor-earnings.process-withdrawal', $withdrawal->withdrawal_id) }}" method="POST" enctype="multipart/form-data" id="approveForm">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="approveModalLabel">
                            <i class="fas fa-check-circle mr-2"></i> الموافقة على طلب سحب الأرباح
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i> تفاصيل الطلب</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label font-weight-bold">اسم المدرس:</label>
                                            <div class="col-sm-8">
                                                <p class="form-control-plaintext">{{ $withdrawal->instructor->name }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label font-weight-bold">المبلغ المطلوب:</label>
                                            <div class="col-sm-8">
                                                <p class="form-control-plaintext text-success font-weight-bold">${{ number_format($withdrawal->amount, 2) }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label font-weight-bold">تاريخ الطلب:</label>
                                            <div class="col-sm-8">
                                                <p class="form-control-plaintext">{{ $withdrawal->requested_at->format('d M Y, h:i A') }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label font-weight-bold">طريقة الدفع:</label>
                                            <div class="col-sm-8">
                                                <p class="form-control-plaintext">
                                                    @if($withdrawal->payment_provider == 'vodafone_cash')
                                                        <span class="badge badge-pill badge-danger"><i class="fas fa-mobile-alt mr-1"></i> فودافون كاش</span>
                                                    @elseif($withdrawal->payment_provider == 'instapay')
                                                        <span class="badge badge-pill badge-info"><i class="fas fa-credit-card mr-1"></i> إنستا باي</span>
                                                    @else
                                                        <span class="badge badge-pill badge-secondary">{{ ucfirst($withdrawal->payment_provider) }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label font-weight-bold">رقم الحساب:</label>
                                            <div class="col-sm-8">
                                                <p class="form-control-plaintext">{{ $withdrawal->provider_account_id }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-warning">
                                    <div class="d-flex">
                                        <div class="mr-3">
                                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                                        </div>
                                        <div>
                                            <h5 class="alert-heading">تنبيه هام!</h5>
                                            <p>يجب التأكد من إتمام عملية التحويل بنجاح قبل الموافقة على هذا الطلب. بعد الموافقة، سيتم إشعار المدرس تلقائيًا.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="transfer_receipt">
                                        <i class="fas fa-file-image text-primary mr-1"></i>
                                        صورة إثبات التحويل <span class="text-danger">*</span>
                                    </label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="transfer_receipt" name="transfer_receipt" accept="image/*" required>
                                        <label class="custom-file-label" for="transfer_receipt">اختر صورة...</label>
                                    </div>
                                    <small class="form-text text-muted">يرجى رفع صورة لإثبات التحويل (لقطة شاشة من تطبيق البنك أو إيصال التحويل)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-3">
                            <label for="notes">
                                <i class="fas fa-comment-alt text-primary mr-1"></i>
                                ملاحظات (اختياري)
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="أي ملاحظات إضافية حول عملية التحويل..."></textarea>
                        </div>

                        <div class="alert alert-success mt-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="approvalConfirm" required>
                                <label class="custom-control-label" for="approvalConfirm">
                                    أؤكد أنني قمت بتحويل مبلغ <strong>${{ number_format($withdrawal->amount, 2) }}</strong> 
                                    إلى حساب المدرس <strong>{{ $withdrawal->instructor->name }}</strong> 
                                    عبر <strong>
                                        @if($withdrawal->payment_provider == 'vodafone_cash')
                                            فودافون كاش
                                        @elseif($withdrawal->payment_provider == 'instapay')
                                            إنستا باي
                                        @else
                                            {{ ucfirst($withdrawal->payment_provider) }}
                                        @endif
                                    </strong>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> إلغاء
                        </button>
                        <button type="submit" class="btn btn-success" id="approveBtn" disabled>
                            <i class="fas fa-check mr-1"></i> تأكيد الموافقة وإتمام العملية
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.instructor-earnings.process-withdrawal', $withdrawal->withdrawal_id) }}" method="POST" id="rejectForm">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel">
                            <i class="fas fa-times-circle mr-2"></i> رفض طلب سحب الأرباح
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <div class="d-flex">
                                <div class="mr-3">
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading">تنبيه!</h5>
                                    <p>عند رفض الطلب، سيتم إعادة المبلغ إلى رصيد المدرس المتاح وسيتم إشعار المدرس بسبب الرفض.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i> تفاصيل الطلب</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>المدرس:</strong> {{ $withdrawal->instructor->name }}</p>
                                <p><strong>المبلغ:</strong> <span class="text-danger">${{ number_format($withdrawal->amount, 2) }}</span></p>
                                <p><strong>تاريخ الطلب:</strong> {{ $withdrawal->requested_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">
                                <i class="fas fa-exclamation-circle text-danger mr-1"></i>
                                سبب الرفض <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="يرجى توضيح سبب رفض طلب السحب..." required></textarea>
                            <small class="form-text text-muted">سيتم إرسال هذا السبب للمدرس في إشعار الرفض</small>
                        </div>

                        <div class="alert alert-danger mt-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="rejectConfirm" required>
                                <label class="custom-control-label" for="rejectConfirm">
                                    أؤكد أنني أريد رفض طلب سحب الأرباح هذا وإعادة المبلغ لرصيد المدرس المتاح
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-arrow-left mr-1"></i> عودة
                        </button>
                        <button type="submit" class="btn btn-danger" id="rejectBtn" disabled>
                            <i class="fas fa-times mr-1"></i> تأكيد رفض الطلب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        top: 5px;
    }
    .timeline-content {
        position: relative;
    }
    .timeline-title {
        margin-bottom: 5px;
    }
    .timeline-date {
        color: #6c757d;
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    .timeline:before {
        content: '';
        position: absolute;
        left: -23px;
        width: 2px;
        height: 100%;
        background-color: #e3e6f0;
    }
    .receipt-preview {
        position: relative;
        display: inline-block;
    }
    .receipt-link {
        display: block;
        position: relative;
        overflow: hidden;
        border-radius: 4px;
    }
    .receipt-image {
        max-width: 200px;
        transition: transform 0.3s ease;
    }
    .receipt-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .receipt-link:hover .receipt-overlay {
        opacity: 1;
    }
    .receipt-link:hover .receipt-image {
        transform: scale(1.05);
    }
    .receipt-overlay i {
        font-size: 24px;
        margin-bottom: 8px;
    }
    .receipt-overlay span {
        font-size: 14px;
    }
</style>

@section('scripts')
@parent
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add form submission console logging
        const approveForm = document.querySelector('#approveForm');
        const rejectForm = document.querySelector('#rejectForm');
        
        if (approveForm) {
            console.log('Approve form action:', approveForm.getAttribute('action'));
            approveForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default submission
                console.log('Approve form submitted');
                
                // Create a loading indicator
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
                submitBtn.disabled = true;
                
                // Submit the form using fetch API
                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.error) {
                        alert('Error: ' + data.error);
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    } else if (data && data.success) {
                        window.location.href = data.redirect || '{{ route("admin.instructor-earnings.withdrawals") }}';
                    } else {
                        this.submit(); // Fall back to normal form submission if response format is unexpected
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.submit(); // Fall back to normal form submission on error
                });
            });
        }
        
        if (rejectForm) {
            console.log('Reject form action:', rejectForm.getAttribute('action'));
            rejectForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default submission
                console.log('Reject form submitted');
                
                // Create a loading indicator
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
                submitBtn.disabled = true;
                
                // Submit the form using fetch API
                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.error) {
                        alert('Error: ' + data.error);
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    } else if (data && data.success) {
                        window.location.href = data.redirect || '{{ route("admin.instructor-earnings.withdrawals") }}';
                    } else {
                        this.submit(); // Fall back to normal form submission if response format is unexpected
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.submit(); // Fall back to normal form submission on error
                });
            });
        }
        
        // Handle file input visual display
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'اختر صورة...');
        });
        
        // Handle approval confirmation
        $('#approvalConfirm').on('change', function() {
            $('#approveBtn').prop('disabled', !this.checked);
        });
        
        // Handle reject confirmation
        $('#rejectConfirm').on('change', function() {
            $('#rejectBtn').prop('disabled', !this.checked);
        });
    });
</script>
@endsection
