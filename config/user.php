<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Registration
    |--------------------------------------------------------------------------
    |
    | This value determines whether new users can register for your application.
    | When disabled, the registration routes will return a 403 error and the
    | registration page will redirect to the login page.
    |
    */

    'registration_enabled' => env('USER_REGISTRATION_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Account Deletion
    |--------------------------------------------------------------------------
    |
    | This value determines whether users can delete their own accounts.
    | When disabled, the account deletion feature will be hidden from the
    | settings page and deletion requests will return a 403 error.
    |
    */

    'account_deletion_enabled' => env('USER_ACCOUNT_DELETION_ENABLED', true),

];
