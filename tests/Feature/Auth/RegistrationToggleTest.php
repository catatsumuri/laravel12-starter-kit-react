<?php

use Illuminate\Support\Facades\Config;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen can be rendered when feature is enabled', function () {
    Config::set('user.registration_enabled', true);

    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('registration screen returns 403 when feature is disabled', function () {
    Config::set('user.registration_enabled', false);

    $response = $this->get(route('register'));

    $response->assertStatus(403);
});

test('users can register when feature is enabled', function () {
    Config::set('user.registration_enabled', true);

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users cannot register when feature is disabled', function () {
    Config::set('user.registration_enabled', false);

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(403);
    $this->assertGuest();
});

test('registration feature flag is shared with frontend', function () {
    Config::set('user.registration_enabled', false);

    $response = $this->get(route('login'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->has('features')
        ->where('features.registration', false)
    );
});
