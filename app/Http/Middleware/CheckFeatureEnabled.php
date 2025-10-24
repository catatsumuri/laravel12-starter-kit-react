<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $configKey = match ($feature) {
            'registration' => 'user.registration_enabled',
            'account-deletion' => 'user.account_deletion_enabled',
            default => null,
        };

        if ($configKey && ! config($configKey)) {
            abort(403, 'This feature is currently disabled.');
        }

        return $next($request);
    }
}
