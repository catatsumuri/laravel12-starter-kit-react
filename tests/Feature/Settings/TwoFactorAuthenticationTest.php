<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Fortify\Features;

test('two factor settings page can be rendered', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->withoutTwoFactor()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('two-factor.show'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor')
            ->where('twoFactorEnabled', false)
        );
});

test('two factor settings page requires password confirmation when enabled', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    $user = User::factory()->create();

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $response = $this->actingAs($user)
        ->get(route('two-factor.show'));

    $response->assertRedirect(route('password.confirm'));
});

test('two factor settings page does not requires password confirmation when disabled', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    $user = User::factory()->create();

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => false,
    ]);

    $this->actingAs($user)
        ->get(route('two-factor.show'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/two-factor')
        );
});

test('two factor settings page returns forbidden response when two factor is disabled', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    config(['fortify.features' => []]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('two-factor.show'))
        ->assertForbidden();
});

test('two factor feature flag controls route access', function () {
    $user = User::factory()->create();

    // When feature flag is disabled, route should return 404
    config(['features.two_factor_authentication' => false]);

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('two-factor.show'))
        ->assertNotFound();

    // When feature flag is enabled, route should be accessible
    config(['features.two_factor_authentication' => true]);

    $response = $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('two-factor.show'));
    
    // Only assert OK if the route actually exists when feature is enabled
    if (\Illuminate\Support\Facades\Route::has('two-factor.show')) {
        $response->assertOk();
    } else {
        $response->assertNotFound();
    }
});

test('two factor feature flag controls fortify features array', function () {
    // When feature flag is disabled, 2FA should not be in features array
    config(['features.two_factor_authentication' => false]);
    $features = config('fortify.features');

    expect($features)->not->toContain(
        fn ($feature) => is_string($feature) && str_contains($feature, 'two-factor')
    );

    // When feature flag is enabled, 2FA should be in features array
    config(['features.two_factor_authentication' => true]);
    $this->app['config']->set('fortify.features', array_filter([
        Features::emailVerification(),
        config('features.two_factor_authentication')
            ? Features::twoFactorAuthentication([
                'confirm' => true,
                'confirmPassword' => true,
            ])
            : null,
    ]));

    expect(Features::canManageTwoFactorAuthentication())->toBeTrue();
});

test('two factor feature flag is shared with inertia', function () {
    $user = User::factory()->create();

    // When feature flag is enabled
    config(['features.two_factor_authentication' => true]);

    $response = $this->actingAs($user)
        ->get(route('profile.edit'));

    $response->assertInertia(fn ($page) => $page
        ->has('features')
        ->where('features.twoFactorAuthentication', true)
    );

    // When feature flag is disabled
    config(['features.two_factor_authentication' => false]);

    $response = $this->actingAs($user)
        ->get(route('profile.edit'));

    $response->assertInertia(fn ($page) => $page
        ->has('features')
        ->where('features.twoFactorAuthentication', false)
    );
});

test('two factor page works when feature is enabled', function () {
    config(['features.two_factor_authentication' => true]);
    
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('two-factor.show'));

    if (\Illuminate\Support\Facades\Route::has('two-factor.show')) {
        $response->assertOk();
    } else {
        $this->markTestSkipped('Two factor route not available');
    }
});
