<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('app name is shared with inertia pages', function () {
    config(['app.name' => 'My Custom App']);

    $response = $this->get(route('login'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->where('name', 'My Custom App')
    );
});

test('app name defaults to Laravel when not configured', function () {
    config(['app.name' => 'Laravel']);

    $response = $this->get(route('login'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->where('name', 'Laravel')
    );
});

test('app name is accessible on authenticated pages', function () {
    config(['app.name' => 'Test Application']);

    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->where('name', 'Test Application')
    );
});
