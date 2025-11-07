<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_name',
        'description',
        'priority',
        'status',
        'assigned_to',
        'start_date',
        'due_date',
        'notes',
        'progress',
    ];

    // ✅ ADD THIS - Cast dates to Carbon objects
    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'progress' => 'integer',
    ];

    // Relationship with file links
    public function fileLinks()
    {
        return $this->hasMany(TaskFileLink::class);
    }

    // Relationship with subtasks
    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }
}
