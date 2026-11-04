<x-guest-layout>
    <div class="mb-7">
        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-[#287a52]">{{ __('Get started') }}</p>
        <h2 class="text-3xl font-semibold tracking-tight text-[#18231d]">{{ __('Create your account') }}</h2>
        <p class="mt-2 text-sm leading-6 text-[#78847d]">{{ __('Set up your space and start making progress.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-[#34443a]">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="{{ __('Your name') }}" class="mt-2 block w-full rounded-xl border border-[#dce5de] bg-white px-4 py-3 text-sm text-[#18231d] shadow-sm transition placeholder:text-[#a0aaa3] focus:border-[#287a52] focus:outline-none focus:ring-4 focus:ring-[#287a52]/10">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-600" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-[#34443a]">{{ __('Email address') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" placeholder="{{ __('you@example.com') }}" class="mt-2 block w-full rounded-xl border border-[#dce5de] bg-white px-4 py-3 text-sm text-[#18231d] shadow-sm transition placeholder:text-[#a0aaa3] focus:border-[#287a52] focus:outline-none focus:ring-4 focus:ring-[#287a52]/10">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-[#34443a]">{{ __('Password') }}</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="{{ __('Create a password') }}" class="mt-2 block w-full rounded-xl border border-[#dce5de] bg-white px-4 py-3 text-sm text-[#18231d] shadow-sm transition placeholder:text-[#a0aaa3] focus:border-[#287a52] focus:outline-none focus:ring-4 focus:ring-[#287a52]/10">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-[#34443a]">{{ __('Confirm password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="{{ __('Enter your password again') }}" class="mt-2 block w-full rounded-xl border border-[#dce5de] bg-white px-4 py-3 text-sm text-[#18231d] shadow-sm transition placeholder:text-[#a0aaa3] focus:border-[#287a52] focus:outline-none focus:ring-4 focus:ring-[#287a52]/10">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-[#287a52] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#216643] focus:outline-none focus:ring-4 focus:ring-[#287a52]/20 active:bg-[#195535]">
            {{ __('Create account') }}
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-[#78847d]">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="font-semibold text-[#287a52] transition hover:text-[#1d5e3d] focus:outline-none focus:ring-2 focus:ring-[#287a52] focus:ring-offset-2">{{ __('Sign in') }}</a>
    </p>
</x-guest-layout>
