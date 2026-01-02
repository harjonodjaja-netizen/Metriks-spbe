<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Subtask;
use App\Models\SubtaskFileLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubtaskController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $validated = $this->validateSubtask($request);

        return DB::transaction(function () use ($task, $validated, $request) {
            $subtask = $task->subtasks()->create($validated);
            $this->saveFileLinks($subtask, $request);

            return redirect()->route('tasks.index')
                ->with('success', 'Subtask created successfully!');
        });
    }

    public function edit(Subtask $subtask)
    {
        return view('subtasks.edit', compact('subtask'));
    }

    public function update(Request $request, Subtask $subtask)
    {
        $validated = $this->validateSubtask($request);

        return DB::transaction(function () use ($subtask, $validated, $request) {
            $subtask->update($validated);
            $subtask->fileLinks()->delete();
            $this->saveFileLinks($subtask, $request);

            return redirect()->route('tasks.index')
                ->with('success', 'Subtask updated successfully!');
        });
    }

    public function destroy(Subtask $subtask)
    {
        $task = $subtask->task;
        $subtask->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Subtask deleted successfully!');
    }

    public function toggle(Request $request, Subtask $subtask)
    {
        $subtask->update(['completed' => $request->boolean('completed')]);

        return response()->json([
            'completed' => $subtask->completed,
            'message' => $subtask->completed ? '✓ Marked complete' : '↻ Reopened',
            'timestamp' => $subtask->updated_at,
        ]);
    }

    private function validateSubtask(Request $request)
    {
        return $request->validate([
            'subtask_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:Low,Medium,High',
            'status' => 'nullable|in:Not Started,In Progress,Review,On Hold,Completed',
            'assigned_to' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            // File links as nested array with correct field names
            'file_links' => 'nullable|array',
            'file_links.*.name' => 'nullable|string|max:255',
            'file_links.*.url' => 'nullable|url',
            'file_links.*.description' => 'nullable|string',
        ]);
    }

    private function saveFileLinks(Subtask $subtask, Request $request)
    {
        // Handle file_links as nested array: file_links[0][name], file_links[0][url]
        if ($request->has('file_links') && is_array($request->file_links)) {
            foreach ($request->file_links as $link) {
                // Skip if URL is empty
                if (empty($link['url'] ?? null)) {
                    continue;
                }

                SubtaskFileLink::create([
                    'subtask_id' => $subtask->id,
                    'link_name' => $link['name'] ?? 'Link',
                    'link_url' => $link['url'],
                    'description' => $link['description'] ?? null,
                ]);
            }
        }
    }
}
