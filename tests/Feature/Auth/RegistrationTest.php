<?php

use Illuminate\Support\Facades\Config;

test('registration screen can be rendered', function () {
    Config::set('user.registration_enabled', true);

    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register', function () {
    Config::set('user.registration_enabled', true);

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response
        ->assertRedirect(route('dashboard', absolute: false))
        ->assertSessionHas('success', 'Login successful!');
});

test('register screen displays password toggle when enabled', function () {
    Config::set('user.registration_enabled', true);
    Config::set('ui.show_password_toggle', true);

    $response = $this->get(route('register'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('auth/register')
        ->where('showPasswordToggle', true)
    );
});

test('register screen hides password toggle when disabled', function () {
    Config::set('user.registration_enabled', true);
    Config::set('ui.show_password_toggle', false);

    $response = $this->get(route('register'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('auth/register')
        ->where('showPasswordToggle', false)
    );
});
