<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskActivity;
use Illuminate\Contracts\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        $activities = TaskActivity::query()
            ->with('user:id,name,email')
            ->latest()
            ->paginate(25);

        return view('admin.activities.index', compact('activities'));
    }
}
