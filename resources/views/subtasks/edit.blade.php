@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Subtask</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('subtasks.update', $subtask) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Subtask Name -->
                        <div class="mb-3">
                            <label for="subtask_name" class="form-label"><strong>Subtask Name</strong> <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('subtask_name') is-invalid @enderror" 
                                   id="subtask_name" 
                                   name="subtask_name" 
                                   value="{{ old('subtask_name', $subtask->subtask_name) }}" 
                                   required>
                            @error('subtask_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $subtask->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Priority and Status Row -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label"><strong>Priority</strong></label>
                                    <select class="form-select @error('priority') is-invalid @enderror" 
                                            id="priority" 
                                            name="priority">
                                        <option value="">Select Priority</option>
                                        <option value="Low" {{ old('priority', $subtask->priority) == 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="Medium" {{ old('priority', $subtask->priority) == 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="High" {{ old('priority', $subtask->priority) == 'High' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('priority')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label"><strong>Status</strong></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="">Select Status</option>
                                        <option value="Not Started" {{ old('status', $subtask->status) == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                        <option value="In Progress" {{ old('status', $subtask->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="On Hold" {{ old('status', $subtask->status) == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                        <option value="Completed" {{ old('status', $subtask->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Dates Row -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" 
                                           class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="{{ old('start_date', $subtask->start_date?->format('Y-m-d')) }}">
                                    @error('start_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="date" 
                                           class="form-control @error('due_date') is-invalid @enderror" 
                                           id="due_date" 
                                           name="due_date" 
                                           value="{{ old('due_date', $subtask->due_date?->format('Y-m-d')) }}">
                                    @error('due_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- File Links Section -->
                        <div class="mb-3">
                            <label class="form-label"><strong>File Links</strong></label>
                            <div id="file_links_container">
                                @if($subtask->file_links && count($subtask->file_links) > 0)
                                    @foreach($subtask->file_links as $index => $link)
                                        <div class="input-group mb-2">
                                            <input type="text" 
                                                   class="form-control file-link-input" 
                                                   name="file_links[]" 
                                                   value="{{ $link['name'] ?? '' }}"
                                                   placeholder="Link Name">
                                            <input type="url" 
                                                   class="form-control file-link-url" 
                                                   name="file_links_url[]" 
                                                   value="{{ $link['url'] ?? '' }}"
                                                   placeholder="https://example.com">
                                            <button class="btn btn-danger remove-link" type="button">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button class="btn btn-sm btn-secondary" id="add_link" type="button">
                                <i class="bi bi-plus"></i> Add Link
                            </button>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3">{{ old('notes', $subtask->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Subtask
                            </button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
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
    // Add link button
    document.getElementById('add_link').addEventListener('click', function() {
        const container = document.getElementById('file_links_container');
        const html = `
            <div class="input-group mb-2">
                <input type="text" class="form-control file-link-input" name="file_links[]" placeholder="Link Name">
                <input type="url" class="form-control file-link-url" name="file_links_url[]" placeholder="https://example.com">
                <button class="btn btn-danger remove-link" type="button">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        attachRemoveListener();
    });

    function attachRemoveListener() {
        document.querySelectorAll('.remove-link').forEach(btn => {
            btn.removeEventListener('click', removeLink);
            btn.addEventListener('click', removeLink);
        });
    }

    function removeLink(e) {
        e.preventDefault();
        e.target.closest('.input-group').remove();
    }

    attachRemoveListener();
});
</script>
@endpush

@endsection
