<x-guest-layout>
    <div class="mb-8">
        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-[#287a52]">{{ __('TaskFlow administration') }}</p>
        <h2 class="text-3xl font-semibold tracking-tight text-[#18231d]">{{ __('Administrator sign in') }}</h2>
        <p class="mt-2 text-sm leading-6 text-[#78847d]">{{ __('Use your administrator account to continue.') }}</p>
    </div>

    <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-[#34443a]">{{ __('Email address') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-2 block w-full rounded-xl border border-[#dce5de] bg-white px-4 py-3 text-sm text-[#18231d] shadow-sm focus:border-[#287a52] focus:outline-none focus:ring-4 focus:ring-[#287a52]/10">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-[#34443a]">{{ __('Password') }}</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" class="mt-2 block w-full rounded-xl border border-[#dce5de] bg-white px-4 py-3 text-sm text-[#18231d] shadow-sm focus:border-[#287a52] focus:outline-none focus:ring-4 focus:ring-[#287a52]/10">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
        </div>

        <label for="remember_me" class="flex w-fit cursor-pointer items-center gap-2.5 text-sm text-[#59675e]">
            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-[#cbd6ce] text-[#287a52] focus:ring-[#287a52]">
            <span>{{ __('Remember me') }}</span>
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-[#287a52] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#216643] focus:outline-none focus:ring-4 focus:ring-[#287a52]/20">
            {{ __('Sign in to admin') }}
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-[#78847d]">
        {{ __('Not an administrator?') }}
        <a href="{{ route('login') }}" class="font-semibold text-[#287a52] hover:text-[#1d5e3d]">{{ __('User sign in') }}</a>
    </p>
</x-guest-layout>
