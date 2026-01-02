<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Menampilkan tampilan kalender dengan tugas yang terjadwal.
     */
    public function index(Request $request)
    {
        $taskEvents = Task::query()
            ->with(['fileLinks'])
            ->get()
            ->map(function (Task $task) {
                $fileUrl = $task->fileLinks->first()?->link_url;

                return [
                    'id' => $task->id,
                    'title' => $task->task_name,
                    'start' => $task->start_date,
                    'end' => $task->due_date,
                    'type' => 'task',
                    'description' => $task->description ?? 'No description',
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'assigned_to' => $task->assigned_to,
                    'progress' => $task->progress ?? 0,
                    'file_links' => $fileUrl,
                    'notes' => $task->notes ?? '',
                ];
            });

        $subtaskEvents = Subtask::query()
            ->where(function ($query) {
                $query->whereNotNull('start_date')
                    ->orWhereNotNull('due_date');
            })
            ->with(['task', 'fileLinks'])
            ->get()
            ->map(function (Subtask $subtask) {
                $start = $subtask->start_date ?? $subtask->due_date;
                $end = $subtask->due_date ?? $subtask->start_date ?? $start;
                $fileUrl = $subtask->fileLinks->first()?->link_url;

                return [
                    'id' => $subtask->id,
                    'title' => '[SUBTASK] ' . $subtask->subtask_name,
                    'start' => $start,
                    'end' => $end,
                    'type' => 'subtask',
                    'description' => $subtask->description ?? 'No description',
                    'priority' => $subtask->priority,
                    'status' => $subtask->status,
                    'assigned_to' => $subtask->assigned_to,
                    'progress' => null,
                    'file_links' => $fileUrl,
                    'notes' => $subtask->notes ?? '',
                ];
            });

        $events = $taskEvents->merge($subtaskEvents);

        // TEMPORARY: Tambahkan data dummy untuk testing jika tidak ada data
        if ($events->isEmpty()) {
            \Log::info('No tasks found, adding dummy data');
            $events = collect([
                [
                    'id' => 999,
                    'title' => '🧪 Test Task - Data Dummy',
                    'start' => now()->format('Y-m-d'),
                    'end' => now()->addDays(2)->format('Y-m-d'),
                    'type' => 'task',
                    'description' => 'Ini adalah data dummy untuk testing calendar. Silakan tambahkan task yang sebenarnya.',
                    'priority' => 'High',
                    'status' => 'In Progress',
                    'assigned_to' => 'Test User',
                    'progress' => 50,
                    'file_links' => null,
                    'notes' => 'Data ini hanya untuk testing',
                ],
                [
                    'id' => 998,
                    'title' => '📝 Sample Task 2',
                    'start' => now()->addDays(3)->format('Y-m-d'),
                    'end' => now()->addDays(5)->format('Y-m-d'),
                    'type' => 'task',
                    'description' => 'Contoh task kedua',
                    'priority' => 'Medium',
                    'status' => 'Not Started',
                    'assigned_to' => 'John Doe',
                    'progress' => 0,
                    'file_links' => null,
                    'notes' => null,
                ]
            ]);
        }

        \Log::info('Total events to display: ' . $events->count());
        
        // Kirim data tugas yang telah diformat ke view
        return view('calendar.index', compact('events'));
    }

    /**
     * Menambahkan tugas baru dan mengarahkan kembali ke halaman kalender.
     */
    public function store(Request $request)
    {
        // Validasi data yang dikirim oleh user
        $request->validate([
            'task_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|string',
            'status' => 'required|string',
            'assigned_to' => 'required|string',
            'description' => 'nullable|string',
            'progress' => 'nullable|integer|min:0|max:100',
            'file_links' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Membuat task baru
        Task::create([
            'task_name' => $request->task_name,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'status' => $request->status,
            'assigned_to' => $request->assigned_to,
            'description' => $request->description,
            'progress' => $request->progress ?? 0,
            'file_links' => $request->file_links,
            'notes' => $request->notes,
        ]);

        // Redirect ke tampilan kalender dengan membawa task terbaru
        return redirect()->route('calendar.index')->with('success', 'Tugas berhasil ditambahkan!');
    }
}