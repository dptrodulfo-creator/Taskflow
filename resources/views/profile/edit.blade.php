@extends('layouts.taskflow')

@section('title', __('Settings'))

@section('content')

<div class="topbar">
    <div>
        <p class="eyebrow">{{ __('Your account') }}</p>
        <h1>{{ __('Settings') }}</h1>
        <p class="sub">{{ __('Manage your profile, security, and preferences.') }}</p>
    </div>
</div>

@if(session('status'))
    <div class="settings-status" role="status">
        {{ match (session('status')) {
            'profile-updated' => __('Profile changes saved.'),
            'preferences-updated' => __('Your preferences have been saved.'),
            'password-updated' => __('Your password has been changed.'),
            'session-revoked' => __('The selected session has been signed out.'),
            default => __('Settings updated.'),
        } }}
    </div>
@endif

<div class="settings-sections">
    <section class="panel settings-panel" id="profile-information">
        <div class="settings-section-heading">
            <span class="settings-section-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M5 20c.7-3.1 3.2-5 7-5s6.3 1.9 7 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            <span>
                <h2>{{ __('Profile information') }}</h2>
                <p>{{ __('Keep your personal details up to date.') }}</p>
            </span>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="settings-form">
            @csrf
            @method('PATCH')

            <div class="settings-avatar-row">
                @if($user->profile_photo_path)
                    <img class="settings-avatar" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_photo_path) }}" alt="{{ __('Profile picture') }}">
                @else
                    <span class="settings-avatar settings-avatar-placeholder" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
                <div class="settings-avatar-control">
                    <label for="avatar">{{ __('Profile picture') }}</label>
                    <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="settings-file-input">
                    <p class="settings-help">{{ __('JPG, PNG, or WebP. Maximum file size: 2 MB.') }}</p>
                    <x-input-error :messages="$errors->get('avatar')" class="settings-error" />
                </div>
            </div>

            <div class="settings-form-grid">
                <div class="settings-field">
                    <label for="name">{{ __('Full name') }}</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" autocomplete="name" required>
                    <x-input-error :messages="$errors->get('name')" class="settings-error" />
                </div>

                <div class="settings-field">
                    <label for="username">{{ __('Username') }}</label>
                    <input id="username" name="username" value="{{ old('username', $user->username) }}" autocomplete="username" minlength="3" maxlength="30" placeholder="{{ __('Choose a username') }}">
                    <p class="settings-help">{{ __('Letters, numbers, dashes, and underscores.') }}</p>
                    <x-input-error :messages="$errors->get('username')" class="settings-error" />
                </div>

                <div class="settings-field">
                    <label for="email">{{ __('Email address') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" required>
                    <x-input-error :messages="$errors->get('email')" class="settings-error" />
                    @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <p class="settings-help">{{ __('Your email address is not verified yet.') }} <a href="{{ route('verification.notice') }}">{{ __('Verify email') }}</a></p>
                    @endif
                </div>

                <div class="settings-field">
                    <label>{{ __('Account created') }}</label>
                    <p class="settings-readonly">{{ $user->created_at->format('F j, Y') }}</p>
                </div>
            </div>

            <div class="settings-actions">
                <button class="btn" type="submit">{{ __('Save changes') }}</button>
            </div>
        </form>
    </section>

    <section class="panel settings-panel" id="security">
        <div class="settings-section-heading">
            <span class="settings-section-icon settings-section-icon-security">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M8 10V7a4 4 0 0 1 8 0v3m-4 4v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            <span>
                <h2>{{ __('Security settings') }}</h2>
                <p>{{ __('Use a strong password that you do not use elsewhere.') }}</p>
            </span>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="settings-form" data-password-form>
            @csrf
            @method('PUT')

            <div class="settings-form-grid">
                @foreach([
                    'update_password_current_password' => ['current_password', __('Current password'), 'current-password'],
                    'update_password_password' => ['password', __('New password'), 'new-password'],
                    'update_password_password_confirmation' => ['password_confirmation', __('Confirm new password'), 'new-password'],
                ] as $id => [$name, $label, $autocomplete])
                    <div class="settings-field">
                        <label for="{{ $id }}">{{ $label }}</label>
                        <div class="settings-password-input">
                            <input id="{{ $id }}" name="{{ $name }}" type="password" autocomplete="{{ $autocomplete }}" required @if($name !== 'current_password') data-password-strength-input @endif>
                            <button type="button" class="settings-password-toggle" data-password-toggle="{{ $id }}" aria-label="{{ __('Show password') }}">{{ __('Show') }}</button>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get($name)" class="settings-error" />
                    </div>
                @endforeach
            </div>

            <div class="password-strength" aria-live="polite">
                <div class="password-strength-bars" aria-hidden="true">
                    <span></span><span></span><span></span><span></span>
                </div>
                <span data-password-strength-label>{{ __('Password strength') }}</span>
            </div>

            <div class="settings-actions">
                <button class="btn" type="submit">{{ __('Change password') }}</button>
                @if(session('status') === 'password-updated')
                    <span class="settings-inline-success">{{ __('Password updated.') }}</span>
                @endif
            </div>
        </form>
    </section>

    <section class="panel settings-panel" id="preferences">
        <div class="settings-section-heading">
            <span class="settings-section-icon settings-section-icon-preferences">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 7h9m4 0h3M4 17h3m4 0h9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    <circle cx="15" cy="7" r="2" fill="currentColor"/>
                    <circle cx="9" cy="17" r="2" fill="currentColor"/>
                </svg>
            </span>
            <span>
                <h2>{{ __('Account preferences') }}</h2>
                <p>{{ __('Personalize the way TaskFlow looks and keeps you informed.') }}</p>
            </span>
        </div>

        <form method="POST" action="{{ route('profile.preferences.update') }}" class="settings-form">
            @csrf
            @method('PATCH')

            <div class="settings-form-grid">
                <div class="settings-field">
                    <label for="theme">{{ __('Appearance') }}</label>
                    <select id="theme" name="theme">
                        <option value="light" @selected(old('theme', $user->theme) === 'light')>{{ __('Light mode') }}</option>
                        <option value="dark" @selected(old('theme', $user->theme) === 'dark')>{{ __('Dark mode') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('theme')" class="settings-error" />
                </div>

                <div class="settings-field">
                    <label for="locale">{{ __('Language') }}</label>
                    <select id="locale" name="locale">
                        <option value="en" @selected(old('locale', $user->locale) === 'en')>{{ __('English') }}</option>
                        <option value="fil" @selected(old('locale', $user->locale) === 'fil')>{{ __('Filipino') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('locale')" class="settings-error" />
                </div>

                <div class="settings-field">
                    <label for="timezone">{{ __('Time zone') }}</label>
                    <select id="timezone" name="timezone">
                        @foreach($supportedTimezones as $timezone)
                            <option value="{{ $timezone }}" @selected(old('timezone', $user->timezone) === $timezone)>{{ str_replace('_', ' ', $timezone) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('timezone')" class="settings-error" />
                </div>
            </div>

            <div class="settings-notification-options">
                <h3>{{ __('Notification preferences') }}</h3>
                @foreach([
                    'notify_upcoming_deadlines' => [__('Upcoming deadline reminders'), __('Get a heads-up for tasks due in the next 7 days.')],
                    'notify_overdue_tasks' => [__('Overdue task alerts'), __('See alerts for tasks that have passed their due date.')],
                    'notify_task_completions' => [__('Task completion updates'), __('Keep completed tasks in your notification center.')],
                ] as $name => [$label, $description])
                    <label class="settings-toggle-row">
                        <span>
                            <strong>{{ $label }}</strong>
                            <small>{{ $description }}</small>
                        </span>
                        <input type="hidden" name="{{ $name }}" value="0">
                        <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $user->{$name}))>
                    </label>
                @endforeach
                <x-input-error :messages="$errors->get('notify_upcoming_deadlines')" class="settings-error" />
                <x-input-error :messages="$errors->get('notify_overdue_tasks')" class="settings-error" />
                <x-input-error :messages="$errors->get('notify_task_completions')" class="settings-error" />
            </div>

            <div class="settings-actions">
                <button class="btn" type="submit">{{ __('Save preferences') }}</button>
            </div>
        </form>
    </section>

    <section class="panel settings-panel" id="account-security">
        <div class="settings-section-heading">
            <span class="settings-section-icon settings-section-icon-security">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="3.5" y="5" width="17" height="14" rx="2" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M7 9h10M7 13h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            <span>
                <h2>{{ __('Account security') }}</h2>
                <p>{{ __('Review recent sign-in activity and manage your sessions.') }}</p>
            </span>
        </div>

        <div class="settings-security-summary">
            <span>{{ __('Last sign in') }}</span>
            <strong>{{ $user->last_login_at?->format('F j, Y \a\t g:i A') ?? __('No sign-in information yet') }}</strong>
        </div>

        @if($databaseSessionsEnabled)
            <h3 class="settings-subheading">{{ __('Active sessions') }}</h3>
            @if($activeSessions->isEmpty())
                <p class="settings-help">{{ __('No active sessions could be found.') }}</p>
            @else
                <div class="settings-session-list">
                    @foreach($activeSessions as $activeSession)
                        <div class="settings-session-row">
                            <span class="settings-session-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <rect x="4" y="5" width="16" height="11" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                                    <path d="M9 20h6m-3-4v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <span class="settings-session-copy">
                                <strong>{{ $activeSession->is_current ? __('This device') : __('Signed-in device') }}</strong>
                                <small>{{ $activeSession->user_agent ?: __('Unknown browser') }} · {{ $activeSession->ip_address ?: __('Unknown location') }}</small>
                                <small>{{ \Illuminate\Support\Carbon::createFromTimestamp($activeSession->last_activity)->diffForHumans() }}</small>
                            </span>
                            @unless($activeSession->is_current)
                                <form method="POST" action="{{ route('profile.sessions.destroy', $activeSession->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="settings-text-button" type="submit">{{ __('Sign out') }}</button>
                                </form>
                            @endunless
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('profile.sessions.destroy-all') }}" class="settings-revoke-all" data-confirm-all-sessions>
                @csrf
                @method('DELETE')
                <div>
                    <strong>{{ __('Sign out from all devices') }}</strong>
                    <p>{{ __('This will end every session, including this one. Enter your password to continue.') }}</p>
                </div>
                <div class="settings-revoke-actions">
                    <label class="sr-only" for="logout-all-password">{{ __('Current password') }}</label>
                    <input id="logout-all-password" type="password" name="password" autocomplete="current-password" placeholder="{{ __('Current password') }}" required>
                    <button class="btn btn-danger" type="submit">{{ __('Sign out everywhere') }}</button>
                </div>
                <x-input-error :messages="$errors->logoutAllDevices->get('password')" class="settings-error" />
            </form>
        @else
            <p class="settings-help">{{ __('Session management is available when the database session driver is enabled.') }}</p>
        @endif
    </section>

    <section class="panel settings-panel settings-danger-zone" id="delete-account">
        <div class="settings-section-heading">
            <span class="settings-section-icon settings-section-icon-danger">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 7h16m-10 4v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span>
                <h2>{{ __('Delete account') }}</h2>
                <p>{{ __('Permanently remove your account and its data. This cannot be undone.') }}</p>
            </span>
        </div>

        <button class="btn btn-danger" type="button" data-open-delete-account>{{ __('Delete my account') }}</button>

        <dialog class="delete-confirmation settings-delete-dialog" data-delete-account-dialog @if($errors->userDeletion->isNotEmpty()) open @endif>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <h2>{{ __('Are you sure you want to delete your account?') }}</h2>
                <p>{{ __('Your profile and task data will be permanently deleted. Enter your password to confirm.') }}</p>
                <label for="delete-account-password">{{ __('Current password') }}</label>
                <input id="delete-account-password" type="password" name="password" autocomplete="current-password" required>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="settings-error" />
                <div class="settings-actions">
                    <button class="btn btn-light" type="button" data-close-delete-account>{{ __('Cancel') }}</button>
                    <button class="btn btn-danger" type="submit">{{ __('Delete account permanently') }}</button>
                </div>
            </form>
        </dialog>
    </section>
</div>

<script>
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            const isVisible = input.type === 'text';

            input.type = isVisible ? 'password' : 'text';
            button.textContent = isVisible ? @json(__('Show')) : @json(__('Hide'));
            button.setAttribute('aria-label', isVisible ? @json(__('Show password')) : @json(__('Hide password')));
        });
    });

    const newPassword = document.querySelector('[data-password-strength-input]');
    const strengthBars = document.querySelectorAll('.password-strength-bars span');
    const strengthLabel = document.querySelector('[data-password-strength-label]');

    if (newPassword) {
        newPassword.addEventListener('input', () => {
            const value = newPassword.value;
            const score = value.length === 0 ? 0 : [
                value.length >= 8,
                /[A-Z]/.test(value),
                /[0-9]/.test(value),
                /[^A-Za-z0-9]/.test(value),
            ].filter(Boolean).length;
            const labels = [
                @json(__('Password strength')),
                @json(__('Weak')),
                @json(__('Fair')),
                @json(__('Good')),
                @json(__('Strong')),
            ];

            strengthBars.forEach((bar, index) => {
                bar.dataset.active = index < score ? 'true' : 'false';
            });
            strengthLabel.textContent = labels[score];
        });
    }

    document.querySelector('[data-password-form]')?.addEventListener('submit', (event) => {
        if (!window.confirm(@json(__('Change your password now?')))) {
            event.preventDefault();
        }
    });

    document.querySelector('[data-confirm-all-sessions]')?.addEventListener('submit', (event) => {
        if (!window.confirm(@json(__('This will sign out all devices, including this one. Continue?')))) {
            event.preventDefault();
        }
    });

    const deleteAccountDialog = document.querySelector('[data-delete-account-dialog]');
    document.querySelector('[data-open-delete-account]')?.addEventListener('click', () => deleteAccountDialog.showModal());
    document.querySelector('[data-close-delete-account]')?.addEventListener('click', () => deleteAccountDialog.close());
</script>

@endsection
