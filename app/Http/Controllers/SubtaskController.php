<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    // Store subtask
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'subtask_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|in:Low,Medium,High',
            'status' => 'required|string',
            'assigned_to' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'file_links' => 'nullable|array',
            'file_links.*' => 'nullable|string',
            'file_links_url' => 'nullable|array',
            'file_links_url.*' => 'nullable|url',
        ]);

        // Process file links
        $file_links = [];
        if ($request->has('file_links') && is_array($request->file_links)) {
            foreach ($request->file_links as $index => $name) {
                if (!empty($name) && isset($request->file_links_url[$index]) && !empty($request->file_links_url[$index])) {
                    $file_links[] = [
                        'name' => $name,
                        'url' => $request->file_links_url[$index],
                    ];
                }
            }
        }

        $validated['task_id'] = $task->id;
        $validated['file_links'] = !empty($file_links) ? $file_links : null;

        Subtask::create($validated);

        return redirect()->route('tasks.index', $task)
            ->with('success', 'Subtask created successfully!');
    }

    // Edit subtask
    public function edit(Subtask $subtask)
    {
        return view('subtasks.edit', compact('subtask'));
    }

    // Update subtask
    public function update(Request $request, Subtask $subtask)
    {
        $validated = $request->validate([
            'subtask_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|in:Low,Medium,High',
            'status' => 'required|string',
            'assigned_to' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'file_links' => 'nullable|array',
            'file_links.*' => 'nullable|string',
            'file_links_url' => 'nullable|array',
            'file_links_url.*' => 'nullable|url',
        ]);

        // Process file links
        $file_links = [];
        if ($request->has('file_links') && is_array($request->file_links)) {
            foreach ($request->file_links as $index => $name) {
                if (!empty($name) && isset($request->file_links_url[$index]) && !empty($request->file_links_url[$index])) {
                    $file_links[] = [
                        'name' => $name,
                        'url' => $request->file_links_url[$index],
                    ];
                }
            }
        }

        $validated['file_links'] = !empty($file_links) ? $file_links : null;
        $subtask->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Subtask updated successfully!');
    }

    // Delete subtask
    public function destroy(Subtask $subtask)
    {
        $subtask->delete();

        return redirect()->back()
            ->with('success', 'Subtask deleted successfully!');
    }

    // Toggle subtask completion
    public function toggle(Request $request, Subtask $subtask)
    {
        $subtask->update(['completed' => $request->boolean('completed')]);

        return response()->json(['completed' => $subtask->completed]);
    }
}
