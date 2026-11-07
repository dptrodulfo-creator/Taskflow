<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::redirect('/admin', '/admin/login');
Route::redirect('/admin/', '/admin/login');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    });

    Route::middleware(EnsureAdmin::class)->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/admins', [UserController::class, 'index'])->name('admins.index');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role.update');
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/activity', [ActivityController::class, 'index'])->name('activities.index');
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});
