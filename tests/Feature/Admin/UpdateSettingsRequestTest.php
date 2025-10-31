<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');
});

test('update settings request validates app_name is required', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => '',
            'app_url' => 'https://example.com',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
        ])
        ->assertSessionHasErrors('app_name');
});

test('update settings request validates app_name max length', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => str_repeat('a', 256),
            'app_url' => 'https://example.com',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
        ])
        ->assertSessionHasErrors('app_name');
});

test('update settings request validates app_url is required', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => '',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
        ])
        ->assertSessionHasErrors('app_url');
});

test('update settings request validates app_url is valid url', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'not-a-valid-url',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
        ])
        ->assertSessionHasErrors('app_url');
});

test('update settings request validates app_url max length', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://'.str_repeat('a', 256).'.com',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
        ])
        ->assertSessionHasErrors('app_url');
});

test('update settings request validates app_locale is required', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_locale' => '',
            'app_fallback_locale' => 'en',
        ])
        ->assertSessionHasErrors('app_locale');
});

test('update settings request validates app_locale is valid', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_locale' => 'fr',
            'app_fallback_locale' => 'en',
        ])
        ->assertSessionHasErrors('app_locale');
});

test('update settings request validates app_fallback_locale is required', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_locale' => 'ja',
            'app_fallback_locale' => '',
        ])
        ->assertSessionHasErrors('app_fallback_locale');
});

test('update settings request validates app_fallback_locale is valid', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'fr',
        ])
        ->assertSessionHasErrors('app_fallback_locale');
});

test('update settings request validates aws_access_key_id max length', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
            'aws_access_key_id' => str_repeat('a', 256),
        ])
        ->assertSessionHasErrors('aws_access_key_id');
});

test('update settings request validates aws_secret_access_key max length', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
            'aws_secret_access_key' => str_repeat('a', 256),
        ])
        ->assertSessionHasErrors('aws_secret_access_key');
});

test('update settings request validates aws_default_region max length', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
            'aws_default_region' => str_repeat('a', 256),
        ])
        ->assertSessionHasErrors('aws_default_region');
});

test('update settings request validates aws_bucket max length', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
            'aws_bucket' => str_repeat('a', 256),
        ])
        ->assertSessionHasErrors('aws_bucket');
});

test('update settings request converts app_debug to boolean', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_debug' => '1',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
        ])
        ->assertRedirect();

    expect(\App\Models\Setting::value('app.debug'))->toBe('1');
});

test('update settings request converts aws_use_path_style_endpoint to boolean', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
            'aws_use_path_style_endpoint' => true,
        ])
        ->assertRedirect();

    expect(\App\Models\Setting::value('aws.use_path_style_endpoint'))->toBe('1');
});

test('update settings request masks aws_secret_access_key with asterisks', function () {
    \App\Models\Setting::updateValue('aws.secret_access_key', 'original-secret');

    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
            'aws_secret_access_key' => '********',
        ])
        ->assertRedirect();

    // Secret should remain unchanged when masked
    expect(\App\Models\Setting::value('aws.secret_access_key'))->toBe('original-secret');
});

test('update settings request accepts all valid fields', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Valid App',
            'app_url' => 'https://valid.example.com',
            'app_debug' => true,
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
            'aws_access_key_id' => 'AKIAIOSFODNN7EXAMPLE',
            'aws_secret_access_key' => 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY',
            'aws_default_region' => 'ap-northeast-1',
            'aws_bucket' => 'my-test-bucket',
            'aws_use_path_style_endpoint' => false,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();
});

test('update settings request allows optional aws fields to be null', function () {
    $this->actingAs($this->user)
        ->patch(route('admin.settings.update'), [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_locale' => 'ja',
            'app_fallback_locale' => 'en',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();
});
