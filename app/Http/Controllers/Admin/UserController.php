<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
        ]);
        $isAdminPage = $request->routeIs('admin.admins.index');
        $users = User::query()
            ->withCount('tasks')
            ->when($isAdminPage, fn ($query) => $query->where('is_admin', true))
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('isAdminPage', 'users'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'is_admin' => ['required', 'boolean'],
        ]);
        $isAdmin = (bool) $validated['is_admin'];

        if ($user->is($request->user()) && ! $isAdmin) {
            throw ValidationException::withMessages([
                'is_admin' => 'You cannot remove your own administrator access.',
            ]);
        }

        DB::transaction(function () use ($isAdmin, $user): void {
            $user = User::query()->lockForUpdate()->findOrFail($user->getKey());

            if ($user->is_admin && ! $isAdmin) {
                $adminCount = User::query()
                    ->where('is_admin', true)
                    ->lockForUpdate()
                    ->get(['id'])
                    ->count();

                if ($adminCount <= 1) {
                    throw ValidationException::withMessages([
                        'is_admin' => 'The last administrator cannot be removed.',
                    ]);
                }
            }

            $user->forceFill(['is_admin' => $isAdmin])->save();
        });

        return back()->with('status', 'Account access updated.');
    }
}
