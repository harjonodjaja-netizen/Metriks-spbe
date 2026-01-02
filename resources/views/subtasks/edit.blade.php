@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header" style="background-color: #ffc107; color: #212529;">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Edit Subtask
                    </h4>
                </div>

                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('subtasks.update', $subtask) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Subtask Name -->
                        <div class="mb-3">
                            <label for="subtask_name" class="form-label">
                                <strong>Subtask Name</strong>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('subtask_name') is-invalid @enderror" 
                                   id="subtask_name" 
                                   name="subtask_name" 
                                   value="{{ old('subtask_name', $subtask->subtask_name) }}" 
                                   required>
                            @error('subtask_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label"><strong>Description</strong></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $subtask->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div class="mb-3">
                            <label for="priority" class="form-label"><strong>Priority</strong></label>
                            <select class="form-select @error('priority') is-invalid @enderror" 
                                    id="priority" 
                                    name="priority">
                                <option value="">-- Select Priority --</option>
                                <option value="Low" {{ old('priority', $subtask->priority) == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ old('priority', $subtask->priority) == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ old('priority', $subtask->priority) == 'High' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label"><strong>Status</strong></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status">
                                <option value="">-- Select Status --</option>
                                <option value="Not Started" {{ old('status', $subtask->status) == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                <option value="In Progress" {{ old('status', $subtask->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="On Hold" {{ old('status', $subtask->status) == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Review" {{ old('status', $subtask->status) == 'Review' ? 'selected' : '' }}>Review</option>
                                <option value="Completed" {{ old('status', $subtask->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Assigned To -->
                        <div class="mb-3">
                            <label for="assigned_to" class="form-label"><strong>Assigned To</strong></label>
                            <input type="text" 
                                   class="form-control @error('assigned_to') is-invalid @enderror" 
                                   id="assigned_to" 
                                   name="assigned_to" 
                                   value="{{ old('assigned_to', $subtask->assigned_to) }}">
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div class="mb-3">
                            <label for="start_date" class="form-label"><strong>Start Date</strong></label>
                            <input type="date" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date', $subtask->start_date?->format('Y-m-d')) }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Due Date -->
                        <div class="mb-3">
                            <label for="due_date" class="form-label"><strong>Due Date</strong></label>
                            <input type="date" 
                                   class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" 
                                   name="due_date" 
                                   value="{{ old('due_date', $subtask->due_date?->format('Y-m-d')) }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Links (NEW - Using Relationship) -->
                        <div class="mb-3">
                            <label class="form-label"><strong>File Links</strong></label>
                            <div id="file_links_container">
                                @forelse($subtask->fileLinks as $index => $link)
                                    <div class="input-group mb-2">
                                        <input type="text" 
                                               class="form-control" 
                                               name="file_links[{{ $index }}][name]" 
                                               value="{{ $link->link_name }}" 
                                               placeholder="Link Name">
                                        <input type="url" 
                                               class="form-control" 
                                               name="file_links[{{ $index }}][url]" 
                                               value="{{ $link->link_url }}" 
                                               placeholder="https://example.com">
                                        <input type="text" 
                                               class="form-control" 
                                               name="file_links[{{ $index }}][description]" 
                                               value="{{ $link->description }}" 
                                               placeholder="Description (optional)">
                                        <button type="button" class="btn btn-danger remove-link">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                @empty
                                    <p class="text-muted">No file links added yet</p>
                                @endforelse
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" id="add_link">
                                <i class="bi bi-plus"></i> Add Link
                            </button>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label"><strong>Notes</strong></label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3">{{ old('notes', $subtask->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Update Subtask
                            </button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addButton = document.getElementById('add_link');
    let linkCount = document.querySelectorAll('#file_links_container .input-group').length;

    addButton.addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('file_links_container');
        
        // Remove "no links" message if present
        const noLinksMsg = container.querySelector('.text-muted');
        if (noLinksMsg) noLinksMsg.remove();

        const html = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="file_links[${linkCount}][name]" placeholder="Link Name">
                <input type="url" class="form-control" name="file_links[${linkCount}][url]" placeholder="https://example.com">
                <input type="text" class="form-control" name="file_links[${linkCount}][description]" placeholder="Description (optional)">
                <button type="button" class="btn btn-danger remove-link">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        linkCount++;
        attachRemoveListeners();
    });

    function attachRemoveListeners() {
        document.querySelectorAll('.remove-link').forEach(btn => {
            btn.removeEventListener('click', removeLink);
            btn.addEventListener('click', removeLink);
        });
    }

    function removeLink(e) {
        e.preventDefault();
        e.target.closest('.input-group').remove();
    }

    attachRemoveListeners();

    // Scroll to first error
    const firstError = document.querySelector('.is-invalid');
    if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstError.focus();
    }
});
</script>
@endpush

@endsection
