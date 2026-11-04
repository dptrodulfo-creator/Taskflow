<?php

namespace App\Providers;

use App\Models\Task;
use App\Observers\TaskObserver;
use App\View\Composers\TaskflowNavigationComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Task::observe(TaskObserver::class);
        View::composer('layouts.taskflow', TaskflowNavigationComposer::class);
    }
}
