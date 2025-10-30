<?php

use App\Models\Setting;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('admin settings page can be rendered', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.settings.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/settings/index')
            ->has('appName')
            ->has('appUrl')
            ->has('appDebug')
            ->has('appLocale')
            ->has('appFallbackLocale')
            ->has('awsAccessKeyId')
            ->has('awsSecretAccessKey')
            ->has('awsDefaultRegion')
            ->has('awsBucket')
            ->has('awsUsePathStyleEndpoint')
        );
});

test('admin settings page requires authentication', function () {
    $response = $this->get(route('admin.settings.index'));

    $response->assertRedirect(route('login'));
});

test('admin settings can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Updated App Name',
            'app_url' => 'https://example.com',
            'app_debug' => false,
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
            'aws_access_key_id' => 'test-key-id',
            'aws_secret_access_key' => 'test-secret-key',
            'aws_default_region' => 'us-west-2',
            'aws_bucket' => 'test-bucket',
            'aws_use_path_style_endpoint' => true,
        ])
        ->assertRedirect()
        ->assertSessionHas('success', 'Settings updated successfully.');

    expect(Setting::value('app.name'))->toBe('Updated App Name');
    expect(Setting::value('app.url'))->toBe('https://example.com');
    expect(Setting::value('app.debug'))->toBe('0');
    expect(Setting::value('app.locale'))->toBe('ja');
    expect(Setting::value('app.fallback_locale'))->toBe('en');
    expect(Setting::value('aws.access_key_id'))->toBe('test-key-id');
    expect(Setting::value('aws.secret_access_key'))->toBe('test-secret-key');
    expect(Setting::value('aws.default_region'))->toBe('us-west-2');
    expect(Setting::value('aws.bucket'))->toBe('test-bucket');
    expect(Setting::value('aws.use_path_style_endpoint'))->toBe('1');
});

test('admin settings update requires authentication', function () {
    $response = $this->patch(route('admin.settings.update'), [
        'app_name' => 'Updated App Name',
        'app_url' => 'https://example.com',
        'app_debug' => false,
        'app_locale' => 'ja',
        'app_fallback_locale' => 'en',
    ]);

    $response->assertRedirect(route('login'));
});

test('admin settings update validates app_name is required', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('admin.settings.update'), [
            'app_name' => '',
            'app_url' => 'https://example.com',
            'app_debug' => false,
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
        ])
        ->assertSessionHasErrors('app_name');
});

test('admin settings update validates app_url is required', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => '',
            'app_debug' => false,
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
        ])
        ->assertSessionHasErrors('app_url');
});

test('admin settings update validates app_url is valid url', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'not-a-valid-url',
            'app_debug' => false,
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
        ])
        ->assertSessionHasErrors('app_url');
});

test('admin settings update validates app_locale is valid', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_debug' => false,
            'app_locale' => 'invalid',
            'app_fallback_locale' => 'en',
        ])
        ->assertSessionHasErrors('app_locale');
});

test('admin settings update validates app_fallback_locale is valid', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_debug' => false,
            'app_locale' => 'ja',
            'app_fallback_locale' => 'invalid',
        ])
        ->assertSessionHasErrors('app_fallback_locale');
});

test('admin settings update enables debug mode', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_debug' => true,
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Setting::value('app.debug'))->toBe('1');
});

test('admin settings update does not update masked aws secret', function () {
    $user = User::factory()->create();

    // Set an initial secret
    Setting::updateValue('aws.secret_access_key', 'original-secret');

    $this->actingAs($user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_debug' => false,
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
            'aws_secret_access_key' => '********',
        ])
        ->assertRedirect();

    // Secret should remain unchanged
    expect(Setting::value('aws.secret_access_key'))->toBe('original-secret');
});

test('admin settings update can change aws secret', function () {
    $user = User::factory()->create();

    // Set an initial secret
    Setting::updateValue('aws.secret_access_key', 'original-secret');

    $this->actingAs($user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_debug' => false,
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
            'aws_secret_access_key' => 'new-secret',
        ])
        ->assertRedirect();

    // Secret should be updated
    expect(Setting::value('aws.secret_access_key'))->toBe('new-secret');
});

test('admin settings page displays database overrides', function () {
    $user = User::factory()->create();

    // Set some database overrides
    Setting::updateValue('app.name', 'Database App Name');
    Setting::updateValue('app.url', 'https://db-override.com');

    $this->actingAs($user)
        ->get(route('admin.settings.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/settings/index')
            ->where('appName', 'Database App Name')
            ->where('appUrl', 'https://db-override.com')
        );
});
