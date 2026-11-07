<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationCenterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskPageController;
use App\Http\Middleware\ApplyUserPreferences;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', ApplyUserPreferences::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences.update');
    Route::delete('/profile/sessions/{sessionId}', [ProfileController::class, 'revokeSession'])->name('profile.sessions.destroy');
    Route::delete('/profile/sessions', [ProfileController::class, 'logoutAllDevices'])->name('profile.sessions.destroy-all');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', ApplyUserPreferences::class])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/notifications', NotificationCenterController::class)->name('notifications.index');
    Route::post('/tasks/{task}/cycle-status', [TaskPageController::class, 'cycleStatus'])->name('tasks.cycle-status');
    Route::resource('tasks', TaskPageController::class)->except(['show']);
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
