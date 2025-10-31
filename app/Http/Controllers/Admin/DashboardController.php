<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(): Response
    {
        // 最新のアクティビティログを取得（全ユーザー分）
        $recentActivities = \Spatie\Activitylog\Models\Activity::query()
            ->with(['subject', 'causer'])  // 操作対象と実行者の情報を含める
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->through(function ($activity) {
                $props = $activity->properties ?? collect();
                $attrs = $props['attributes'] ?? [];
                $old = $props['old'] ?? [];

                // properties から名前を抽出するヘルパー
                $pick = function (array $arr) {
                    foreach (['name', 'title', 'label', 'subject', 'heading', 'email'] as $k) {
                        if (! empty($arr[$k]) && is_string($arr[$k])) {
                            return $arr[$k];
                        }
                    }

                    return null;
                };

                $subjectLabel = $pick($attrs) ?? $pick($old);

                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'properties' => $activity->properties,
                    'created_at' => $activity->created_at,
                    'subject_type' => class_basename($activity->subject_type),
                    'subject_id' => $activity->subject_id,
                    'subject' => $activity->subject ? [
                        'type' => class_basename($activity->subject_type),
                        'id' => $activity->subject->id,
                        'name' => $activity->subject->name ?? null,
                        'email' => $activity->subject->email ?? null,
                    ] : null,
                    'subject_label' => $subjectLabel,
                    'causer' => $activity->causer ? [
                        'id' => $activity->causer->id,
                        'name' => $activity->causer->name ?? null,
                        'email' => $activity->causer->email ?? null,
                    ] : [
                        'id' => null,
                        'name' => 'System',
                        'email' => null,
                    ],
                ];
            });

        return Inertia::render('admin/dashboard', [
            'recentActivities' => Inertia::scroll(fn () => $recentActivities),
        ]);
    }
}
