<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /**
     * Show the admin settings page.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('admin/settings/index', [
            'appName' => Setting::value('app.name', config('app.name')),
            'appUrl' => Setting::value('app.url', config('app.url')) ?? '',
            'appDebug' => Setting::valueBool('app.debug', config('app.debug')) ?? false,
            'appLocale' => Setting::value('app.locale', config('app.locale')) ?? 'en',
            'appFallbackLocale' => Setting::value('app.fallback_locale', config('app.fallback_locale')) ?? 'en',
            'awsAccessKeyId' => Setting::value('aws.access_key_id', config('filesystems.disks.s3.key')) ?? '',
            'awsSecretAccessKey' => Setting::value('aws.secret_access_key') ? '********' : '',
            'awsDefaultRegion' => Setting::value('aws.default_region', config('filesystems.disks.s3.region')) ?? 'us-east-1',
            'awsBucket' => Setting::value('aws.bucket', config('filesystems.disks.s3.bucket')) ?? '',
            'awsUsePathStyleEndpoint' => Setting::valueBool('aws.use_path_style_endpoint', config('filesystems.disks.s3.use_path_style_endpoint')) ?? false,
        ]);
    }

    /**
     * Update the application settings.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $appName = $validated['app_name'];
        $appUrl = $validated['app_url'];
        $appDebug = $validated['app_debug'];
        $appLocale = $validated['app_locale'];
        $appFallbackLocale = $validated['app_fallback_locale'];
        $awsAccessKeyId = $validated['aws_access_key_id'] ?? null;
        $awsSecretAccessKey = $validated['aws_secret_access_key'] ?? null;
        $awsDefaultRegion = $validated['aws_default_region'] ?? null;
        $awsBucket = $validated['aws_bucket'] ?? null;
        $awsUsePathStyleEndpoint = $validated['aws_use_path_style_endpoint'];

        // Prepare settings to update atomically
        $updates = [
            'app.name' => $appName,
            'app.url' => $appUrl,
            'app.debug' => $appDebug ? '1' : '0',
            'app.locale' => $appLocale,
            'app.fallback_locale' => $appFallbackLocale,
            'aws.use_path_style_endpoint' => $awsUsePathStyleEndpoint ? '1' : '0',
        ];

        if (! is_null($awsAccessKeyId)) {
            $updates['aws.access_key_id'] = $awsAccessKeyId;
        }

        if (! is_null($awsSecretAccessKey) && $awsSecretAccessKey !== '') {
            $updates['aws.secret_access_key'] = $awsSecretAccessKey;
        }

        if (! is_null($awsDefaultRegion)) {
            $updates['aws.default_region'] = $awsDefaultRegion;
        }

        if (! is_null($awsBucket)) {
            $updates['aws.bucket'] = $awsBucket;
        }

        // Update all settings atomically
        Setting::putMany($updates);

        // Update runtime config
        config(['app.name' => $appName]);
        config(['app.url' => $appUrl]);
        config(['app.debug' => $appDebug]);
        config(['app.locale' => $appLocale]);
        config(['app.fallback_locale' => $appFallbackLocale]);

        if (! is_null($awsAccessKeyId)) {
            config(['filesystems.disks.s3.key' => $awsAccessKeyId]);
        }

        if (! is_null($awsSecretAccessKey) && $awsSecretAccessKey !== '') {
            config(['filesystems.disks.s3.secret' => $awsSecretAccessKey]);
        }

        if (! is_null($awsDefaultRegion)) {
            config(['filesystems.disks.s3.region' => $awsDefaultRegion]);
        }

        if (! is_null($awsBucket)) {
            config(['filesystems.disks.s3.bucket' => $awsBucket]);
        }

        config(['filesystems.disks.s3.use_path_style_endpoint' => $awsUsePathStyleEndpoint]);

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Show the environment and configuration viewer.
     */
    public function environment(Request $request): Response
    {
        $envVars = [
            'APP_NAME' => env('APP_NAME'),
            'APP_ENV' => env('APP_ENV'),
            'APP_DEBUG' => env('APP_DEBUG'),
            'APP_URL' => env('APP_URL'),
            'APP_LOCALE' => env('APP_LOCALE'),
            'APP_FALLBACK_LOCALE' => env('APP_FALLBACK_LOCALE'),
            'APP_FAKER_LOCALE' => env('APP_FAKER_LOCALE'),
            'AWS_ACCESS_KEY_ID' => env('AWS_ACCESS_KEY_ID'),
            'AWS_SECRET_ACCESS_KEY' => env('AWS_SECRET_ACCESS_KEY') ? '********' : null,
            'AWS_DEFAULT_REGION' => env('AWS_DEFAULT_REGION'),
            'AWS_BUCKET' => env('AWS_BUCKET'),
            'AWS_USE_PATH_STYLE_ENDPOINT' => env('AWS_USE_PATH_STYLE_ENDPOINT'),
        ];

        $configVars = [
            'app.name' => config('app.name'),
            'app.env' => config('app.env'),
            'app.debug' => config('app.debug'),
            'app.url' => config('app.url'),
            'app.locale' => config('app.locale'),
            'app.fallback_locale' => config('app.fallback_locale'),
            'app.faker_locale' => config('app.faker_locale'),
            'filesystems.disks.s3.key' => config('filesystems.disks.s3.key'),
            'filesystems.disks.s3.secret' => config('filesystems.disks.s3.secret') ? '********' : null,
            'filesystems.disks.s3.region' => config('filesystems.disks.s3.region'),
            'filesystems.disks.s3.bucket' => config('filesystems.disks.s3.bucket'),
            'filesystems.disks.s3.use_path_style_endpoint' => config('filesystems.disks.s3.use_path_style_endpoint'),
        ];

        $dbSettings = [
            'app.name' => Setting::value('app.name'),
            'app.debug' => Setting::valueBool('app.debug'),
            'app.url' => Setting::value('app.url'),
            'app.locale' => Setting::value('app.locale'),
            'app.fallback_locale' => Setting::value('app.fallback_locale'),
            'app.faker_locale' => Setting::value('app.faker_locale'),
            'aws.access_key_id' => Setting::value('aws.access_key_id'),
            'aws.secret_access_key' => Setting::value('aws.secret_access_key') ? '********' : null,
            'aws.default_region' => Setting::value('aws.default_region'),
            'aws.bucket' => Setting::value('aws.bucket'),
            'aws.use_path_style_endpoint' => Setting::valueBool('aws.use_path_style_endpoint'),
        ];

        return Inertia::render('admin/settings/environment/index', [
            'envVars' => $envVars,
            'configVars' => $configVars,
            'dbSettings' => $dbSettings,
        ]);
    }
}
