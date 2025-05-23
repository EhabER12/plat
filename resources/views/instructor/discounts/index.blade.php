@extends('layouts.instructor')

@section('title', 'Manage Discounts')
@section('page-title', 'Manage Discounts')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage course discounts to boost your sales</p>
        <a href="{{ route('instructor.discounts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Create New Discount
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Your Discounts</h5>
        </div>
        <div class="card-body">
            @if(count($discounts) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Applies To</th>
                                <th>Valid Period</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discounts as $discount)
                                <tr>
                                    <td>{{ $discount->name }}</td>
                                    <td>{{ ucfirst($discount->discount_type) }}</td>
                                    <td>
                                        @if($discount->discount_type == 'percentage')
                                            {{ $discount->discount_value }}%
                                        @else
                                            ${{ number_format($discount->discount_value, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($discount->applies_to_all_courses)
                                            <span class="badge bg-info">All Your Courses</span>
                                        @else
                                            {{ count($discount->courses) }} course(s)
                                        @endif
                                    </td>
                                    <td>
                                        @if($discount->start_date && $discount->end_date)
                                            {{ $discount->start_date->format('M d, Y') }} - {{ $discount->end_date->format('M d, Y') }}
                                        @elseif($discount->start_date)
                                            From {{ $discount->start_date->format('M d, Y') }}
                                        @elseif($discount->end_date)
                                            Until {{ $discount->end_date->format('M d, Y') }}
                                        @else
                                            No time limit
                                        @endif
                                    </td>
                                    <td>
                                        @if($discount->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('instructor.discounts.edit', $discount->discount_id) }}" class="btn btn-sm btn-primary me-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('instructor.discounts.destroy', $discount->discount_id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this discount?')">
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
                    {{ $discounts->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <p class="mb-0">You haven't created any discounts yet. Discounts are a great way to boost enrollment in your courses.</p>
                    <div class="mt-3">
                        <a href="{{ route('instructor.discounts.create') }}" class="btn btn-sm btn-primary">Create your first discount</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 
 

@section('title', 'Manage Discounts')
@section('page-title', 'Manage Discounts')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage course discounts to boost your sales</p>
        <a href="{{ route('instructor.discounts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Create New Discount
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Your Discounts</h5>
        </div>
        <div class="card-body">
            @if(count($discounts) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Applies To</th>
                                <th>Valid Period</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discounts as $discount)
                                <tr>
                                    <td>{{ $discount->name }}</td>
                                    <td>{{ ucfirst($discount->discount_type) }}</td>
                                    <td>
                                        @if($discount->discount_type == 'percentage')
                                            {{ $discount->discount_value }}%
                                        @else
                                            ${{ number_format($discount->discount_value, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($discount->applies_to_all_courses)
                                            <span class="badge bg-info">All Your Courses</span>
                                        @else
                                            {{ count($discount->courses) }} course(s)
                                        @endif
                                    </td>
                                    <td>
                                        @if($discount->start_date && $discount->end_date)
                                            {{ $discount->start_date->format('M d, Y') }} - {{ $discount->end_date->format('M d, Y') }}
                                        @elseif($discount->start_date)
                                            From {{ $discount->start_date->format('M d, Y') }}
                                        @elseif($discount->end_date)
                                            Until {{ $discount->end_date->format('M d, Y') }}
                                        @else
                                            No time limit
                                        @endif
                                    </td>
                                    <td>
                                        @if($discount->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('instructor.discounts.edit', $discount->discount_id) }}" class="btn btn-sm btn-primary me-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('instructor.discounts.destroy', $discount->discount_id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this discount?')">
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
                    {{ $discounts->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <p class="mb-0">You haven't created any discounts yet. Discounts are a great way to boost enrollment in your courses.</p>
                    <div class="mt-3">
                        <a href="{{ route('instructor.discounts.create') }}" class="btn btn-sm btn-primary">Create your first discount</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 