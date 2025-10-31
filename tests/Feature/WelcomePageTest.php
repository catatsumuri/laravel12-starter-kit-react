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

test('authenticated users can access dashboard', function () {
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('dashboard'));
});
