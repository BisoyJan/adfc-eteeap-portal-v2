<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
    ) {}

    public function index(Request $request): Response
    {
        $notifications = $request->user()
            ->appNotifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('notifications/index', [
            'notifications' => $notifications,
        ]);
    }

    public function markRead(Notification $notification, Request $request): RedirectResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to notification.');
        }

        $this->notificationService->markRead($notification);

        return redirect()->back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $this->notificationService->markAllRead($request->user());

        return redirect()->back();
    }
}
