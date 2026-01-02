@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Calendar View</h2>
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-list-task"></i> Back to Task List
            </a>
        </div>
    </div>

    <!-- Legend for Status Colors -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3"><i class="bi bi-palette"></i> Status Legend</h6>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="legend-item">
                            <span class="legend-box" style="background-color: #6c757d;"></span>
                            <span>Not Started</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box" style="background-color: #17a2b8;"></span>
                            <span>In Progress</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box" style="background-color: #6f42c1;"></span>
                            <span>Review</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box" style="background-color: #ffc107;"></span>
                            <span>On Hold</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-box" style="background-color: #28a745;"></span>
                            <span>Completed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-3">
                    <div id="calendar"></div>
                    <div id="calendar-loading" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Loading calendar...</p>
                    </div>
                    <div id="calendar-error" class="alert alert-warning" style="display: none;">
                        <strong>⚠️ Calendar tidak dapat dimuat.</strong> Periksa console untuk detail error.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Detail Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel">Task Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="taskDetails"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="editTaskBtn" href="#" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Task
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Calendar Styling */
    #calendar {
        max-width: 100%;
        margin: 0 auto;
        min-height: 600px;
    }

    /* FullCalendar responsive adjustments */
    .fc-toolbar {
        flex-wrap: wrap;
        gap: 10px;
    }

    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 600;
        color: #2c3e50;
    }

    .fc-button {
        background-color: #007bff !important;
        border-color: #007bff !important;
        text-transform: capitalize !important;
        padding: 0.4rem 0.8rem !important;
    }

    .fc-button:hover {
        background-color: #0056b3 !important;
        border-color: #0056b3 !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .fc-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .fc-button-active {
        background-color: #0056b3 !important;
        border-color: #0056b3 !important;
    }

    /* Calendar day cells */
    .fc-daygrid-day {
        transition: background-color 0.2s ease;
    }

    .fc-daygrid-day:hover {
        background-color: #f8f9fa;
    }

    .fc-day-today {
        background-color: #fff3cd !important;
    }

    /* Event styling */
    .fc-event {
        border-radius: 4px;
        padding: 2px 4px;
        margin: 2px 0;
        font-size: 0.85rem;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none !important;
    }

    .fc-event:hover {
        transform: scale(1.05);
        box-shadow: 0 3px 8px rgba(0,0,0,0.3);
        z-index: 999;
    }

    .fc-event-title {
        font-weight: 500;
    }

    /* Legend styling */
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .legend-box {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        display: inline-block;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Modal styling */
    .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    /* Task detail items */
    .task-detail-item {
        padding: 10px;
        border-bottom: 1px solid #e9ecef;
    }

    .task-detail-item:last-child {
        border-bottom: none;
    }

    .task-detail-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }

    .task-detail-value {
        color: #212529;
    }

    /* Notes Display in Modal */
    .notes-display {
        max-height: 200px;
        overflow-y: auto;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
        border: 1px solid #e0e0e0;
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .fc-toolbar {
            flex-direction: column;
            align-items: stretch !important;
        }

        .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }

        .fc-toolbar-title {
            font-size: 1.2rem !important;
        }

        .fc-button {
            font-size: 0.85rem !important;
            padding: 0.3rem 0.6rem !important;
        }

        .fc-event {
            font-size: 0.75rem;
        }

        .legend-item {
            font-size: 0.85rem;
        }
    }

    @media (max-width: 576px) {
        .fc-toolbar-title {
            font-size: 1rem !important;
        }

        .fc-daygrid-day-number {
            font-size: 0.85rem;
        }
    }
</style>
@endsection

