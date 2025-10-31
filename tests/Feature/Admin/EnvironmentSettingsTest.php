<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('environment settings page can be rendered with password confirmation', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('admin.settings.environment.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/settings/environment/index')
            ->has('envVars')
            ->has('configVars')
            ->has('dbSettings')
        );
});

test('environment settings page requires password confirmation', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('admin.settings.environment.index'));

    $response->assertRedirect(route('password.confirm'));
});

test('environment settings page requires authentication', function () {
    $response = $this->get(route('admin.settings.environment.index'));

    $response->assertRedirect(route('login'));
});

test('environment settings page includes expected environment variables', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('admin.settings.environment.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/settings/environment/index')
            ->where('envVars.APP_NAME', config('app.name'))
            ->where('envVars.APP_ENV', config('app.env'))
            ->has('envVars.APP_DEBUG')
            ->has('envVars.APP_URL')
            ->has('envVars.APP_LOCALE')
            ->has('envVars.APP_FALLBACK_LOCALE')
        );
});

test('environment settings page includes expected config variables', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('admin.settings.environment.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/settings/environment/index')
            ->has('configVars')
            ->where('configVars', fn ($configVars) => $configVars['app.name'] === config('app.name') &&
                $configVars['app.env'] === config('app.env') &&
                $configVars['app.debug'] === config('app.debug') &&
                $configVars['app.url'] === config('app.url') &&
                $configVars['app.locale'] === config('app.locale') &&
                $configVars['app.fallback_locale'] === config('app.fallback_locale')
            )
        );
});

test('environment settings page masks sensitive values', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('admin.settings.environment.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/settings/environment/index')
            ->where('envVars', fn ($envVars) => $envVars['AWS_SECRET_ACCESS_KEY'] === '********' || $envVars['AWS_SECRET_ACCESS_KEY'] === null
            )
            ->where('configVars', fn ($configVars) => $configVars['filesystems.disks.s3.secret'] === '********' || $configVars['filesystems.disks.s3.secret'] === null
            )
            ->where('dbSettings', fn ($dbSettings) => $dbSettings['aws.secret_access_key'] === '********' || $dbSettings['aws.secret_access_key'] === null
            )
        );
});
