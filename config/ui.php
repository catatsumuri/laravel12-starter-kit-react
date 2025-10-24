<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Show Password Toggle
    |--------------------------------------------------------------------------
    |
    | This value determines whether the password visibility toggle button
    | is displayed on login and registration forms. When enabled, users
    | can click an icon to show or hide their password as they type.
    |
    */

    'show_password_toggle' => env('SHOW_PASSWORD_TOGGLE', true),

    /*
    |--------------------------------------------------------------------------
    | Disable Welcome Page
    |--------------------------------------------------------------------------
    |
    | When this value is set to true, the welcome page will be disabled and
    | guest users will be redirected to the login page. Authenticated users
    | will continue to be redirected to the dashboard.
    |
    */

    'disable_welcome_page' => env('DISABLE_WELCOME_PAGE', false),

];