@push('scripts')
<!-- FullCalendar v5 CDN -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== 🔍 CALENDAR DEBUG START ===');
    
    var calendarEl = document.getElementById('calendar');
    console.log('1. Calendar element found:', calendarEl ? '✅ YES' : '❌ NO');
    console.log('2. FullCalendar library loaded:', typeof FullCalendar !== 'undefined' ? '✅ YES' : '❌ NO');
    
    // Function to get color based on status
    function getStatusColor(status) {
        const statusLower = status.toLowerCase();
        switch(statusLower) {
            case 'completed':
                return '#28a745'; // Green
            case 'in progress':
                return '#17a2b8'; // Cyan
            case 'review':
                return '#6f42c1'; // Purple
            case 'on hold':
                return '#ffc107'; // Yellow
            case 'not started':
                return '#6c757d'; // Gray
            default:
                return '#6c757d'; // Default gray
        }
    }

    // Function to get priority badge
    function getPriorityBadge(priority) {
        const priorityLower = priority.toLowerCase();
        switch(priorityLower) {
            case 'high':
                return '<span class="badge" style="background-color: #dc3545; color: white;"><i class="bi bi-exclamation-circle-fill"></i> High</span>';
            case 'medium':
                return '<span class="badge" style="background-color: #fd7e14; color: white;"><i class="bi bi-dash-circle-fill"></i> Medium</span>';
            case 'low':
                return '<span class="badge" style="background-color: #28a745; color: white;"><i class="bi bi-arrow-down-circle-fill"></i> Low</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }

    // Function to get status badge
    function getStatusBadge(status) {
        const statusLower = status.toLowerCase();
        switch(statusLower) {
            case 'completed':
                return '<span class="badge" style="background-color: #28a745; color: white;"><i class="bi bi-check-circle-fill"></i> Completed</span>';
            case 'in progress':
                return '<span class="badge" style="background-color: #17a2b8; color: white;"><i class="bi bi-arrow-clockwise"></i> In Progress</span>';
            case 'review':
                return '<span class="badge" style="background-color: #6f42c1; color: white;"><i class="bi bi-eye-fill"></i> Review</span>';
            case 'on hold':
                return '<span class="badge" style="background-color: #ffc107; color: #212529;"><i class="bi bi-pause-circle-fill"></i> On Hold</span>';
            case 'not started':
                return '<span class="badge" style="background-color: #6c757d; color: white;"><i class="bi bi-circle"></i> Not Started</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }

    // Prepare events data from Laravel
    var events = @json($events);
    
    console.log('3. Total events from database:', events.length);
    console.log('4. Events data:', events);
    
    if (!calendarEl) {
        console.error('❌ Calendar element not found!');
        document.getElementById('calendar-error').style.display = 'block';
        return;
    }

    if (typeof FullCalendar === 'undefined') {
        console.error('❌ FullCalendar library not loaded!');
        document.getElementById('calendar-error').style.display = 'block';
        return;
    }

    if (events.length === 0) {
        console.warn('⚠️ No events data! Calendar will be empty.');
    }
    
    // Transform events to add colors
    var calendarEvents = events.map(function(event) {
        console.log('Processing event:', event.title);
        return {
            id: event.id,
            title: event.title,
            start: event.start,
            end: event.end,
            backgroundColor: getStatusColor(event.status),
            borderColor: getStatusColor(event.status),
            extendedProps: {
                type: event.type,
                description: event.description,
                priority: event.priority,
                status: event.status,
                assigned_to: event.assigned_to,
                progress: event.progress,
                file_links: event.file_links,
                notes: event.notes,
                task_id: event.id
            }
        };
    });
    
    console.log('5. Calendar events after transformation:', calendarEvents.length, 'events');

    try {
        console.log('6. Initializing FullCalendar...');
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek,dayGridDay'
            },
            displayEventTime: false,
            events: calendarEvents,
            editable: false,
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,
            weekends: true,
            height: 'auto',
            
            // Event click handler
            eventClick: function(info) {
                console.log('Event clicked:', info.event.title);
                var event = info.event;
                var props = event.extendedProps;
                
                // Format dates
                var startDate = new Date(event.start);
                var endDate = event.end ? new Date(event.end) : startDate;
                
                var options = { year: 'numeric', month: 'short', day: 'numeric' };
                var formattedStart = startDate.toLocaleDateString('en-US', options);
                var formattedEnd = endDate.toLocaleDateString('en-US', options);
                
                // Build task/subtask details HTML
                var detailsHtml = `
                    <div class="task-detail-item">
                        <div class="task-detail-label">Type</div>
                        <div class="task-detail-value">
                            ${props.type === 'task' ? '<span class="badge bg-primary">TASK</span>' : '<span class="badge bg-info">SUBTASK</span>'}
                        </div>
                    </div>
                    <div class="task-detail-item">
                        <div class="task-detail-label">${props.type === 'task' ? 'Task Name' : 'Subtask Name'}</div>
                        <div class="task-detail-value"><strong>${event.title.replace('[TASK] ', '').replace('[SUBTASK] ', '')}</strong></div>
                    </div>
                    <div class="task-detail-item">
                        <div class="task-detail-label">Description</div>
                        <div class="task-detail-value">${props.description || 'No description'}</div>
                    </div>
                `;
                
                // Add priority only for tasks
                if (props.type === 'task') {
                    detailsHtml += `
                    <div class="task-detail-item">
                        <div class="task-detail-label">Priority</div>
                        <div class="task-detail-value">${getPriorityBadge(props.priority)}</div>
                    </div>
                    `;
                }
                
                detailsHtml += `
                    <div class="task-detail-item">
                        <div class="task-detail-label">Status</div>
                        <div class="task-detail-value">${getStatusBadge(props.status)}</div>
                    </div>
                    <div class="task-detail-item">
                        <div class="task-detail-label">Assigned To</div>
                        <div class="task-detail-value">${props.assigned_to || 'Unassigned'}</div>
                    </div>
                    <div class="task-detail-item">
                        <div class="task-detail-label">Start Date</div>
                        <div class="task-detail-value">${formattedStart}</div>
                    </div>
                    <div class="task-detail-item">
                        <div class="task-detail-label">Due Date</div>
                        <div class="task-detail-value">${formattedEnd}</div>
                    </div>
                `;
                
                // Add progress only for tasks
                if (props.type === 'task' && props.progress !== undefined) {
                    detailsHtml += `
                    <div class="task-detail-item">
                        <div class="task-detail-label">Progress</div>
                        <div class="task-detail-value">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar ${props.progress >= 75 ? 'bg-success' : props.progress >= 50 ? 'bg-info' : props.progress >= 25 ? 'bg-warning' : 'bg-danger'}" 
                                     role="progressbar" 
                                     style="width: ${props.progress}%;" 
                                     aria-valuenow="${props.progress}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    ${props.progress}%
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                }
                
                if (props.file_links) {
                    detailsHtml += `
                    <div class="task-detail-item">
                        <div class="task-detail-label">File Links</div>
                        <div class="task-detail-value">
                            <a href="${props.file_links}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark"></i> View File
                            </a>
                        </div>
                    </div>
                    `;
                }
                
                if (props.notes) {
                    detailsHtml += `
                    <div class="task-detail-item">
                        <div class="task-detail-label">Notes</div>
                        <div class="notes-display">${props.notes}</div>
                    </div>
                    `;
                }
                
                // Update modal content
                document.getElementById('taskDetails').innerHTML = detailsHtml;
                document.getElementById('editTaskBtn').href = props.type === 'task' ? '/tasks/' + props.task_id + '/edit' : '/subtasks/' + props.task_id + '/edit';
                
                // Show modal
                var modal = new bootstrap.Modal(document.getElementById('taskModal'));
                modal.show();
            },
            
            // Responsive height
            contentHeight: 'auto',
            
            // Add more space on mobile
            dayMaxEventRows: window.innerWidth < 768 ? 2 : 4,
            
            // Loading and error handlers
            loading: function(isLoading) {
                console.log('Calendar loading state:', isLoading);
            },
            
            eventDidMount: function(info) {
                console.log('Event mounted:', info.event.title);
            }
        });

        console.log('7. Rendering calendar...');
        calendar.render();
        console.log('✅ Calendar rendered successfully!');
        console.log('=== 🎉 CALENDAR DEBUG END ===');

        // Responsive calendar on window resize
        window.addEventListener('resize', function() {
            calendar.setOption('dayMaxEventRows', window.innerWidth < 768 ? 2 : 4);
        });

    } catch (error) {
        console.error('❌ Error initializing calendar:', error);
        document.getElementById('calendar-error').style.display = 'block';
        document.getElementById('calendar-error').innerHTML = '<strong>❌ Error:</strong> ' + error.message;
    }
});
</script>
@endpush
