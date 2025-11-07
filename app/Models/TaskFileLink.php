<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskFileLink extends Model
{
    protected $fillable = ['task_id', 'link_name', 'link_url', 'description'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
