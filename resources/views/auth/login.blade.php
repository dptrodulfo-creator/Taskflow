<x-guest-layout>
    <x-auth-session-status class="mb-6 rounded-xl bg-[#edf6ef] px-4 py-3 text-sm text-[#287a52]" :status="session('status')" />

    <div class="mb-8">
        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-[#287a52]">{{ __('Welcome back') }}</p>
        <h2 class="text-3xl font-semibold tracking-tight text-[#18231d]">{{ __('Sign in to your account') }}</h2>
        <p class="mt-2 text-sm leading-6 text-[#78847d]">{{ __('Pick up where you left off.') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-[#34443a]">{{ __('Email address') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="{{ __('you@example.com') }}" class="mt-2 block w-full rounded-xl border border-[#dce5de] bg-white px-4 py-3 text-sm text-[#18231d] shadow-sm transition placeholder:text-[#a0aaa3] focus:border-[#287a52] focus:outline-none focus:ring-4 focus:ring-[#287a52]/10">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <label for="password" class="block text-sm font-medium text-[#34443a]">{{ __('Password') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="rounded text-sm font-medium text-[#287a52] transition hover:text-[#1d5e3d] focus:outline-none focus:ring-2 focus:ring-[#287a52] focus:ring-offset-2">{{ __('Forgot password?') }}</a>
                @endif
            </div>
            <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="{{ __('Enter your password') }}" class="mt-2 block w-full rounded-xl border border-[#dce5de] bg-white px-4 py-3 text-sm text-[#18231d] shadow-sm transition placeholder:text-[#a0aaa3] focus:border-[#287a52] focus:outline-none focus:ring-4 focus:ring-[#287a52]/10">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
        </div>

        <label for="remember_me" class="flex w-fit cursor-pointer items-center gap-2.5 text-sm text-[#59675e]">
            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-[#cbd6ce] text-[#287a52] shadow-sm focus:ring-[#287a52]">
            <span>{{ __('Remember me') }}</span>
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-[#287a52] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#216643] focus:outline-none focus:ring-4 focus:ring-[#287a52]/20 active:bg-[#195535]">
            {{ __('Sign in') }}
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-[#78847d]">
        {{ __('New to TaskFlow?') }}
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="font-semibold text-[#287a52] transition hover:text-[#1d5e3d] focus:outline-none focus:ring-2 focus:ring-[#287a52] focus:ring-offset-2">{{ __('Create an account') }}</a>
        @endif
    </p>

    <p class="mt-4 text-center text-sm text-[#78847d]">
        <a href="{{ route('admin.login') }}" class="font-medium text-[#287a52] transition hover:text-[#1d5e3d]">{{ __('Administrator sign in') }}</a>
    </p>
</x-guest-layout>
