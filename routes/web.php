<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SetupGuideController;
use App\Http\Controllers\SubtaskController; // ✅ ADD THIS

// Route root ke daftar tugas
Route::get('/', [TaskController::class, 'index'])->name('tasks.index');

// Route resource untuk CRUD tasks - EXCLUDE 'index' karena sudah ada di root
Route::resource('tasks', TaskController::class)->except(['index']);

// ✅ ROUTE CALENDAR - Menggunakan CalendarController untuk views/calendar/index
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

// Route untuk setup guide
Route::get('/setup-guide', [SetupGuideController::class, 'index'])->name('setup.guide');

// ✅ SUBTASK ROUTES
Route::post('tasks/{task}/subtasks', [SubtaskController::class, 'store'])->name('subtasks.store');
Route::patch('subtasks/{subtask}/toggle', [SubtaskController::class, 'toggle'])->name('subtasks.toggle');
Route::delete('subtasks/{subtask}', [SubtaskController::class, 'destroy'])->name('subtasks.destroy');
Route::resource('subtasks', SubtaskController::class)->only(['store', 'edit', 'update', 'destroy']);

