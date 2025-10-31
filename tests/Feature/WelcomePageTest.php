<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('welcome page can be rendered when enabled', function () {
    config(['ui.disable_welcome_page' => false]);

    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('welcome'));
});

test('welcome page redirects to login when disabled', function () {
    config(['ui.disable_welcome_page' => true]);

    $response = $this->get(route('home'));

    $response->assertRedirect(route('login'));
});

test('welcome page displays register link when registration is enabled', function () {
    config(['user.registration_enabled' => true]);

    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->has('features')
        ->where('features.registration', true)
    );
});

test('welcome page hides register link when registration is disabled', function () {
    config(['user.registration_enabled' => false]);

    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->has('features')
        ->where('features.registration', false)
    );
});

test('welcome page always displays login link regardless of registration setting', function () {
    config(['user.registration_enabled' => false]);

    $response = $this->get(route('home'));

    $response->assertStatus(200);
});

test('welcome page shares account deletion feature flag', function () {
    config(['user.account_deletion_enabled' => false]);

    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->has('features')
        ->where('features.accountDeletion', false)
    );
});

test('welcome page displays dashboard link for authenticated users', function () {
    $user = \App\Models\User::factory()->create();

    config(['user.registration_enabled' => false]);

    $response = $this
        ->actingAs($user)
        ->get(route('home'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->where('auth.user.id', $user->id)
    );
});

test('authenticated users can access dashboard', function () {
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('dashboard'));
});
