<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PromotionalBroadcastNotification extends Notification
{
    use Queueable;

    public string $bannerPath;

    public function __construct(
        public string $title,
        public string $bodyMessage,
        public ?string $actionUrl = null,
        ?string $customBanner = null
    ) {
        if ($customBanner) {
            $this->bannerPath = $customBanner;
        } else {
            $defaultBanners = ['images/community_header_01.png', 'images/notif_header_01.png'];
            $this->bannerPath = $defaultBanners[array_rand($defaultBanners)];
        }
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->bodyMessage);

        if ($this->actionUrl) {
            $mail->action('Check It Out', $this->actionUrl);
        }

        return $mail->line('Thank you for being a valued customer of Masaganang Pagpapala!');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'       => $this->title,
            'message'     => $this->bodyMessage,
            'details'     => $this->bodyMessage,
            'action_url'  => $this->actionUrl ?? route('shop'),
            'banner_path' => $this->bannerPath,
            'type'        => 'promotional',
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}