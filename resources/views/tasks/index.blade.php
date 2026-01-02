@extends('layouts.app')

@section('content')
<div class="container-fluid full-bleed py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-3">Task Tracker</h2>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="row mb-3 g-3">
        <!-- Search Bar -->
        <div class="col-lg-6 col-md-12 position-relative">
            <form method="GET" action="{{ route('tasks.index') }}">
                <div class="input-group">
                    <input type="text" 
                           name="search" 
                           id="searchTasks" 
                           class="form-control" 
                           placeholder="Search by Task Name, Description, Notes, or ID"
                           value="{{ request('search') }}"
                           autocomplete="off">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Filter Section -->
        <div class="col-lg-6 col-md-12">
            <form method="GET" action="{{ route('tasks.index') }}" class="d-flex gap-2 justify-content-lg-end flex-wrap">
                <label class="col-form-label text-primary fw-semibold">Filter:</label>
                
                <select name="filter_priority" onchange="this.form.submit()" class="form-select" style="max-width: 150px;">
                    <option value="">All Priorities</option>
                    <option value="High" {{ request('filter_priority') == 'High' ? 'selected' : '' }}>High</option>
                    <option value="Medium" {{ request('filter_priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="Low" {{ request('filter_priority') == 'Low' ? 'selected' : '' }}>Low</option>
                </select>
                
                <select name="filter_status" onchange="this.form.submit()" class="form-select" style="max-width: 150px;">
                    <option value="">All Status</option>
                    <option value="Not Started" {{ request('filter_status') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                    <option value="In Progress" {{ request('filter_status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Review" {{ request('filter_status') == 'Review' ? 'selected' : '' }}>Review</option>
                    <option value="On Hold" {{ request('filter_status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                    <option value="Completed" {{ request('filter_status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
                
                <select name="sort_due_date" onchange="this.form.submit()" class="form-select" style="max-width: 200px;">
                    <option value="asc" {{ request('sort_due_date') == 'asc' ? 'selected' : '' }}>Earliest Due Date</option>
                    <option value="desc" {{ request('sort_due_date') == 'desc' ? 'selected' : '' }}>Latest Due Date</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Add New Task Button -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end">
            <a href="{{ route('tasks.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add New Task
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Desktop Table View -->
    <div class="table-responsive d-none d-lg-block">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-header-custom">
                <tr>
                    <th class="th-blue" style="width: 50px;"></th>
                    <th class="th-blue">ID</th>
                    <th class="th-blue">Task Name</th>
                    <th class="th-blue">Description</th>
                    <th class="th-blue">Priority</th>
                    <th class="th-blue">Status</th>
                    <th class="th-blue">Assigned To</th>
                    <th class="th-blue">Start Date</th>
                    <th class="th-blue">Due Date</th>
                    <th class="th-blue">File Links</th>
                    <th class="th-blue">Notes</th>
                    <th class="th-blue">Progress</th>
                    <th class="th-blue" style="min-width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                    <!-- Main Task Row -->
                    <tr class="task-row" data-task-id="{{ $task->id }}">
                        <td style="text-align: center; padding: 8px;">
                            <button class="btn btn-sm btn-outline-secondary expand-toggle" data-task-id="{{ $task->id }}" title="Expand subtasks" style="width: 30px; height: 30px; padding: 0;">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </td>
                        <td>{{ $task->id }}</td>
                        <td><strong>{{ $task->task_name }}</strong></td>
                        <td>
                            <button type="button"
                                    class="btn btn-link p-0 text-decoration-none text-preview-trigger text-dark"
                                    data-preview-title="Task Description"
                                    data-preview-text='@json($task->description ?? "")'>
                                {{ Str::limit($task->description ?? '', 50) }}
                            </button>
                        </td>
                        <td>
                            @if(strtolower($task->priority ?? '') == 'high')
                                <span class="badge badge-priority-high"><i class="bi bi-exclamation-circle-fill"></i> High</span>
                            @elseif(strtolower($task->priority ?? '') == 'medium')
                                <span class="badge badge-priority-medium"><i class="bi bi-dash-circle-fill"></i> Medium</span>
                            @else
                                <span class="badge badge-priority-low"><i class="bi bi-arrow-down-circle-fill"></i> Low</span>
                            @endif
                        </td>
                        <td>
                            @if($task->status == 'Completed')
                                <span class="badge badge-status-completed"><i class="bi bi-check-circle-fill"></i> Completed</span>
                            @elseif($task->status == 'In Progress')
                                <span class="badge badge-status-progress"><i class="bi bi-arrow-clockwise"></i> In Progress</span>
                            @elseif($task->status == 'Review')
                                <span class="badge badge-status-review"><i class="bi bi-eye-fill"></i> Review</span>
                            @elseif($task->status == 'On Hold')
                                <span class="badge badge-status-hold"><i class="bi bi-pause-circle-fill"></i> On Hold</span>
                            @else
                                <span class="badge badge-status-not-started"><i class="bi bi-circle"></i> Not Started</span>
                            @endif
                        </td>
                        <td>{{ $task->assigned_to }}</td>
                        <td>{{ \Carbon\Carbon::parse($task->start_date)->format('j M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($task->due_date)->format('j M Y') }}</td>
                        <td>
                            @if($task->fileLinks && $task->fileLinks->count() > 0)
                                <div class="dropdown" data-bs-popper="static">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="white-space: nowrap;">
                                        <i class="bi bi-file-earmark"></i>
                                        {{ $task->fileLinks->count() }} Link{{ $task->fileLinks->count() > 1 ? 's' : '' }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @foreach($task->fileLinks as $link)
                                            <li>
                                                <a class="dropdown-item" href="{{ $link->link_url }}" target="_blank" rel="noopener noreferrer" title="{{ $link->description ?? '' }}" style="white-space: normal;">
                                                    <i class="bi bi-file-earmark"></i> 
                                                    <strong>{{ $link->link_name }}</strong>
                                                    @if($link->description)
                                                        <br><small class="text-muted">{{ Str::limit($link->description, 50) }}</small>
                                                    @endif
                                                </a>
                                            </li>
                                            @if(!$loop->last)
                                                <li><hr class="dropdown-divider"></li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <span class="text-muted">No Links</span>
                            @endif
                        </td>
                        <td>
                            <button type="button"
                                    class="btn btn-link p-0 text-decoration-none text-preview-trigger text-dark"
                                    data-preview-title="Task Notes"
                                    data-preview-text='@json($task->notes ?? "")'>
                                {{ Str::limit($task->notes ?? '', 30) }}
                            </button>
                        </td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar @if($task->progress >= 75) bg-success @elseif($task->progress >= 50) bg-info @elseif($task->progress >= 25) bg-warning @else bg-danger @endif" style="width: {{ $task->progress ?? 0 }}%;" role="progressbar">
                                    {{ $task->progress ?? 0 }}%
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button"
                                        class="btn btn-sm btn-action btn-action-edit btn-open-task-modal"
                                        data-task='@json($task)'>
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-action-delete" title="Delete Task" onclick="return confirm('Delete task?')"><i class="bi bi-trash-fill"></i> Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Subtasks Row -->
                    <tr class="subtask-row" id="subtasks-{{ $task->id }}" style="display: none;">
                        <td colspan="13" class="p-0">
                            <div class="subtask-container" style="background-color: #f8f9fa; border-left: 4px solid #007bff; padding: 15px; margin: 5px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <i class="bi bi-list-check"></i> Subtasks
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSubtaskModal{{ $task->id }}">
                                        <i class="bi bi-plus"></i> Add Subtask
                                    </button>
                                </div>

                                @if($task->subtasks && $task->subtasks->count() > 0)
                                    <div class="subtask-list">
                                        <div class="table-responsive" style="font-size: 0.9rem;">
                                            <table class="table table-sm table-hover mb-0" style="background-color: #ffffff;">
                                                <thead style="background: linear-gradient(135deg, #f0f4f8 0%, #e8eef7 100%); border-bottom: 2px solid #cbd5e1;">
                                                    <tr>
                                                        <th style="padding: 12px; color: #334155; font-weight: 600;">Subtask Name</th>
                                                        <th style="padding: 12px; color: #334155; font-weight: 600;">Description</th>
                                                        <th style="padding: 12px; color: #334155; font-weight: 600;">Priority</th>
                                                        <th style="padding: 12px; color: #334155; font-weight: 600;">Status</th>
                                                        <th style="padding: 12px; color: #334155; font-weight: 600;">Assigned To</th>
                                                        <th style="padding: 12px; color: #334155; font-weight: 600;">Start Date</th>
                                                        <th style="padding: 12px; color: #334155; font-weight: 600;">Due Date</th>
                                                        <th style="padding: 12px; color: #334155; font-weight: 600;">Links</th>
                                                        <th style="padding: 12px; color: #334155; font-weight: 600;">Notes</th>
                                                        <th style="width: 60px; padding: 12px; color: #334155; font-weight: 600;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($task->subtasks as $subtask)
                                                        <tr style="border-bottom: 1px solid #e2e8f0; transition: all 0.2s ease;">
                                                            <td style="padding: 12px; vertical-align: middle;">
                                                                <strong style="color: #1e293b;">{{ $subtask->subtask_name }}</strong>
                                                            </td>
                                                            <td style="padding: 12px; vertical-align: middle; color: #64748b;">
                                                                <button type="button"
                                                                        class="btn btn-link p-0 text-decoration-none text-preview-trigger text-dark"
                                                                        data-preview-title="Subtask Description"
                                                                        data-preview-text='@json($subtask->description ?? "")'>
                                                                    {{ $subtask->description ? Str::limit($subtask->description, 30) : '-' }}
                                                                </button>
                                                            </td>
                                                            <td style="padding: 12px; vertical-align: middle;">
                                                                @if($subtask->priority == 'High')
                                                                    <span class="badge badge-priority-high"><i class="bi bi-exclamation-circle-fill"></i> High</span>
                                                                @elseif($subtask->priority == 'Medium')
                                                                    <span class="badge badge-priority-medium"><i class="bi bi-dash-circle-fill"></i> Medium</span>
                                                                @else
                                                                    <span class="badge badge-priority-low"><i class="bi bi-arrow-down-circle-fill"></i> Low</span>
                                                                @endif
                                                            </td>
                                                            <td style="padding: 12px; vertical-align: middle;">


                                                                @if($subtask->status == 'Completed')
                                                                    <span class="badge" style="background-color: #d1fae5; color: #065f46; padding: 6px 12px; font-weight: 500;">Completed</span>
                                                                @elseif($subtask->status == 'In Progress')
                                                                    <span class="badge" style="background-color: #cffafe; color: #164e63; padding: 6px 12px; font-weight: 500;">In Progress</span>
                                                                @elseif($subtask->status == 'On Hold')
                                                                    <span class="badge" style="background-color: #fef3c7; color: #92400e; padding: 6px 12px; font-weight: 500;">On Hold</span>
                                                                @else
                                                                    <span class="badge" style="background-color: #e2e8f0; color: #475569; padding: 6px 12px; font-weight: 500;">{{ $subtask->status }}</span>
                                                                @endif
                                                            </td>
                                                            <td style="padding: 12px; vertical-align: middle; color: #64748b;">{{ $subtask->assigned_to ?? '-' }}</td>
                                                            <td style="padding: 12px; vertical-align: middle; color: #64748b;">{{ $subtask->start_date ? \Carbon\Carbon::parse($subtask->start_date)->format('j M Y') : '-' }}</td>
                                                            <td style="padding: 12px; vertical-align: middle; color: #64748b;">{{ $subtask->due_date ? \Carbon\Carbon::parse($subtask->due_date)->format('j M Y') : '-' }}</td>
                                                            <td style="padding: 12px;">
                                                                @if($subtask->fileLinks && $subtask->fileLinks->count() > 0)
                                                                    <div class="dropdown" data-bs-popper="static">
                                                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="white-space: nowrap;">
                                                                            <i class="bi bi-file-earmark"></i>
                                                                            {{ $subtask->fileLinks->count() }} Link{{ $subtask->fileLinks->count() > 1 ? 's' : '' }}
                                                                        </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                                            @foreach($subtask->fileLinks as $link)
                                                                                <li>
                                                                                    <a class="dropdown-item" href="{{ $link->link_url }}" target="_blank" rel="noopener noreferrer" title="{{ $link->description ?? '' }}" style="white-space: normal;">
                                                                                        <i class="bi bi-file-earmark"></i>
                                                                                        <strong>{{ $link->link_name }}</strong>
                                                                                        @if($link->description)
                                                                                            <br><small class="text-muted">{{ Str::limit($link->description, 50) }}</small>
                                                                                        @endif
                                                                                    </a>
                                                                                </li>
                                                                                @if(!$loop->last)
                                                                                    <li><hr class="dropdown-divider"></li>
                                                                                @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">No Links</span>
                                                                @endif
                                                            </td>
                                                            <td style="padding: 12px; vertical-align: middle; color: #64748b;">
                                                                <button type="button"
                                                                        class="btn btn-link p-0 text-decoration-none text-preview-trigger text-dark"
                                                                        data-preview-title="Subtask Notes"
                                                                        data-preview-text='@json($subtask->notes ?? "")'>
                                                                    {{ $subtask->notes ? Str::limit($subtask->notes, 20) : '-' }}
                                                                </button>
                                                            </td>
                                                            <td style="padding: 12px; vertical-align: middle; text-align: center; background-color: #f8fafc;">
                                                                <div style="display: flex; gap: 6px; justify-content: center;">
                                                                    <a href="{{ route('subtasks.edit', $subtask) }}" class="btn btn-sm btn-warning" title="Edit" style="padding: 4px 8px;"><i class="bi bi-pencil"></i></a>
                                                                    <form action="{{ route('subtasks.destroy', $subtask) }}" method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')" title="Delete" style="padding: 4px 8px;"><i class="bi bi-trash"></i></button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                       </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted"><i class="bi bi-info-circle"></i> No subtasks yet.</p>
                                @endif

                            </div>
                        </td>
                    </tr>
                    <!-- Add Subtask Modal -->
                    <div class="modal fade" id="addSubtaskModal{{ $task->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ url('/tasks/'.$task->id.'/subtasks') }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Subtask to "{{ $task->task_name }}"</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Subtask Name</strong> <span class="text-danger">*</span></label>
                                            <input type="text" name="subtask_name" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="2" placeholder="Optional"></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Priority</label>
                                                    <select name="priority" class="form-select">
                                                        <option value="Low">Low</option>
                                                        <option value="Medium">Medium</option>
                                                        <option value="High" selected>High</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="Not Started">Not Started</option>
                                                        <option value="In Progress">In Progress</option>
                                                        <option value="On Hold">On Hold</option>
                                                        <option value="Completed">Completed</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Assigned To</label>
                                                    <input type="text" name="assigned_to" class="form-control" placeholder="Optional" value="{{ $task->assigned_to }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Start Date</label>
                                                    <input type="date" name="start_date" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Due Date</label>
                                                    <input type="date" name="due_date" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- NEW: File Links (multiple) -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>File Links</strong> <small class="text-muted">Optional</small></label>

                                            <div class="file-links-list">
                                                <!-- initial single empty row; user can add more -->
                                                <div class="file-link-row d-flex gap-2 mb-2 align-items-start">
                                                    <input type="text" name="file_links[][name]" class="form-control" placeholder="Link name (e.g. Spec, Screenshot)">
                                                    <input type="url" name="file_links[][url]" class="form-control" placeholder="https://example.com">
                                                    <input type="text" name="file_links[][description]" class="form-control" placeholder="Short description (optional)">
                                                    <button type="button" class="btn btn-sm btn-danger btn-remove-file-link" title="Remove link">&times;</button>
                                                </div>
                                            </div>

                                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2 btn-add-file-link">+ Add Link</button>
                                            <small class="form-text text-muted d-block mt-1">Add one or more file links for this subtask. Leave blank to skip.</small>

                                            <!-- hidden template -->
                                            <div class="file-link-template d-none">
                                                <div class="file-link-row d-flex gap-2 mb-2 align-items-start">
                                                    <input type="text" name="file_links[][name]" class="form-control" placeholder="Link name">
                                                    <input type="url" name="file_links[][url]" class="form-control" placeholder="https://example.com">
                                                    <input type="text" name="file_links[][description]" class="form-control" placeholder="Description (optional)">
                                                    <button type="button" class="btn btn-sm btn-danger btn-remove-file-link" title="Remove link">&times;</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Notes</label>
                                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Add Subtask</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="13" class="text-center py-4">
                            <p class="text-muted mb-0">No tasks found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile View -->
    <div class="d-lg-none">
        @forelse ($tasks as $task)
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong>{{ $task->task_name }}</strong>
                    <span class="badge bg-light text-dark float-end">ID: {{ $task->id }}</span>
                </div>
                <div class="card-body">
                    <p><strong>Description:</strong></p>
                    <div class="desc-display">{{ $task->description ?? 'No description' }}</div>
                    
                    <p class="mt-3"><strong>Priority:</strong> 
                        @if($task->priority == 'High')
                            <span class="badge badge-priority-high">High</span>
                        @elseif($task->priority == 'Medium')
                            <span class="badge badge-priority-medium">Medium</span>
                        @else
                            <span class="badge badge-priority-low">Low</span>
                        @endif
                    </p>
                    
                    <p><strong>Status:</strong>
                        @if($task->status == 'Completed')
                            <span class="badge badge-status-completed">Completed</span>
                        @elseif($task->status == 'In Progress')
                            <span class="badge badge-status-progress">In Progress</span>
                        @elseif($task->status == 'Review')
                            <span class="badge badge-status-review">Review</span>
                        @elseif($task->status == 'On Hold')
                            <span class="badge badge-status-hold">On Hold</span>
                        @else
                            <span class="badge badge-status-not-started">Not Started</span>
                        @endif
                    </p>
                    
                    <p><strong>Assigned To:</strong> {{ $task->assigned_to }}</p>
                    <p><strong>Dates:</strong> {{ \Carbon\Carbon::parse($task->start_date)->format('j M Y') }} - {{ \Carbon\Carbon::parse($task->due_date)->format('j M Y') }}</p>
                    
                    @if($task->fileLinks && $task->fileLinks->count() > 0)
                        <p>
                            <strong>File Links:</strong>
                            <div class="mt-2">
                                @foreach($task->fileLinks as $link)
                                    <a href="{{ $link->link_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary me-2 mb-2">
                                        <i class="bi bi-file-earmark"></i> {{ $link->link_name }}
                                    </a>
                                @endforeach
                            </div>
                        </p>
                    @endif
                  
                    <p><strong>Notes:</strong></p>
                    <div class="notes-display">{{ $task->notes ?? 'No notes' }}</div>
                    
                    <p class="mt-3"><strong>Progress:</strong>
                        <div class="progress mt-2" style="height: 25px;">
                            <div class="progress-bar @if($task->progress >= 75) bg-success @elseif($task->progress >= 50) bg-info @elseif($task->progress >= 25) bg-warning @else bg-danger @endif" style="width: {{ $task->progress ?? 0 }}%;">{{ $task->progress ?? 0 }}%</div>
                        </div>
                    </p>
                    
                    <div class="action-buttons-mobile mt-3">
                        <a href="{{ route('tasks.edit', $task) }}" class="btn-action-mobile btn-action-edit"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="flex: 1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action-mobile btn-action-delete w-100" onclick="return confirm('Delete?')"><i class="bi bi-trash-fill"></i> Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> No tasks found.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $tasks->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- Text Preview Modal (single reusable) --}}
<div class="modal fade" id="textPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="textPreviewTitle">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="textPreviewBody" style="white-space: pre-wrap; font-family: inherit; margin: 0;"></pre>
            </div>
        </div>
    </div>
</div>

{{-- Single modal placed outside the table/loops (near end of file, before @endsection) --}}
<div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="task-modal-form" method="POST" action="#">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="task-id" name="id" />

                    <div class="mb-3">
                        <label for="task-task-name" class="form-label">Task Name</label>
                        <input id="task-task-name" name="task_name" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label for="task-description" class="form-label">Description</label>
                        <textarea id="task-description" name="description" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="task-priority" class="form-label">Priority</label>
                            <select id="task-priority" name="priority" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="task-status" class="form-label">Status</label>
                            <select id="task-status" name="status" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Review">Review</option>
                                <option value="On Hold">On Hold</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="task-assigned-to" class="form-label">Assigned To</label>
                            <input id="task-assigned-to" name="assigned_to" class="form-control" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="task-start" class="form-label">Start Date</label>
                            <input id="task-start" name="start_date" type="date" class="form-control" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="task-due" class="form-label">Due Date</label>
                            <input id="task-due" name="due_date" type="date" class="form-control" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="task-notes" class="form-label">Notes</label>
                        <textarea id="task-notes" name="notes" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="task-progress" class="form-label">Progress (%)</label>
                            <input id="task-progress" name="progress" type="number" min="0" max="100" class="form-control" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File Links</label>
                        <div id="task-file-links-list"></div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="task-add-file-link">+ Add Link</button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Table Styling */
    .table { background-color: white; }
    .th-blue { background: linear-gradient(135deg, #3c7ffaff 0%, #468affff 100%) !important; color: white !important; font-weight: 600; white-space: nowrap; padding: 12px 10px !important; border: 1px solid #1e3c72 !important; text-align: center; vertical-align: middle; }
    .table-header-custom { position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    
    /* Badge Styling */
    .badge-priority-high { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 6px 12px; font-size: 0.85rem; font-weight: 600; border-radius: 20px; }
    .badge-priority-medium { background: linear-gradient(135deg, #fd7e14 0%, #e8590c 100%); color: white; padding: 6px 12px; font-size: 0.85rem; font-weight: 600; border-radius: 20px; }
    .badge-priority-low { background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; padding: 6px 12px; font-size: 0.85rem; font-weight: 600; border-radius: 20px; }
    .badge-status-completed { background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); color: white; padding: 6px 12px; font-size: 0.85rem; font-weight: 600; border-radius: 20px; }
    .badge-status-progress { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 6px 12px; font-size: 0.85rem; font-weight: 600; border-radius: 20px; }
    .badge-status-review { background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%); color: white; padding: 6px 12px; font-size: 0.85rem; font-weight: 600; border-radius: 20px; }
    .badge-status-hold { background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: #212529; padding: 6px 12px; font-size: 0.85rem; font-weight: 600; border-radius: 20px; }
    .badge-status-not-started { background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 6px 12px; font-size: 0.85rem; font-weight: 600; border-radius: 20px; }
    
    /* Preview triggers (no hover-expand) */
    .text-preview-trigger { max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; text-align: left; }
    
    /* Mobile Displays */
    .desc-display, .notes-display { max-height: 200px; overflow-y: auto; padding: 10px; background-color: #f8f9fa; border-radius: 5px; border: 1px solid #e0e0e0; white-space: pre-wrap; word-wrap: break-word; overflow-wrap: break-word; }
    
    /* Action Buttons */
    .action-buttons { display: flex; gap: 6px; justify-content: center; }
    .btn-action { font-size: 0.75rem !important; padding: 6px 10px !important; border-radius: 18px !important; font-weight: 500; border: none; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 4px; white-space: nowrap; min-width: 75px; height: 30px; }
    .btn-action:hover { transform: translateY(-2px); box-shadow: 0 3px 8px rgba(0,0,0,0.25); }
    .btn-action-edit { background-color: #ffc107 !important; color: #212529 !important; }
    .btn-action-delete { background-color: #dc3545 !important; color: white !important; }
    
    /* Subtask Styling */
    .expand-toggle { transition: all 0.2s ease; }
    .expand-toggle:hover { background-color: #e7f3ff !important; transform: scale(1.05); }
    .subtask-container { animation: slideDown 0.3s ease; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .subtask-item { transition: all 0.2s ease; }
    .subtask-item:hover { background-color: #f0f0f0 !important; transform: translateX(5px); }
    
    /* Progress Bar */
    .progress { background-color: #e9ecef; border-radius: 10px; overflow: hidden; }
    .progress-bar { font-weight: 600; transition: width 0.6s ease; }
    
    /* Table Responsive */
    .table-responsive { border-radius: 8px; overflow-x: auto !important; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .table-responsive::-webkit-scrollbar { height: 10px; }
    .table-responsive::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    
    /* Card Styling */
    .card { border: 1px solid #dee2e6; transition: transform 0.2s, box-shadow 0.2s; }
    .card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important; }
    
    /* Mobile Buttons */
    .action-buttons-mobile { display: flex; gap: 8px; }
    .btn-action-mobile { flex: 1; font-size: 0.875rem; padding: 10px 16px; border-radius: 25px; font-weight: 500; border: none; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 42px; }
    .btn-action-mobile:hover { transform: translateY(-2px); box-shadow: 0 3px 10px rgba(0,0,0,0.2); }
    .btn-action-mobile.btn-action-edit { background-color: #ffc107 !important; color: #212529 !important; }
    .btn-action-mobile.btn-action-delete { background-color: #dc3545 !important; color: white !important; }
    
    /* ✅ File Links Dropdown Styling - FIXED */
    .dropdown-menu { 
        z-index: 10001 !important;
        min-width: 280px; 
        max-height: 500px; 
        overflow-y: auto;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    .dropdown-item { white-space: normal; padding: 0.75rem 1rem; }
    .dropdown-item i { margin-right: 6px; }
    .dropdown-divider { margin: 0.25rem 0; }

    { 
    /* make page full-bleed (ignores layout gutters) */
    .full-bleed {
        width: 100vw;
        margin-left: calc(50% - 50vw);
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* optional: let content inside have some horizontal breathing room */
    .full-bleed > .row,
    .full-bleed > .container,
    .full-bleed .table-responsive {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle subtask row
    document.querySelectorAll('.expand-toggle').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const taskId = this.dataset.taskId;
            const subtaskRow = document.getElementById(`subtasks-${taskId}`);
            const icon = this.querySelector('i');
            
            if (subtaskRow.style.display === 'none') {
                subtaskRow.style.display = 'table-row';
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-down');
            } else {
                subtaskRow.style.display = 'none';
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-right');
            }
        });
    });

    // Text preview modal (preserves formatting, safe textContent)
    const textPreviewModalEl = document.getElementById('textPreviewModal');
    const textPreviewTitleEl = document.getElementById('textPreviewTitle');
    const textPreviewBodyEl = document.getElementById('textPreviewBody');
    const textPreviewModal = textPreviewModalEl ? new bootstrap.Modal(textPreviewModalEl) : null;

    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('.text-preview-trigger');
        if (!btn || !textPreviewModal) return;

        const raw = btn.getAttribute('data-preview-text') ?? '';
        const title = btn.getAttribute('data-preview-title') ?? 'Preview';

        let text = '';
        try {
            text = JSON.parse(raw);
        } catch {
            text = raw;
        }

        textPreviewTitleEl.textContent = title;
        textPreviewBodyEl.textContent = text || '';
        textPreviewModal.show();
    });

    // Helper to safely parse date to yyyy-mm-dd for <input type="date">
    const toDateInput = (val) => {
        if (!val) return '';
        try {
            // normalize server date strings (ISO or Y-m-d)
            const d = new Date(val);
            if (isNaN(d)) return '';
            return d.toISOString().slice(0,10);
        } catch (e) { return ''; }
    };

    const taskModalEl = document.getElementById('taskModal');
    const bsModal = new bootstrap.Modal(taskModalEl);
    const form = document.getElementById('task-modal-form');

    // open modal when any .btn-open-task-modal is clicked (delegation)
    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-open-task-modal');
        if (!btn) return;

        const payload = btn.getAttribute('data-task');
        if (!payload) return console.warn('no task data on button');

        let task;
        try {
            task = JSON.parse(payload);
        } catch (err) {
            console.error('invalid task JSON', err);
            return;
        }

        // populate form fields
        document.getElementById('task-id').value = task.id ?? '';
        document.getElementById('task-task-name').value = task.task_name ?? '';
        document.getElementById('task-description').value = task.description ?? '';
        document.getElementById('task-priority').value = task.priority ?? '';
        document.getElementById('task-status').value = task.status ?? '';
        document.getElementById('task-assigned-to').value = task.assigned_to ?? '';
        document.getElementById('task-start').value = toDateInput(task.start_date ?? task.start) ;
        document.getElementById('task-due').value = toDateInput(task.due_date ?? task.due);
        document.getElementById('task-notes').value = task.notes ?? '';
        document.getElementById('task-progress').value = task.progress ?? 0;

        // File links (expects relation serialized as task.file_links)
        const fileLinksList = document.getElementById('task-file-links-list');
        const addFileLinkBtn = document.getElementById('task-add-file-link');

        const renderFileLinkRow = (index, link) => {
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 align-items-start';
            row.innerHTML = `
                <div class="col-md-4">
                    <input type="text" name="file_links[${index}][name]" class="form-control" placeholder="Link name" value="${(link?.link_name ?? '').replace(/"/g, '&quot;')}">
                </div>
                <div class="col-md-6">
                    <input type="url" name="file_links[${index}][url]" class="form-control" placeholder="https://example.com" value="${(link?.link_url ?? '').replace(/"/g, '&quot;')}">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 task-remove-file-link">Remove</button>
                </div>
                <div class="col-md-10">
                    <textarea name="file_links[${index}][description]" class="form-control" placeholder="Optional description" rows="2">${(link?.description ?? '')}</textarea>
                </div>
            `;
            return row;
        };

        if (fileLinksList) {
            fileLinksList.innerHTML = '';
            const links = Array.isArray(task.file_links) ? task.file_links : [];
            const initial = links.length ? links : [{}];
            initial.forEach((link, idx) => fileLinksList.appendChild(renderFileLinkRow(idx, link)));

            if (addFileLinkBtn) {
                addFileLinkBtn.onclick = function () {
                    const nextIndex = fileLinksList.querySelectorAll('.row').length;
                    fileLinksList.appendChild(renderFileLinkRow(nextIndex, {}));
                };
            }
        }

        // set form action to resource update route (adjust if your route differs)
        form.action = `/tasks/${task.id}`;

        // show modal
        bsModal.show();
    });

    // reset form when modal closes
    taskModalEl.addEventListener('hidden.bs.modal', function () {
        form.reset();
        form.action = '#';

        const fileLinksList = document.getElementById('task-file-links-list');
        if (fileLinksList) fileLinksList.innerHTML = '';
    });

    // Remove file link row (delegated)
    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('.task-remove-file-link');
        if (!btn) return;
        const row = btn.closest('.row');
        if (row) row.remove();
    });

    document.addEventListener('click', function (e) {
        // Add new file link row
        const addBtn = e.target.closest('.btn-add-file-link');
        if (addBtn) {
            const modal = addBtn.closest('.modal');
            if (!modal) return;
            const list = modal.querySelector('.file-links-list');
            const tpl = modal.querySelector('.file-link-template .file-link-row');
            if (!list || !tpl) return;
            const clone = tpl.cloneNode(true);
            clone.classList.remove('d-none');
            list.appendChild(clone);
            const firstInput = clone.querySelector('input');
            if (firstInput) firstInput.focus();
            return;
        }

        // Remove a file link row
        const remBtn = e.target.closest('.btn-remove-file-link');
        if (remBtn) {
            const row = remBtn.closest('.file-link-row');
            if (!row) return;
            // If it's the last row, just clear inputs instead of removing (optional)
            const parent = row.parentElement;
            if (parent && parent.querySelectorAll('.file-link-row').length === 1) {
                row.querySelectorAll('input').forEach(i => i.value = '');
            } else {
                row.remove();
            }
            return;
        }
    });
});
</script>
@endpush
