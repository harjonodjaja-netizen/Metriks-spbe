<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'order',
    ];

    protected $casts = [
        'start_date' => 'datetime:Y-m-d',
        'due_date' => 'datetime:Y-m-d',
        'completed' => 'boolean',
    ];

    // Relationship to Task
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    // Relationship to file links (NEW!)
    public function fileLinks()
    {
        return $this->hasMany(SubtaskFileLink::class, 'subtask_id');
    }
}