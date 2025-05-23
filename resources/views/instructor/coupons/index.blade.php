@extends('layouts.instructor')

@section('title', 'Manage Coupons')
@section('page-title', 'Manage Coupons')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage discount coupons for your courses</p>
        <a href="{{ route('instructor.coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Create New Coupon
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Your Coupons</h5>
        </div>
        <div class="card-body">
            @if(count($coupons) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Usage</th>
                                <th>Valid Period</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->code }}</td>
                                    <td>{{ ucfirst($coupon->type) }}</td>
                                    <td>
                                        @if($coupon->type == 'percentage')
                                            {{ $coupon->value }}%
                                        @else
                                            ${{ number_format($coupon->value, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $coupon->used_count }} 
                                        @if($coupon->max_uses)
                                            / {{ $coupon->max_uses }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->valid_from && $coupon->valid_to)
                                            {{ $coupon->valid_from->format('M d, Y') }} - {{ $coupon->valid_to->format('M d, Y') }}
                                        @elseif($coupon->valid_from)
                                            From {{ $coupon->valid_from->format('M d, Y') }}
                                        @elseif($coupon->valid_to)
                                            Until {{ $coupon->valid_to->format('M d, Y') }}
                                        @else
                                            No time limit
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('instructor.coupons.edit', $coupon->coupon_id) }}" class="btn btn-sm btn-primary me-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('instructor.coupons.destroy', $coupon->coupon_id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this coupon?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $coupons->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <p class="mb-0">You haven't created any coupons yet. Coupons can help boost sales by offering discounts to your students.</p>
                    <div class="mt-3">
                        <a href="{{ route('instructor.coupons.create') }}" class="btn btn-sm btn-primary">Create your first coupon</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 
 

@section('title', 'Manage Coupons')
@section('page-title', 'Manage Coupons')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage discount coupons for your courses</p>
        <a href="{{ route('instructor.coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Create New Coupon
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Your Coupons</h5>
        </div>
        <div class="card-body">
            @if(count($coupons) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Usage</th>
                                <th>Valid Period</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->code }}</td>
                                    <td>{{ ucfirst($coupon->type) }}</td>
                                    <td>
                                        @if($coupon->type == 'percentage')
                                            {{ $coupon->value }}%
                                        @else
                                            ${{ number_format($coupon->value, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $coupon->used_count }} 
                                        @if($coupon->max_uses)
                                            / {{ $coupon->max_uses }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->valid_from && $coupon->valid_to)
                                            {{ $coupon->valid_from->format('M d, Y') }} - {{ $coupon->valid_to->format('M d, Y') }}
                                        @elseif($coupon->valid_from)
                                            From {{ $coupon->valid_from->format('M d, Y') }}
                                        @elseif($coupon->valid_to)
                                            Until {{ $coupon->valid_to->format('M d, Y') }}
                                        @else
                                            No time limit
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('instructor.coupons.edit', $coupon->coupon_id) }}" class="btn btn-sm btn-primary me-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('instructor.coupons.destroy', $coupon->coupon_id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this coupon?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $coupons->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <p class="mb-0">You haven't created any coupons yet. Coupons can help boost sales by offering discounts to your students.</p>
                    <div class="mt-3">
                        <a href="{{ route('instructor.coupons.create') }}" class="btn btn-sm btn-primary">Create your first coupon</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 