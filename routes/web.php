<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/tasks/data', [TaskController::class, 'data'])->name('tasks.data');
    Route::post('/tasks/bulk-action', [TaskController::class, 'bulkAction'])->name('tasks.bulk-action');
    Route::post('/tasks/fill-demo', [TaskController::class, 'fillDemo'])->name('tasks.fill-demo');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'setStatus'])->name('tasks.status');
    Route::resource('tasks', TaskController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

    Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
});
