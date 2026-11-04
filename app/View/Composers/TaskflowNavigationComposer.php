<?php

namespace App\View\Composers;

use App\Services\UnreadNotificationCounter;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskflowNavigationComposer
{
    public function __construct(private readonly UnreadNotificationCounter $unreadNotificationCounter) {}

    public function compose(View $view): void
    {
        $user = Auth::user();

        $view->with(
            'unreadNotificationCount',
            $user === null ? 0 : $this->unreadNotificationCounter->for($user),
        );
    }
}
