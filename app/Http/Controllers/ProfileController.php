<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private const LOCALES = ['en', 'fil'];

    private const TIMEZONES = [
        'UTC',
        'Asia/Manila',
        'Asia/Singapore',
        'Asia/Tokyo',
        'Asia/Kolkata',
        'Australia/Sydney',
        'Europe/London',
        'Europe/Paris',
        'America/Los_Angeles',
        'America/Denver',
        'America/Chicago',
        'America/New_York',
        'America/Toronto',
        'Pacific/Auckland',
    ];

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $activeSessions = collect();

        if (config('session.driver') === 'database') {
            $activeSessions = DB::connection(config('session.connection'))
                ->table(config('session.table'))
                ->where('user_id', $user->getKey())
                ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->timestamp)
                ->orderByDesc('last_activity')
                ->get(['id', 'ip_address', 'user_agent', 'last_activity'])
                ->map(fn (object $session): object => (object) [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => $session->last_activity,
                    'is_current' => $session->id === $request->session()->getId(),
                ]);
        }

        return view('profile.edit', [
            'activeSessions' => $activeSessions,
            'databaseSessionsEnabled' => config('session.driver') === 'database',
            'supportedTimezones' => self::TIMEZONES,
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        unset($validated['avatar']);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $previousPhotoPath = $user->profile_photo_path;
        if ($request->hasFile('avatar')) {
            $photoPath = $request->file('avatar')->storePublicly('profile-photos', 'public');

            if ($photoPath === false) {
                return back()->withErrors(['avatar' => __('The profile picture could not be saved.')]);
            }

            $user->profile_photo_path = $photoPath;
        }

        $user->save();

        if ($previousPhotoPath !== $user->profile_photo_path && $previousPhotoPath !== null) {
            Storage::disk('public')->delete($previousPhotoPath);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'theme' => ['required', Rule::in(['light', 'dark'])],
            'locale' => ['required', Rule::in(self::LOCALES)],
            'timezone' => ['required', Rule::in(self::TIMEZONES)],
            'notify_upcoming_deadlines' => ['required', 'boolean'],
            'notify_overdue_tasks' => ['required', 'boolean'],
            'notify_task_completions' => ['required', 'boolean'],
        ]);

        $request->user()->update($validated);

        return Redirect::route('profile.edit')->with('status', 'preferences-updated');
    }

    public function revokeSession(Request $request, string $sessionId): RedirectResponse
    {
        $this->ensureDatabaseSessionsAreEnabled();
        abort_if($sessionId === $request->session()->getId(), 422, 'The current session cannot be revoked here.');

        $deleted = DB::connection(config('session.connection'))
            ->table(config('session.table'))
            ->where('user_id', $request->user()->getKey())
            ->where('id', $sessionId)
            ->delete();

        abort_unless($deleted > 0, 404);

        return Redirect::route('profile.edit')->with('status', 'session-revoked');
    }

    public function logoutAllDevices(Request $request): RedirectResponse
    {
        $this->ensureDatabaseSessionsAreEnabled();
        $request->validateWithBag('logoutAllDevices', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        DB::connection(config('session.connection'))
            ->table(config('session.table'))
            ->where('user_id', $user->getKey())
            ->delete();

        $user->forceFill(['remember_token' => Str::random(60)])->save();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function ensureDatabaseSessionsAreEnabled(): void
    {
        abort_unless(
            config('session.driver') === 'database',
            409,
            'Session management requires the database session driver.',
        );
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $profilePhotoPath = $user->profile_photo_path;

        $accountDeleted = DB::transaction(function () use ($user): bool {
            $user = $user->newQuery()->lockForUpdate()->findOrFail($user->getKey());

            if ($user->isAdmin()) {
                $adminCount = $user->newQuery()
                    ->where('is_admin', true)
                    ->lockForUpdate()
                    ->get(['id'])
                    ->count();

                if ($adminCount <= 1) {
                    return false;
                }
            }

            $user->delete();

            return true;
        });

        if (! $accountDeleted) {
            return Redirect::route('profile.edit')->withErrors([
                'password' => __('The last administrator cannot delete their account.'),
            ], 'userDeletion');
        }

        DB::connection(config('session.connection'))
            ->table(config('session.table'))
            ->where('user_id', $user->getKey())
            ->delete();

        Auth::logout();

        if ($profilePhotoPath !== null) {
            Storage::disk('public')->delete($profilePhotoPath);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
