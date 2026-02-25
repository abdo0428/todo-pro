<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('tasks.index'));

Route::resource('tasks', TaskController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::patch('tasks/{task}/status', [TaskController::class, 'setStatus'])->name('tasks.status');
Route::post('tasks/bulk-action', [TaskController::class, 'bulkAction'])->name('tasks.bulk-action');
