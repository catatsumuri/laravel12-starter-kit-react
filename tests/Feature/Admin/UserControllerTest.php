<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
});

it('displays users index', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);
    User::factory()->count(3)->create();

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/users/index')
        ->has('users.data', 4)
    );
});

it('displays create form', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);

    $response = $this->actingAs($admin)->get('/admin/users/create');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('admin/users/create'));
});

it('creates user with valid data', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    $response = $this->actingAs($admin)->post('/admin/users', $userData);

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('success', 'User created successfully.');
    $this->assertDatabaseHas('users', ['name' => 'Test User', 'email' => 'test@example.com']);
});

it('fails to create user with invalid data', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);

    $response = $this->actingAs($admin)->post('/admin/users', []);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
});

it('fails to create user with duplicate email', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->actingAs($admin)->post('/admin/users', [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('displays user details', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->get("/admin/users/{$user->id}");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/users/show')
        ->has('user')
        ->where('user.id', $user->id)
    );
});

it('displays edit form', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->get("/admin/users/{$user->id}/edit");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/users/edit')
        ->has('user')
        ->where('user.id', $user->id)
    );
});

it('updates user with valid data', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);
    $user = User::factory()->create();
    $updateData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ];

    $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", $updateData);

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('success', 'User updated successfully.');
    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
});

it('updates user password when provided', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);
    $user = User::factory()->create();
    $updateData = [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'newpassword123',
    ];

    $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", $updateData);

    $response->assertRedirect('/admin/users');
    $user->refresh();
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
});

it('fails to update user with invalid data', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", [
        'name' => '',
        'email' => 'invalid-email',
    ]);

    $response->assertSessionHasErrors(['name', 'email']);
});

it('fails to update user with duplicate email', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);

    $response = $this->actingAs($admin)->put("/admin/users/{$user1->id}", [
        'name' => $user1->name,
        'email' => 'user2@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('deletes user', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->delete("/admin/users/{$user->id}");

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('success', 'User deleted successfully.');
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('prevents self deletion', function () {
    $admin = User::factory()->create();
    $admin->syncRoles(['admin']);

    $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

    $response->assertRedirect();
    $response->assertSessionHas('error', 'You cannot delete your own account from here.');
    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

it('filters users by search', function () {
    $admin = User::factory()->create(['name' => 'Admin User']);
    $admin->syncRoles(['admin']);
    User::factory()->create(['name' => 'John Doe']);
    User::factory()->create(['name' => 'Jane Smith']);

    $response = $this->actingAs($admin)->get('/admin/users?search=John');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('users.data', 1)
        ->where('filters.search', 'John')
    );
});
