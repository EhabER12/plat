@extends('layouts.admin')

@section('title', 'Website Appearance')
@section('page-title', 'Website Appearance Settings')

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="appearanceTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="navbar-banner-tab" data-bs-toggle="tab" data-bs-target="#navbar-banner" 
                        type="button" role="tab" aria-controls="navbar-banner" aria-selected="true">
                    <i class="fas fa-bars me-1"></i> Navbar Banner
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" 
                        type="button" role="tab" aria-controls="hero" aria-selected="false">
                    <i class="fas fa-image me-1"></i> Hero Section
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="features-tab" data-bs-toggle="tab" data-bs-target="#features" 
                        type="button" role="tab" aria-controls="features" aria-selected="false">
                    <i class="fas fa-th-large me-1"></i> Features
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" 
                        type="button" role="tab" aria-controls="stats" aria-selected="false">
                    <i class="fas fa-chart-bar me-1"></i> Stats
                </button>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="appearanceTabsContent">
        
            <!-- Navbar Banner Tab -->
            <div class="tab-pane fade show active" id="navbar-banner" role="tabpanel" aria-labelledby="navbar-banner-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Navbar Banner Settings</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.website-appearance.navbar-banner') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="banner_title" class="form-label">Banner Title</label>
                                    <input type="text" class="form-control" id="banner_title" name="banner_title" 
                                           value="{{ $navbarBannerSettings['banner_title'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="banner_bg_color" class="form-label">Background Color</label>
                                    <input type="color" class="form-control form-control-color" id="banner_bg_color" name="banner_bg_color" 
                                           value="{{ $navbarBannerSettings['banner_bg_color'] ?? '#f8f9fa' }}">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="banner_subtitle" class="form-label">Banner Subtitle</label>
                                <textarea class="form-control" id="banner_subtitle" name="banner_subtitle" rows="2">{{ $navbarBannerSettings['banner_subtitle'] ?? '' }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="banner_image" class="form-label">Banner Background Image</label>
                                @if(isset($navbarBannerSettings['banner_image']))
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $navbarBannerSettings['banner_image']) }}" 
                                             alt="Current Banner" class="img-thumbnail" style="max-height: 100px">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="banner_image" name="banner_image">
                                <small class="text-muted">Recommended size: 1920x200px. Leave empty to keep current image.</small>
                            </div>
                            
                            <h6 class="fw-bold mt-4 mb-3">Statistics Settings</h6>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="students_count" class="form-label">Students Count</label>
                                    <input type="number" class="form-control" id="students_count" name="students_count" 
                                           value="{{ $navbarBannerSettings['students_count'] ?? 0 }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="courses_count" class="form-label">Courses Count</label>
                                    <input type="number" class="form-control" id="courses_count" name="courses_count" 
                                           value="{{ $navbarBannerSettings['courses_count'] ?? 0 }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="instructors_count" class="form-label">Instructors Count</label>
                                    <input type="number" class="form-control" id="instructors_count" name="instructors_count" 
                                           value="{{ $navbarBannerSettings['instructors_count'] ?? 0 }}">
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Banner Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Hero Section Tab -->
            <div class="tab-pane fade" id="hero" role="tabpanel" aria-labelledby="hero-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Hero Section Settings</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.website-appearance.hero') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="hero_title" class="form-label">Hero Title</label>
                                <input type="text" class="form-control" id="hero_title" name="hero_title" 
                                       value="{{ $heroSettings['hero_title'] ?? '' }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="hero_subtitle" class="form-label">Hero Subtitle</label>
                                <textarea class="form-control" id="hero_subtitle" name="hero_subtitle" rows="3">{{ $heroSettings['hero_subtitle'] ?? '' }}</textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="hero_bg_color" class="form-label">Background Color</label>
                                    <input type="color" class="form-control form-control-color" id="hero_bg_color" name="hero_bg_color" 
                                           value="{{ $heroSettings['hero_bg_color'] ?? '#4361ee' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="hero_image" class="form-label">Hero Image</label>
                                    @if(isset($heroSettings['hero_image']))
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $heroSettings['hero_image']) }}" 
                                                 alt="Current Hero" class="img-thumbnail" style="max-height: 100px">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control" id="hero_image" name="hero_image">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="button_text" class="form-label">Button Text</label>
                                    <input type="text" class="form-control" id="button_text" name="button_text" 
                                           value="{{ $heroSettings['button_text'] ?? 'Get Started' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="button_url" class="form-label">Button URL</label>
                                    <input type="text" class="form-control" id="button_url" name="button_url" 
                                           value="{{ $heroSettings['button_url'] ?? '/courses' }}">
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Hero Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Features Tab -->
            <div class="tab-pane fade" id="features" role="tabpanel" aria-labelledby="features-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Features Section Settings</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.website-appearance.features') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="features_title" class="form-label">Section Title</label>
                                <input type="text" class="form-control" id="features_title" name="features_title" 
                                       value="{{ $featuresSettings['features_title'] ?? 'Our Features' }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="features_subtitle" class="form-label">Section Subtitle</label>
                                <textarea class="form-control" id="features_subtitle" name="features_subtitle" rows="2">{{ $featuresSettings['features_subtitle'] ?? 'Discover what makes our platform different' }}</textarea>
                            </div>
                            
                            <div class="mt-4 mb-3">
                                <h6 class="fw-bold">Features List</h6>
                                <div id="features-container">
                                    @if(isset($featuresSettings['features_list']) && is_array($featuresSettings['features_list']))
                                        @foreach($featuresSettings['features_list'] as $index => $feature)
                                            <div class="feature-item card mb-3">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-5 mb-3">
                                                            <label class="form-label">Title</label>
                                                            <input type="text" class="form-control" name="features[{{ $index }}][title]" 
                                                                   value="{{ $feature['title'] ?? '' }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">Icon</label>
                                                            <input type="text" class="form-control" name="features[{{ $index }}][icon]" 
                                                                   value="{{ $feature['icon'] ?? 'fa-star' }}">
                                                            <small class="text-muted">FontAwesome icon name (e.g. fa-star)</small>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label">Color</label>
                                                            <input type="color" class="form-control form-control-color" name="features[{{ $index }}][color]" 
                                                                   value="{{ $feature['color'] ?? '#4361ee' }}">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="form-label">Description</label>
                                                            <textarea class="form-control" name="features[{{ $index }}][description]" rows="2">{{ $feature['description'] ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-danger remove-feature mt-3">
                                                        <i class="fas fa-trash-alt me-1"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <button type="button" id="add-feature" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus me-1"></i> Add Feature
                                </button>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Features Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Stats Tab -->
            <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Statistics Settings</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.website-appearance.stats') }}" method="POST">
                            @csrf
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="students_count" class="form-label">Students Count</label>
                                    <input type="number" class="form-control" id="students_count" name="students_count" 
                                           value="{{ $statsSettings['students_count'] ?? 0 }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="students_text" class="form-label">Students Text</label>
                                    <input type="text" class="form-control" id="students_text" name="students_text" 
                                           value="{{ $statsSettings['students_text'] ?? 'Happy Students' }}">
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="courses_count" class="form-label">Courses Count</label>
                                    <input type="number" class="form-control" id="courses_count" name="courses_count" 
                                           value="{{ $statsSettings['courses_count'] ?? 0 }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="courses_text" class="form-label">Courses Text</label>
                                    <input type="text" class="form-control" id="courses_text" name="courses_text" 
                                           value="{{ $statsSettings['courses_text'] ?? 'Online Courses' }}">
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="instructors_count" class="form-label">Instructors Count</label>
                                    <input type="number" class="form-control" id="instructors_count" name="instructors_count" 
                                           value="{{ $statsSettings['instructors_count'] ?? 0 }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="instructors_text" class="form-label">Instructors Text</label>
                                    <input type="text" class="form-control" id="instructors_text" name="instructors_text" 
                                           value="{{ $statsSettings['instructors_text'] ?? 'Expert Instructors' }}">
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Stats Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Features add/remove functionality
        const featuresContainer = document.getElementById('features-container');
        const addFeatureBtn = document.getElementById('add-feature');
        let featureCount = document.querySelectorAll('.feature-item').length;
        
        addFeatureBtn.addEventListener('click', function() {
            const featureHtml = `
                <div class="feature-item card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" name="features[${featureCount}][title]" value="">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Icon</label>
                                <input type="text" class="form-control" name="features[${featureCount}][icon]" value="fa-star">
                                <small class="text-muted">FontAwesome icon name (e.g. fa-star)</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color" name="features[${featureCount}][color]" value="#4361ee">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="features[${featureCount}][description]" rows="2"></textarea>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger remove-feature mt-3">
                            <i class="fas fa-trash-alt me-1"></i> Remove
                        </button>
                    </div>
                </div>
            `;
            
            featuresContainer.insertAdjacentHTML('beforeend', featureHtml);
            featureCount++;
            
            // Re-attach event listeners for new elements
            attachRemoveFeatureListeners();
        });
        
        function attachRemoveFeatureListeners() {
            document.querySelectorAll('.remove-feature').forEach(button => {
                button.removeEventListener('click', removeFeature);
                button.addEventListener('click', removeFeature);
            });
        }
        
        function removeFeature() {
            this.closest('.feature-item').remove();
        }
        
        // Initialize event listeners
        attachRemoveFeatureListeners();
    });
</script>
@endsection 