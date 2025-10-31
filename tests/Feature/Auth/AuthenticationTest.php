<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
});

test('regular users can authenticate and are redirected to dashboard', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $user->assignRole('user');

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response
        ->assertRedirect(route('dashboard', absolute: false))
        ->assertSessionHas('success', 'Login successful!');
});

test('admin users can authenticate and are redirected to admin dashboard', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    // Create admin role if it doesn't exist
    $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
    $user->assignRole($adminRole);

    // Verify role is assigned
    expect($user->fresh()->hasRole('admin'))->toBeTrue();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->create();

    $user->forceFill([
        'two_factor_secret' => encrypt('test-secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $response->assertSessionHas('login.id', $user->id);
    $this->assertGuest();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $this->assertGuest();
    $response
        ->assertRedirect(route('home'))
        ->assertSessionHas('success', 'You have been logged out successfully.');
});

test('users are rate limited', function () {
    $user = User::factory()->create();

    RateLimiter::increment(md5('login'.implode('|', [$user->email, '127.0.0.1'])), amount: 5);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertTooManyRequests();
});

test('login screen displays password toggle when enabled', function () {
    config(['ui.show_password_toggle' => true]);

    $response = $this->get(route('login'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('auth/login')
        ->where('showPasswordToggle', true)
    );
});

test('login screen hides password toggle when disabled', function () {
    config(['ui.show_password_toggle' => false]);

    $response = $this->get(route('login'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('auth/login')
        ->where('showPasswordToggle', false)
    );
});

test('last login timestamp is updated on successful login', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'last_login_at' => null,
    ]);

    expect($user->last_login_at)->toBeNull();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $user->refresh();

    expect($user->last_login_at)->not->toBeNull();
    expect($user->last_login_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

test('last login timestamp is not updated on failed login', function () {
    $user = User::factory()->create([
        'last_login_at' => null,
    ]);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $user->refresh();

    expect($user->last_login_at)->toBeNull();
});
