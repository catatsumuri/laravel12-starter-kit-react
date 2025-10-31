<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication
    |--------------------------------------------------------------------------
    |
    | This value determines whether the two-factor authentication feature is
    | enabled for your application. When disabled, users will not be able to
    | enable 2FA and the settings page will be hidden from the navigation.
    |
    */

    'two_factor_authentication' => env('FEATURE_TWO_FACTOR', true),

    /*
    |--------------------------------------------------------------------------
    | Default Appearance
    |--------------------------------------------------------------------------
    |
    | This value determines the default appearance (theme) for your application
    | when a user has not set a preference. Valid values are: 'light', 'dark',
    | or 'system'. The 'system' option respects the user's OS preference.
    |
    */

    'default_appearance' => env('DEFAULT_APPEARANCE', 'system'),

    /*
    |--------------------------------------------------------------------------
    | Appearance Settings
    |--------------------------------------------------------------------------
    |
    | This value determines whether users can customize their appearance
    | preferences. When disabled, all users will use the default appearance
    | and the settings page will be hidden from the navigation.
    |
    */

    'appearance_settings' => env('FEATURE_APPEARANCE_SETTINGS', true),

];
