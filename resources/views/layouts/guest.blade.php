<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>TaskFlow</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#18231d] antialiased">
        <main class="min-h-screen bg-[#f3f6f3] p-3 sm:p-6 lg:p-8">
            <div class="mx-auto grid min-h-[calc(100vh-1.5rem)] max-w-6xl overflow-hidden rounded-3xl bg-white shadow-xl shadow-[#193b2b]/10 sm:min-h-[calc(100vh-3rem)] lg:min-h-[calc(100vh-4rem)] lg:grid-cols-2">
                <aside class="relative hidden overflow-hidden bg-[#173b2b] px-10 py-12 text-white lg:flex lg:flex-col lg:justify-between xl:px-14 xl:py-14">
                    <div class="absolute -right-28 -top-24 h-80 w-80 rounded-full border border-white/10"></div>
                    <div class="absolute -right-12 -top-8 h-48 w-48 rounded-full border border-white/10"></div>

                    <a href="/" class="relative z-10 inline-flex w-fit items-center gap-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/80 focus:ring-offset-4 focus:ring-offset-[#173b2b]" aria-label="TaskFlow home">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#b8e3c4] text-[#173b2b]">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m5 12.5 4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span class="text-xl font-bold tracking-tight">TaskFlow</span>
                    </a>

                    <div class="relative z-10 max-w-md py-12">
                        <p class="mb-5 text-xs font-semibold uppercase tracking-[0.2em] text-[#b8e3c4]">{{ __('A little more focus, every day') }}</p>
                        <h1 class="text-4xl font-semibold leading-tight tracking-tight xl:text-5xl">{{ __('Make space for your best work.') }}</h1>
                        <p class="mt-5 max-w-sm text-base leading-7 text-white/70">{{ __('Bring your tasks together, see what matters, and keep moving forward at your own pace.') }}</p>

                        <div class="mt-10 flex items-center gap-3 border-t border-white/15 pt-6">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-[#b8e3c4]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m5 12.5 4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <p class="text-sm text-white/80">{{ __('A clearer plan. A calmer day.') }}</p>
                        </div>
                    </div>

                    <p class="relative z-10 text-xs text-white/50">{{ __('Plan clearly. Move steadily. Finish well.') }}</p>
                </aside>

                <section class="flex flex-col justify-center px-6 py-9 sm:px-12 sm:py-12 lg:px-14 xl:px-20">
                    <a href="/" class="mb-10 inline-flex w-fit items-center gap-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#287a52] focus:ring-offset-4 lg:hidden" aria-label="TaskFlow home">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#173b2b] text-white">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m5 12.5 4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span class="text-lg font-bold tracking-tight">TaskFlow</span>
                    </a>

                    <div class="mx-auto w-full max-w-md">
                        {{ $slot }}
                    </div>

                    <p class="mt-10 text-center text-xs text-[#78847d]">{{ __('Your next productive day starts here.') }}</p>
                </section>
            </div>
        </main>
    </body>
</html>
