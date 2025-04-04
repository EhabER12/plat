@extends('admin.layout')

@section('title', 'Categories Management')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Categories Management</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-folder-plus me-1"></i> Add New Category
            </button>
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
        
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Categories List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Parent Category</th>
                                <th>Courses</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ $category->category_id }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ Str::limit($category->description, 50) }}</td>
                                    <td>
                                        @if($category->parent)
                                            {{ $category->parent->name }}
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->courses_count }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewCategoryModal{{ $category->category_id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->category_id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->category_id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- View Category Modal -->
                                        <div class="modal fade" id="viewCategoryModal{{ $category->category_id }}" tabindex="-1" aria-labelledby="viewCategoryModalLabel{{ $category->category_id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="viewCategoryModalLabel{{ $category->category_id }}">Category Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>ID:</strong> {{ $category->category_id }}</p>
                                                        <p><strong>Name:</strong> {{ $category->name }}</p>
                                                        <p><strong>Description:</strong> {{ $category->description }}</p>
                                                        <p><strong>Parent Category:</strong> 
                                                            @if($category->parent)
                                                                {{ $category->parent->name }}
                                                            @else
                                                                None
                                                            @endif
                                                        </p>
                                                        <p><strong>Number of Courses:</strong> {{ $category->courses_count }}</p>
                                                        <p><strong>Created At:</strong> {{ $category->created_at }}</p>
                                                        <p><strong>Updated At:</strong> {{ $category->updated_at }}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Edit Category Modal -->
                                        <div class="modal fade" id="editCategoryModal{{ $category->category_id }}" tabindex="-1" aria-labelledby="editCategoryModalLabel{{ $category->category_id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editCategoryModalLabel{{ $category->category_id }}">Edit Category</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('admin.categories.update', $category->category_id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="name{{ $category->category_id }}" class="form-label">Name</label>
                                                                <input type="text" class="form-control" id="name{{ $category->category_id }}" name="name" value="{{ $category->name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="description{{ $category->category_id }}" class="form-label">Description</label>
                                                                <textarea class="form-control" id="description{{ $category->category_id }}" name="description" rows="3">{{ $category->description }}</textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="parent_id{{ $category->category_id }}" class="form-label">Parent Category</label>
                                                                <select class="form-select" id="parent_id{{ $category->category_id }}" name="parent_id">
                                                                    <option value="">None</option>
                                                                    @foreach($categories as $parentCategory)
                                                                        @if($parentCategory->category_id != $category->category_id && !$parentCategory->parent_id)
                                                                            <option value="{{ $parentCategory->category_id }}" {{ $category->parent_id == $parentCategory->category_id ? 'selected' : '' }}>
                                                                                {{ $parentCategory->name }}
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Update Category</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Delete Category Modal -->
                                        <div class="modal fade" id="deleteCategoryModal{{ $category->category_id }}" tabindex="-1" aria-labelledby="deleteCategoryModalLabel{{ $category->category_id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteCategoryModalLabel{{ $category->category_id }}">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete the category <strong>{{ $category->name }}</strong>?</p>
                                                        <p>This will also delete all subcategories within this category.</p>
                                                        @if($category->courses_count > 0)
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                                This category has {{ $category->courses_count }} courses. Deleting this category will orphan these courses.
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.categories.delete', $category->category_id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete Category</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No categories found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Parent Category</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">None</option>
                                @foreach($categories as $category)
                                    @if(!$category->parent_id)
                                        <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 