@extends('layouts.admin')

@section('title', 'Commission Settings')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Commission Settings</h1>
        <div>
            <a href="{{ route('admin.instructor-earnings.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Earnings
            </a>
            <a href="{{ route('admin.instructor-earnings.withdrawals') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-money-bill-wave fa-sm text-white-50"></i> Manage Withdrawals
            </a>
        </div>
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Sharing Settings</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.instructor-earnings.update-settings') }}" method="POST">
                        @csrf
                        
                        <div class="form-group row">
                            <label for="instructor_rate" class="col-sm-4 col-form-label">Instructor Commission Rate (%)</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control @error('instructor_rate') is-invalid @enderror" id="instructor_rate" name="instructor_rate" value="{{ old('instructor_rate', $instructorRate) }}" min="0" max="100" step="1" required>
                                @error('instructor_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Percentage of course revenue that goes to instructors.</small>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="platform_rate" class="col-sm-4 col-form-label">Platform Commission Rate (%)</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control @error('platform_rate') is-invalid @enderror" id="platform_rate" name="platform_rate" value="{{ old('platform_rate', $platformRate) }}" min="0" max="100" step="1" required>
                                @error('platform_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Percentage of course revenue that goes to the platform.</small>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> The instructor rate and platform rate must add up to 100%.
                        </div>
                        
                        <hr>
                        
                        <div class="form-group row">
                            <label for="min_withdrawal_amount" class="col-sm-4 col-form-label">Minimum Withdrawal Amount ($)</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control @error('min_withdrawal_amount') is-invalid @enderror" id="min_withdrawal_amount" name="min_withdrawal_amount" value="{{ old('min_withdrawal_amount', $minWithdrawalAmount) }}" min="0" step="1" required>
                                @error('min_withdrawal_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">The minimum amount that instructors can withdraw.</small>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="processing_days" class="col-sm-4 col-form-label">Withdrawal Processing Days</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control @error('processing_days') is-invalid @enderror" id="processing_days" name="processing_days" value="{{ old('processing_days', $processingDays) }}" min="1" max="30" step="1" required>
                                @error('processing_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">The number of days it takes to process a withdrawal request.</small>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-sm-8 offset-sm-4">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Sharing Example</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>For a $100 course:</h5>
                        <div class="progress mb-2" style="height: 30px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $instructorRate }}%;" aria-valuenow="{{ $instructorRate }}" aria-valuemin="0" aria-valuemax="100">
                                Instructor: ${{ $instructorRate }}
                            </div>
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $platformRate }}%;" aria-valuenow="{{ $platformRate }}" aria-valuemin="0" aria-valuemax="100">
                                Platform: ${{ $platformRate }}
                            </div>
                        </div>
                        <ul class="list-unstyled">
                            <li><strong>Instructor receives:</strong> ${{ $instructorRate }}</li>
                            <li><strong>Platform receives:</strong> ${{ $platformRate }}</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Important</h6>
                        <p class="mb-0">Changing these settings will only affect future earnings. Existing earnings will not be recalculated.</p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Withdrawal Process</h6>
                </div>
                <div class="card-body">
                    <p>Current withdrawal process:</p>
                    <ol>
                        <li>Instructor requests a withdrawal</li>
                        <li>Admin reviews and approves/rejects the request</li>
                        <li>If approved, funds are transferred to the instructor's payment account</li>
                        <li>Processing time: <strong>{{ $processingDays }} days</strong></li>
                        <li>Minimum withdrawal amount: <strong>${{ $minWithdrawalAmount }}</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Update platform rate when instructor rate changes
    document.getElementById('instructor_rate').addEventListener('input', function() {
        var instructorRate = parseInt(this.value) || 0;
        var platformRate = 100 - instructorRate;
        
        if (platformRate < 0) {
            platformRate = 0;
        }
        
        document.getElementById('platform_rate').value = platformRate;
    });
    
    // Update instructor rate when platform rate changes
    document.getElementById('platform_rate').addEventListener('input', function() {
        var platformRate = parseInt(this.value) || 0;
        var instructorRate = 100 - platformRate;
        
        if (instructorRate < 0) {
            instructorRate = 0;
        }
        
        document.getElementById('instructor_rate').value = instructorRate;
    });
</script>
@endsection
@endsection
