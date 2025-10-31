<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows admin users to access admin routes', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertOk();
});

it('denies regular users access to admin routes', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $response = $this->actingAs($user)->get('/admin/users');

    $response->assertForbidden();
});

it('denies unauthenticated users access to admin routes', function () {
    $response = $this->get('/admin/users');

    $response->assertRedirect('/login');
});

it('shows admin navigation only to admin users', function () {
    $admin = User::factory()->withoutTwoFactor()->create();
    $admin->syncRoles(['admin']); // Use syncRoles to replace default role

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('auth.user.roles')
        ->where('auth.user.roles.0.name', 'admin')
    );
});

it('hides admin navigation from regular users', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('auth.user.roles.0.name', 'user')
    );
});

it('assigns default user role to new users', function () {
    $user = User::factory()->create();

    expect($user->hasRole('user'))->toBeTrue();
    expect($user->hasRole('admin'))->toBeFalse();
});

it('displays user roles in admin users index', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);

    $regularUser = User::factory()->create();
    $regularUser->assignRole('user');

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/users/index')
        ->has('users.data.0.roles')
        ->has('users.data.1.roles')
    );
});
