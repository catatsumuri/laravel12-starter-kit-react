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
        // Get fresh user instance from database with roles
        $userId = auth()->id();
        $user = $userId ? \App\Models\User::with('roles')->find($userId) : null;

        // Check if user has admin role
        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();

        if ($isAdmin) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        // Default redirect for regular users
        return redirect()->intended(route('dashboard', absolute: false));
    }
}
