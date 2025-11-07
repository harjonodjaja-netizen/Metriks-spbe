<?php

// app/Http/Requests/TaskRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize()
    {
        return true;  // Pastikan ini mengizinkan permintaan dari pengguna
    }

    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'description' => 'required|string|max:1000',  // You might want to limit description length
            'priority' => 'required|in:HIGH,MEDIUM,LOW',
            'status' => 'required|in:NOT_STARTED,IN_PROGRESS,COMPLETED',  // Fixed validation for status
            'assigned_to' => 'required|exists:users,id', // Memastikan `assigned_to` adalah ID yang valid dari tabel `users`
            'start_date' => 'nullable|date',  // Assuming you want to make start_date optional
            'due_date' => 'nullable|date|after_or_equal:start_date',  // Making due_date optional but it must be after start_date if provided
            'progress' => 'nullable|integer|between:0,100',  // Optional but must be between 0 and 100
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Task Name is required.',
            'description.required' => 'Description is required.',
            'priority.required' => 'Priority must be selected.',
            'status.required' => 'Status must be selected.',
            'assigned_to.required' => 'Assigned To is required.',
            'assigned_to.exists' => 'Assigned user must be a valid user.',
            'start_date.date' => 'Start Date must be a valid date.',
            'due_date.date' => 'Due Date must be a valid date.',
            'due_date.after_or_equal' => 'Due Date must be after or equal to Start Date.',
            'progress.integer' => 'Progress must be an integer.',
            'progress.between' => 'Progress must be between 0 and 100.',
        ];
    }
}
