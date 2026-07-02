<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\EteeapMailNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function send(
        User $recipient,
        string $type,
        string $title,
        ?string $body = null,
        array $data = [],
        bool $email = false,
    ): Notification {
        $notification = Notification::create([
            'user_id' => $recipient->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'channel' => $email ? 'email_and_in_app' : 'in_app',
            'sent_at' => $email ? now() : null,
        ]);

        if ($email) {
            NotificationFacade::send(
                $recipient,
                new EteeapMailNotification($title, $body, $data),
            );
        }

        return $notification;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function sendToRole(
        string $role,
        string $type,
        string $title,
        ?string $body = null,
        array $data = [],
        bool $email = false,
    ): void {
        $recipients = User::whereHas('roles', fn ($q) => $q->where('name', $role))->get();

        foreach ($recipients as $recipient) {
            $this->send($recipient, $type, $title, $body, $data, $email);
        }
    }

    public function markRead(Notification $notification): void
    {
        $notification->update(['read_at' => now()]);
    }

    public function markAllRead(User $user): void
    {
        Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
