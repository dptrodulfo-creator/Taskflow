<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ApplyUserPreferences
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $originalLocale = App::getLocale();
        $originalTimezone = config('app.timezone');
        $originalPhpTimezone = date_default_timezone_get();

        App::setLocale($user->locale ?? $originalLocale);
        config(['app.timezone' => $user->timezone ?? $originalTimezone]);
        date_default_timezone_set($user->timezone ?? $originalPhpTimezone);

        try {
            return $next($request);
        } finally {
            App::setLocale($originalLocale);
            config(['app.timezone' => $originalTimezone]);
            date_default_timezone_set($originalPhpTimezone);
        }
    }
}
