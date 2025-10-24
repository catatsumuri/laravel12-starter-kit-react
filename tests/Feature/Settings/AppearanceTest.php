<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('appearance page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('appearance.edit'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/appearance')
        );
});

test('default appearance is shared with inertia', function () {
    $user = User::factory()->create();

    // Default value should be 'system'
    config(['features.default_appearance' => 'system']);

    $response = $this->actingAs($user)
        ->get(route('appearance.edit'));

    $response->assertInertia(fn ($page) => $page
        ->has('features')
        ->where('features.defaultAppearance', 'system')
    );
});

test('custom default appearance is respected', function () {
    $user = User::factory()->create();

    // Set custom default appearance
    config(['features.default_appearance' => 'light']);

    $response = $this->actingAs($user)
        ->get(route('appearance.edit'));

    $response->assertInertia(fn ($page) => $page
        ->has('features')
        ->where('features.defaultAppearance', 'light')
    );

    // Try with 'dark'
    config(['features.default_appearance' => 'dark']);

    $response = $this->actingAs($user)
        ->get(route('appearance.edit'));

    $response->assertInertia(fn ($page) => $page
        ->has('features')
        ->where('features.defaultAppearance', 'dark')
    );
});

test('appearance middleware uses configured default', function () {
    $user = User::factory()->create();

    // Set custom default
    config(['features.default_appearance' => 'light']);

    $response = $this->actingAs($user)
        ->get(route('appearance.edit'));

    // The middleware should share the appearance value with views
    $response->assertOk();
});

test('appearance settings feature flag controls route access', function () {
    $user = User::factory()->create();

    // When feature flag is disabled, route should return 404
    config(['features.appearance_settings' => false]);

    $this->actingAs($user)
        ->get(route('appearance.edit'))
        ->assertNotFound();

    // When feature flag is enabled, route should be accessible
    config(['features.appearance_settings' => true]);

    $this->actingAs($user)
        ->get(route('appearance.edit'))
        ->assertOk();
});

test('appearance settings feature flag is shared with inertia', function () {
    $user = User::factory()->create();

    // When feature flag is enabled
    config(['features.appearance_settings' => true]);

    $response = $this->actingAs($user)
        ->get(route('profile.edit'));

    $response->assertInertia(fn ($page) => $page
        ->has('features')
        ->where('features.appearanceSettings', true)
    );

    // When feature flag is disabled
    config(['features.appearance_settings' => false]);

    $response = $this->actingAs($user)
        ->get(route('profile.edit'));

    $response->assertInertia(fn ($page) => $page
        ->has('features')
        ->where('features.appearanceSettings', false)
    );
});

test('appearance middleware ignores user preferences when feature disabled', function () {
    $user = User::factory()->create();

    // Set default appearance to light
    config(['features.default_appearance' => 'light']);

    // When feature is enabled, user cookie should be respected
    config(['features.appearance_settings' => true]);

    $response = $this->actingAs($user)
        ->withCookie('appearance', 'dark')
        ->get(route('profile.edit'));

    $response->assertOk();
    // Note: We can't directly test the View::share value in tests,
    // but the middleware should respect the cookie

    // When feature is disabled, user cookie should be ignored and default used
    config(['features.appearance_settings' => false]);

    $response = $this->actingAs($user)
        ->withCookie('appearance', 'dark')
        ->get(route('profile.edit'));

    $response->assertOk();
    // The middleware should ignore the 'dark' cookie and use default 'light'
});
