<?php

namespace App\Http\Middleware;

use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user()?->load('roles:id,name,display_name'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'unreadNotificationsCount' => fn () => $request->user()
                ? $request->user()->appNotifications()->unread()->count()
                : 0,
            'recentNotifications' => Inertia::defer(fn () => $request->user()
                ? $request->user()->appNotifications()->latest()->limit(5)->get()
                : collect(),
            ),
        ];
    }
}
