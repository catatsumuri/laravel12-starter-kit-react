<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): RedirectResponse
    {
        $user = $request->user();

        // Check if user has admin role
        if ($user && $user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        // Default redirect for regular users
        return redirect()->intended(route('dashboard', absolute: false));
    }
}
