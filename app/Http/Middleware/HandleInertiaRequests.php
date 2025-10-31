<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');

        $flash = array_filter([
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
            'info' => $request->session()->get('info'),
            'warning' => $request->session()->get('warning'),
            'status' => $request->session()->get('status'),
        ], fn ($value) => $value !== null);

        $notifications = [];
        if ($request->user()) {
            $rawNotifications = DB::table('notifications')
                ->where('notifiable_type', 'App\\Models\\User')
                ->where('notifiable_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            $notifications = $rawNotifications->map(function ($notification) {
                $data = json_decode($notification->data, true);

                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'info',
                    'title' => $data['title'] ?? '',
                    'message' => $data['message'] ?? '',
                    'time' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                    'read' => $notification->read_at !== null,
                ];
            })->toArray();
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user()?->load('roles'),
            ],
            'features' => [
                'twoFactorAuthentication' => config('features.two_factor_authentication'),
                'appearanceSettings' => config('features.appearance_settings', true),
                'defaultAppearance' => config('features.default_appearance', 'system'),
                'registration' => config('user.registration_enabled'),
                'accountDeletion' => config('user.account_deletion_enabled'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => empty($flash) ? null : $flash,
            'notifications' => $notifications,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
            'translations' => $this->frontendTranslations($locale),
            'fallbackTranslations' => $fallbackLocale !== $locale
                ? $this->frontendTranslations($fallbackLocale)
                : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function frontendTranslations(string $locale): array
    {
        $translations = Lang::get('frontend', [], $locale);

        return is_array($translations) ? $translations : [];
    }
}
