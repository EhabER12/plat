@extends('layouts.app')

@section('title', 'Books')

@section('content')
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold mb-3">Explore Our Books</h1>
            <p class="lead text-muted">Discover a wide range of educational books to enhance your learning journey</p>
        </div>
    </div>

    @if($books->count() > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            @foreach($books as $book)
                <div class="col mb-4">
                    <div class="card h-100 book-card shadow-sm">
                        <div class="book-cover-container">
                            <img src="{{ $book->getCoverImageUrlAttribute() }}" class="card-img-top book-cover" alt="{{ $book->title }}">
                            <div class="book-overlay">
                                <a href="{{ route('books.show', $book) }}" class="btn btn-primary btn-sm">View Details</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $book->title }}</h5>
                            <p class="card-text text-muted mb-1">
                                <small>
                                    <i class="fas fa-user"></i> {{ $book->author ?? 'Unknown Author' }}
                                </small>
                            </p>
                            <p class="card-text text-muted mb-2">
                                <small>
                                    <i class="fas fa-language"></i> {{ $book->language }}
                                    @if($book->pages)
                                        <span class="mx-1">•</span>
                                        <i class="fas fa-file-alt"></i> {{ $book->pages }} pages
                                    @endif
                                </small>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="h5 text-primary mb-0">${{ number_format($book->price, 2) }}</span>
                                <a href="{{ route('books.show', $book) }}" class="btn btn-outline-primary btn-sm">Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="d-flex justify-content-center mt-5">
            {{ $books->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-book-open fa-5x text-muted"></i>
            </div>
            <h3>No books available yet</h3>
            <p class="text-muted">Check back soon for new additions to our library!</p>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .book-card {
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
    }
    
    .book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .book-cover-container {
        position: relative;
        overflow: hidden;
        height: 280px;
    }
    
    .book-cover {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.5s ease;
    }
    
    .book-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .book-card:hover .book-overlay {
        opacity: 1;
    }
    
    .book-card:hover .book-cover {
        transform: scale(1.05);
    }
</style>
@endpush 