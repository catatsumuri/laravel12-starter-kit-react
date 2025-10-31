<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user can delete their account when feature is enabled', function () {
    config(['user.account_deletion_enabled' => true]);

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    expect($user->fresh()->trashed())->toBeTrue();
});

test('user cannot delete their account when feature is disabled', function () {
    config(['user.account_deletion_enabled' => false]);

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

    $response->assertStatus(403);

    expect($user->fresh())->not->toBeNull();
    $this->assertAuthenticated();
});

test('account deletion feature flag is shared with frontend', function () {
    config(['user.account_deletion_enabled' => false]);

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('profile.edit'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->has('features')
        ->where('features.accountDeletion', false)
    );
});
