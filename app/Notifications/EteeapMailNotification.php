<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EteeapMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public string $title,
        public ?string $body = null,
        public array $data = [],
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = property_exists($notifiable, 'name') ? $notifiable->name : 'User';
        $message = (new MailMessage)
            ->subject($this->title)
            ->greeting("Dear {$name},");

        if ($this->body) {
            $message->line($this->body);
        }

        if (isset($this->data['url'])) {
            $message->action('View Details', $this->data['url']);
        }

        return $message->line('Thank you for using our application.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->data;
    }
}
