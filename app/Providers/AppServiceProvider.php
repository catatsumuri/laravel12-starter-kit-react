<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        if ($appName = Setting::value('app.name')) {
            config(['app.name' => $appName]);
        }

        if ($appUrl = Setting::value('app.url')) {
            config(['app.url' => $appUrl]);
        }

        if (! is_null($appDebug = Setting::valueBool('app.debug'))) {
            config(['app.debug' => $appDebug]);
        }

        if ($appLocale = Setting::value('app.locale')) {
            config(['app.locale' => $appLocale]);
        }

        if ($appFallbackLocale = Setting::value('app.fallback_locale')) {
            config(['app.fallback_locale' => $appFallbackLocale]);
        }

        $this->syncAwsSettings();
    }

    protected function syncAwsSettings(): void
    {
        $awsKey = Setting::value('aws.access_key_id');
        if (! is_null($awsKey)) {
            config(['filesystems.disks.s3.key' => $awsKey]);
        }

        $awsSecret = Setting::value('aws.secret_access_key');
        if (! is_null($awsSecret)) {
            config(['filesystems.disks.s3.secret' => $awsSecret]);
        }

        $awsRegion = Setting::value('aws.default_region');
        if (! is_null($awsRegion)) {
            config(['filesystems.disks.s3.region' => $awsRegion]);
        }

        $awsBucket = Setting::value('aws.bucket');
        if (! is_null($awsBucket)) {
            config(['filesystems.disks.s3.bucket' => $awsBucket]);
        }

        if (! is_null($usePathStyle = Setting::valueBool('aws.use_path_style_endpoint'))) {
            config(['filesystems.disks.s3.use_path_style_endpoint' => $usePathStyle]);
        }
    }
}
