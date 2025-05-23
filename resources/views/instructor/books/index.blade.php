@extends('layouts.instructor')

@section('title', 'Manage Books')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Books</h1>
        <a href="{{ route('instructor.books.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Book
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Books</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBooks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 mb-4">
            <a href="{{ route('instructor.books.index', ['status' => 'published']) }}" class="text-decoration-none">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Published</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $publishedBooks }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-4 mb-4">
            <a href="{{ route('instructor.books.index', ['status' => 'draft']) }}" class="text-decoration-none">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Drafts</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $draftBooks }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-edit fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Your Books</h6>
            <div class="btn-group">
                <a href="{{ route('instructor.books.index') }}" class="btn btn-sm btn-outline-secondary {{ !isset($status) ? 'active' : '' }}">All</a>
                <a href="{{ route('instructor.books.index', ['status' => 'published']) }}" class="btn btn-sm btn-outline-secondary {{ isset($status) && $status == 'published' ? 'active' : '' }}">Published</a>
                <a href="{{ route('instructor.books.index', ['status' => 'draft']) }}" class="btn btn-sm btn-outline-secondary {{ isset($status) && $status == 'draft' ? 'active' : '' }}">Drafts</a>
            </div>
        </div>
        <div class="card-body">
            <!-- Search and Sorting -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form action="{{ route('instructor.books.index') }}" method="GET" class="form-inline">
                        @if(isset($status))
                        <input type="hidden" name="status" value="{{ $status }}">
                        @endif
                        @if(isset($sort))
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        @endif
                        @if(isset($direction))
                        <input type="hidden" name="direction" value="{{ $direction }}">
                        @endif
                        <div class="input-group w-100">
                            <input type="text" class="form-control" name="search" placeholder="Search books..." value="{{ $search ?? '' }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(isset($search))
                                <a href="{{ route('instructor.books.index', ['status' => $status ?? null, 'sort' => $sort ?? null, 'direction' => $direction ?? null]) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-right">
                    <div class="form-inline justify-content-end">
                        <label class="mr-2">Sort by:</label>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-toggle="dropdown" aria-expanded="false">
                                {{ ucfirst(str_replace('_', ' ', $sort ?? 'created_at')) }} 
                                <i class="fas fa-{{ ($direction ?? 'desc') == 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="sortDropdown">
                                <a class="dropdown-item" href="{{ route('instructor.books.index', ['status' => $status ?? null, 'search' => $search ?? null, 'sort' => 'title', 'direction' => 'asc']) }}">
                                    Title <i class="fas fa-arrow-up"></i>
                                </a>
                                <a class="dropdown-item" href="{{ route('instructor.books.index', ['status' => $status ?? null, 'search' => $search ?? null, 'sort' => 'title', 'direction' => 'desc']) }}">
                                    Title <i class="fas fa-arrow-down"></i>
                                </a>
                                <a class="dropdown-item" href="{{ route('instructor.books.index', ['status' => $status ?? null, 'search' => $search ?? null, 'sort' => 'price', 'direction' => 'asc']) }}">
                                    Price <i class="fas fa-arrow-up"></i>
                                </a>
                                <a class="dropdown-item" href="{{ route('instructor.books.index', ['status' => $status ?? null, 'search' => $search ?? null, 'sort' => 'price', 'direction' => 'desc']) }}">
                                    Price <i class="fas fa-arrow-down"></i>
                                </a>
                                <a class="dropdown-item" href="{{ route('instructor.books.index', ['status' => $status ?? null, 'search' => $search ?? null, 'sort' => 'created_at', 'direction' => 'desc']) }}">
                                    Newest First
                                </a>
                                <a class="dropdown-item" href="{{ route('instructor.books.index', ['status' => $status ?? null, 'search' => $search ?? null, 'sort' => 'created_at', 'direction' => 'asc']) }}">
                                    Oldest First
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($books->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="booksTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Price</th>
                            <th>Pages</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $book)
                        <tr>
                            <td>
                                <img src="{{ $book->getCoverImageUrlAttribute() }}" alt="{{ $book->title }}" width="50" height="70" class="img-thumbnail">
                            </td>
                            <td>{{ $book->title }}</td>
                            <td>{{ $book->author ?? 'N/A' }}</td>
                            <td>${{ number_format($book->price, 2) }}</td>
                            <td>{{ $book->pages ?? 'N/A' }}</td>
                            <td>
                                @if($book->is_published)
                                <span class="badge badge-success">Published</span>
                                @else
                                <span class="badge badge-warning">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('instructor.books.edit', $book) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('instructor.books.destroy', $book) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this book?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    @if($book->pdf_file)
                                    <a href="{{ $book->getPdfUrlAttribute() }}" target="_blank" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-file-pdf"></i> View PDF
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $books->appends(['status' => $status ?? null, 'search' => $search ?? null, 'sort' => $sort ?? null, 'direction' => $direction ?? null])->links() }}
            </div>
            @else
            <div class="text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-book-open fa-4x text-muted"></i>
                </div>
                @if(isset($search) && !empty($search))
                    <h4>No books found matching "{{ $search }}"</h4>
                    <p class="text-muted">Try using different keywords or <a href="{{ route('instructor.books.index', ['status' => $status ?? null]) }}">clear your search</a></p>
                @elseif(isset($status) && $status == 'published')
                    <h4>You don't have any published books yet</h4>
                    <p class="text-muted">Start by publishing one of your draft books or <a href="{{ route('instructor.books.create') }}">create a new book</a></p>
                @elseif(isset($status) && $status == 'draft')
                    <h4>You don't have any draft books</h4>
                    <p class="text-muted">All your books are published or <a href="{{ route('instructor.books.create') }}">create a new draft</a></p>
                @else
                    <h4>You haven't added any books yet</h4>
                    <p class="text-muted">Get started by adding your first book</p>
                    <a href="{{ route('instructor.books.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Add New Book
                    </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize datatables only if there are records
        @if($books->count() > 0)
        $('#booksTable').DataTable({
            "paging": false,
            "info": false,
            "searching": false,
            "order": []
        });
        @endif
    });
</script>
@endpush 