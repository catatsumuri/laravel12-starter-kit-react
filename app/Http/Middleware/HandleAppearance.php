<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HandleAppearance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $defaultAppearance = config('features.default_appearance', 'system');

        // If appearance settings are disabled, always use default and ignore user preferences
        if (! config('features.appearance_settings', true)) {
            View::share('appearance', $defaultAppearance);
        } else {
            View::share('appearance', $request->cookie('appearance') ?? $defaultAppearance);
        }

        return $next($request);
    }
}
