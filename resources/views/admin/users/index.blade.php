@extends('layouts.admin')

@section('title', $isAdminPage ? 'Admin accounts' : 'Users')

@section('content')
    <header class="mb-7 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Account management</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $isAdminPage ? 'Admin accounts' : 'Users' }}</h1>
            <p class="mt-2 text-slate-600">{{ $isAdminPage ? 'Manage who has administrator privileges.' : 'Review registered accounts and grant administrator access.' }}</p>
        </div>
        <a href="{{ route($isAdminPage ? 'admin.users.index' : 'admin.admins.index') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            {{ $isAdminPage ? 'All users' : 'Admin accounts' }}
        </a>
    </header>

    <form method="GET" class="mb-5 flex flex-wrap gap-3">
        <label class="sr-only" for="search">Search accounts</label>
        <input id="search" type="search" name="search" value="{{ request('search') }}" placeholder="Search name or email" class="min-w-0 flex-1 rounded-xl border-slate-300 bg-white shadow-sm focus:border-emerald-600 focus:ring-emerald-600 sm:max-w-md">
        <button class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Search</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-5 py-3">Account</th><th class="px-5 py-3">Tasks</th><th class="px-5 py-3">Access</th><th class="px-5 py-3">Joined</th><th class="px-5 py-3 text-right">Manage access</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                <p class="mt-1 text-slate-500">{{ $user->email }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ number_format($user->tasks_count) }}</td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $user->is_admin ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $user->is_admin ? 'Administrator' : 'User' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                @if (! $user->is(auth()->user()) || ! $user->is_admin)
                                    <form method="POST" action="{{ route('admin.users.role.update', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button name="is_admin" value="{{ $user->is_admin ? '0' : '1' }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            {{ $user->is_admin ? 'Remove admin' : 'Make admin' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Current account</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">No accounts matched your search.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
