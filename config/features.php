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

];
