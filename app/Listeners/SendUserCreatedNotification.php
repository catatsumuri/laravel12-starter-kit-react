<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;

class SendUserCreatedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        // Notify user ID 1 when a new user is registered
        $adminUser = User::find(1);

        if ($adminUser && $event->user->id !== 1) {
            // Method 1: Using Notification facade
            Notification::send($adminUser, new UserCreatedNotification($event->user));

            // Method 2: Using notify() on the user model (alternative)
            // $adminUser->notify(new UserCreatedNotification($event->user));
        }
    }
}
