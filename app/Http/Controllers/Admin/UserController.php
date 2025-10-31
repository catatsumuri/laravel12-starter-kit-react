<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        $users = User::query()
            ->with('roles')
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/users/index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        return Inertia::render('admin/users/create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        return to_route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): Response
    {
        return Inertia::render('admin/users/show', [
            'user' => $user,
        ]);
    }

    /**
     * Display the activity log for the specified user.
     *
     * このメソッドは、指定されたユーザー「が」実行した操作を表示します。
     * （ユーザー「に対して」行われた操作ではありません）
     */
    public function activities(User $user): Response
    {
        // ユーザーが実行したアクティビティログを取得
        $activities = \Spatie\Activitylog\Models\Activity::causedBy($user)
            ->with(['subject', 'causer'])  // 操作対象と実行者のモデル情報を含める
            ->orderBy('created_at', 'desc')
            ->paginate(20)
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

        return Inertia::render('admin/users/activities', [
            'user' => $user,
            'activities' => $activities,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('admin/users/edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->validated('password'));
        }

        $user->update($data);

        return to_route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account from here.');
        }

        $user->delete();

        return to_route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
