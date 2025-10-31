<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('flash messages are shared with inertia responses', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->withSession([
            'success' => 'Operation completed successfully!',
            'error' => 'An error occurred.',
            'info' => 'Information message.',
            'warning' => 'Warning message.',
            'status' => 'Status message.',
        ])
        ->get(route('dashboard'));

    $response->assertInertia(fn (Assert $page) => $page
        ->has('flash')
        ->where('flash.success', 'Operation completed successfully!')
        ->where('flash.error', 'An error occurred.')
        ->where('flash.info', 'Information message.')
        ->where('flash.warning', 'Warning message.')
        ->where('flash.status', 'Status message.')
    );
});

test('flash property is null when no flash messages are present', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response->assertInertia(fn (Assert $page) => $page
        ->where('flash', null)
    );
});

test('flash property filters out null values', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->withSession([
            'success' => 'Success message',
        ])
        ->get(route('dashboard'));

    $response->assertInertia(fn (Assert $page) => $page
        ->has('flash')
        ->where('flash.success', 'Success message')
        ->missing('flash.error')
        ->missing('flash.info')
        ->missing('flash.warning')
        ->missing('flash.status')
    );
});

test('multiple flash message types can be set simultaneously', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->withSession([
            'success' => 'Success!',
            'warning' => 'Be careful!',
        ])
        ->get(route('dashboard'));

    $response->assertInertia(fn (Assert $page) => $page
        ->has('flash')
        ->where('flash.success', 'Success!')
        ->where('flash.warning', 'Be careful!')
        ->missing('flash.error')
        ->missing('flash.info')
        ->missing('flash.status')
    );
});
