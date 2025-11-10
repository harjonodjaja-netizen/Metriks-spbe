<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'subtask_name',
        'description',
        'priority',
        'status',
        'assigned_to',
        'start_date',
        'due_date',
        'notes',
        'completed',
        'file_links',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed' => 'boolean',
        'file_links' => 'array',  // ✅ IMPORTANT
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
