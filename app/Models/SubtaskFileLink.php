<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubtaskFileLink extends Model
{
    use HasFactory;

    protected $table = 'subtask_file_links';

    protected $fillable = [
        'subtask_id',
        'link_name',
        'link_url',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship back to Subtask
    public function subtask()
    {
        return $this->belongsTo(Subtask::class, 'subtask_id');
    }
}