<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | TaskFlow</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="flex w-full flex-col border-b border-slate-200 bg-white p-5 lg:min-h-screen lg:w-64 lg:border-b-0 lg:border-r">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-xl font-bold tracking-tight text-slate-900">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-700 text-white">T</span>
                <span>TaskFlow <span class="text-emerald-700">Admin</span></span>
            </a>

            <nav class="mt-8 flex flex-wrap gap-2 lg:flex-col" aria-label="Admin navigation">
                @foreach ([
                    ['admin.dashboard', 'Overview', 'admin.dashboard'],
                    ['admin.users.index', 'Users', 'admin.users.*'],
                    ['admin.admins.index', 'Admin accounts', 'admin.admins.*'],
                    ['admin.tasks.index', 'Task monitoring', 'admin.tasks.*'],
                    ['admin.reports.index', 'Reports', 'admin.reports.*'],
                    ['admin.activities.index', 'Activity logs', 'admin.activities.*'],
                ] as [$routeName, $label, $activeRoute])
                    <a href="{{ route($routeName) }}"
                        @class([
                            'rounded-xl px-4 py-3 text-sm font-semibold transition',
                            'bg-emerald-50 text-emerald-800' => request()->routeIs($activeRoute),
                            'text-slate-600 hover:bg-slate-50 hover:text-slate-900' => ! request()->routeIs($activeRoute),
                        ])>
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-8 border-t border-slate-200 pt-5 lg:mt-auto">
                <p class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                <form method="POST" action="{{ route('admin.logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Sign out
                    </button>
                </form>
            </div>
        </aside>

        <main class="min-w-0 flex-1 p-4 sm:p-7 lg:p-10">
            <div class="mx-auto max-w-7xl">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
