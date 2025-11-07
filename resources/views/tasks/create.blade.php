@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Task</h4>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
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

                    <form action="{{ route('tasks.store') }}" method="POST" novalidate>
                        @csrf

                        <!-- Task Name -->
                        <div class="mb-3">
                            <label for="task_name" class="form-label"><strong>Task Name</strong> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('task_name') is-invalid @enderror" id="task_name" name="task_name" value="{{ old('task_name') }}" required>
                            @error('task_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label"><strong>Description</strong></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div class="mb-3">
                            <label for="priority" class="form-label"><strong>Priority</strong> <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="">Select Priority</option>
                                <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label"><strong>Status</strong> <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Not Started" {{ old('status') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Review" {{ old('status') == 'Review' ? 'selected' : '' }}>Review</option>
                                <option value="On Hold" {{ old('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Assigned To -->
                        <div class="mb-3">
                            <label for="assigned_to" class="form-label"><strong>Assigned To</strong> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to" value="{{ old('assigned_to') }}" required>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div class="mb-3">
                            <label for="start_date" class="form-label"><strong>Start Date</strong> <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Due Date -->
                        <div class="mb-3">
                            <label for="due_date" class="form-label"><strong>Due Date</strong> <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Links - FIXED -->
                        <div class="mb-3">
                            <label class="form-label"><strong>File Links</strong></label>
                            <div id="fileLinksContainer">
                                <div class="file-link-group mb-3">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <input type="text" name="file_links[0][name]" class="form-control" placeholder="Link name (e.g., Google Drive)">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="url" name="file_links[0][url]" class="form-control" placeholder="https://example.com">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeLink(this)">
                                                <i class="bi bi-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row g-2 mt-1">
                                        <div class="col-md-10">
                                            <textarea name="file_links[0][description]" class="form-control" placeholder="Optional description" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addLink()" style="margin-top: 10px;">
                                <i class="bi bi-plus-circle"></i> Add Another Link
                            </button>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label"><strong>Notes</strong></label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Progress -->
                        <div class="mb-3">
                            <label for="progress" class="form-label"><strong>Progress (%)</strong> <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('progress') is-invalid @enderror" id="progress" name="progress" value="{{ old('progress', 0) }}" min="0" max="100" required>
                            @error('progress')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Create Task</button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let linkCount = 1;

function addLink() {
    const container = document.getElementById('fileLinksContainer');
    const newLink = document.createElement('div');
    newLink.className = 'file-link-group mb-3';
    newLink.innerHTML = `
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="file_links[${linkCount}][name]" class="form-control" placeholder="Link name">
            </div>
            <div class="col-md-6">
                <input type="url" name="file_links[${linkCount}][url]" class="form-control" placeholder="https://example.com">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeLink(this)">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
        </div>
        <div class="row g-2 mt-1">
            <div class="col-md-10">
                <textarea name="file_links[${linkCount}][description]" class="form-control" placeholder="Optional description" rows="2"></textarea>
            </div>
        </div>
    `;
    container.appendChild(newLink);
    linkCount++;
}

function removeLink(button) {
    button.closest('.file-link-group').remove();
}
</script>
@endsection
