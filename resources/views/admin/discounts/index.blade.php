@extends('admin.layout')

@section('title', 'Manage Discounts')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Discounts</h1>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Create New Discount
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Discounts</h5>
        </div>
        <div class="card-body">
            @if(count($discounts) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Applies To</th>
                                <th>Valid Period</th>
                                <th>Status</th>
                                <th>Usage</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discounts as $discount)
                                <tr>
                                    <td>{{ $discount->code }}</td>
                                    <td>{{ ucfirst($discount->type) }}</td>
                                    <td>
                                        @if($discount->type == 'percentage')
                                            {{ $discount->value }}%
                                        @else
                                            ${{ number_format($discount->value, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($discount->applies_to_all_courses())
                                            <span class="badge bg-info">All Courses</span>
                                        @else
                                            @if($discount->courses)
                                            {{ $discount->courses->count() }} course(s)
                                            @else
                                            0 course(s)
                                            @endif
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
                                    <td>{{ $discount->usage_count }} / {{ $discount->usage_limit ?? 'Unlimited' }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('admin.discounts.edit', $discount->discount_id) }}" class="btn btn-sm btn-primary me-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.discounts.destroy', $discount->discount_id) }}" method="POST" class="d-inline delete-form">
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
                    No discounts found. <a href="{{ route('admin.discounts.create') }}">Create your first discount</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 