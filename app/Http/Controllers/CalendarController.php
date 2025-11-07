<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Menampilkan tampilan kalender dengan tugas yang terjadwal.
     */
    public function index(Request $request)
    {
        // Ambil data tugas yang ada dan urutkan berdasarkan tanggal mulai atau tanggal jatuh tempo
        $tasks = Task::query()
            ->select('id', 'task_name', 'start_date', 'due_date', 'priority', 'status', 'assigned_to', 'description', 'progress', 'file_links', 'notes')
            ->get();

        // Format data untuk ditampilkan di kalender
        $events = $tasks->map(function ($task) {
            return [
                'id' => $task->id, // ID tugas untuk edit
                'title' => $task->task_name, // Nama tugas
                'start' => $task->start_date, // Tanggal mulai
                'end' => $task->due_date, // Tanggal jatuh tempo
                'description' => $task->description ?? 'No description', // Deskripsi tugas
                'priority' => $task->priority, // Prioritas
                'status' => $task->status, // Status tugas
                'assigned_to' => $task->assigned_to, // Penugasan
                'progress' => $task->progress ?? 0, // Progress tugas (default 0)
                'file_links' => $task->file_links, // Link file
                'notes' => $task->notes, // Catatan tambahan
            ];
        });

        // TEMPORARY: Tambahkan data dummy untuk testing jika tidak ada data
        if ($events->isEmpty()) {
            \Log::info('No tasks found, adding dummy data');
            $events = collect([
                [
                    'id' => 999,
                    'title' => '🧪 Test Task - Data Dummy',
                    'start' => now()->format('Y-m-d'),
                    'end' => now()->addDays(2)->format('Y-m-d'),
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