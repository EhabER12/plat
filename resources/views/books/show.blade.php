@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="book-cover-wrapper position-sticky" style="top: 100px;">
                <div class="book-image-container mb-4">
                    <img src="{{ $book->getCoverImageUrlAttribute() }}" alt="{{ $book->title }}" class="img-fluid rounded shadow book-cover">
                    <div class="book-hover-effect"></div>
                </div>

                <div class="book-price-section p-4 bg-white rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="h3 text-primary mb-0">${{ number_format($book->price, 2) }}</span>
                        <span class="badge bg-success p-2">
                            <i class="fas fa-book"></i> PDF Book
                        </span>
                    </div>

                    <div class="book-details-list">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="fas fa-language me-2"></i> Language:</span>
                            <span>{{ $book->language }}</span>
                        </div>
                        @if($book->pages)
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="fas fa-file-alt me-2"></i> Pages:</span>
                            <span>{{ $book->pages }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="fas fa-user me-2"></i> Author:</span>
                            <span>{{ $book->author ?? 'Not specified' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i> Published:</span>
                            <span>{{ $book->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        @auth
                            @if($book->pdf_file)
                                @if($isOwner || $hasPurchased)
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('books.pdf', $book) }}" class="btn btn-primary btn-lg mb-2" target="_blank">
                                            <i class="fas fa-eye me-2"></i> عرض الكتاب (PDF)
                                        </a>
                                        <a href="{{ route('books.download', $book) }}" class="btn btn-outline-primary btn-lg">
                                            <i class="fas fa-download me-2"></i> تحميل الكتاب (PDF)
                                        </a>
                                    </div>
                                @else
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('books.checkout', $book) }}" class="btn btn-primary btn-lg">
                                            <i class="fas fa-shopping-cart me-2"></i> شراء الكتاب - ${{ number_format($book->price, 2) }}
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning mb-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i> ملف PDF غير متوفر حالياً
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-lock me-2"></i> سجل دخول لتحميل الكتاب
                            </a>
                        @endauth

                        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i> العودة إلى الكتب
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('books.index') }}">Books</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $book->title }}</li>
                </ol>
            </nav>

            <h1 class="display-5 fw-bold mb-4">{{ $book->title }}</h1>

            <div class="book-description bg-white p-4 rounded shadow-sm mb-5">
                <h4 class="border-bottom pb-2 mb-4">Book Description</h4>
                <div class="description-content">
                    {!! $book->description ?? '<p class="text-muted">No description available for this book.</p>' !!}
                </div>
            </div>

            @if($relatedBooks->count() > 0)
            <div class="related-books bg-white p-4 rounded shadow-sm">
                <h4 class="border-bottom pb-2 mb-4">Related Books</h4>
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    @foreach($relatedBooks as $relatedBook)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm related-book-card">
                            <div class="row g-0">
                                <div class="col-4">
                                    <img src="{{ $relatedBook->getCoverImageUrlAttribute() }}" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="{{ $relatedBook->title }}">
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $relatedBook->title }}</h5>
                                        <p class="card-text text-muted small">
                                            <i class="fas fa-user"></i> {{ $relatedBook->author ?? 'Unknown Author' }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-primary">${{ number_format($relatedBook->price, 2) }}</span>
                                            <a href="{{ route('books.show', $relatedBook) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .book-cover {
        width: 100%;
        transition: transform 0.5s ease;
    }

    .book-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 5px;
    }

    .book-hover-effect {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.2) 100%);
    }

    .book-image-container:hover .book-cover {
        transform: scale(1.03);
    }

    .book-price-section {
        transition: all 0.3s ease;
    }

    .book-price-section:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }

    .description-content {
        line-height: 1.8;
    }

    .related-book-card {
        transition: all 0.3s ease;
    }

    .related-book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>
@endpush