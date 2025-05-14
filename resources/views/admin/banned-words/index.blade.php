@extends('layouts.admin')

@section('title', 'Manage Banned Words')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Banned Words</h6>
                    <div class="dropdown no-arrow">
                        <a class="btn btn-primary btn-sm" href="{{ route('admin.banned-words.create') }}">
                            <i class="fas fa-plus fa-sm"></i> Add New Word
                        </a>
                        <button type="button" class="btn btn-info btn-sm ml-2" data-toggle="modal" data-target="#bulkImportModal">
                            <i class="fas fa-file-import fa-sm"></i> Bulk Import
                        </button>
                        <a class="btn btn-secondary btn-sm ml-2" href="{{ route('admin.banned-words.flagged-messages') }}">
                            <i class="fas fa-flag fa-sm"></i> View Flagged Messages
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form action="{{ route('admin.banned-words.index') }}" method="GET" class="form-inline">
                                <div class="form-group mr-2">
                                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                                </div>
                                <div class="form-group mr-2">
                                    <select name="type" class="form-control">
                                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <select name="status" class="form-control">
                                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <select name="severity" class="form-control">
                                        <option value="" {{ request('severity') == '' ? 'selected' : '' }}>All Severities</option>
                                        <option value="1" {{ request('severity') == '1' ? 'selected' : '' }}>Severity 1</option>
                                        <option value="2" {{ request('severity') == '2' ? 'selected' : '' }}>Severity 2</option>
                                        <option value="3" {{ request('severity') == '3' ? 'selected' : '' }}>Severity 3</option>
                                        <option value="4" {{ request('severity') == '4' ? 'selected' : '' }}>Severity 4</option>
                                        <option value="5" {{ request('severity') == '5' ? 'selected' : '' }}>Severity 5</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.banned-words.index') }}" class="btn btn-secondary ml-2">Reset</a>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="float-right">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#testFilterModal">
                                    <i class="fas fa-flask"></i> Test Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Word</th>
                                    <th>Type</th>
                                    <th>Replacement</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bannedWords as $word)
                                    <tr>
                                        <td>{{ $word->id }}</td>
                                        <td>{{ $word->word }}</td>
                                        <td>
                                            <span class="badge badge-{{ $word->type == 'profanity' ? 'danger' : ($word->type == 'contact_info' ? 'warning' : 'info') }}">
                                                {{ ucfirst(str_replace('_', ' ', $word->type)) }}
                                            </span>
                                        </td>
                                        <td>{{ $word->replacement ?: 'Auto (*)' }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-{{ $word->severity >= 4 ? 'danger' : ($word->severity >= 3 ? 'warning' : 'info') }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $word->severity * 20 }}%" 
                                                     aria-valuenow="{{ $word->severity }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="5">
                                                    {{ $word->severity }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.banned-words.toggle-status', $word) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-{{ $word->active ? 'success' : 'secondary' }}">
                                                    {{ $word->active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.banned-words.edit', $word) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.banned-words.destroy', $word) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No banned words found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $bannedWords->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Import Modal -->
<div class="modal fade" id="bulkImportModal" tabindex="-1" role="dialog" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkImportModalLabel">Bulk Import Banned Words</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.banned-words.bulk-import') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="words">Words (one per line)</label>
                        <textarea id="words" name="words" class="form-control" rows="10" placeholder="Enter one word per line"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select id="type" name="type" class="form-control" required>
                                    <option value="general">General</option>
                                    <option value="profanity">Profanity</option>
                                    <option value="contact_info">Contact Information</option>
                                    <option value="platform_bypass">Platform Bypass</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="severity">Severity</label>
                                <select id="severity" name="severity" class="form-control" required>
                                    <option value="1">1 - Low</option>
                                    <option value="2">2</option>
                                    <option value="3" selected>3 - Medium</option>
                                    <option value="4">4</option>
                                    <option value="5">5 - High</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="active">Status</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="active" name="active" checked>
                                    <label class="form-check-label" for="active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import Words</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Test Filter Modal -->
<div class="modal fade" id="testFilterModal" tabindex="-1" role="dialog" aria-labelledby="testFilterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testFilterModalLabel">Test Content Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="testContent">Enter content to test</label>
                    <textarea id="testContent" class="form-control" rows="5" placeholder="Enter text to check for banned words..."></textarea>
                </div>
                <div class="mt-3" id="testResults" style="display: none;">
                    <h6>Results:</h6>
                    <div class="card mb-2">
                        <div class="card-header">
                            <strong>Original Content</strong>
                        </div>
                        <div class="card-body" id="originalContent"></div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <strong>Filtered Content</strong>
                        </div>
                        <div class="card-body" id="filteredContent"></div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <strong>Found Words</strong>
                        </div>
                        <div class="card-body" id="foundWords"></div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <strong>Summary</strong>
                        </div>
                        <div class="card-body" id="filterSummary"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="runFilterTest">Test Filter</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Delete confirmation
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this banned word?')) {
                this.submit();
            }
        });
        
        // Test filter functionality
        $('#runFilterTest').on('click', function() {
            const content = $('#testContent').val();
            if (!content) {
                alert('Please enter some content to test.');
                return;
            }
            
            // Show loading state
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Testing...');
            
            // Send test request
            $.ajax({
                url: '{{ route("admin.banned-words.test-filter") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    content: content
                },
                success: function(response) {
                    // Display results
                    $('#testResults').show();
                    $('#originalContent').text(response.original_content);
                    $('#filteredContent').text(response.filtered_content);
                    
                    // Format found words
                    let foundWordsHtml = '';
                    if (response.found_words && response.found_words.length > 0) {
                        foundWordsHtml = '<ul>';
                        response.found_words.forEach(function(item) {
                            foundWordsHtml += '<li><strong>' + item.word + '</strong> - Type: ' + item.type + ', Severity: ' + item.severity + '</li>';
                        });
                        foundWordsHtml += '</ul>';
                    } else {
                        foundWordsHtml = '<p>No banned words found.</p>';
                    }
                    $('#foundWords').html(foundWordsHtml);
                    
                    // Summary
                    let summaryHtml = '';
                    if (response.has_banned_content) {
                        const severityClass = response.highest_severity >= 4 ? 'danger' : (response.highest_severity >= 3 ? 'warning' : 'info');
                        summaryHtml = `
                            <div class="alert alert-${severityClass}">
                                <strong>Banned content detected!</strong><br>
                                Highest severity level: <span class="badge badge-${severityClass}">${response.highest_severity}</span><br>
                                Number of banned words: ${response.found_words.length}
                            </div>
                        `;
                    } else {
                        summaryHtml = '<div class="alert alert-success">No banned content detected.</div>';
                    }
                    $('#filterSummary').html(summaryHtml);
                },
                error: function(error) {
                    alert('Error testing filter: ' + error.statusText);
                },
                complete: function() {
                    // Reset button state
                    $('#runFilterTest').prop('disabled', false).html('Test Filter');
                }
            });
        });
    });
</script>
@endsection 