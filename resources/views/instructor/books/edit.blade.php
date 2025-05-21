@extends('layouts.instructor')

@section('title', 'Edit Book')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Book: {{ $book->title }}</h1>
        <a href="{{ route('instructor.books.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Books
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Book Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.books.update', $book) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="title">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $book->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" class="form-control @error('author') is-invalid @enderror" id="author" name="author" value="{{ old('author', $book->author) }}">
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6">{{ old('description', $book->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $book->price) }}" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pages">Number of Pages</label>
                                    <input type="number" min="1" class="form-control @error('pages') is-invalid @enderror" id="pages" name="pages" value="{{ old('pages', $book->pages) }}">
                                    @error('pages')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="language">Language</label>
                                    <select class="form-control @error('language') is-invalid @enderror" id="language" name="language">
                                        <option value="English" {{ old('language', $book->language) == 'English' ? 'selected' : '' }}>English</option>
                                        <option value="Arabic" {{ old('language', $book->language) == 'Arabic' ? 'selected' : '' }}>Arabic</option>
                                        <option value="French" {{ old('language', $book->language) == 'French' ? 'selected' : '' }}>French</option>
                                        <option value="Spanish" {{ old('language', $book->language) == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                                        <option value="German" {{ old('language', $book->language) == 'German' ? 'selected' : '' }}>German</option>
                                    </select>
                                    @error('language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cover_image">Cover Image</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" accept="image/*" onchange="previewImage(this)">
                                <label class="custom-file-label" for="cover_image">Choose file</label>
                                @error('cover_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mt-3 text-center">
                                <img id="cover_preview" src="{{ $book->getCoverImageUrlAttribute() }}" alt="Cover Preview" class="img-thumbnail" style="max-height: 250px; max-width: 100%;">
                            </div>
                            <small class="form-text text-muted">Leave empty to keep the current cover image.</small>
                        </div>

                        <div class="form-group mt-4">
                            <label for="pdf_file">PDF File</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('pdf_file') is-invalid @enderror" id="pdf_file" name="pdf_file" accept="application/pdf">
                                <label class="custom-file-label" for="pdf_file">Choose file</label>
                                @error('pdf_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Maximum file size: 10MB. Leave empty to keep the current PDF file.</small>
                            
                            @if($book->pdf_file)
                            <div class="mt-2">
                                <span class="text-success">
                                    <i class="fas fa-check-circle"></i> Current PDF file: {{ $book->pdf_file }}
                                </span>
                                <a href="{{ $book->getPdfUrlAttribute() }}" target="_blank" class="btn btn-sm btn-info ml-2" 
                                   onclick="window.open(this.href, '_blank', 'location=yes,height=800,width=800,scrollbars=yes,status=yes'); return false;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('books.download', $book) }}" class="btn btn-sm btn-secondary ml-2">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                            @endif
                        </div>

                        <div class="form-group mt-4">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_published" name="is_published" {{ old('is_published', $book->is_published) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_published">Published</label>
                            </div>
                            <small class="form-text text-muted">If unchecked, the book will be saved as a draft.</small>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Book
                    </button>
                    <a href="{{ route('instructor.books.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize the rich text editor for description
    $(document).ready(function() {
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace('description');
        }
        
        // Update file input label with selected filename
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
    
    // Preview the selected cover image
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $('#cover_preview').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush 