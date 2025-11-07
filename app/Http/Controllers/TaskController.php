<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskFileLink;
use Illuminate\Http\Request;
use Exception;

class TaskController extends Controller
{
public function index(Request $request)
{
    $search = $request->get('search', '');
    $sortDueDate = $request->get('sort_due_date', 'asc');
    $filterPriority = $request->get('filter_priority', '');
    $filterStatus = $request->get('filter_status', '');

    // ✅ FIXED: Don't use ->with() on query yet
    $tasks = Task::query();

    if ($search) {
        $tasks->where(function ($query) use ($search) {
            $query->where('task_name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
            
            if (is_numeric($search)) {
                $query->orWhere('id', (int) $search);
            }
        });
    }

    if ($filterPriority) {
        $tasks->where('priority', $filterPriority);
    }

    if ($filterStatus) {
        $tasks->where('status', $filterStatus);
    }

    if ($sortDueDate && in_array($sortDueDate, ['asc', 'desc'])) {
        $tasks->orderBy('due_date', $sortDueDate);
    }

    // ✅ FIXED: Use with() before paginate()
    $tasks = $tasks->with('fileLinks')->paginate(15);

    return view('tasks.index', compact('tasks', 'search', 'sortDueDate', 'filterPriority', 'filterStatus'));
}

    public function calendar()
    {
        $tasks = Task::with(['fileLinks', 'subtasks'])->get(); // ✅ UPDATED: Added fileLinks
        
        $events = $tasks->map(function($task) {
            // Format file links
            $fileLinksText = '';
            if ($task->fileLinks->count() > 0) {
                $fileLinksText = $task->fileLinks->map(fn($link) => $link->link_name)->implode(', ');
            }
            
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
                'file_links' => $fileLinksText ?: 'No links',
                'notes' => $task->notes ?? '',
            ];
        });

        // ✅ ADDED: Include subtask events
        $subtasks = \App\Models\Subtask::whereNotNull('start_date')->with('task')->get();
        $subtaskEvents = $subtasks->map(function($subtask) {
            return [
                'id' => $subtask->id,
                'title' => '[SUBTASK] ' . $subtask->subtask_name,
                'start' => $subtask->start_date,
                'end' => $subtask->due_date,
                'type' => 'subtask',
                'description' => $subtask->description ?? 'No description',
                'status' => $subtask->status,
                'assigned_to' => $subtask->assigned_to,
                'progress' => null,
                'file_links' => 'N/A',
                'notes' => $subtask->notes ?? '',
            ];
        });

        $events = $events->merge($subtaskEvents);

        return view('calendar.index', compact('events'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string',
            'status' => 'required|string',
            'assigned_to' => 'required|string|max:255',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'progress' => 'required|integer|min:0|max:100',
            'file_links' => 'nullable|array', // ✅ UPDATED: Changed to array
            'file_links.*.name' => 'nullable|string',
            'file_links.*.url' => 'nullable|url',
            'file_links.*.description' => 'nullable|string',
        ]);

        try {
            $validated['priority'] = ucfirst(strtolower($validated['priority']));
            $validated['status'] = match(strtolower($validated['status'])) {
                'not started' => 'Not Started',
                'in progress' => 'In Progress',
                'review' => 'Review',
                'on hold' => 'On Hold',
                'completed' => 'Completed',
                default => ucwords(strtolower($validated['status']))
            };

            // Remove file_links from validated data (will be saved separately)
            $fileLinksData = $validated['file_links'] ?? [];
            unset($validated['file_links']);

            $task = Task::create($validated);

            // ✅ ADDED: Save multiple file links
            if (!empty($fileLinksData)) {
                foreach ($fileLinksData as $link) {
                    if (!empty($link['url'])) {
                        TaskFileLink::create([
                            'task_id' => $task->id,
                            'link_name' => $link['name'] ?? 'Link',
                            'link_url' => $link['url'],
                            'description' => $link['description'] ?? null,
                        ]);
                    }
                }
            }

            return redirect()->route('tasks.index')->with('success', 'Tugas berhasil ditambahkan!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan tugas: ' . $e->getMessage());
        }
    }

    public function edit(Task $task)
    {
        $task->load('fileLinks'); // ✅ ADDED: Load file links
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string',
            'status' => 'required|string',
            'assigned_to' => 'required|string|max:255',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'progress' => 'required|integer|min:0|max:100',
            'file_links' => 'nullable|array', // ✅ UPDATED: Changed to array
            'file_links.*.name' => 'nullable|string',
            'file_links.*.url' => 'nullable|url',
            'file_links.*.description' => 'nullable|string',
        ]);

        try {
            $validated['priority'] = ucfirst(strtolower($validated['priority']));
            $validated['status'] = match(strtolower($validated['status'])) {
                'not started' => 'Not Started',
                'in progress' => 'In Progress',
                'review' => 'Review',
                'on hold' => 'On Hold',
                'completed' => 'Completed',
                default => ucwords(strtolower($validated['status']))
            };

            // Remove file_links from validated data (will be saved separately)
            $fileLinksData = $validated['file_links'] ?? [];
            unset($validated['file_links']);

            $task->update($validated);

            // ✅ ADDED: Delete old file links and create new ones
            $task->fileLinks()->delete();
            
            if (!empty($fileLinksData)) {
                foreach ($fileLinksData as $link) {
                    if (!empty($link['url'])) {
                        TaskFileLink::create([
                            'task_id' => $task->id,
                            'link_name' => $link['name'] ?? 'Link',
                            'link_url' => $link['url'],
                            'description' => $link['description'] ?? null,
                        ]);
                    }
                }
            }

            return redirect()->route('tasks.index')->with('success', 'Tugas berhasil diperbarui!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui tugas: ' . $e->getMessage());
        }
    }

    public function destroy(Task $task)
    {
        try {
            // ✅ ADDED: Delete related file links
            $task->fileLinks()->delete();
            
            $deletedId = $task->id;
            $task->delete();
            \DB::statement('UPDATE tasks SET id = id - 1 WHERE id > ?', [$deletedId]);
            $maxId = Task::max('id') ?? 0;
            \DB::statement('ALTER TABLE tasks AUTO_INCREMENT = ' . ($maxId + 1));
            return redirect()->route('tasks.index')->with('success', 'Tugas berhasil dihapus dan ID diatur ulang!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus tugas: ' . $e->getMessage());
        }
    }
}
