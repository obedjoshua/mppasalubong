<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $type = 'general',
        public ?string $actionUrl = null,
        public ?string $bannerImage = null
    ) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $defaultBanner = asset('images/community_header_01.png');

        return [
            'title'        => $this->title,
            'message'      => $this->message,
            'details'      => $this->message,
            'type'         => $this->type,
            'action_url'   => $this->actionUrl ?? route('notifications.index'),
            'banner_image' => $this->bannerImage ?? $defaultBanner,
        ];
    }
}